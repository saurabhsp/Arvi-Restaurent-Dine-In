@extends('layout')
@section('title',__('Admin login'))
@section('content')
<div class="panel" style="max-width:460px;margin:45px auto"><p class="muted" style="margin:0">{{ __('ADMIN ACCESS') }}</p><h1>{{ __('Admin login') }}</h1><form method="post" action="{{ route('admin.login.submit') }}">@csrf<label for="username">{{ __('Username') }}</label><input id="username" type="text" name="username" required autocomplete="username" value="{{ old('username') }}"><label for="password">{{ __('Password') }}</label><input id="password" type="password" name="password" required autocomplete="current-password"><p><button type="submit">{{ __('Log in') }}</button></p></form></div>
@endsection
