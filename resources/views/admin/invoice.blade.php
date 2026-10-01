<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ __('Invoice') }} {{ $order->order_number }}</title>
    <style>
        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            background: #eee;
            color: #23170f;
            font: 13px Arial, sans-serif
        }

        .receipt {
            width: 80mm;
            min-height: 120mm;
            margin: 20px auto;
            background: #fff;
            padding: 7mm
        }

        .center {
            text-align: center
        }

        .brand {
            font: 700 24px Georgia, serif
        }

        .muted {
            color: #6c625b
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0
        }

        th,
        td {
            padding: 7px 0;
            border-bottom: 1px dashed #907a69;
            text-align: left;
            vertical-align: top
        }

        th:last-child,
        td:last-child {
            text-align: right
        }

        .total {
            font-size: 17px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            margin-top: 12px
        }

        .payment-qr {
            text-align: center;
            margin: 18px 0 8px;
            break-inside: avoid
        }

        .payment-qr img {
            display: block;
            width: 48mm;
            max-width: 100%;
            height: auto;
            margin: 8px auto
        }

        .payment-qr p {
            margin: 5px 0
        }

        .print {
            display: block;
            margin: 0 auto 22px;
            border: 0;
            padding: 10px 16px;
            background: #6e3d23;
            color: #fff;
            cursor: pointer
        }

        @media print {
            body {
                background: #fff
            }

            .receipt {
                margin: 0;
                width: 80mm
            }

            .print {
                display: none
            }

            @page {
                size: 80mm auto;
                margin: 0
            }
        }
    </style>
</head>

<body>
    <button class="print" type="button" onclick="window.print()">{{ __('Print invoice') }}</button>
    <article class="receipt">
        <div class="center">
            <div class="brand">Day Night Cafe</div>
            <p class="muted">{{ __('Customer bill') }}</p>
        </div>
        <p>{{ __('Bill no') }}: {{ $order->order_number }}<br>{{ __('Date') }}: {{ $order->created_at->format('d M Y, h:i A') }}</p>
        <p><strong>{{ $order->customer_name }}</strong><br>{{ $order->customer_phone ?: __('Mobile not provided') }}</p>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}<br><span class="muted">{{ $item->quantity }} × ₹{{ number_format($item->unit_price,2) }}</span></td>
                    <td>₹{{ number_format($item->line_total,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total"><span>{{ __('Total') }}</span><span>₹{{ number_format($order->total,2) }}</span></div>
        @if($order->payment_method === 'upi' && $order->payment_status !== 'paid' && $paymentQrCode)
        <div class="payment-qr"><strong>{{ __('Scan to pay with UPI') }}</strong><img src="{{ asset('storage/'.$paymentQrCode->image_path) }}" alt="{{ __('UPI payment QR code') }}">
            <p>{{ __('Pay') }} ₹{{ number_format($order->total,2) }}</p>@if($paymentQrCode->upi_id)<p>{{ $paymentQrCode->upi_id }}</p>@endif
        </div>
        @endif
        <p class="center muted">{{ __(ucfirst($order->payment_status)) }} · {{ strtoupper($order->payment_method) }}<br>{{ __('Thank you. Please visit again.') }}</p>
    </article>
    <script>
        window.addEventListener('load', () => {
            const image = document.querySelector('.payment-qr img');
            if (image && !image.complete) {
                image.addEventListener('load', () => window.print(), {
                    once: true
                })
            } else {
                window.print()
            }
        })
    </script>
</body>

</html>