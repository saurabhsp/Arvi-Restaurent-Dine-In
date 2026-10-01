@extends('layout')
@section('title',__('My Orders'))
@section('content')
@include('admin.nav')
<style>
    .orders-table {
        min-width: 0;
        width: 100%;
        table-layout: fixed
    }

    .orders-table .inline-order-select {
        width: 100% !important;
        min-width: 0 !important
    }

    .orders-table td {
        vertical-align: middle
    }

    .save-state {
        min-height: 20px;
        color: var(--muted)
    }
</style>
<div class="row" style="justify-content:space-between;margin-top:24px">
    <div>
        <p class="muted" style="margin:0">{{ __('LIVE ORDER LIST') }}</p>
        <h1 style="margin-top:4px">{{ __('My Orders') }}</h1>
    </div><label style="margin:0;min-width:180px">{{ __('Order date') }}<input id="order-date" type="date" value="{{ $date }}"></label>
</div>
<section class="panel">
    <p class="muted" id="refresh-note">{{ __('New orders appear automatically.') }}</p>
    <p class="save-state" id="save-state" aria-live="polite"></p>
    <div class="table-wrap">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>{{ __('Order ID') }}</th>
                    <th>{{ __('Customer name') }}</th>
                    <th>{{ __('Mobile number') }}</th>
                    <th>{{ __('Items x qty / price') }}</th>
                    <th>{{ __('Payment status') }}</th>
                    <th>{{ __('Pay via') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Print') }}</th>
                </tr>
            </thead>
            <tbody id="orders-body"></tbody>
        </table>
    </div>
</section>
<script>
    const endpoint = @json(route('admin.dashboard.data')),
        invoiceBase = @json(url('/admin/orders')),
        dateInput = document.querySelector('#order-date'),
        csrf = @json(csrf_token()),
        ordersBody = document.querySelector('#orders-body'),
        saveState = document.querySelector('#save-state');

    let isSaving = false;

    const text = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    } [char]));

    const money = value => `₹${Number(value || 0).toFixed(2)}`;

    const labels = {
        paid: @json(__('Paid')),
        unpaid: @json(__('Unpaid')),
        credit: @json(__('Credit (Udhari)')),
        cash: @json(__('Cash')),
        upi: 'UPI',
        card: @json(__('Card')),
        print: @json(__('Print')),
        empty: @json(__('No orders for this date.')),
        updated: @json(__('Updated at')),
        saving: @json(__('Saving change...')),
        saved: @json(__('Saved.')),
        saveError: @json(__('Could not save. Please try again.')),
        waiting: @json(__('Waiting for connection...'))
    };

    const choices = (selected, values) =>
        `<select class="inline-order-select" data-order-select>
            ${values.map(([value, label]) =>
                `<option value="${value}" ${selected === value ? 'selected' : ''}>${label}</option>`
            ).join('')}
        </select>`;

    function render(data) {
        const rows = data.orders.map(order => `<tr><td><strong>${text(order.order_number)}</strong></td><td>${text(order.customer_name)}</td><td>${text(order.customer_phone||'-')}</td><td>${order.items.map(item=>`&bull; ${text(item.product_name)} x ${item.quantity} (${money(item.line_total)})`).join(' ')}</td><td><form data-order="${order.id}" data-field="payment_status">${choices(order.payment_status,[['paid',labels.paid],['unpaid',labels.unpaid],['credit',labels.credit]])}</form></td><td><form data-order="${order.id}" data-field="payment_method">${choices(order.payment_method,[['cash',labels.cash],['upi',labels.upi],['card',labels.card]])}</form></td><td>${money(order.total)}</td><td><a class="btn" target="_blank" href="${invoiceBase}/${order.id}/invoice">${labels.print}</a></td></tr>`).join('');
        ordersBody.innerHTML = rows || `<tr><td colspan="8" class="muted">${labels.empty}</td></tr>`;
        document.querySelector('#refresh-note').textContent = `${labels.updated} ${new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}`
    }
    ordersBody.addEventListener('change', async event => {
        const select = event.target.closest('[data-order-select]');
        if (!select || isSaving) return;
        const form = select.closest('form'),
            body = {};
        body[form.dataset.field] = select.value;
        isSaving = true;
        select.disabled = true;
        saveState.textContent = labels.saving;
        try {
            const response = await fetch(`${invoiceBase}/${form.dataset.order}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(body)
            });
            if (!response.ok) throw new Error('Save failed');
            saveState.textContent = labels.saved;
            await loadOrders()
        } catch (error) {
            saveState.textContent = labels.saveError;
            select.disabled = false
        } finally {
            isSaving = false
        }
    });
    async function loadOrders() {
        if (isSaving || ordersBody.querySelector('select:focus')) return;
        try {
            const response = await fetch(`${endpoint}?date=${encodeURIComponent(dateInput.value)}`, {
                headers: {
                    Accept: 'application/json'
                }
            });
            if (response.ok) render(await response.json())
        } catch (error) {
            document.querySelector('#refresh-note').textContent = labels.waiting
        }
    }
    dateInput.addEventListener('change', loadOrders);
    loadOrders();
    setInterval(() => {
        if (!document.hidden) loadOrders()
    }, 2000);
</script>
@endsection