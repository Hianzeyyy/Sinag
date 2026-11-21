<x-mail::message>
# Your report has been resolved

The GAD Office has resolved report **{{ $incidentId }}**. You can now sign in to SINAG and share feedback from your Evidence Vault.

<x-mail::button :url="$loginUrl">
Sign in to SINAG
</x-mail::button>

Thank you,<br>
SINAG - PSU GAD
</x-mail::message>
