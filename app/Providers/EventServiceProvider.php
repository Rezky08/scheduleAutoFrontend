<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\AlgenKelompokDosenQueEvent' => [
            'App\Listeners\AlgenKelompokDosenQueListener'
        ],
        'App\Events\AlgenJadwalQueEvent' => [
            'App\Listeners\AlgenJadwalQueListener'
        ],
        'App\Events\AlgenKelompokDosenEvent' => [
            'App\Listeners\AlgenKelompokDosenListener'
        ],
        'App\Events\AlgenJadwalEvent' => [
            'App\Listeners\AlgenJadwalListener'
        ],
        'App\Events\CheckAlgenResultEvent' => [
            'App\Listeners\CheckAlgenResultListener'
        ],
        'App\Events\UpdateStatusKelompokDosenEvent' => [
            'App\Listeners\UpdateStatusKelompokDosenListener'
        ],
        'App\Events\UpdateStatusJadwalEvent' => [
            'App\Listeners\UpdateStatusJadwalListener'
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
