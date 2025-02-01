<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Botble\Member\Models\Member;


class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $member;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message,)
    {
        $this->message = $message;
        $this->member = Member::find($message->user_id); // Attach member data
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('match.' . $this->message->match_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'member' => $this->member, // Send member data with the event
        ];
    }
}
