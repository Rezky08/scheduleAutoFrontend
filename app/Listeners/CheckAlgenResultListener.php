<?php

namespace App\Listeners;

use App\Events\CheckAlgenResultEvent;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CheckAlgenResultListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    private $guzzle_request;
    public function __construct()
    {
        $this->guzzle_request = new Client();
    }

    /**
     * Handle the event.
     *
     * @param  CheckAlgenResultEvent  $event
     * @return void
     */
    public function handle(CheckAlgenResultEvent $event)
    {
        try {
            $res = $this->guzzle_request->get($event->url_python, ['json' => ['celery_id' => $event->celery_id]]);
            $res = $res->getBody()->getContents();
            $res = json_decode($res);
            return $res;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
}
