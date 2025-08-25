{{-- Buyer & Invoice Info --}}
<div class="grid grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">Buyer Ref (TIN)</label>
        <input type="text" wire:model.live="buyer_organization_ref"
            class="w-full border rounded-lg p-2 focus:ring focus:ring-indigo-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200 text-right">
    </div>
    <div>
        <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">VAT Treatment</label>
        <select wire:model.live="vat_treatment"
            class="w-full border rounded-lg p-2 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
            <option value="standard">Standard</option>
            <option value="zero-rated">Zero Rated</option>
            <option value="exempt">Exempt</option>
        </select>
    </div>
</div>

{{-- Invoice Items --}}
<div>
    <h3 class="text-lg font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Items</h3>
    <table class="w-full border rounded-lg text-sm">
        <thead class="bg-zinc-100 dark:bg-zinc-800">
            <tr>
                <th class="p-2 text-left">Description</th>
                <th class="p-2 text-right">Qty</th>
                <th class="p-2 text-right">Unit Price</th>
                <th class="p-2 text-right">Total</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr class="border-t dark:border-zinc-700">
                    <td class="p-2">
                        <input type="text" wire:model.live="items.{{ $index }}.description"
                            class="w-full border rounded p-1 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200 text-left">
                    </td>
                    <td class="p-2 text-right">
                        <input type="number" min="1" wire:model.live="items.{{ $index }}.quantity"
                            class="w-20 border rounded p-1 text-right dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
                    </td>
                    <td class="p-2 text-right">
                        <input type="number" min="0" step="0.01"
                            wire:model.live="items.{{ $index }}.price"
                            class="w-28 border rounded p-1 text-right dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
                    </td>
                    <td class="p-2 text-right">
                        ₦{{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 2) }}
                    </td>
                    <td class="p-2 text-center">
                        <button type="button" wire:click="removeItem({{ $index }})"
                            class="text-red-500 hover:text-red-700 cursor-pointer">✕</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-zinc-500 dark:text-zinc-400">
                        No items added yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <button type="button" wire:click="addItem"
        class="mt-3 px-4 py-2 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-600 cursor-pointer">
        + Add Item
    </button>
</div>

{{-- Totals --}}
@php
    $subtotal = collect($items)->sum(function ($item) {
        return ($item['quantity'] ?? 1) * ($item['price'] ?? 0);
    });
    $taxAmount = $tax ?? 0; // Assuming $tax is a property in your Livewire component
    $total = $subtotal + $taxAmount;
@endphp

<div class="flex justify-end mt-6">
    <div class="w-full max-w-xs">
        <div class="flex justify-between py-1">
            <span class="text-zinc-600 dark:text-zinc-400">Subtotal:</span>
            <span class="text-zinc-800 dark:text-zinc-200">₦{{ number_format($subtotal, 2) }}</span>
        </div>
        <div class="flex justify-between items-center py-1">
            <span class="text-zinc-600 dark:text-zinc-400">Tax:</span>
            <input type="number" wire:model.live="tax" placeholder="Tax amount"
                class="w-28 border rounded p-1 text-right dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200">
        </div>
        <div class="border-t dark:border-zinc-600 my-2"></div>
        <div class="flex justify-between py-1 font-bold">
            <span class="text-zinc-800 dark:text-zinc-100">Total:</span>
            <span class="text-zinc-800 dark:text-zinc-100">₦{{ number_format($total, 2) }}</span>
        </div>
    </div>
</div>
