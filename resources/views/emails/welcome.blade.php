@component('mail::message')
# Welcome {{ $user->first_name }}

Thank you for signing up to Coach Hub!  Welcome to one of the worlds top Learning platforms!

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
