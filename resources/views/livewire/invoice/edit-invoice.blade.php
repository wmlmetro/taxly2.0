<?php

use Livewire\Volt\Component;
use App\Models\Invoice;

new class extends Component {
    public int $invoiceId;
    public string $buyer_organization_ref = '';
    public string $vat_treatment = 'standard';
    public array $items = [];
    public float $tax = 0.0;

    public function mount(Invoice $invoice)
    {
        $this->invoiceId = $invoice->id;
        $this->buyer_organization_ref = $invoice->buyer_organization_ref;
        $this->vat_treatment = $invoice->vat_treatment;
        $this->tax = $invoice->tax_breakdown['VAT'] ?? 0;
        $this->items = $invoice
            ->items()
            ->get(['description', 'quantity', 'price'])
            ->toArray();
    }

    protected function rules()
    {
        return [
            'buyer_organization_ref' => 'required|string',
            'vat_treatment' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }

    public function addItem()
    {
        $this->items[] = ['description' => '', 'quantity' => 1, 'price' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updateInvoice()
    {
        $this->validate();

        $invoice = Invoice::findOrFail($this->invoiceId);

        $invoice->update([
            'buyer_organization_ref' => $this->buyer_organization_ref,
            'vat_treatment' => $this->vat_treatment,
            'total_amount' => collect($this->items)->sum(fn($i) => $i['quantity'] * $i['price']),
            'tax_breakdown' => ['VAT' => $this->tax],
        ]);

        $invoice->items()->delete();

        $itemsWithTotal = collect($this->items)->map(function ($item) {
            return [
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'line_total' => $item['quantity'] * $item['price'],
            ];
        });

        $invoice->items()->createMany($itemsWithTotal);

        session()->flash('success', 'Invoice updated successfully!');
        $this->redirect(route('invoices.index', absolute: false));
    }
}; ?>

<section class="w-full">
    <div class="max-w-4xl mx-auto bg-white dark:bg-zinc-900 shadow-lg rounded-xl p-8 space-y-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">✏️ Edit Invoice #{{ $invoiceId }}</h2>

        @include('livewire.invoice.partials.form')

        <div class="flex justify-end">
            <button wire:click="updateInvoice"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 cursor-pointer">
                💾 Update Invoice
            </button>
        </div>

        @if (session()->has('success'))
            <div class="mt-4 text-green-700 dark:text-green-200 bg-green-100 dark:bg-green-800 p-2 rounded">
                {{ session('success') }}
            </div>
        @endif
    </div>
</section>
