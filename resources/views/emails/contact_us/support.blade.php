@component('mail::message')
# New Contact Us Message

The message:<br>

@component('mail::panel')

    {{ $user->message }}

@endcomponent

User Info:<br>

@component('mail::panel')

    {{--$model->getAttributes() will return an array of raw attributes (as they are stored in the database), --}}
    {{--and $model->toArray() will return all the model's raw, mutated, and appended attributes.--}}

    <strong>ID:</strong>            {{ $user->id }} <br>
    <strong>FIRST NAME:</strong>    {{ $user->first_name }} <br>
    <strong>LAST NAME:</strong>     {{ $user->last_name }} <br>
    <strong>EMAIL:</strong>         {{ $user->email }} <br>
    <strong>PHONE #:</strong>       {{ $user->phone_number }} <br>
    <strong>PREFERS CALL:</strong>  {{ $user->prefer_call ? 'Yes' : 'No' }} <br>

@endcomponent


Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
