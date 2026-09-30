@extends('layout')
@section('title',__('Order history'))
@section('content')
<div class="row" style="justify-content:space-between"><div><p class="muted">{{ __('YOUR ORDERS') }}</p><h1>{{ __('Order history') }}</h1><p>{{ __('Orders for mobile number') }} <strong>{{ $phone }}</strong></p></div><a class="btn" href="{{ route('home') }}">{{ __('Back to menu') }}</a></div>
<div class="history-list">
@forelse($orders as $order)
    <article class="panel history-card">
        <header><div><h2 style="margin:0">{{ __('Order') }} {{ $order->order_number }}</h2><p class="muted">{{ $order->created_at->format('d M Y, h:i A') }}</p></div><div><strong>{{ __('Payment status') }}: {{ __(ucfirst($order->payment_status)) }}</strong></div></header>
        <ul class="history-items">@foreach($order->items as $item)<li>{{ $item->quantity }} × {{ $item->product?->display_name ?: $item->product_name }} — ₹{{ number_format($item->line_total,2) }}</li>@endforeach</ul>
        <div class="total-line"><strong>{{ __('Total') }}</strong><strong>₹{{ number_format($order->total,2) }}</strong></div>
    </article>
@empty
    <div class="panel"><h2>{{ __('No history found') }}</h2><p class="muted">{{ __('No previous orders match this mobile number.') }}</p></div>
@endforelse
</div>
<div style="margin-top:20px">{{ $orders->links() }}</div>
@endsection
