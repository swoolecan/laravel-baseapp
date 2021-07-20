<?php

namespace Framework\Baseapp\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

abstract class AbstractEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user User
     */
    /*public function __construct(User $user)
    {
        $this->user = $user;
    }*/

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    /*public function broadcastOn(): Channel
    {
        return new Channel('post.' . $this->post->id);
        return new PrivateChannel('channel-name');
    }*/

    /**
     * The event's broadcast name.
     */
    /*public function broadcastAs(): string
    {
        return 'comment.posted';
    }*/
}
