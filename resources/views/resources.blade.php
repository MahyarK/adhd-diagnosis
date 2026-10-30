@extends('layouts.app')

@section('title', __('assessment.ui.resource_library'))

@section('content')
    <main id="main" class="page resource-shell">
        <section class="page-hero">
            <h1>{{ $tools['resources']['title'] }}</h1>
            <p class="lede">{{ $tools['resources']['intro'] }}</p>
        </section>

        <section class="resource-grid" aria-label="{{ __('assessment.ui.resource_library') }}">
            @foreach ($tools['resources']['cards'] as $card)
                <article class="resource-card reveal">
                    <p class="kicker">{{ $card['eyebrow'] }}</p>
                    <h2>{{ $card['title'] }}</h2>
                    <p>{{ $card['body'] }}</p>
                    <div>
                        <h3>{{ $tools['resources']['try_today'] }}</h3>
                        <p>{{ $card['try'] }}</p>
                    </div>
                    <a href="{{ $card['url'] }}" target="_blank" rel="noreferrer">{{ $card['source'] }}</a>
                </article>
            @endforeach
        </section>

        <p class="dashboard-note">{{ $tools['resources']['note'] }}</p>
    </main>
@endsection
