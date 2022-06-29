@component('mail::message')

<div>Category title: {{ $title }} </div>
<div>Category content: {{ $content }} </div>

<div>The category was prepared by: {{ config('contacts.admin_email') }}</div>

@component('mail::button', ['url' => config('contacts.admin_email')])
Button Text
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
