<?php

use function Livewire\Volt\{with, state};
use App\Models\Invoice;
use Livewire\WithPagination;

with(WithPagination::class);

state([
    'organizationId' => auth()->user()->organization_id, // filter per organization
]);

$invoices = function () {
    return Invoice::where('organization_id', $this->organizationId)->orderBy('created_at', 'desc')->paginate(10);
};

$delete = function ($id) {
    $invoice = Invoice::where('organization_id', $this->organizationId)->findOrFail($id);
    $invoice->delete();
    session()->flash('success', 'Invoice deleted successfully.');
};

$submitToFirs = function ($id) {
    $invoice = Invoice::where('organization_id', $this->organizationId)->findOrFail($id);

    try {
        $payload = $invoice->toFirsPayload();
        var_dump($payload); // For debugging purposes
        app(\App\Services\WestMetroApiService::class)->post('SubmitInvoice', $payload);

        $invoice->update(['status' => 'submitted']);
        session()->flash('success', "Invoice #{$invoice->id} submitted to FIRS successfully.");
    } catch (\Exception $e) {
        session()->flash('error', "Failed to submit invoice #{$invoice->id} to FIRS: " . $e->getMessage());
    }
};

?>

<section class="w-full">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">📑 Invoices</h2>
        <a href="{{ route('invoices.create') }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 cursor-pointer">
            + Create Invoice
        </a>
    </div>

    <div class="bg-white dark:bg-zinc-900 shadow rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800">
                <tr class="text-left text-zinc-700 dark:text-zinc-300">
                    <th class="p-3">#</th>
                    <th class="p-3">Buyer Ref</th>
                    <th class="p-3">VAT Treatment</th>
                    <th class="p-3 text-right">Total</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->invoices() as $invoice)
                    <tr class="border-t border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200">
                        <td class="p-3">{{ $invoice->id }}</td>
                        <td class="p-3">{{ $invoice->buyer_organization_ref }}</td>
                        <td class="p-3 capitalize">{{ $invoice->vat_treatment }}</td>
                        <td class="p-3 text-right">₦{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="p-3">{{ ucfirst($invoice->status) }}</td>
                        <td class="p-3 text-right">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open"
                                    class="px-3 py-1 bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                    ⋮
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak
                                    class="absolute right-0 mt-2 w-44 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded shadow-lg z-50">
                                    <a href="{{ route('invoices.show', $invoice->id) }}"
                                        class="block px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700">👁️
                                        View</a>

                                    <a href="{{ route('invoices.edit', $invoice->id) }}"
                                        class="block px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700">✏️
                                        Edit</a>

                                    <button wire:click="submitToFirs({{ $invoice->id }})"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer">
                                        📤 Submit to FIRS
                                    </button>

                                    <button wire:click="delete({{ $invoice->id }})"
                                        onclick="return confirm('Are you sure you want to delete this invoice?')"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                        🗑️ Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->invoices()->links() }}
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mt-4 text-green-700 bg-green-100 dark:text-green-200 dark:bg-green-800 p-2 rounded">
            {{ session('success') }}
        </div>
    @endif
</section>
