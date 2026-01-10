<?php

namespace App\Mail;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseCreatedNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Course $course
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Курс успешно создан - ' . $this->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.course-created',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
