<?php

namespace App\Listeners;

use App\Events\AlgenKelompokDosenEvent;
use App\Events\AlgenKelompokDosenQueEvent;
use App\ProcessLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AlgenKelompokDosenQueListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    private $process_log_model;
    public function __construct()
    {
        $this->process_log_model = new ProcessLog();
    }

    /**
     * Handle the event.
     *
     * @param  AlgenKelompokDosenQueEvent  $event
     * @return void
     */
    public function handle(AlgenKelompokDosenQueEvent $event)
    {
        $process_log = $this->process_log_model->find($event->process_id);
        if (!$process_log) {
            Log::warning('Process ' . $event->process_id . ' Tidak Ditemukan');
            return false;
        }

        while (true) {
            $res = event(new AlgenKelompokDosenEvent($process_log->id));
            if ($res) {
                break;
            }
            sleep(30);
        }
    }
}
