<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $conversation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($conversation)
    {
        $this->conversation = $conversation;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = [];
        foreach ($this->conversation->users as $user) {
            // add a private channel for each user
            $channels[] = new PrivateChannel('new-message-notification.'.$user['id']);
        }
        return $channels;
    }

}
