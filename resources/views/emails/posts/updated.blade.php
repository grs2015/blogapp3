@component('mail::message')

<div>Post title: {{ $title }} </div>
<div>Post summary: {{ $summary }} </div>

<div>The post was updated by: {{ $user->first_name }} {{ $user->last_name }}</div>

@component('mail::button', ['url' => $user->email])
Button Text
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
