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
        <p>Welcome to the Premium Plan of StoryTail!</p>
        <p>Your subscription request has been approved and you can now enjoy exclusive resources and premium-quality content.</p>
        <p style="margin-top: 20px;">If you have any questions, feel free to reach out. We're here to help!</p>
    </div>
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>&copy; {{ date('Y') }} StoryTail. All rights reserved.</p>
    </div>
</div>
