@component('mail::message')
# Hello {{ $user->first_name }}

Thanks for reaching out, we have received your message.<br>

Your message:<br>

@component('mail::panel')

    {{ $user->message }}

@endcomponent

One of our customer support specialists will be in contact with you shortly.

Thanks,<br>
The {{ config('app.name') }} Team <br><br>

<strong>Reference ID:</strong>            {{ $user->id }} <br>

@endcomponent
