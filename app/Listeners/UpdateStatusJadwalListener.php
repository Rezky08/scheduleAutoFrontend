<?php

namespace App\Listeners;

use App\Events\AlgenJadwalEvent;
use App\Events\UpdateStatusJadwalEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStatusJadwalListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UpdateStatusJadwalEvent  $event
     * @return void
     */
    public function handle(UpdateStatusJadwalEvent $event)
    {
        foreach ($event->process_id as $index => $item) {
            event(new AlgenJadwalEvent($item));
        }
    }
}
