@component('mail::message')
# Welcome {{ $user->first_name }}

Thank you for signing up to Coach Hub!  Please verify your email address by clicking the button below

@component('mail::button', [
    'url' => config('app.url') . '/identify/verify?email=' . urlencode($user->email) . '&token=' . urlencode($user->confirmEmail->token)
    ])
Confirm Email Address
@endcomponent

or you may enter the confirmation code below: <br>

{{ $user->confirmEmail->token }} <br>

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
