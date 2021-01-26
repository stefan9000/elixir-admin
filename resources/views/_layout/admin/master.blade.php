<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <title>Control | Elixir Events</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
</head>
<body>
<div class="wrapper">
    <input id="api_token" type="hidden" value="{{ auth()->user()->api_token }}"/>
    <aside>
        <img src="/images/logo.svg" alt="Logo">
        <div id="nav" class="nav-left">
            <navigation :items="items"></navigation>
        </div>
    </aside>
    <main>
        <header>
            <nav>
                <div class="sub-nav">
                    <a href="{{ route('admin_event_create') }}"> <em></em> <span> Create Event </span></a>
                    <a href="{{ route('admin_news_create') }}"> <em></em> <span> Publish News </span></a>
                </div>
                <a class="logout" href="{{ route('admin_logout') }}"><em></em> <span> Logout </span></a>
            </nav>
        </header>
        <div class="content">
            @yield('content')
        </div>
    </main>
</div>

<!-- SCRIPT -->
<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/navigation.js') }}"></script>
@yield('scripts')
</body>
</html>
