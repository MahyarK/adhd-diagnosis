<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.resource_library') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell resource-shell">
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['resources']['eyebrow'] }}</p>
            <h1>{{ $tools['resources']['title'] }}</h1>
            <p>{{ $tools['resources']['intro'] }}</p>
        </section>

        <section class="resource-grid" aria-label="{{ __('assessment.ui.resource_library') }}">
            @foreach ($tools['resources']['cards'] as $card)
                <article class="resource-card">
                    <p class="eyebrow">{{ $card['eyebrow'] }}</p>
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
</body>
</html>
