<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = ['name', 'is_group'];

    public function participants()
    {
        return $this->hasMany(ChatRoomParticipant::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
