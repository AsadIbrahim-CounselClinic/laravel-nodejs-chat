const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const db = require('./utilities/database');
const app = express();

const server = http.createServer(app);

const io = new Server(server, {
    cors: {
        origin: ["http://localhost:8000", "http://127.0.0.1:8000"],
        methods: ["GET", "POST"],
        credentials: true,
    },
});

app.use(cors({
    origin: ["http://localhost:8000", "http://127.0.0.1:8000"],
    methods: ["GET", "POST"],
    credentials: true,
}));


io.on('connection', (socket) => {
    console.log(`User connected: ${socket.id}`);

    socket.on('join-room', (room) => {
        const sql = `
            SELECT chat_room_participants.id
            FROM chat_rooms
            INNER JOIN chat_room_participants
            ON chat_room_participants.chat_room_id = chat_rooms.id
            WHERE chat_room_participants.user_id = ? AND chat_rooms.name = ?
        `;
        const values = [room.userId, room.roomId];
    
        db.execute(sql, values)
            .then(([rows]) => {
                if (rows.length === 1) {

                    socket.join(room.roomId);
                    console.log(`User joined room: ${room.roomId}`);
                    socket.emit('room-joined', { roomId: room.roomId });
                } else {

                    console.log(`Join request denied for user: ${room.userId}`);
                    socket.emit('room-join-error', { message: 'You are not allowed to join this room.' });
                }
            })
            .catch((err) => {
                console.error('Database error:', err);
                socket.emit('room-join-error', { message: 'An error occurred. Please try again later.' });
            });
    });
    

    // Send message
    socket.on('send-message', async (data) => {
        console.log(data);
        const { roomId, message } = data;
    
        try {

            const validationSql = `
                SELECT chat_room_participants.id
                FROM chat_rooms
                INNER JOIN chat_room_participants
                ON chat_room_participants.chat_room_id = chat_rooms.id
                WHERE chat_room_participants.user_id = ? AND chat_rooms.name = ?
            `;
            const validationValues = [message.userId, roomId];
    
            const [validationRows] = await db.execute(validationSql, validationValues);
    
            if (validationRows.length === 0) {
                console.log('User is not allowed to send messages to this room.');
                socket.emit('message-send-error', { message: 'You are not allowed to send messages to this room.' });
                return;
            }
    
            const insertSql = `
            INSERT INTO messages (user_id, message, name, chat_room_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?)
        `;
        
        const currentTime = new Date();
        const insertValues = [message.userId, message.message, message.name, message.roomId, currentTime, currentTime];
    
            const [result] = await db.execute(insertSql, insertValues);
    
            console.log('Insert Result:', result);

            // Emit the message back to the room
            io.to(roomId).emit('receive-message', {
                message: message.message,
                replyTo: message.replyTo || null,
                senderId: socket.id,
                senderName: message.name,
                timestamp: new Date(),
            });
        } catch (err) {
            console.error('Database error:', err);
            socket.emit('message-send-error', { message: 'An error occurred while sending your message. Please try again later.' });
        }
    });
    
    

    socket.on('disconnect', () => {
        console.log(`User disconnected: ${socket.id}`);
    });
});

server.listen(3000, () => {
    console.log('Socket.IO server is running on port 3000');
});


