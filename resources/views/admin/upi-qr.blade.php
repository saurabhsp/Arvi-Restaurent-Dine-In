@extends('layout')
@section('title',__('UPI QR code'))
@section('content')
@include('admin.nav')
<div style="max-width:620px"><p class="muted" style="margin:24px 0 0">{{ __('PAYMENT SETUP') }}</p><h1>{{ __('UPI QR code') }}</h1></div>
@if($paymentQrCode)
<section class="panel" style="max-width:620px"><h2>{{ __('Active QR code') }}</h2><img src="{{ asset('storage/'.$paymentQrCode->image_path) }}" alt="{{ __('Active UPI payment QR code') }}" style="display:block;max-width:320px;width:100%;margin:18px 0"><p><strong>UPI ID:</strong> {{ $paymentQrCode->upi_id ?: __('Not set') }}<br><strong>{{ __('Payee') }}:</strong> {{ $paymentQrCode->payee_name ?: __('Not set') }}</p><form method="post" action="{{ route('admin.upi-qr.delete',$paymentQrCode) }}" data-confirm="{{ __('Delete this QR code?') }}" onsubmit="return confirm(this.dataset.confirm)">@csrf @method('DELETE')<button type="submit">{{ __('Delete QR code') }}</button></form></section>
@else
<section class="panel" style="max-width:620px"><h2>{{ __('Upload UPI QR code') }}</h2><p class="muted">{{ __('Only one payment QR code can be active. Upload a clear, tightly cropped PNG, JPG, or WebP up to 4 MB. The UPI ID must match the QR code.') }}</p><form method="post" action="{{ route('admin.upi-qr.upload') }}" enctype="multipart/form-data">@csrf<label for="upi_id">{{ __('UPI ID shown in your QR') }}</label><input id="upi_id" name="upi_id" type="text" required maxlength="100" placeholder="restaurant@bank" value="{{ old('upi_id') }}"><label for="payee_name">{{ __('Payee name') }}</label><input id="payee_name" name="payee_name" type="text" required maxlength="100" value="{{ old('payee_name') }}"><label for="qr_image">{{ __('QR code image') }}</label><input id="qr_image" type="file" name="qr_image" accept="image/png,image/jpeg,image/webp" required><p><button type="submit">{{ __('Upload QR code') }}</button></p></form></section>
@endif
@endsection
