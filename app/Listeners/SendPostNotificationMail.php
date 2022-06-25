<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Events\PostUpdated;
use Illuminate\Support\Facades\Mail;
use App\Mail\PostCreatedNotificationMarkdown;
use App\Mail\PostUpdatedNotificationMarkdown;

class SendPostNotificationMail
{
    /**
     * Handle user login events.
     */
    public function handlePostCreatedNotification(PostCreated $event)
    {
        Mail::to(config('contacts.admin_email'))->send(new PostCreatedNotificationMarkdown($event->user, $event->title, $event->summary));
    }

    /**
     * Handle user logout events.
     */
    public function handlePostUpdatedNotification(PostUpdated $event)
    {
        if ($event->user->isAuthor()) {
            Mail::to(config('contacts.admin_email'))->send(new PostUpdatedNotificationMarkdown($event->user, $event->title, $event->summary));
        } else {
            Mail::to($event->author->email)->send(new PostUpdatedNotificationMarkdown($event->user, $event->title, $event->summary));
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            PostCreated::class,
            [SendPostNotificationMail::class, 'handlePostCreatedNotification']
        );

        $events->listen(
            PostUpdated::class,
            [SendPostNotificationMail::class, 'handlePostUpdatedNotification']
        );
    }
}
