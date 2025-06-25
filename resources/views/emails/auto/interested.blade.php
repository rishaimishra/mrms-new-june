@component('mail::message')
    Hi Admin
    <div>{{ $user->name }} interested in  {{ $auto->name }}</div>

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
