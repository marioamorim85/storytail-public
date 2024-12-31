<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        @if(isset($logo))
            <img src="{{ $message->embed($logo) }}" alt="StoryTail Logo" style="max-width: 200px;">
        @endif
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px;">
        <h2 style="color: #FF6600; margin-bottom: 20px; border-bottom: 2px solid #FF6600; padding-bottom: 10px;">
            Hello {{ $user->first_name }},
        </h2>
        <p>We have reviewed your comment on the book "{{ $comment->book->title }}" and unfortunately, it did not meet our community guidelines.</p>
        <p style="background: #f5f5f5; padding: 15px; border-radius: 5px; font-style: italic;">
            "{{ $comment->comment_text }}"
        </p>
        <p>Please review our community guidelines and feel free to submit a new comment.</p>
        <p>If you have any questions, don't hesitate to contact us.</p>
    </div>
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>&copy; {{ date('Y') }} StoryTail. All rights reserved.</p>
    </div>
</div>
