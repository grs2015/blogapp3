<?php

namespace App\Listeners;

use App\Events\PostCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\PostCreatedNotificationMarkdown;

class SendNotificationMailToAdmin
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
     * @param  \App\Events\PostCreated  $event
     * @return void
     */
    public function handle(PostCreated $event)
    {
        Mail::to('admin@admin.com')->send(new PostCreatedNotificationMarkdown($event->user, $event->title, $event->summary));
    }
}
