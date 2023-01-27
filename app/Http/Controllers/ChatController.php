<?php

namespace App\Http\Controllers;

use App\Events\ChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function users(Request $request)
    {
        // only send back list of names and ids
        $users = User::where('id', '!=', $request->user()->id)->get(['id', 'name']);
        return response()->json([
            'status' => 'success',
            'users' => $users
        ]);
    }

    public function fetchConversations(Request $request)
    {
        $user = $request->user();
        $conversations = $user->involvedConversations()->get();

        return response()->json([
            'status' => 'success',
            'conversations' => $conversations
        ]);
    }

    public function fetchMessages(Request $request)
    {
        $user = $request->user();
        $conversation = Conversation::find($request->conversation_id);
        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ]);
        }
        $messages = $conversation->messages()->with('user')->get();
        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }


    public function createConversation(Request $request)
    {
        // logger
        logger($request->all());
        // validate receiver_id
        $request->validate([
            'receiver_id' => 'required'
        ]);
        // All conversations start with the user and a receiver
        $user = $request->user();
        $receiver = User::find($request->receiver_id);

        // if receiver exists
        if (!$receiver) {
            return response()->json([
                'status' => 'error',
                'message' => 'Receiver not found'
            ]);
        }

        // Check if the conversation already exists between the two users
        // get all involved conversations
        $conversations = $user->involvedConversations()->get();
        // loop through conversations
        foreach ($conversations as $involved) {
            $conversation = $involved->conversation;
            // check if the conversation has the receiver
            if ($conversation->users->contains($receiver) && $conversation->users->count() == 2) {
                return response()->json([
                    'status' => 'success',
                    'conversation' => $conversation
                ]);
            }
        }

            $conversation = new Conversation();
            $conversation->title = $user->name . ' and ' . $receiver->name;
            $conversation->save();
            $conversation->users()->attach([$user->id, $receiver->id]);


        return response()->json([
            'status' => 'success',
            'conversation' => $conversation
        ]);
    }

    public function sendMessage(Request $request)
    {
        // ensure conversation_id, message (text) are present
        $request->validate([
            'conversation_id' => 'required',
            'message' => 'required'
        ]);
        $user = $request->user();
        $conversation = Conversation::find($request->conversation_id);

        // Check if the conversation exists
        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ]);
        }

        // guard: user in conversation.users()
        if (!$conversation->users->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not part of this conversation'
            ]);
        }

        // create message for user and content as message
        $message = new Message([
            'user_id' => $user->id,
            'content' => $request->message,
            'conversation_id' => $conversation->id
        ]);

        // save message
        $message->save();

        // Send the message to the conversation
        event(new ChatMessage($conversation));

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully'
        ]);
    }
}
