<?php

namespace App\Http\Controllers;

use App\Events\ChatMessage;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Sends a list of potential users to chat with for selection UIs.
     * Exclude the current user from the list. Only sends back the id and name.
     * @param Request $request the request
     * @return JsonResponse the response
     */
    public function users(Request $request)
    {
        // only send back list of names and ids
        $users = User::where('id', '!=', $request->user()->id)->get(['id', 'name']);
        return response()->json([
            'status' => 'success',
            'users' => $users
        ]);
    }

    /**
     * Grabs an authenticated user's conversations and returns them.
     * Sorts by most recent message. Also returns read status and conversation users.
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchConversations(Request $request)
    {
        $user = $request->user();
        $involvedConversations = $user->involvedConversations()->get();

        // resolve from relationship
        $conversations = [];
        foreach ($involvedConversations as $involved) {
            $conversation = $involved->conversation;
            // set conversation.users
            $conversation->users = $conversation->users()->get();
            // hide conversation.users.email_verified_at .email .created_at .updated_at
            $conversation->users->makeHidden(['email_verified_at', 'email', 'created_at', 'updated_at']);
            // add read field
            $conversation->unread = $involved->unread();

            $conversations[] = $conversation;
        }

        /*
         * Quicksort the conversations by the last message
         * TODO optimize
         */

        // helper function
        function partition(&$conversations, $low, $high)
        {
            $pivot = $conversations[$high]->messages->last()->created_at;
            $i = $low - 1;
            for ($j = $low; $j < $high; $j++) {
                if ($conversations[$j]->messages->last()->created_at > $pivot) {
                    $i++;
                    $temp = $conversations[$i];
                    $conversations[$i] = $conversations[$j];
                    $conversations[$j] = $temp;
                }
            }
            $temp = $conversations[$i + 1];
            $conversations[$i + 1] = $conversations[$high];
            $conversations[$high] = $temp;
            return $i + 1;
        }

        // quicksort
        function quicksort(&$conversations, $low, $high)
        {
            if ($low < $high) {
                $pi = partition($conversations, $low, $high);
                quicksort($conversations, $low, $pi - 1);
                quicksort($conversations, $pi + 1, $high);
            }
        }

        // call quicksort
        quicksort($conversations, 0, count($conversations) - 1);

        return response()->json([
            'status' => 'success',
            'conversations' => $conversations
        ]);
    }

    /**
     * Fetches a conversation's messages and returns them.
     * @param Request $request the request
     * @param $conversation_id conversation's id
     * @return JsonResponse the response with the messages or error
     */
    public function fetchMessages(Request $request, $conversation_id)
    {
        $user = $request->user();
        $conversation = Conversation::find($conversation_id);
        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ]);
        }

        // guard: check if the user is involved in the conversation
        if (!$conversation->users->contains($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not involved in this conversation'
            ]);
        }

        $messages = $conversation->messages()->with('user')->get();

        // mark messages as read
        $user->involvedConversations()
            ->where('conversation_id', $conversation->id)
            ->update(['unread' => 0]);

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }

    /**
     * Fetches a given conversation and returns it.
     * @param Request $request the request
     * @param $conversation_id conversation's id
     * @return JsonResponse the response with the conversation or error
     */
    public function fetchConversation(Request $request, $conversation_id)
    {
        $user = $request->user();
        $conversation = Conversation::find($conversation_id);
        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ]);
        }

        // guard: check if the user is involved in the conversation
        if (!$conversation->users->contains($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not involved in this conversation'
            ]);
        }

        // hide conversation.users.email_verified_at .email .created_at .updated_at
        $conversation->users->makeHidden(['email_verified_at', 'email', 'created_at', 'updated_at']);

        return response()->json([
            'status' => 'success',
            'conversation' => $conversation
        ]);
    }

    /**
     * Creates a conversation between two users.
     * @param Request $request the request
     * @return JsonResponse the response with the conversation or error
     */
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
        $conversation->name = $user->name . ' and ' . $receiver->name;
        $conversation->save();
        $conversation->users()->attach([$user->id, $receiver->id]);

        // create an inital message to start the conversation from $user
        $message = new Message([
            'user_id' => $user->id,
            'content' => 'Created this conversation.',
            'conversation_id' => $conversation->id
        ]);

        $message->save();
        event(new ChatMessage($conversation));


        return response()->json([
            'status' => 'success',
            'conversation' => $conversation
        ]);
    }

    /**
     * Sends a message to a conversation.
     * @param Request $request the request
     * @return JsonResponse the response with the message or error
     */
    public function sendMessage(Request $request)
    {
        // ensure conversation_id, message (text) are present
        $request->validate([
            'conversation_id' => 'required',
            'message' => 'required'
        ]);
        $user = $request->user();
        $conversation = Conversation::where(
            'id',
            $request->conversation_id
        )->first();

        // Check if the conversation exists
        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ]);
        }

        // guard: check if the user is involved in the conversation
        if (!$conversation->users->contains($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not involved in this conversation'
            ]);
        }

        // create message for user and content as message
        $message = new Message([
            'user_id' => $user->id,
            'content' => $request->message,
            'conversation_id' => $conversation->id
        ]);

        $message->save();

        // update conversation as unread for all non-senders
        foreach ($conversation->users as $user) {
            if ($user->id != $message->user_id) {
                $user->involvedConversations()
                    ->where('conversation_id', $conversation->id)
                    ->update(['unread' => 1]);
            }
        }

        // Event trigger
        event(new ChatMessage($conversation));

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully'
        ]);
    }
}
