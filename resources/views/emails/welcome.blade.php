@component('mail::message')
# Welcome {{ $user->first_name }}

Thank you for signing up to {{ config('app.name') }}!  We welcome you to our platform!

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
