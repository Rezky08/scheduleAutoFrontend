<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlgenJadwalEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $process_id;
    public $url_python;
    public $expired;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($process_id)
    {
        $this->process_id = $process_id;
        $this->url_python = "http://localhost:5000/jadwal/result";
        $this->expired = 6;
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
