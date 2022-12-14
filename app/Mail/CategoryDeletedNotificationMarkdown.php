<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CategoryDeletedNotificationMarkdown extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public string $title,
        public string $content
    ) {  }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(config('contacts.admin_email'), 'ADMIN DE')
            ->markdown('emails.categories.deleted', [
                'title' => $this->title,
                'content' => $this->content,
            ]);
    }
}
