<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\TagCreated;
use Illuminate\Support\Facades\Mail;
use App\Mail\TagCreatedNotificationMarkdown;

class SendTagNotificationMail
{
    public function handleTagCreatedNotification(TagCreated $event)
    {
        foreach(User::whereAuthor()->get()->pluck('email') as $author_email) {
            Mail::to($author_email)->later(now()->addMinutes(10), new TagCreatedNotificationMarkdown($event->title, $event->content));
            // send(new TagCreatedNotificationMarkdown($event->title, $event->content));
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
            TagCreated::class,
            [SendTagNotificationMail::class, 'handleTagCreatedNotification']
        );
    }
}
