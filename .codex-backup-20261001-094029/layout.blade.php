<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Day Night Cafe')</title>
    <style>
        :root {
            --ink: #24170f;
            --wood: #6e3d23;
            --wood-dark: #472717;
            --paper: #fffaf2;
            --line: #dec6a8
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: var(--ink);
            background: repeating-linear-gradient(90deg, #efe3cf 0, #f7eddd 3px, #f1e5d1 7px, #f8eedf 13px)
        }

        header {
            background: #2b1b12;
            color: #fff;
            padding: 16px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px
        }

        header a {
            color: #fff;
            text-decoration: none
        }

        .brand {
            font-family: Georgia, serif;
            font-size: 26px;
            font-weight: bold
        }

        .brand img {
            max-height: 50px;
            vertical-align: middle
        }

        main {
            max-width: 1150px;
            margin: auto;
            padding: 26px 18px 120px
        }

        h1,
        h2,
        h3 {
            font-family: Georgia, serif
        }

        h1 {
            font-size: clamp(34px, 5vw, 58px);
            margin: 8px 0 14px
        }

        .hero {
            padding: clamp(30px, 7vw, 80px);
            background: linear-gradient(110deg, #2b1a12e8, #58331fdc), repeating-linear-gradient(90deg, #6b3d27, #8d5836 14px, #75452e 27px);
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px #301a1640
        }

        .hero p {
            font-size: 18px;
            max-width: 600px
        }

        .panel,
        .card {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 5px 15px #2b1b1215
        }

        .panel {
            padding: 22px;
            margin: 22px 0
        }

        .row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px
        }

        input,
        textarea,
        select {
            font: inherit;
            padding: 12px;
            border: 1px solid #bca384;
            border-radius: 6px;
            background: #fff;
            max-width: 100%
        }

        input[type=text],
        input[type=email],
        input[type=password],
        input[type=number],
        input[type=date],
        input[type=search],
        textarea,
        select {
            width: 100%
        }

        label {
            display: block;
            font-weight: bold;
            margin: 12px 0 5px
        }

        button,
        .btn {
            font: inherit;
            border: 0;
            background: var(--wood);
            color: #fff;
            padding: 12px 16px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block
        }

        button:hover,
        .btn:hover {
            background: var(--wood-dark)
        }

        .muted {
            color: #745f50
        }

        .chips {
            display: flex;
            gap: 9px;
            overflow: auto;
            padding: 15px 0;
            margin: 15px 0;
            background: #f5e9d7
        }

        .chips a {
            white-space: nowrap;
            background: #fff7e9;
            border: 1px solid #b99b75;
            border-radius: 18px;
            padding: 10px 16px;
            color: var(--ink);
            text-decoration: none
        }

        .card {
            overflow: hidden
        }

        .photo {
            height: 180px;
            background: linear-gradient(135deg, #cfaa7d, #896144);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 22px
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .card-body {
            padding: 18px
        }

        .card h3 {
            margin: 0 0 8px
        }

        .price {
            font-weight: bold;
            color: #683719;
            font-size: 20px
        }

        .qty {
            width: 75px !important
        }

        footer {
            text-align: center;
            padding: 35px;
            color: #765d4a
        }

        .alert {
            background: #e7f4df;
            border: 1px solid #95c379;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0
        }

        .errors {
            background: #fff1ed;
            border-color: #dc9682
        }

        .nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap
        }

        .table-wrap {
            overflow: auto
        }

        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 850px
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dbc7ab
        }

        section {
            scroll-margin-top: 80px
        }

        .admin-stats h2 {
            font-size: 32px;
            margin: 8px 0 0
        }

        .customer-details-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr))
        }

        .food-search-box {
            max-width: 460px;
            margin: 0 auto 30px
        }

        .selected-items {
            margin: 10px 0;
            display: grid;
            gap: 4px
        }

        .selected-item {
            display: flex;
            justify-content: space-between;
            gap: 12px
        }

        .charges {
            border-top: 1px dashed #bca384;
            margin-top: 10px;
            padding-top: 10px;
            display: grid;
            gap: 5px
        }

        .order-bar {
            z-index: 4
        }

        @media(max-width:600px) {
            header {
                align-items: flex-start;
                flex-direction: column
            }

            .hero {
                border-radius: 0;
                margin: -26px -18px 0
            }

            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px
            }

            .photo {
                height: 130px
            }

            .card-body {
                padding: 12px
            }

            .card h3 {
                font-size: 17px
            }

            .panel {
                padding: 16px
            }

            .customer-details-grid {
                grid-template-columns: 1fr
            }

            .food-search-box {
                margin-bottom: 36px
            }

            .order-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #fff9ef;
                padding: 12px;
                box-shadow: 0 -3px 15px #0002;
                max-height: 48vh;
                overflow: auto
            }

            .order-bar button {
                width: 100%;
                margin-top: 4px
            }

            .selected-items {
                font-size: 13px
            }

            .selected-item {
                align-items: flex-start
            }

            .admin-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .admin-stats .panel {
                margin: 0
            }

            .admin-stats h2 {
                font-size: 24px
            }
        }
    </style>
</head>

<body>
    <header><a class="brand" href="{{ route('home') }}">@if(file_exists(public_path('logo.png')))<img src="{{ asset('logo.png') }}" alt="Restaurant logo">@else Day Night Cafe @endif</a>
        <nav class="nav"><a href="{{ route('home') }}">Menu</a>@if(session('admin_user_id')) <a class="btn" href="{{ route('admin.dashboard') }}">Admin dashboard</a>@else <a class="btn" href="{{ route('admin.login') }}">Admin login</a>@endif</nav>
    </header>
    <main>@if(session('success'))<div class="alert">{{ session('success') }}</div>@endif @if($errors->any())<div class="alert errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif @yield('content')</main>
    <footer>Fresh food &middot; Warm hospitality</footer>
</body>

</html>