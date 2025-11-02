@php
    // Dynamic spacing based on item count
    $headerMargin = $spacing === 'tight' ? '8px' : ($spacing === 'compact' ? '10px' : '15px');
    $headerPadding = $spacing === 'tight' ? '12px' : ($spacing === 'compact' ? '15px' : '20px');
    $sectionMargin = $spacing === 'tight' ? '6px' : ($spacing === 'compact' ? '8px' : '12px');
    $rowMargin = $spacing === 'tight' ? '3px' : ($spacing === 'compact' ? '5px' : '8px');
    $cellPadding = $spacing === 'tight' ? '5px' : ($spacing === 'compact' ? '7px' : '10px');
    $headerFontSize = $spacing === 'tight' ? '20px' : ($spacing === 'compact' ? '24px' : '28px');
    $subHeaderFontSize = $spacing === 'tight' ? '12px' : ($spacing === 'compact' ? '14px' : '16px');
@endphp

<style>
    body {
        font-family: 'aealarabiya', sans-serif;
        direction: rtl;
        text-align: right;
        font-size: {{ $fontSize }}px;
        background-color: #ffffff;
    }
    .header {
        text-align: center;
        margin-bottom: {{ $headerMargin }};
        padding: {{ $headerPadding }};
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px 8px 0 0;
    }
    .header h1 {
        font-size: {{ $headerFontSize }};
        color: #ffffff;
        margin-bottom: 5px;
        font-weight: bold;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .header h2 {
        font-size: {{ $subHeaderFontSize }};
        color: #f0f0f0;
        font-weight: normal;
    }
    .info-table {
        width: 100%;
        margin-bottom: {{ $sectionMargin }};
        background-color: #ffffff;
        border-collapse: collapse;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
    }
    .info-table td {
        padding: {{ $cellPadding }};
        border: 1px solid #e5e7eb;
        text-align: right;
    }
    .info-table .info-label {
        font-weight: bold;
        color: #374151;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        width: 25%;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: {{ $sectionMargin }} 0;
    }
    th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: {{ $cellPadding }};
        text-align: right;
        font-weight: bold;
        border: 1px solid #9ca3af;
    }
    td {
        padding: {{ $cellPadding }};
        text-align: right;
        border: 1px solid #d1d5db;
    }
    tr:nth-child(even) {
        background-color: #f9fafb;
    }
    tr:nth-child(odd) {
        background-color: #ffffff;
    }
    .totals {
        width: 50%;
        float: right;
        margin-top: {{ $sectionMargin }};
    }
    .totals-row {
        padding: {{ $cellPadding }};
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 2px;
    }
    .totals-row.total {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        font-weight: bold;
        font-size: 14px;
        padding: 10px 12px;
        margin-top: 8px;
        border-radius: 6px;
    }
    .totals-row.paid {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        padding: 8px 12px;
        margin-top: 5px;
        border-radius: 6px;
    }
    .totals-row.remaining {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        font-weight: bold;
        padding: 8px 12px;
        margin-top: 5px;
        border-radius: 6px;
    }
    .totals-row.payment-method {
        background-color: #f8fafc;
        padding: 6px 10px;
        margin-top: 8px;
        border: 1px solid #e2e8f0;
    }
    .footer {
        clear: both;
        margin-top: 30px;
        padding: 15px;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-top: 3px solid #667eea;
        text-align: center;
        color: #6b7280;
        font-size: 10px;
        border-radius: 0 0 8px 8px;
    }
    .footer p {
        margin: 3px 0;
    }
</style>

<div class="header">
    <h1>متجر العطور</h1>
    <h2>فاتورة بيع</h2>
</div>

<table class="info-table">
    <tr>
        <td class="info-label">رقم الفاتورة:</td>
        <td>{{ $sale->invoice_number }}</td>
        <td class="info-label">العميل:</td>
        <td>
            @if($sale->customer)
                {{ $sale->customer->name }}
            @else
                عميل عادي
            @endif
        </td>
    </tr>
    <tr>
        <td class="info-label">التاريخ:</td>
        <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</td>
        <td class="info-label">الهاتف:</td>
        <td>
            @if($sale->customer)
                {{ $sale->customer->phone }}
            @else
                -
            @endif
        </td>
    </tr>
    <tr>
        <td class="info-label">الوقت:</td>
        <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('H:i') }}</td>
        <td class="info-label">طريقة الدفع:</td>
        <td>
            @if($sale->payment_method === 'cash') نقدي
            @elseif($sale->payment_method === 'card') بطاقة
            @elseif($sale->payment_method === 'transfer') تحويل
            @else {{ $sale->payment_method }}
            @endif
        </td>
    </tr>
</table>

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
        <strong>المجموع الفرعي:</strong> {{ number_format($sale->subtotal, 2) }} EGP
    </div>
    <div class="totals-row">
        <strong>الضريبة:</strong> {{ number_format($sale->tax, 2) }} EGP
    </div>
    <div class="totals-row">
        <strong>الخصم:</strong> {{ number_format($sale->discount, 2) }} EGP
    </div>
    <div class="totals-row total">
        <strong>الإجمالي:</strong> {{ number_format($sale->total, 2) }} EGP
    </div>
    <div class="totals-row paid">
        <strong>المبلغ المدفوع:</strong> {{ number_format($sale->paid_amount ?? $sale->total, 2) }} EGP
    </div>
    @if($sale->remaining_amount > 0)
    <div class="totals-row remaining">
        <strong>المتبقي:</strong> {{ number_format($sale->remaining_amount, 2) }} EGP
    </div>
    @endif
    <div class="totals-row payment-method">
        <strong>حالة الدفع:</strong>
        @if($sale->payment_status === 'paid') مدفوع بالكامل
        @elseif($sale->payment_status === 'partial') مدفوع جزئياً
        @else غير مدفوع
        @endif
    </div>
</div>

<div class="footer">
    <p>شكراً لتعاملكم معنا</p>
    <p>متجر العطور - جميع الحقوق محفوظة © {{ date('Y') }}</p>
</div>
