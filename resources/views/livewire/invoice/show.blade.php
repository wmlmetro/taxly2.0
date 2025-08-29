<?php

use Livewire\Volt\Component;
use App\Models\Invoice;

new class extends Component {
    public Invoice $invoice;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load('items');
    }
}; ?>

<section class="w-full">
    <div class="max-w-7xl mx-auto bg-white dark:bg-zinc-900 shadow-lg rounded-xl p-8 space-y-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">
            🧾 Invoice #{{ $invoice->id }}
        </h2>

        {{-- Invoice Header --}}
        <div class="grid grid-cols-2 gap-6 text-sm text-zinc-700 dark:text-zinc-300">
            <div>
                <p><span class="font-semibold">Buyer Ref:</span> {{ $invoice->buyer_organization_ref }}</p>
                <p><span class="font-semibold">Invoice Type:</span> {{ $invoice->invoice_type_code }}</p>
                <p><span class="font-semibold">Currency:</span> {{ $invoice->document_currency_code }}</p>
                <p><span class="font-semibold">VAT Treatment:</span> {{ ucfirst($invoice->vat_treatment) }}</p>
            </div>
            <div>
                <p><span class="font-semibold">Status:</span> {{ ucfirst($invoice->status) }}</p>
                <p><span class="font-semibold">Issue Date:</span>
                    {{ \Carbon\Carbon::parse($invoice->issue_date)->format('d M, Y') }}</p>
                <p><span class="font-semibold">Due Date:</span>
                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</p>
                <p><span class="font-semibold">Created:</span> {{ $invoice->created_at->format('d M, Y') }}</p>
            </div>
        </div>

        {{-- Supplier --}}
        <div class="border-t pt-4">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200 mb-2">Supplier</h3>
            <p><span class="font-semibold">Name:</span> {{ $invoice->supplier['party_name'] ?? '-' }}</p>
            <p><span class="font-semibold">TIN:</span> {{ $invoice->supplier['tin'] ?? '-' }}</p>
            <p><span class="font-semibold">Email:</span> {{ $invoice->supplier['email'] ?? '-' }}</p>
            <p><span class="font-semibold">Address:</span>
                {{ $invoice->supplier['postal_address']['street_name'] ?? '' }},
                {{ $invoice->supplier['postal_address']['city_name'] ?? '' }},
                {{ $invoice->supplier['postal_address']['postal_zone'] ?? '' }},
                {{ $invoice->supplier['postal_address']['country'] ?? '' }}
            </p>
        </div>

        {{-- Customer --}}
        <div class="border-t pt-4">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200 mb-2">Customer</h3>
            <p><span class="font-semibold">Name:</span> {{ $invoice->customer['party_name'] ?? '-' }}</p>
            <p><span class="font-semibold">TIN:</span> {{ $invoice->customer['tin'] ?? '-' }}</p>
            <p><span class="font-semibold">Email:</span> {{ $invoice->customer['email'] ?? '-' }}</p>
            <p><span class="font-semibold">Address:</span>
                {{ $invoice->customer['postal_address']['street_name'] ?? '' }},
                {{ $invoice->customer['postal_address']['city_name'] ?? '' }},
                {{ $invoice->customer['postal_address']['postal_zone'] ?? '' }},
                {{ $invoice->customer['postal_address']['country'] ?? '' }}
            </p>
        </div>

        {{-- Items --}}
        <div class="border-t pt-4">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200 mb-2">Items</h3>
            <table class="w-full text-sm border rounded-lg">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="p-2 text-left">Description</th>
                        <th class="p-2 text-left">HSN</th>
                        <th class="p-2 text-left">Category</th>
                        <th class="p-2 text-right">Qty</th>
                        <th class="p-2 text-right">Unit Price</th>
                        <th class="p-2 text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="p-2">{{ $item->description }}</td>
                            <td class="p-2">{{ $item->hsn_code }}</td>
                            <td class="p-2">{{ $item->product_category }}</td>
                            <td class="p-2 text-right">{{ $item->quantity }}</td>
                            <td class="p-2 text-right">₦{{ number_format($item->price, 2) }}</td>
                            <td class="p-2 text-right">₦{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="flex justify-end space-x-8 text-right text-zinc-700 dark:text-zinc-300 border-t pt-4">
            <div>
                <p class="font-semibold">Line Extension:</p>
                <p class="font-semibold">Tax Exclusive:</p>
                <p class="font-semibold">Tax Inclusive:</p>
                <p class="font-semibold">Tax (VAT):</p>
                <p class="font-bold">Grand Total:</p>
            </div>
            <div>
                <p>₦{{ number_format($invoice->legal_monetary_total['line_extension_amount'] ?? $invoice->items->sum('line_total'), 2) }}
                </p>
                <p>₦{{ number_format($invoice->legal_monetary_total['tax_exclusive_amount'] ?? 0, 2) }}</p>
                <p>₦{{ number_format($invoice->legal_monetary_total['tax_inclusive_amount'] ?? 0, 2) }}</p>
                <p>₦{{ number_format($invoice->tax_breakdown['VAT'] ?? 0, 2) }}</p>
                <p class="font-bold">₦{{ number_format($invoice->total_amount, 2) }}</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-2">
            <a href="{{ route('invoices.index') }}"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 cursor-pointer">
                ⬅ Back to Invoices
            </a>

            <a href="{{ route('invoices.pdf', $invoice->id) }}"
                class="px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 cursor-pointer">
                ⬇ Download PDF
            </a>
            <a href="{{ route('invoices.email', $invoice->id) }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 cursor-pointer">
                📧 Send via Email
            </a>
        </div>
    </div>
</section>
