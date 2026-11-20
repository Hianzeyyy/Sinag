@component('mail::message')
# Hello {{ $userName }},

@if ($status === 'approved')
Your SINAG institutional account has been verified and approved by the GAD administrator. You can now sign in using your institutional email.

@if ($remarks)
**Administrator's note:** {{ $remarks }}
@endif

<x-mail::button :url="$loginUrl">
Sign in to SINAG
</x-mail::button>
@else
We regret to inform you that your SINAG account registration was declined by the administrator.

@if ($remarks)
**Reason:** {{ $remarks }}

@endif
Please verify your institutional details before re-applying. If you have questions or wish to re-apply, contact the PSU Asingan GAD office.
@endif

Regards,<br>
SINAG - PSU GAD
@endcomponent
