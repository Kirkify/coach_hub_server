@component('mail::message')
# Welcome {{ $user->first_name }}

Thank you for signing up to Coach Hub!  Please verify your email address by clicking the button below

@component('mail::button', [
    'url' => config('app.url') . '/identify/verify?email=' . urlencode($user->email) . '&token=' . urlencode($user->email_token)
    ])
Confirm Email Address
@endcomponent

or enter this confirmation code <br>

{{ $user->email_token }} <br>

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
