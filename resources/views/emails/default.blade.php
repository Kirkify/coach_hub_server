@component('mail::message')
# Hey {{ $user->first_name }}

{{ $message }}

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
