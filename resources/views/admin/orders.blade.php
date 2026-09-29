@extends('layout')
@section('title','My Orders')
@section('content')
@include('admin.nav')
<style>
    .orders-table {
        min-width: 1120px
    }

    .orders-table .inline-order-select {
        width: 150px !important;
        min-width: 150px !important
    }

    .orders-table td {
        vertical-align: middle
    }

    .save-state {
        min-height: 20px;
        color: #745f50
    }
</style>
<div class="row" style="justify-content:space-between;margin-top:24px">
    <div>
        <p class="muted" style="margin:0">LIVE ORDER LIST</p>
        <h1 style="margin-top:4px">My Orders</h1>
    </div><label style="margin:0;min-width:180px">Order date<input id="order-date" type="date" value="{{ $date }}"></label>
</div>
<section class="panel">
    <p class="muted" id="refresh-note">New orders appear automatically.</p>
    <p class="save-state" id="save-state" aria-live="polite"></p>
    <div class="table-wrap">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer name</th>
                    <th>Mobile number</th>
                    <th>Items x qty / price</th>
                    <th>Payment status</th>
                    <th>Pay via</th>
                    <th>Total</th>
                    <th>Print</th>
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
    const money = value => `Rs. ${Number(value||0).toFixed(2)}`;
    const choices = (selected, values) => `<select class="inline-order-select" data-order-select>${values.map(([value,label])=>`<option value="${value}" ${selected===value?'selected':''}>${label}</option>`).join('')}</select>`;

    function render(data) {
        const rows = data.orders.map(order => `<tr><td><strong>${text(order.order_number)}</strong></td><td>${text(order.customer_name)}</td><td>${text(order.customer_phone||'-')}</td><td style="white-space:nowrap">${order.items.map(item=>`&bull; ${text(item.product_name)} x ${item.quantity} (${money(item.line_total)})`).join(' ')}</td><td><form data-order="${order.id}" data-field="payment_status">${choices(order.payment_status,[['paid','Paid'],['unpaid','Unpaid'],['credit','Credit (Udhari)']])}</form></td><td><form data-order="${order.id}" data-field="payment_method">${choices(order.payment_method,[['cash','Cash'],['upi','UPI'],['card','Card']])}</form></td><td>${money(order.total)}</td><td><a class="btn" target="_blank" href="${invoiceBase}/${order.id}/invoice">Print</a></td></tr>`).join('');
        ordersBody.innerHTML = rows || '<tr><td colspan="8" class="muted">No orders for this date.</td></tr>';
        document.querySelector('#refresh-note').textContent = `Updated at ${new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}. Live refresh every 2 seconds.`
    }
    ordersBody.addEventListener('change', async event => {
        const select = event.target.closest('[data-order-select]');
        if (!select || isSaving) return;
        const form = select.closest('form'),
            body = {};
        body[form.dataset.field] = select.value;
        isSaving = true;
        select.disabled = true;
        saveState.textContent = 'Saving change...';
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
            saveState.textContent = 'Saved.';
            await loadOrders()
        } catch (error) {
            saveState.textContent = 'Could not save. Please try again.';
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
            document.querySelector('#refresh-note').textContent = 'Waiting for connection...'
        }
    }
    dateInput.addEventListener('change', loadOrders);
    loadOrders();
    setInterval(() => {
        if (!document.hidden) loadOrders()
    }, 2000);
</script>
@endsection