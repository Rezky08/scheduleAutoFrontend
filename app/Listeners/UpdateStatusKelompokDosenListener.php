<?php

namespace App\Listeners;

use App\Events\AlgenKelompokDosenEvent;
use App\Events\UpdateStatusKelompokDosenEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStatusKelompokDosenListener
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
     * @param  UpdateStatusKelompokDosenEvent  $event
     * @return void
     */
    public function handle(UpdateStatusKelompokDosenEvent $event)
    {
        foreach ($event->process_id as $index => $item) {
            event(new AlgenKelompokDosenEvent($item));
        }
    }
}
