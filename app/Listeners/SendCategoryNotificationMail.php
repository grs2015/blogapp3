<?php

namespace App\Listeners;

use App\Models\User;

use App\Events\CategoryCreated;
use Illuminate\Support\Facades\Mail;
use App\Mail\CategoryCreatedNotificationMarkdown;


class SendCategoryNotificationMail
{
    public function handleCategoryCreatedNotification(CategoryCreated $event)
    {
        foreach(User::whereAuthor()->get()->pluck('email') as $author_email) {
            Mail::to($author_email)->later(now()->addMinutes(10), new CategoryCreatedNotificationMarkdown($event->title, $event->content));
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
            CategoryCreated::class,
            [SendCategoryNotificationMail::class, 'handleCategoryCreatedNotification']
        );



    }
}
