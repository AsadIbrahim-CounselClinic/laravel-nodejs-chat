<?php

namespace App\Models;

use App\Casts\DecryptMessage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Add this line
        'message', // Include other fields you want to allow mass assignment for
        'name', // Include other fields you want to allow mass assignment for
        'chat_room_id',
        'reply_to',
    ];

    protected $casts = [
        'message' => DecryptMessage::class,
    ];

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
