<?php

use function Livewire\Volt\{state, rules};
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;

state([
    'buyer_organization_ref' => '',
    'vat_treatment' => 'standard',
    'items' => [['description' => '', 'quantity' => 1, 'price' => 0]],
    'tax' => 0,
]);

rules([
    'buyer_organization_ref' => 'required|string',
    'vat_treatment' => 'required|string|in:standard,zero-rated,exempt',
    'items.*.description' => 'required|string',
    'items.*.quantity' => 'required|numeric|min:1',
    'items.*.price' => 'required|numeric|min:0',
    'tax' => 'nullable|numeric|min:0',
]);

$addItem = function () {
    $this->items[] = ['description' => '', 'quantity' => 1, 'price' => 0];
};

$removeItem = function ($index) {
    unset($this->items[$index]);
    $this->items = array_values($this->items);
};

$subtotal = fn() => collect($this->items)->sum(fn($i) => $i['quantity'] * $i['price']);
$total = fn() => $this->subtotal() + $this->tax;

$save = function () {
    $this->validate();

    // Save Invoice header
    $invoice = Invoice::create([
        'organization_id' => Auth::user()->organization_id,
        'buyer_organization_ref' => $this->buyer_organization_ref,
        'vat_treatment' => $this->vat_treatment,
        'total_amount' => $this->total(),
        'tax_breakdown' => json_encode(['tax' => $this->tax]),
        'status' => 'draft',
    ]);

    // Save line items
    foreach ($this->items as $item) {
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $item['description'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'line_total' => $item['quantity'] * $item['price'],
        ]);
    }

    session()->flash('success', 'Invoice created!');
    $this->reset(['buyer_organization_ref', 'vat_treatment', 'items', 'tax']);
    $this->items = [['description' => '', 'quantity' => 1, 'price' => 0]];
    $this->redirect(route('invoices.index', absolute: false));
};
?>

<section class="w-full">
    <div class="max-w-4xl mx-auto bg-white dark:bg-zinc-900 shadow-lg rounded-xl p-8 space-y-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">🧾 Create Invoice</h2>

        @include('livewire.invoice.partials.form')

        {{-- Submit --}}
        <div class="flex justify-end">
            <button wire:click="save"
                class="px-6 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 cursor-pointer">
                💾 Save Invoice
            </button>
        </div>

        @if (session()->has('success'))
            <div class="mt-4 text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-800 p-2 rounded">
                {{ session('success') }}
            </div>
        @endif
    </div>
</section>
