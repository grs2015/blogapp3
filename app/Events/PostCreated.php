<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $user;
    public $title;
    public $summary;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Model $user, string $title, string $summary)
    {
        $this->user = $user;
        $this->title = $title;
        $this->summary = $summary;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
