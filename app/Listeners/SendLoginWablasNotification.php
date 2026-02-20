<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\WablasService;

class SendLoginWablasNotification
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        $wablas = app(WablasService::class);
        $adminPhone = config('services.wablas.admin_phone');

        if ($adminPhone) {
            $message = "User {$user->email} logged in at " . now()->toDateTimeString();
            $wablas->send($adminPhone, $message);
        }
    }
}
