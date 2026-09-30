@extends('layout')
@section('title',__('Order confirmed | Dine In'))
@section('content')
<div class="panel" style="max-width:680px;margin:30px auto">
    <p class="muted">{{ __('ORDER PLACED') }}</p>
    <h1>{{ __('Thank you, :name!', ['name'=>$order->customer_name]) }}</h1>
    <p>{{ __('Your order') }} <strong>{{ $order->order_number }}</strong> {{ __('is with the restaurant.') }}</p>
    <div class="charges">
        @foreach($order->items as $item)
            <div class="selected-item"><span>{{ $item->quantity }} × {{ $item->product_name }}</span><strong>₹{{ number_format($item->line_total, 2) }}</strong></div>
        @endforeach
        <div class="selected-item"><strong>{{ __('Total') }}</strong><strong>₹{{ number_format($order->total, 2) }}</strong></div>
    </div>
    @if($order->payment_method === 'upi')
        <div style="margin-top:28px;text-align:center">
            <h2>{{ __('Pay with UPI') }}</h2>
            @if($paymentQrCode)
                <p>{{ __('Scan this QR with GPay, PhonePe, or another UPI app. Pay') }} <strong>₹{{ number_format($order->total, 2) }}</strong>.</p>
                <img src="{{ asset('storage/'.$paymentQrCode->image_path) }}" alt="{{ __('Restaurant UPI payment QR code') }}" style="display:block;width:min(100%,300px);height:auto;margin:16px auto;background:white;padding:8px">
                @if($upiUrl)
                    <p><a class="btn" href="{{ $upiUrl }}">{{ __('Open GPay, PhonePe, or another UPI app') }}</a></p>
                    <p class="muted">{{ __('On a phone, choose your installed UPI app. If the button does not open one, scan the QR from another device.') }}</p>
                    <p>UPI ID: <strong>{{ $paymentQrCode->upi_id }}</strong></p>
                @endif
                <p class="muted">{{ __('Payment stays unpaid until the restaurant confirms it.') }}</p>
            @else
                <p>{{ __('The restaurant has not added a UPI QR code yet. Please ask staff for payment details.') }}</p>
            @endif
        </div>
    @else
        <p style="margin-top:24px">{{ __('Please pay at the restaurant. Staff will confirm the payment.') }}</p>
    @endif
    <p><a class="btn" href="{{ route('home') }}">{{ __('Back to menu') }}</a></p>
</div>
@endsection
