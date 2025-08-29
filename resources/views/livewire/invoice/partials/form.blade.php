<div class="grid grid-cols-3 gap-6">
    <div>
        <label class="block text-sm font-medium">Issue Date</label>
        <input type="date" wire:model="issue_date"
            class="w-full border rounded-lg p-2 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
    </div>
    <div>
        <label class="block text-sm font-medium">Due Date</label>
        <input type="date" wire:model="due_date"
            class="w-full border rounded-lg p-2 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
    </div>
    <div>
        <label class="block text-sm font-medium">Invoice Type</label>
        <select wire:model="invoice_type_code" class="w-full border rounded-lg p-2">
            @foreach ($this->invoiceTypes['data'] as $type)
                <option value="{{ $type['code'] }}">{{ $type['value'] }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="flex flex-col md:flex-row justify-between gap-10">
    {{-- Supplier Info --}}
    <div>
        <h3 class="text-lg font-semibold mb-3">Supplier (Accounting Supplier Party)</h3>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" wire:model="supplier.party_name" class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">TIN</label>
                <input type="text" wire:model="supplier.tin" class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" wire:model="supplier.email" class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Street</label>
                <input type="text" wire:model="supplier.postal_address.street_name"
                    class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">City</label>
                <input type="text" wire:model="supplier.postal_address.city_name"
                    class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Postal Code</label>
                <input type="text" wire:model="supplier.postal_address.postal_zone"
                    class="w-full border rounded-lg p-2">
            </div>
        </div>
    </div>

    {{-- Customer Info --}}
    <div>
        <h3 class="text-lg font-semibold mb-3">Customer (Accounting Customer Party)</h3>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" wire:model="customer.party_name" class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">TIN</label>
                <input type="text" wire:model="customer.tin" class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" wire:model="customer.email" class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Street</label>
                <input type="text" wire:model="customer.postal_address.street_name"
                    class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">City</label>
                <input type="text" wire:model="customer.postal_address.city_name"
                    class="w-full border rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Postal Code</label>
                <input type="text" wire:model="customer.postal_address.postal_zone"
                    class="w-full border rounded-lg p-2">
            </div>
        </div>
    </div>
</div>

{{-- Invoice Items --}}
<div>
    <h3 class="text-lg font-semibold mb-2">Invoice Items</h3>
    <table class="w-full border rounded-lg text-sm">
        <thead class="bg-zinc-100 dark:bg-zinc-800">
            <tr>
                <th class="p-2">Desc</th>
                <th class="p-2">HSN <small>(Example: CC-001)</small></th>
                <th class="p-2">Category <small>(Example: Food and Beverages)</small></th>
                <th class="p-2 text-right">Qty</th>
                <th class="p-2 text-right">Unit Price</th>
                <th class="p-2 text-right">Total</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
                <tr>
                    <td class="p-2"><input type="text" wire:model="items.{{ $i }}.description"
                            class="w-full border rounded p-1"></td>
                    <td class="p-2"><input type="text" wire:model="items.{{ $i }}.hsn_code"
                            class="w-full border rounded p-1"></td>
                    <td class="p-2"><input type="text" wire:model="items.{{ $i }}.product_category"
                            class="w-full border rounded p-1"></td>
                    <td class="p-2 text-right"><input type="number"
                            wire:model.live.debounce.500ms="items.{{ $i }}.quantity"
                            class="w-20 border rounded p-1 text-right"></td>
                    <td class="p-2 text-right"><input type="number" step="0.01"
                            wire:model.live.debounce.500ms="items.{{ $i }}.price_amount"
                            class="w-24 border rounded p-1 text-right"></td>
                    <td class="p-2 text-right">
                        ₦{{ number_format(($item['quantity'] ?? 1) * ($item['price_amount'] ?? 0), 2) }}</td>
                    <td class="p-2 text-center">
                        <button type="button" wire:click="removeItem({{ $i }})"
                            class="text-red-500 hover:text-red-700">✕</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button type="button" wire:click="addItem"
        class="mt-3 px-4 py-2 bg-indigo-500 text-white rounded-lg cursor-pointer">+ Add
        Item</button>
</div>

{{-- Totals --}}
<div class="flex justify-end mt-6">
    <div class="w-full max-w-xs">
        <div class="flex justify-between py-1">
            <span>Subtotal:</span>
            <span>₦{{ number_format($this->subtotal, 2) }}</span>
        </div>
        <div class="flex justify-between py-1">
            <span>Tax:</span>
            <input type="number" wire:model.live.debounce.500ms="tax" class="w-28 border rounded p-1 text-right">
        </div>
        <div class="border-t my-2"></div>
        <div class="flex justify-between py-1 font-bold">
            <span>Total:</span>
            <span>₦{{ number_format($this->total, 2) }}</span>
        </div>
    </div>
</div>
