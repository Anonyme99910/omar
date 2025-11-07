<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة {{ $sale->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 12mm 15mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            unicode-bidi: embed;
        }
        * {
            unicode-bidi: embed;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 3px solid #3b82f6;
            background: linear-gradient(to bottom, #eff6ff 0%, #ffffff 100%);
            padding: 15px;
        }
        .header h1 {
            font-size: 26px;
            margin-bottom: 5px;
            color: #1e40af;
        }
        .header h2 {
            font-size: 16px;
            color: #64748b;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            background: #f8fafc;
            border-radius: 6px;
            direction: rtl;
        }
        .info-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
            text-align: right;
            direction: rtl;
        }
        .info-box p {
            margin: 3px 0;
            font-size: 10px;
            text-align: right;
            direction: rtl;
        }
        .info-box strong {
            color: #1e293b;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: right;
            font-size: 10px;
        }
        th {
            background-color: #3b82f6;
            color: #ffffff;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .totals {
            width: 50%;
            float: right;
            margin-top: 12px;
            direction: rtl;
        }
        .totals-row {
            display: table;
            width: 100%;
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            direction: rtl;
        }
        .totals-row span {
            display: table-cell;
            text-align: right;
        }
        .totals-row span:last-child {
            text-align: left;
            font-weight: 600;
        }
        .totals-row.total {
            font-weight: bold;
            font-size: 13px;
            background: #3b82f6;
            color: #ffffff;
            border: none;
            margin-top: 8px;
            border-radius: 4px;
        }
        .totals-row.paid {
            background: #10b981;
            color: #ffffff;
            margin-top: 5px;
            border-radius: 4px;
            border: none;
        }
        .totals-row.remaining {
            background: #ef4444;
            color: #ffffff;
            margin-top: 5px;
            border-radius: 4px;
            border: none;
            font-weight: bold;
        }
        .totals-row.payment-method {
            background: #f8fafc;
            margin-top: 8px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }
        .footer {
            clear: both;
            margin-top: 20px;
            padding-top: 12px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>متجر العطور</h1>
        <h2>فاتورة بيع</h2>
    </div>

    <div class="info-section">
        <div class="info-box">
            <p><strong>رقم الفاتورة:</strong> {{ $sale->invoice_number }}</p>
            <p><strong>التاريخ:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</p>
            <p><strong>الوقت:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('H:i') }}</p>
        </div>
        <div class="info-box">
            @if($sale->customer)
                <p><strong>العميل:</strong> {{ $sale->customer->name }}</p>
                <p><strong>الهاتف:</strong> {{ $sale->customer->phone }}</p>
            @else
                <p><strong>العميل:</strong> عميل عادي</p>
            @endif
            <p><strong>طريقة الدفع:</strong> 
                @if($sale->payment_method === 'cash') نقدي
                @elseif($sale->payment_method === 'card') بطاقة
                @elseif($sale->payment_method === 'transfer') تحويل
                @else {{ $sale->payment_method }}
                @endif
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">#</th>
                <th>المنتج</th>
                <th style="width: 60px; text-align: center;">الكمية</th>
                <th style="width: 80px;">السعر</th>
                <th style="width: 80px;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->product->name_ar }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }} EGP</td>
                <td>{{ number_format($item->total_price, 2) }} EGP</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span>المجموع الفرعي:</span>
            <span>{{ number_format($sale->subtotal, 2) }} EGP</span>
        </div>
        <div class="totals-row">
            <span>الضريبة:</span>
            <span>{{ number_format($sale->tax, 2) }} EGP</span>
        </div>
        <div class="totals-row">
            <span>الخصم:</span>
            <span>{{ number_format($sale->discount, 2) }} EGP</span>
        </div>
        <div class="totals-row total">
            <span>الإجمالي:</span>
            <span>{{ number_format($sale->total, 2) }} EGP</span>
        </div>
        <div class="totals-row paid">
            <span>المبلغ المدفوع:</span>
            <span>{{ number_format($sale->paid_amount ?? $sale->total, 2) }} EGP</span>
        </div>
        @if($sale->remaining_amount > 0)
        <div class="totals-row remaining">
            <span>المتبقي:</span>
            <span>{{ number_format($sale->remaining_amount, 2) }} EGP</span>
        </div>
        @endif
        <div class="totals-row payment-method">
            <span>طريقة الدفع:</span>
            <span style="font-weight: bold;">
                @if($sale->payment_method === 'cash') نقدي
                @elseif($sale->payment_method === 'card') بطاقة
                @elseif($sale->payment_method === 'transfer') تحويل
                @else {{ $sale->payment_method }}
                @endif
            </span>
        </div>
        <div class="totals-row payment-method" style="margin-top: 3px;">
            <span>حالة الدفع:</span>
            <span style="font-weight: bold;">
                @if($sale->payment_status === 'paid') مدفوع بالكامل
                @elseif($sale->payment_status === 'partial') مدفوع جزئياً
                @else غير مدفوع
                @endif
            </span>
        </div>
    </div>

    <div class="footer">
        <p>شكراً لتعاملكم معنا</p>
        <p>متجر العطور - جميع الحقوق محفوظة © {{ date('Y') }}</p>
    </div>
</body>
</html>
