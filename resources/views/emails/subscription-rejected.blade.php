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
        <p>Your request for a Premium subscription has been reviewed.</p>
        <p>Unfortunately, we cannot approve your request at this time.</p>
        <p>If you have any questions or would like to submit a new request, please don't hesitate to contact us.</p>
        <p style="margin-top: 20px;">Thank you for your interest in StoryTail Premium!</p>
    </div>
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>&copy; {{ date('Y') }} StoryTail. All rights reserved.</p>
    </div>
</div>
