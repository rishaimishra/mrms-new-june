@component('mail::message')
    Hi Admin
    <div>{{ $user->name }} interested in  {{ $realEstate->id }}</div>
    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
