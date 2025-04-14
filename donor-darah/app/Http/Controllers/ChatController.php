<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Menampilkan halaman chat antara 2 user
    public function show($userId)
    {
        $otherUser = User::findOrFail($userId);

        $messages = Message::where(function ($query) use ($userId) {
                $query->where('from_user_id', auth()->id())
                      ->where('to_user_id', $userId);
            })->orWhere(function ($query) use ($userId) {
                $query->where('from_user_id', $userId)
                      ->where('to_user_id', auth()->id());
            })
            ->orderBy('created_at')
            ->get();

        return view('chat.show', compact('messages', 'otherUser'));
    }

    // Mengirim pesan
    public function send(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        Message::create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $userId,
            'message' => $request->message,
        ]);

        return redirect()->route('chat.show', $userId);
    }
}
