@extends('layout')
@section('title','Create admin')
@section('content')
<div class="panel" style="max-width:520px;margin:45px auto">
    <p class="muted" style="margin:0">ADMIN SETUP</p>
    <h1>Create admin</h1>
    <form method="post" action="{{ route('admin.create.store') }}">@csrf<label for="name">Name</label><input id="name" type="text" name="name" required maxlength="100" value="{{ old('name') }}"><label for="username">Username</label><input id="username" type="text" name="username" required maxlength="50" autocomplete="username" value="{{ old('username') }}"><label for="mobile">Mobile number</label><input id="mobile" type="text" name="mobile" required inputmode="tel" maxlength="10" value="{{ old('mobile') }}"><label for="password">Password</label><input id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
        <p><button type="submit">Create admin</button></p>
    </form>
</div>
@endsection