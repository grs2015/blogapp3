<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostCreatedNotificationMarkdown extends Mailable
{
    use Queueable, SerializesModels;

    public Model $user;
    public $title;
    public $summary;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Model $user, string $title, string $summary)
    {
        $this->user = $user;
        $this->title = $title;
        $this->summary = $summary;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from($this->user->email, 'ADMIN DE')
            ->markdown('emails.posts.created', [
                'title' => $this->title,
                'summary' => $this->summary,
                'user' => $this->user
            ]);
    }
}
