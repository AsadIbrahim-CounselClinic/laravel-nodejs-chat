const ChatService = require('../services/chatService');
const encryption = require('../utilities/message_encryption');

module.exports = (io) => {
    io.on('connection', (socket) => {
        console.log(`User connected: ${socket.id}`);

        socket.on('join-room', async (room) => {
            try {
                const isAllowed = await ChatService.joinRoom(room.userId, room.roomId);
                if (isAllowed) {
                    socket.join(room.roomId);
                    console.log(`User joined room: ${room.roomId}`);
                    socket.emit('room-joined', { roomId: room.roomId });
                } else {
                    console.log(`Join request denied for user: ${room.userId}`);
                    socket.emit('room-join-error', { message: 'You are not allowed to join this room.' });
                }
            } catch (err) {
                console.error('Database error:', err);
                socket.emit('room-join-error', { message: 'An error occurred. Please try again later.' });
            }
        });

        socket.on('send-message', async (data) => {

            try {
                const { message } = data;
                const encryptedMessage = encryption.encryptMessage(message.message);
                await ChatService.sendMessage({
                    userId: message.userId,
                    roomId: message.roomId,
                    roomName: message.roomName,
                    content: encryptedMessage,
                    name: message.name,
                });

                io.to(message.roomName).emit('receive-message', {
                    message: message.message,
                    replyTo: message.replyTo || null,
                    senderId: socket.id,
                    senderName: message.name,
                    timestamp: new Date(),
                });
            } catch (err) {
                console.error('Error sending message:', err);
                socket.emit('message-send-error', { message: err.message });
            }
        });

        socket.on('disconnect', () => {
            console.log(`User disconnected: ${socket.id}`);
        });
    });
};