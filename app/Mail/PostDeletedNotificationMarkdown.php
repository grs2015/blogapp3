<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostDeletedNotificationMarkdown extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public Model $user,
        public string $title,
        public string $summary
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from($this->user->email, $this->user->first_name . ' ' . $this->user->role)
            ->markdown('emails.posts.deleted', [
                'title' => $this->title,
                'summary' => $this->summary,
                'user' => $this->user
            ]);
    }
}
