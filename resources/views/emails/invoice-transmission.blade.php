<x-mail::message>
    # Hello {{ $name }},

    <p>You have received an invoice transmission (IRN: {{ $irn }}).
        Please confirm that you have received it by clicking the button below:</p>

    <x-mail::button :url="{{ $confirmUrl }}">
        Confirm Receipt
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}

    <p>If you did not request this, you can ignore this email.</p>
</x-mail::message>
