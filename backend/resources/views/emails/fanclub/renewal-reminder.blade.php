@component('mail::message')
# Hi {{ $name }},

Your KLP48 Fanclub membership is set to expire on **{{ $expiresAt }}**.

Renew now to keep your benefits and avoid any interruption to your access.

@component('mail::button', ['url' => $renewUrl])
Renew Membership
@endcomponent

Thanks for being part of the KLP48 family!

Thanks,<br>
{{ config('app.name') }}
@endcomponent
