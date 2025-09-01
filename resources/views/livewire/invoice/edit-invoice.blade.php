<?php

use Livewire\Volt\Component;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Services\WestMetroApiService;

new class extends Component {
    public int $invoiceId;

    // Header
    public string $issue_date = '';
    public string $due_date = '';
    public string $invoice_type_code = '396';
    public string $document_currency_code = 'NGN';
    public string $vat_treatment = 'standard';
    public array $items = [];
    public float $tax = 0.0;
    public array $invoiceTypes = [];
    public float $subtotal = 0.0;
    public float $total = 0.0;

    // Parties
    public array $customer = [
        'party_name' => '',
        'tin' => '',
        'email' => '',
        'postal_address' => [
            'street_name' => '',
            'city_name' => '',
            'postal_zone' => '',
            'country' => 'NG',
        ],
    ];

    public array $supplier = [
        'party_name' => '',
        'tin' => '',
        'email' => '',
        'postal_address' => [
            'street_name' => '',
            'city_name' => '',
            'postal_zone' => '',
            'country' => 'NG',
        ],
    ];

    public function mount(Invoice $invoice, WestMetroApiService $api)
    {
        $this->invoiceTypes = $api->getInvoiceTypes();

        $this->invoiceId = $invoice->id;

        $this->issue_date = optional($invoice->issue_date)->format('Y-m-d');
        $this->due_date = optional($invoice->due_date)->format('Y-m-d');
        $this->invoice_type_code = $invoice->invoice_type_code;
        $this->document_currency_code = $invoice->document_currency_code;
        $this->vat_treatment = $invoice->vat_treatment;
        $this->tax = $invoice->tax_breakdown['VAT'] ?? 0;
        $this->subtotal = $invoice->legal_monetary_total['tax_exclusive_amount'] ?? 0;
        $this->total = $invoice->legal_monetary_total['payable_amount'];

        // Load customer
        $customer = $invoice->customer;
        $this->customer = [
            'party_name' => $customer->name,
            'tin' => $customer->tin,
            'email' => $customer->email,
            'postal_address' => [
                'street_name' => $customer->street_name,
                'city_name' => $customer->city_name,
                'postal_zone' => $customer->postal_zone,
                'country' => $customer->country,
            ],
        ];

        $supplier = $invoice->organization;
        $this->supplier = [
            'party_name' => $supplier->trade_name ?? '',
            'tin' => $supplier->tin ?? '',
            'email' => $supplier->email ?? '',
            'postal_address' => [
                'street_name' => $supplier->street_name ?? '',
                'city_name' => $supplier->city_name ?? '',
                'postal_zone' => $supplier->postal_zone ?? '',
                'country' => $supplier->country ?? 'NG',
            ],
        ];

        // Load items
        $this->items = $invoice->items
            ->map(function ($item) {
                return [
                    'description' => $item->description,
                    'hsn_code' => $item->hsn_code,
                    'product_category' => $item->product_category,
                    'quantity' => $item->quantity,
                    'price_amount' => $item->price,
                    'base_quantity' => $item->price_details['base_quantity'] ?? 1,
                    'price_unit' => $item->price_details['price_unit'] ?? 'NGN per 1',
                    'discount_rate' => $item->discount_rate ?? 0,
                    'fee_rate' => $item->fee_rate ?? 0,
                ];
            })
            ->toArray();
    }

    protected function rules()
    {
        return [
            'issue_date' => 'required|date',
            'document_currency_code' => 'required|string|size:3',
            'customer.party_name' => 'required|string',
            'customer.tin' => 'required|string',
            'customer.email' => 'required|email',
            'items.*.description' => 'required|string',
            'items.*.hsn_code' => 'required|string',
            'items.*.product_category' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price_amount' => 'required|numeric|min:0',
        ];
    }

    public function addItem()
    {
        $this->items[] = [
            'description' => '',
            'hsn_code' => '',
            'product_category' => '',
            'quantity' => 1,
            'price_amount' => 0,
            'base_quantity' => 1,
            'price_unit' => 'NGN per 1',
            'discount_rate' => 0,
            'fee_rate' => 0,
        ];
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

        // Ensure customer exists / update
        $customer = Customer::updateOrCreate(
            ['tin' => $this->customer['tin']],
            [
                'name' => $this->customer['party_name'],
                'email' => $this->customer['email'],
                'street_name' => $this->customer['postal_address']['street_name'] ?? null,
                'city_name' => $this->customer['postal_address']['city_name'] ?? null,
                'postal_zone' => $this->customer['postal_address']['postal_zone'] ?? null,
                'country' => $this->customer['postal_address']['country'] ?? 'NG',
            ],
        );

        $subtotal = collect($this->items)->sum(fn($i) => $i['quantity'] * $i['price_amount']);
        $total = $subtotal + $this->tax;

        // Update invoice
        $invoice->update([
            'customer_id' => $customer->id,
            'buyer_organization_ref' => $this->customer['tin'],
            'total_amount' => $total,
            'tax_breakdown' => ['VAT' => $this->tax],
            'vat_treatment' => $this->vat_treatment,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'invoice_type_code' => $this->invoice_type_code,
            'document_currency_code' => $this->document_currency_code,
            'legal_monetary_total' => [
                'line_extension_amount' => $subtotal,
                'tax_exclusive_amount' => $subtotal,
                'tax_inclusive_amount' => $total,
                'payable_amount' => $total,
            ],
        ]);

        // Replace items
        $invoice->items()->delete();

        $itemsWithDetails = collect($this->items)->map(
            fn($item) => [
                'description' => $item['description'],
                'hsn_code' => $item['hsn_code'],
                'product_category' => $item['product_category'],
                'quantity' => $item['quantity'],
                'price' => $item['price_amount'],
                'line_total' => $item['quantity'] * $item['price_amount'],
                'price_details' => [
                    'price_amount' => $item['price_amount'],
                    'base_quantity' => $item['base_quantity'],
                    'price_unit' => $item['price_unit'],
                ],
                'item' => [
                    'name' => $item['description'],
                    'description' => $item['description'],
                ],
            ],
        );

        $invoice->items()->createMany($itemsWithDetails);

        session()->flash('success', 'Invoice updated successfully!');
        $this->redirect(route('invoices.index', absolute: false));
    }
};
?>

<section class="w-full">
    <div class="max-w-7xl mx-auto bg-white dark:bg-zinc-900 shadow-lg rounded-xl p-8 space-y-6">
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
