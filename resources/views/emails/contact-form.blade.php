<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ $message->embed($logo) }}" alt="StoryTail Logo" style="max-width: 200px;">
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px;">
        <h2 style="color: #FF6600; margin-bottom: 20px; border-bottom: 2px solid #FF6600; padding-bottom: 10px;">
            New Contact Message from {{ $name }}
        </h2>
        <div style="margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px;">From:</p>
            <p style="color: #444; margin-top: 0;">{{ $email }}</p>
        </div>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px;">Message:</p>
            <p style="color: #444; margin-top: 0;">{{ $messageText }}</p>
        </div>
    </div>
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>&copy; {{ date('Y') }} StoryTail. All rights reserved.</p>
    </div>
</div>
