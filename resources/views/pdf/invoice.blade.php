<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f4f4f4;
        }

        h1,
        h2,
        h3 {
            margin: 0;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h1>Invoice #{{ $invoice->id }}</h1>
    <p><strong>Buyer Ref:</strong> {{ $invoice->buyer_organization_ref }}</p>
    <p><strong>VAT Treatment:</strong> {{ ucfirst($invoice->vat_treatment) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
    <p><strong>Date:</strong> {{ $invoice->created_at->format('d M, Y') }}</p>

    <h3>Items</h3>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">₦{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">₦{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 class="text-right">Total: ₦{{ number_format($invoice->items->sum('line_total'), 2) }}</h3>
    <h3 class="text-right">VAT: ₦{{ number_format($invoice->tax_breakdown['VAT'] ?? 0, 2) }}</h3>
    <h2 class="text-right">Grand Total: ₦{{ number_format($invoice->total_amount, 2) }}</h2>
</body>

</html>
