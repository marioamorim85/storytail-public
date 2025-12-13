<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        @if(isset($logo))
            <img src="{{ $logo }}" alt="StoryTail Logo" style="max-width: 200px;">
        @endif
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px;">
        <h2 style="color: #FF6600; margin-bottom: 20px; border-bottom: 2px solid #FF6600; padding-bottom: 10px;">
            Hello {{ $user->first_name }},
        </h2>
        <p>Your comment on the book "{{ $comment->book->title }}" has been approved and is now visible to other users.</p>
        <p style="background: #f5f5f5; padding: 15px; border-radius: 5px; font-style: italic;">
            "{{ $comment->comment_text }}"
        </p>
        <p>Thank you for contributing to our community!</p>
    </div>
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>&copy; {{ date('Y') }} StoryTail. All rights reserved.</p>
    </div>
</div>
