<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::with('user')->get();
        return view('chat', compact('messages'));
    }

    public function store(Request $request)
    {
        $request->validate(['message' => 'required']);
        $message = Message::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'name' => Auth::user()->name,
        ]);

        return response()->json($message);
    }
}
