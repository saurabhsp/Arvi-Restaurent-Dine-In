<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Day Night Cafe')</title>
    <script>
        try {
            document.documentElement.dataset.theme = localStorage.getItem('dine-theme') === 'dark' ? 'dark' : 'light'
        } catch (e) {}
    </script>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>

<body>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ __('Day Night Cafe home') }}">
            @if(file_exists(public_path('laptop_logo2.png')))<img src="{{ asset('laptop_logo2.png') }}" alt="{{ __('Restaurant logo') }}">@else <span>Day Night Cafe</span> @endif
        </a>
        <div class="header-actions">
            <form method="post" action="{{ route('locale.update') }}" class="locale-form">@csrf
                <label class="sr-only" for="site-locale">{{ __('Language') }}</label>
                <select id="site-locale" name="locale" aria-label="{{ __('Language') }}" onchange="this.form.submit()">
                    <option value="en" @selected(app()->getLocale()==='en')>EN</option>
                    <option value="mr" @selected(app()->getLocale()==='mr')>मराठी</option>
                </select>
            </form>
            <button type="button" id="theme-toggle" class="icon-button" aria-label="{{ __('Switch day or night theme') }}" title="{{ __('Switch day or night theme') }}">◐</button>
            <nav class="desktop-top-nav" aria-label="{{ __('Main navigation') }}">
                <a href="{{ route('home') }}">{{ __('Menu') }}</a>
                @if(session('customer_phone') && !session('admin_user_id'))<a href="{{ route('history') }}">{{ __('Order history') }}</a>@endif
                @if(session('admin_user_id'))<a href="{{ route('admin.dashboard') }}">{{ __('Admin dashboard') }}</a>@else<a href="{{ route('admin.login') }}">{{ __('Admin login') }}</a>@endif
            </nav>
            <details class="mobile-nav">
                <summary aria-label="{{ __('Open navigation menu') }}" title="{{ __('Open navigation menu') }}"><span></span><span></span><span></span></summary>
                <nav class="mobile-drawer" aria-label="{{ __('Main navigation') }}">
                    <a href="{{ route('home') }}">{{ __('Menu') }}</a>
                    @if(session('customer_phone') && !session('admin_user_id'))<a href="{{ route('history') }}">{{ __('Order history') }}</a>@endif
                    @if(session('admin_user_id'))
                    @include('admin.links')
                    @else
                    <a href="{{ route('admin.login') }}">{{ __('Admin login') }}</a>
                    @endif
                </nav>
            </details>
        </div>
    </header>
    <main class="{{ request()->is('admin*') ? 'admin-main' : '' }}">
        @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
        @yield('content')
    </main>
    <footer>{{ __('Fresh food · Warm hospitality') }}</footer>
    <script>
        document.querySelector('#theme-toggle').addEventListener('click', () => {
            const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = next;
            try {
                localStorage.setItem('dine-theme', next)
            } catch (e) {}
        })
    </script>
</body>

</html>