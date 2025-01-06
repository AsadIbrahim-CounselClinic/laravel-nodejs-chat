const crypto = require('crypto');
require('dotenv').config();

const LARAVEL_KEY = Buffer.from(process.env.APP_KEY.slice(7), 'base64'); // Remove "base64:" prefix
if (LARAVEL_KEY.length !== 32) {
    throw new Error('Invalid key length: Expected 32 bytes');
}

const IV_LENGTH = 16; // AES block size

function encryptMessage(message) {

    const iv = crypto.randomBytes(IV_LENGTH);
    const cipher = crypto.createCipheriv('aes-256-cbc', LARAVEL_KEY, iv);
    let encrypted = cipher.update(message, 'utf8', 'base64');
    encrypted += cipher.final('base64');
    return `${iv.toString('base64')}:${encrypted}`;
    
}

module.exports = { encryptMessage };
