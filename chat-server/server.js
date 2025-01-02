const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

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
    console.log('A user connected');
    socket.on('new-message', (message) => {
        io.emit('receive-message', message);
    });
    socket.on('disconnect', () => {
        console.log('A user disconnected');
    });
});

server.listen(3000, () => {
    console.log('Socket.IO server is running on port 3000');
});


// So here are the following requirements we need to fullfil. 
// 1. Right now the channel is open which means whoever enters the links which get the messages from all the participants. Which is not good. So the user should first request for the chat to some user and than that chat between those 2 users should be private between 2 participants. Might need to update the database schema also
// 2.  The messages should be encripted following the HIPAA complience. 