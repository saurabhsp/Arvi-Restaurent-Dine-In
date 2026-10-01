@extends('layout')
@section('title',__('Menu | Day Night Cafe'))
@section('content')
<div class="hero">
    <p>{{ __('WELCOME TO OUR TABLE') }}</p>
    <h1>{{ __('Good food, served with warmth.') }}</h1>
    <p>{{ __('Browse our menu and place your order right here.') }}</p>
</div>
@if(!$orderDetails)
<div class="panel">
    <h2>{{ __('Start your order') }}</h2>
    <form method="post" action="{{ route('start') }}">@csrf
        <div class="grid customer-details-grid">
            <div><label for="name">{{ __('Customer name') }}</label><input id="name" name="name" type="text" maxlength="100" required value="{{ old('name') }}" placeholder="{{ __('Enter customer name') }}"></div>
            <div><label for="phone">{{ __('Mobile number') }} <span class="muted">({{ __('optional') }})</span></label><input id="phone" name="phone" type="tel" inputmode="numeric" maxlength="10" value="{{ old('phone') }}" placeholder="{{ __('Enter mobile number') }}"></div>
            <div><label for="payment_method">{{ __('Pay via') }}</label><select id="payment_method" name="payment_method" required>
                    <option value="cash">{{ __('Cash') }}</option>
                    <option value="upi">UPI</option>
                    <option value="card">{{ __('Card') }}</option>
                </select></div>
        </div>
        <p class="row"><button type="submit">{{ __('Start order') }}</button><button type="submit" form="history-form" id="history-button" style="display:none">{{ __('View history') }}</button></p>
    </form>
    <form id="history-form" method="post" action="{{ route('history.open') }}">@csrf<input type="hidden" name="phone" id="history-phone"></form>
</div>
<script>
    const phone = document.querySelector('#phone'),
        historyButton = document.querySelector('#history-button');

    function historyState() {
        const valid = /^[0-9]{10}$/.test(phone.value.trim());
        historyButton.style.display = valid ? 'inline-block' : 'none';
        document.querySelector('#history-phone').value = phone.value.trim()
    }
    phone.addEventListener('input', historyState);
    historyState()
</script>
@else
<div class="panel"><strong>{{ __('Ordering for') }} {{ $orderDetails['name'] }}</strong> <span class="muted">· {{ __('Choose foods and quantities below.') }}</span>@if($orderDetails['phone']) <a class="btn" href="{{ route('history') }}" style="margin-left:12px">{{ __('View history') }}</a>@endif</div>
@if($orderDetails['payment_method'] === 'upi')
<div class="panel" style="text-align:center">
    <h2>{{ __('UPI payment') }}</h2>@if($paymentQrCode)<p>{{ __('Place your order to see the final amount and open a UPI app. You can also scan this QR code.') }}</p><img src="{{ asset('storage/'.$paymentQrCode->image_path) }}" alt="{{ __('Restaurant UPI QR code') }}" style="width:min(100%,220px);height:auto;background:white;padding:8px">
    <p class="muted">{{ __('Payment stays unpaid until staff confirms it.') }}</p>@else<p>{{ __('UPI QR is not set up yet. Ask staff for payment details after ordering.') }}</p>@endif
</div>
@endif
<nav class="chips" aria-label="{{ __('Categories') }}">@foreach($categories as $category)<a href="#category-{{ $category->id }}">{{ $category->display_name }}</a>@endforeach</nav>
<form method="post" action="{{ route('orders.store') }}" id="order-form">@csrf
    <input type="hidden" name="name" value="{{ $orderDetails['name'] }}"><input type="hidden" name="phone" value="{{ $orderDetails['phone'] }}"><input type="hidden" name="payment_method" value="{{ $orderDetails['payment_method'] }}">
    @foreach($categories as $category)
    <section id="category-{{ $category->id }}">
        <h2>{{ $category->display_name }}</h2>@if($category->display_description)<p class="muted">{{ $category->display_description }}</p>@endif
        <div class="product-row">@forelse($category->products as $product)
            <article class="card">
                <div class="photo">@if($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->display_name }}">@else <span>{{ __('Plate') }}</span>@endif</div>
                <div class="card-body">
                    <h3>{{ $product->display_name }}</h3>
                    <p class="muted">{{ $product->display_description }}</p>
                    <div class="row" style="justify-content:space-between"><span class="price">₹{{ number_format($product->price,2) }}</span>
                        <div><label for="qty-{{ $product->id }}">{{ __('Qty') }}</label><input class="qty" id="qty-{{ $product->id }}" type="number" min="0" max="50" value="0" data-id="{{ $product->id }}" data-name="{{ $product->display_name }}" data-price="{{ $product->price }}"></div>
                    </div>
                </div>
            </article>
            @empty <p class="muted">{{ __('Items coming soon.') }}</p> @endforelse
        </div>
    </section>
    @endforeach
    <div class="order-bar panel"><strong>{{ __('Selected items') }}</strong>
        <div id="selected-items" class="selected-items"><span class="muted">{{ __('No food selected.') }}</span></div>
        <div class="charges">
            <div class="selected-item"><span>{{ __('Other charges') }}</span><strong>₹0.00</strong></div>
            <div class="selected-item"><strong>{{ __('Total') }}</strong><strong>₹<span id="total">0.00</span></strong></div>
        </div><button type="submit">{{ __('Place order') }}</button>
    </div>
</form>
<script>
    const form = document.querySelector('#order-form'),
        quantities = [...document.querySelectorAll('.qty')],
        itemsBox = document.querySelector('#selected-items'),
        emptyText = @json(__('No food selected.'));

    function refresh() {
        let sum = 0;
        itemsBox.replaceChildren();
        let count = 0;
        quantities.forEach(q => {
            const quantity = Number(q.value) || 0,
                price = Number(q.dataset.price);
            if (quantity > 0) {
                count++;
                const line = quantity * price;
                sum += line;
                const row = document.createElement('div'),
                    name = document.createElement('span'),
                    amount = document.createElement('strong');
                row.className = 'selected-item';
                name.textContent = `${q.dataset.name} × ${quantity}`;
                amount.textContent = `₹${line.toFixed(2)}`;
                row.append(name, amount);
                itemsBox.append(row)
            }
        });
        if (!count) {
            const empty = document.createElement('span');
            empty.className = 'muted';
            empty.textContent = emptyText;
            itemsBox.append(empty)
        }
        document.querySelector('#total').textContent = sum.toFixed(2)
    }
    quantities.forEach(q => q.addEventListener('input', refresh));
    form.addEventListener('submit', e => {
        form.querySelectorAll('.generated').forEach(x => x.remove());
        const selected = quantities.filter(q => Number(q.value) > 0);
        if (!selected.length) {
            e.preventDefault();
            alert(@json(__('Choose at least one food item.')));
            return
        }
        selected.forEach((q, i) => {
            for (const [key, value] of [
                    ['id', q.dataset.id],
                    ['quantity', q.value]
                ]) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.className = 'generated';
                input.name = `items[${i}][${key}]`;
                input.value = value;
                form.append(input)
            }
        })
    })
</script>
@endif
@endsection