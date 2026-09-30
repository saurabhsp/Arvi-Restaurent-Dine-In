<a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
<a href="{{ route('admin.orders.create') }}">{{ __('New order') }}</a>
<a href="{{ route('admin.orders') }}">{{ __('My Orders') }}</a>
<a href="{{ route('admin.categories') }}">{{ __('Categories') }}</a>
<a href="{{ route('admin.products') }}">{{ __('Products') }}</a>
<a href="{{ route('admin.upi-qr') }}">{{ __('UPI QR') }}</a>
<form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit">{{ __('Log out') }}</button></form>
