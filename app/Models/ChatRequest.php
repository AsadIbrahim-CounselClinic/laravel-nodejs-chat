<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRequest extends Model
{
    //
    protected $fillable = ['sender_id', 'recipient_id', 'status'];
}
