<?php

namespace App\Mail;

use App\Post\Entities\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function build(): PostPublishedMail
    {
        return $this->subject('New Post Published!')
            ->view('emails.post_published')
            ->with([
                'title'   => $this->post->title,
                'description' => $this->post->description,
            ]);
    }
}
