<?php

namespace App\Http\Controllers;

use App\Models\{ChatRoom, Message, ChatRoomParticipant, ChatRequest, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        // $message = Message::create([
        //     'user_id' => Auth::id(),
        //     'message' => $request->message,
        //     'name' => Auth::user()->name,
        // ]);
        $message = Message::create([
            'chat_room_id' => $request->room_id,
            'reply_to' => $request->reply_to,
            'message' => $request->message,
            'user_id' => auth()->id(),
            'name' => Auth::user()->name,
        ]);

        return response()->json($message);
    }

    public function createChatRoom(Request $request, User $user){
        // $request->validate(['participant_id' => 'required|exists|users:id']);

        $createChatRoom = ChatRoom::create([
            'name' => Str::random(10). Auth::user()->email,
            'is_group' => false
        ]);


        ChatRoomParticipant::insert([
            [
                'chat_room_id' => $createChatRoom->id,
                'user_id' => Auth::id(),
            ],
            [
                'chat_room_id' => $createChatRoom->id,
                'user_id' => $user->id,
            ]
        ]);

        return redirect()->back()->withSuccess($createChatRoom->name);

    }

    public function fetchMessages(Request $request, ChatRoom $chatRoom)
    {
        $chatRoom->load('participants', 'messages');

        $participant = $chatRoom->participants->firstWhere('user_id', Auth::id());

        if (!$participant) {
            abort(404, 'You are not a participant in this chat room.');
        }

        $messages = $chatRoom->messages;
        $roomId = $chatRoom->id;
        $roomName = $chatRoom->name;
        return view('chat', compact('messages', 'roomId', 'roomName'));
    }
}
