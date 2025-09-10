<?php

use function Livewire\Volt\{state, rules, computed};
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;
use App\Services\WestMetroApiService;

$organization = Auth::user()->organization;

state([
    // Invoice Header
    'issue_date' => now()->toDateString(),
    'due_date' => now()->addDays(7)->toDateString(),
    'invoice_type_code' => '396',
    'document_currency_code' => 'NGN',
    'validCustomer' => false,

    // Parties
    'supplier' => [
        'party_name' => $organization->trade_name ?? '',
        'tin' => $organization->tin ?? '',
        'email' => $organization->email ?? '',
        'postal_address' => [
            'street_name' => $organization->street_name ?? '',
            'city_name' => $organization->city_name ?? '',
            'postal_zone' => $organization->postal_zone ?? '',
            'country' => $organization->country ?? 'NG',
        ],
    ],
    'customer' => [
        'party_name' => '',
        'tin' => '',
        'email' => '',
        'postal_address' => ['street_name' => '', 'city_name' => '', 'postal_zone' => '', 'country' => 'NG'],
    ],

    // Items
    'items' => [
        [
            'description' => '',
            'hsn_code' => '',
            'product_category' => '',
            'quantity' => 1,
            'price_amount' => 0,
            'base_quantity' => 1,
            'price_unit' => 'NGN per 1',
            'discount_rate' => 0,
            'fee_rate' => 0,
        ],
    ],

    // Totals
    'tax' => 0,
]);

$invoiceTypes = computed(fn() => app(WestMetroApiService::class)->getInvoiceTypes());

$subtotal = computed(fn() => collect($this->items)->sum(fn($i) => $i['quantity'] * $i['price_amount']));
$total = computed(fn() => $this->subtotal() + $this->tax);

rules([
    'issue_date' => 'required|date',
    'document_currency_code' => 'required|string|size:3',
    'supplier.party_name' => 'required|string',
    'supplier.tin' => 'required|string',
    'supplier.email' => 'required|email',
    'customer.party_name' => 'required|string',
    'customer.tin' => 'required|string',
    'customer.email' => 'required|email',
    'items.*.description' => 'required|string',
    'items.*.hsn_code' => 'required|string',
    'items.*.product_category' => 'required|string',
    'items.*.quantity' => 'required|numeric|min:1',
    'items.*.price_amount' => 'required|numeric|min:0',
]);

$addItem = fn() => ($this->items[] = [
    'description' => '',
    'hsn_code' => '',
    'product_category' => '',
    'quantity' => 1,
    'price_amount' => 0,
    'base_quantity' => 1,
    'price_unit' => 'NGN per 1',
    'discount_rate' => 0,
    'fee_rate' => 0,
]);

$removeItem = fn($i) => ($this->items = array_values(array_filter($this->items, fn($k) => $k !== $i, ARRAY_FILTER_USE_KEY)));

$save = function () {
    if (!$this->validCustomer) {
        session()->flash('error', 'Cannot create invoice without a valid customer TIN.');
        return;
    }

    $this->validate();

    $organization = Auth::user()->organization;

    $customer = Customer::firstOrCreate(
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

    $invoice = Invoice::create([
        'organization_id' => $organization->id,
        'customer_id' => $customer->id,
        'buyer_organization_ref' => $this->customer['tin'],
        'total_amount' => $this->total(),
        'tax_breakdown' => ['VAT' => $this->tax],
        'vat_treatment' => 'standard',
        'status' => 'draft',
        'issue_date' => $this->issue_date,
        'due_date' => $this->due_date,
        'invoice_type_code' => $this->invoice_type_code,
        'document_currency_code' => $this->document_currency_code,
        'legal_monetary_total' => [
            'line_extension_amount' => $this->subtotal(),
            'tax_exclusive_amount' => $this->subtotal(),
            'tax_inclusive_amount' => $this->total(),
            'payable_amount' => $this->total(),
        ],
    ]);

    foreach ($this->items as $item) {
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $item['description'],
            'item_name' => $item['description'],
            'item_description' => $item['description'],
            'hsn_code' => $item['hsn_code'],
            'product_category' => $item['product_category'],
            'quantity' => $item['quantity'],
            'price' => $item['price_amount'],
            'price_amount' => $item['price_amount'],
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
        ]);
    }

    session()->flash('success', 'Invoice created!');
    $this->items = [
        [
            'description' => '',
            'hsn_code' => '',
            'product_category' => '',
            'quantity' => 1,
            'price_amount' => 0,
            'base_quantity' => 1,
            'price_unit' => 'NGN per 1',
            'discount_rate' => 0,
            'fee_rate' => 0,
        ],
    ];

    $this->redirect(route('invoices.index', absolute: false));
};

$lookupCustomer = function () {
    $this->validate(
        [
            'customer.tin' => 'required|string',
        ],
        [
            'customer.tin.required' => 'Please enter a TIN to lookup.',
        ],
    );

    // Step 1: Check in local database first
    $localCustomer = \App\Models\Customer::where('tin', $this->customer['tin'])->first();

    if ($localCustomer) {
        $this->customer['party_name'] = $localCustomer->name ?? '';
        $this->customer['email'] = $localCustomer->email ?? '';
        $this->customer['postal_address'] = [
            'street_name' => $localCustomer->street_name ?? '',
            'city_name' => $localCustomer->city_name ?? '',
            'postal_zone' => $localCustomer->postal_zone ?? '',
            'country' => $localCustomer->country ?? 'NG',
        ];

        $this->validCustomer = true;
        session()->flash('success', 'Customer found locally and details populated!');
        return;
    }

    // Step 2: If not found, check API
    try {
        $api = app(\App\Services\WestMetroApiService::class);
        $result = $api->getTin($this->customer['tin']);

        if (!empty($result['data'])) {
            $data = $result['data'];

            $this->customer['party_name'] = $data['entityName'] ?? '';
            $this->customer['email'] = $data['email'] ?? '';
            $this->customer['postal_address'] = [
                'street_name' => $data['street'] ?? '',
                'city_name' => $data['city'] ?? '',
                'postal_zone' => $data['postalCode'] ?? '',
                'country' => $data['country'] ?? 'NG',
            ];

            $this->validCustomer = true;
            session()->flash('success', 'Customer found via FIRS API and details populated!');
        } else {
            $this->validCustomer = false;
            session()->flash('error', 'TIN not found in FIRS database. Cannot create invoice.');
        }
    } catch (\Exception $e) {
        $this->validCustomer = false;
        session()->flash('error', 'Failed to fetch customer from API: ' . $e->getMessage());
    }
};

?>

<section class="w-full">
    <div class="max-w-7xl mx-auto bg-white dark:bg-zinc-900 shadow-lg rounded-xl p-8 space-y-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">🧾 Create Invoice</h2>

        @include('livewire.invoice.partials.form')

        {{-- Submit --}}
        <div class="flex justify-end">
            <button wire:click="save" @disabled(!$validCustomer)
                class="px-6 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
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
