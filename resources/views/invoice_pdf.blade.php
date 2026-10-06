<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - #{{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .invoice-card { max-width: 800px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 36px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 24px; border-bottom: 2px solid #0c831f; }
        .brand-logo { font-size: 24px; font-weight: 900; color: #0c831f; letter-spacing: -0.5px; }
        .brand-sub { font-size: 12px; color: #64748b; margin-top: 2px; }
        .invoice-title { font-size: 18px; font-weight: 800; color: #0f172a; text-align: right; }
        .invoice-meta { font-size: 13px; color: #475569; text-align: right; margin-top: 4px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin: 28px 0; }
        .info-box { background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #f1f5f9; }
        .info-title { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 8px; }
        .info-detail { font-size: 13px; color: #0f172a; line-height: 1.5; font-weight: 500; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th { background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; text-align: left; padding: 12px 14px; }
        .items-table td { padding: 14px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #334155; }
        .summary-box { width: 320px; margin-left: auto; margin-top: 24px; font-size: 13px; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; color: #475569; }
        .summary-row.total { font-size: 16px; font-weight: 800; color: #0c831f; border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 6px; }
        .actions-bar { max-width: 800px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn-print { background: #0c831f; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-card { border: none; box-shadow: none; padding: 0; }
            .actions-bar { display: none; }
        }
    </style>
</head>
<body>
    <div class="actions-bar">
        <a href="/" style="text-decoration: none; color: #64748b; font-size: 14px; font-weight: 600;">&larr; SonarbanglaMart Quick Commerce</a>
        <button onclick="window.print()" class="btn-print">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print / Save as PDF
        </button>
    </div>

    <div class="invoice-card">
        <div class="invoice-header">
            <div>
                <div class="brand-logo">SonarbanglaMart</div>
                <div class="brand-sub">Quick-Commerce Express Grocery Delivery</div>
            </div>
            <div>
                <div class="invoice-title">OFFICIAL TAX INVOICE</div>
                <div class="invoice-meta">Order #{{ $order->order_number }}</div>
                <div class="invoice-meta">Date: {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <div class="info-title">Fulfilled By (Store)</div>
                <div class="info-detail">
                    <strong>{{ $store->name ?? 'SonarbanglaMart Hub' }}</strong><br>
                    {{ $store->address ?? '' }}<br>
                    @if(!empty($store->store_phone ?? $store->phone ?? ''))
                    Contact: +91 {{ $store->store_phone ?? $store->phone }}<br>
                    @endif
                    @if(!empty($store->gstin))
                    GSTIN: <strong>{{ $store->gstin }}</strong>
                    @endif
                </div>
            </div>
            <div class="info-box">
                <div class="info-title">Billed To (Customer)</div>
                <div class="info-detail">
                    <strong>{{ $order->user_name ?? 'Customer' }}</strong><br>
                    Phone: {{ $order->user_phone }}<br>
                    Address: {{ $order->delivery_address }}<br>
                    @if(!empty($order->user_gstin ?? $customerGst ?? ''))
                    GSTIN: <strong>{{ $order->user_gstin ?? $customerGst }}</strong><br>
                    @endif
                    Payment Mode: <strong>{{ $order->payment_method ?? 'Cash on Delivery' }}</strong>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td><strong>{{ $item->product_name ?? 'Product Item' }}</strong></td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">₹{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">₹{{ number_format($item->total ?? ($item->price * $item->quantity), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-box">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if(($order->handling_fee ?? 0) > 0)
            <div class="summary-row">
                <span>Handling Fee:</span>
                <span>+₹{{ number_format($order->handling_fee, 2) }}</span>
            </div>
            @endif
            <div class="summary-row">
                <span>Delivery Charge:</span>
                <span>{{ ($order->delivery_fee ?? 0) == 0 ? 'FREE' : '+₹' . number_format($order->delivery_fee, 2) }}</span>
            </div>
            @if(($order->wallet_paid ?? 0) > 0)
            <div class="summary-row" style="color: #0c831f;">
                <span>Wallet Discount Applied:</span>
                <span>-₹{{ number_format($order->wallet_paid, 2) }}</span>
            </div>
            @endif
            @if(($order->discount_amount ?? 0) > 0)
            <div class="summary-row" style="color: #0c831f;">
                <span>Coupon / Promo Savings:</span>
                <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @endif
            <div class="summary-row total">
                <span>Grand Total / Payable:</span>
                <span>₹{{ number_format(($order->payable_amount > 0 ? $order->payable_amount : ($order->grand_total > 0 ? $order->grand_total : ($order->subtotal + ($order->delivery_fee ?? 0) + ($order->handling_fee ?? 2)))), 2) }}</span>
            </div>
        </div>
    </div>
</body>
</html>
