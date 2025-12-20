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
        
        <p>Thanks for signing up for StoryTail!</p>
        
        <p>Before getting started, could you verify your email address by clicking the link below?</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #FF6600; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                Verify Email Address
            </a>
        </div>
        
        <p>If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</p>
        <p style="word-break: break-all; color: #666; font-size: 14px;">{{ $url }}</p>

        <p style="margin-top: 20px;">If you didn't create an account, no further action is required.</p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
        <p>&copy; {{ date('Y') }} StoryTail. All rights reserved.</p>
    </div>
</div>
