@component('mail::message')

<div>Tag title: {{ $title }} </div>
<div>Tag content: {{ $content }} </div>

<div>The tag was updated by: {{ config('contacts.admin_email') }}</div>

@component('mail::button', ['url' => config('contacts.admin_email')])
Button Text
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
