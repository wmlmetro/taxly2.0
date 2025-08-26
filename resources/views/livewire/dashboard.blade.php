<?php

use Livewire\Volt\Component;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    public int $totalInvoices = 0;
    public int $pendingInvoices = 0;
    public int $validatedInvoices = 0;
    public float $revenueCollected = 0;
    public $recentInvoices = [];

    public array $labels = [];
    public array $invoiceCounts = [];
    public array $revenueData = [];
    public array $taxBreakdown = [];

    public function mount()
    {
        $this->totalInvoices = Invoice::count();
        $this->pendingInvoices = Invoice::whereIn('status', ['draft', 'submitted'])->count();
        $this->validatedInvoices = Invoice::where('status', 'validated')->count();
        $this->revenueCollected = Invoice::where('status', 'closed')->sum('total_amount');
        $this->recentInvoices = Invoice::latest()->take(5)->get();

        // Build chart data
        $months = collect(range(5, 0))->map(fn($i) => Carbon::now()->subMonths($i));
        $this->labels = $months->map(fn($m) => $m->format('M Y'))->toArray();

        $this->invoiceCounts = $months->map(fn($m) => Invoice::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count())->toArray();

        $this->revenueData = $months->map(fn($m) => Invoice::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->where('status', 'closed')->sum('total_amount'))->toArray();

        $this->taxBreakdown = [
            'VAT' => Invoice::sum('tax_breakdown->VAT'),
            'WHT' => Invoice::sum('wht_amount'),
        ];
    }

    // ✅ Export all invoices as CSV
    public function exportCsv()
    {
        $fileName = 'invoices_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Buyer Ref', 'VAT Treatment', 'Total Amount', 'Status', 'Created At']);

            Invoice::orderBy('created_at', 'desc')->chunk(200, function ($invoices) use ($handle) {
                foreach ($invoices as $invoice) {
                    fputcsv($handle, [$invoice->id, $invoice->buyer_organization_ref, $invoice->vat_treatment, $invoice->total_amount, ucfirst($invoice->status), $invoice->created_at->toDateTimeString()]);
                }
            });

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    // ✅ Export all invoices as PDF
    public function generateReport()
    {
        $invoices = Invoice::latest()->get();

        $pdf = Pdf::loadView('reports.invoices', compact('invoices'))->setPaper('a4', 'portrait');

        return response()->streamDownload(fn() => print $pdf->output(), 'invoices_report_' . now()->format('Y-m-d_His') . '.pdf');
    }
}; ?>

<div class="flex flex-col gap-6">
    {{-- ✅ Quick Actions --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">📊 Dashboard</h1>
        <div class="flex gap-3">
            <a href="{{ route('invoices.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 cursor-pointer">
                + Create Invoice
            </a>
            <button wire:click="exportCsv"
                class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 cursor-pointer">
                📥 Export CSV
            </button>
            <button wire:click="generateReport"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 cursor-pointer">
                📑 Generate Report
            </button>
        </div>
    </div>

    {{-- ✅ Top Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Invoices</p>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $totalInvoices }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingInvoices }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Validated</p>
            <p class="text-2xl font-bold text-blue-600">{{ $validatedInvoices }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Revenue Collected</p>
            <p class="text-2xl font-bold text-green-600">₦{{ number_format($revenueCollected, 2) }}</p>
        </div>
    </div>

    {{-- ✅ Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
            <h3 class="font-semibold text-zinc-800 dark:text-zinc-100 mb-2">Invoices Created (Last 6 Months)</h3>
            <canvas id="invoicesChart"></canvas>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
            <h3 class="font-semibold text-zinc-800 dark:text-zinc-100 mb-2">Revenue Trend</h3>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- ✅ Tax Breakdown --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
            <h3 class="font-semibold text-zinc-800 dark:text-zinc-100 mb-2">Tax Breakdown</h3>
            <canvas id="taxChart"></canvas>
        </div>

        {{-- ✅ Recent Invoices --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold text-zinc-800 dark:text-zinc-100">Recent Invoices</h3>
                <a href="{{ route('invoices.index') }}" class="text-indigo-600 dark:text-indigo-400">View All</a>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-zinc-100 dark:bg-zinc-800">
                    <tr>
                        <th class="p-2 text-left">#</th>
                        <th class="p-2 text-left">Buyer Ref</th>
                        <th class="p-2 text-right">Amount</th>
                        <th class="p-2">Status</th>
                        <th class="p-2 text-right">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentInvoices as $invoice)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="p-2">{{ $invoice->id }}</td>
                            <td class="p-2">{{ $invoice->buyer_organization_ref }}</td>
                            <td class="p-2 text-right">₦{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="p-2">{{ ucfirst($invoice->status) }}</td>
                            <td class="p-2 text-right">{{ $invoice->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Invoices per Month --}}
    <script>
        new Chart(document.getElementById('invoicesChart'), {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Invoices',
                    data: @json($invoiceCounts),
                    backgroundColor: '#6366F1'
                }]
            }
        });
    </script>

    {{-- Revenue per Month --}}
    <script>
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Revenue (₦)',
                    data: @json($revenueData),
                    borderColor: '#22C55E',
                    backgroundColor: '#22C55E40',
                    fill: true,
                    tension: 0.3
                }]
            }
        });
    </script>

    {{-- Tax Breakdown --}}
    <script>
        new Chart(document.getElementById('taxChart'), {
            type: 'pie',
            data: {
                labels: ['VAT', 'WHT'],
                datasets: [{
                    data: @json(array_values($taxBreakdown)),
                    backgroundColor: ['#FACC15', '#EF4444'],
                }]
            }
        });
    </script>
@endpush
