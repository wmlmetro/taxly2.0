@component('mail::message')
    # Invoice #{{ $invoice->id }}

    Dear Customer,

    Please find attached your invoice.

    **Buyer Ref:** {{ $invoice->buyer_organization_ref }}
    **Total Amount:** ₦{{ number_format($invoice->total_amount, 2) }}
    **Status:** {{ ucfirst($invoice->status) }}

    @component('mail::button', ['url' => route('invoices.show', $invoice->id)])
        View Invoice Online
    @endcomponent

    Thanks,
    {{ config('app.name') }}
@endcomponent
