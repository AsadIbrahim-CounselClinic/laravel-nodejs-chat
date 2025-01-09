const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const config = require('./config/env');
const corsConfig = require('./config/cors');
const chatSocket = require('./socket/chatSocket');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: corsConfig,
});

// Middleware
app.use(cors(corsConfig));

// Initialize Socket.IO handlers
chatSocket(io);

// Start server
server.listen(config.port, () => {
    console.log(`Socket.IO server is running on port ${config.port}`);
});