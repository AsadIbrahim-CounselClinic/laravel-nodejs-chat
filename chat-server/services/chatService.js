const db = require('../utilities/database');

class ChatService {
    static async joinRoom(userId, roomId) {
        const sql = `
            SELECT crp.id
            FROM chat_rooms AS cr
            INNER JOIN chat_room_participants AS crp
            ON crp.chat_room_id = cr.id
            WHERE crp.user_id = ? AND cr.name = ?
        `;
        const values = [userId, roomId];
        const [rows] = await db.execute(sql, values);
        return rows.length === 1;
    }

    static async sendMessage(message) {

        const { userId, roomId, content, name, roomName } = message;
        const validationSql = `
            SELECT crp.id
            FROM chat_rooms AS cr
            INNER JOIN chat_room_participants AS crp
            ON crp.chat_room_id = cr.id
            WHERE crp.user_id = ? AND cr.name = ?
        `;
        const validationValues = [userId, roomName];
        const [validationRows] = await db.execute(validationSql, validationValues);

        if (validationRows.length === 0) {
            throw new Error('User is not allowed to send messages to this room.');
        }

        const insertSql = `
            INSERT INTO messages (user_id, message, name, chat_room_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?)
        `;
        const currentTime = new Date();
        const insertValues = [userId, content, name, roomId, currentTime, currentTime];
        const [result] = await db.execute(insertSql, insertValues);
        return result;
        
    }
}

module.exports = ChatService;