<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\TagCreated;
use App\Events\TagDeleted;
use App\Events\TagUpdated;
use Illuminate\Support\Facades\Mail;
use App\Mail\TagCreatedNotificationMarkdown;
use App\Mail\TagDeletedNotificationMarkdown;
use App\Mail\TagUpdatedNotificationMarkdown;

class SendTagNotificationMail
{
    public function handleTagCreatedNotification(TagCreated $event)
    {
        foreach(User::whereAuthor()->get()->pluck('email') as $author_email) {
            Mail::to($author_email)->later(now()->addMinutes(10), new TagCreatedNotificationMarkdown($event->title, $event->content));
            // send(new TagCreatedNotificationMarkdown($event->title, $event->content));
        }
    }

    public function handleTagUpdatedNotification(TagUpdated $event)
    {
        foreach(User::whereAuthor()->get()->pluck('email') as $author_email) {
            Mail::to($author_email)->later(now()->addMinutes(10), new TagUpdatedNotificationMarkdown($event->title, $event->content));
            // send(new TagCreatedNotificationMarkdown($event->title, $event->content));
        }
    }

    public function handleTagDeletedNotification(TagDeleted $event)
    {
        foreach(User::whereAuthor()->get()->pluck('email') as $author_email) {
            Mail::to($author_email)->later(now()->addMinutes(10), new TagDeletedNotificationMarkdown($event->title, $event->content));
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

        $events->listen(
            TagUpdated::class,
            [SendTagNotificationMail::class, 'handleTagUpdatedNotification']
        );

        $events->listen(
            TagDeleted::class,
            [SendTagNotificationMail::class, 'handleTagDeletedNotification']
        );


    }
}
