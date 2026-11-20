<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Ember Café — a warm welcome, a good cup, and a simpler way to run your café.">
    <title>@yield('title', 'Welcome') · Ember Café</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/cafe.css') }}">
    <script src="{{ asset('js/cafe.js') }}" defer></script>
    @stack('head')
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<aside class="sidebar">
    <a class="brand" href="{{ route('home') }}"><span class="brand-mark">e<span>•</span></span><span>ember<span class="brand-sub">CAFÉ & COMMUNITY</span></span></a>
    <div class="nav-label">YOUR CAFÉ, CONNECTED</div>
    <nav aria-label="Main navigation">
        @foreach (['home' => ['⌂', 'Overview'], 'pos' => ['▦', 'Point of sale'], 'customers.index' => ['◎', 'Customers'], 'users.index' => ['♧', 'Staff accounts'], 'about' => ['i', 'About Ember']] as $route => [$icon, $label])
        <a href="{{ route($route) }}" class="nav-item {{ request()->routeIs($route) ? 'active' : '' }}" @if(request()->routeIs($route)) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">{{ $icon }}</span>{{ $label }}<span class="nav-arrow">↗</span></a>
        @endforeach
    </nav>
    <div class="sidebar-bottom"><div class="mini-bean" aria-hidden="true">☕</div><strong>A little warmth.<br>In every cup.</strong><p>Made for the daily ritual.</p><div class="workspace"><span class="avatar">EC</span><div><b>Ember Café</b><small>Store workspace</small></div><span class="status-dot"></span></div></div>
</aside>
<div class="shell">
    <header class="topbar"><span>THE EMBER WORKSPACE <span class="topbar-divider">/</span> <b>@yield('title', 'Overview')</b></span><div>@auth<span class="local-badge"><span class="status-dot"></span> {{ auth()->user()->full_name }}</span><form class="inline-form" method="post" action="{{ route('logout') }}">@csrf<button class="text-button" type="submit">Log out</button></form>@else<a class="text-link" href="{{ route('login') }}">Log in</a><a class="text-link" href="{{ route('register') }}">Register</a>@endauth</div></header>
    <main id="main" class="main @yield('main-class')">@if(session('success'))<div class="flash-message" role="status">{{ session('success') }}</div>@endif @yield('content')</main>
    <footer class="footer"><span>© {{ date('Y') }} Ember Café</span><span>Good coffee. Thoughtful service.</span></footer>
</div>
@stack('scripts')
</body>
</html>
