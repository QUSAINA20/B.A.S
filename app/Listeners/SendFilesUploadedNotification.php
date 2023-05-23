<?php

namespace App\Listeners;

use App\Events\FilesUploadedEvent;
use App\Notifications\FilesUploadedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFilesUploadedNotification
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
     * @param  \App\Events\FilesUploadedEvent  $event
     * @return void
     */
    public function handle(FilesUploadedEvent $event)
    {
        $user = $event->user;
        $urls = $event->urls;
        $notification = new FilesUploadedNotification($urls, $user);


        Notification::send($user, $notification);
    }
}
