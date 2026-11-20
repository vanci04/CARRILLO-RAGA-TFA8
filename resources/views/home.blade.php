@extends('layouts.app')
@section('title', 'Overview')
@section('content')
<div class="page-heading"><div><p class="eyebrow">A FRESH START</p><h1>Good things are brewing.</h1><p>Welcome to Ember. Make a little room for a great café day.</p></div><a class="button dark" href="{{ route('pos') }}">Open point of sale <span>↗</span></a></div>
<section class="hero">
    <div class="hero-copy"><span class="pill">SLOW MOMENTS. GREAT COFFEE.</span><h2>Your daily grind,<br>made a little<br><em>smoother.</em></h2><p>From the first hello to the last latte.<br>One warm space for your people and your orders.</p><a href="{{ route('pos') }}" class="button cream">Let’s make a coffee <span>↗</span></a><div class="hero-foot"><span class="tiny-star">✳</span> Crafted with care. Served with a smile.</div></div>
    <div class="hero-art" aria-label="Illustration of an espresso cup with latte art"><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><span class="art-note">a cup of<br><em>something good.</em></span><div class="bean bean-one"></div><div class="bean bean-two"></div><div class="saucer"><div class="cup-handle"></div><div class="coffee"><div class="latte-heart">♥</div><span></span></div></div><span class="roast-stamp">SMALL BATCH<br><b>✳</b><br>BIG HEART</span></div>
</section>
<div class="section-heading"><h2>A place for everything.</h2><span>The essentials, right here.</span></div>
<section class="quick-grid" aria-label="Workspace shortcuts">
    <a class="quick-card" href="{{ route('pos') }}"><div><span class="card-icon orange">▦</span><span class="round-arrow">↗</span></div><h3>Serve something good</h3><p>Browse the menu, build an order, and keep the coffee flowing.</p><span class="text-link">Go to point of sale →</span></a>
    <a class="quick-card" href="{{ route('customers.index') }}"><div><span class="card-icon green">◎</span><span class="round-arrow">↗</span></div><h3>Know your regulars</h3><p>A familiar face makes the day. Find your customer accounts here.</p><span class="text-link">View customers →</span></a>
    <a class="quick-card" href="{{ route('users.index') }}"><div><span class="card-icon tan">♧</span><span class="round-arrow">↗</span></div><h3>Meet the good people</h3><p>The baristas, cashiers, and crew behind every great café moment.</p><span class="text-link">View staff accounts →</span></a>
</section>
<div class="bottom-note"><span>✳</span><div><b>More than a cup of coffee.</b><p>A welcoming corner, a shared ritual, a little Ember in your day.</p></div><a href="{{ route('about') }}">Our story ↗</a></div>
@endsection
