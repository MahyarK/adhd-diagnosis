<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.dashboard') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell dashboard-shell" data-tool="dashboard" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['dashboard']['eyebrow'] }}</p>
            <h1>{{ $tools['dashboard']['title'] }}</h1>
            <p>{{ $tools['dashboard']['intro'] }}</p>
        </section>

        <section class="dashboard-grid">
            <article class="dashboard-next printable-tool">
                <p class="eyebrow">{{ $tools['dashboard']['next_eyebrow'] }}</p>
                <h2 id="dashboardNextTitle">{{ $tools['dashboard']['empty_title'] }}</h2>
                <p id="dashboardNextBody">{{ $tools['dashboard']['empty_body'] }}</p>
                <div class="tool-actions">
                    <a class="primary-link" id="dashboardNextLink" href="{{ route('tools.goal', ['lang' => $locale]) }}">{{ $tools['dashboard']['start_link'] }}</a>
                    <button type="button" class="secondary-button" id="dashboardExportButton">{{ $tools['dashboard']['export'] }}</button>
                    <button type="button" class="secondary-button" id="dashboardClearButton">{{ $tools['dashboard']['clear'] }}</button>
                </div>
            </article>

            <section class="dashboard-cards" aria-label="{{ $tools['dashboard']['saved_title'] }}">
                @foreach ($tools['dashboard']['cards'] as $key => $card)
                    <article class="dashboard-card" data-dashboard-card="{{ $key }}">
                        <span class="dashboard-status" data-dashboard-status="{{ $key }}">{{ $tools['dashboard']['not_saved'] }}</span>
                        <h3>{{ $card['title'] }}</h3>
                        <p data-dashboard-summary="{{ $key }}">{{ $card['empty'] }}</p>
                        <a href="{{ route($card['route'], ['lang' => $locale]) }}">{{ $card['action'] }}</a>
                    </article>
                @endforeach
            </section>
        </section>

        <p class="dashboard-note">{{ $tools['dashboard']['privacy'] }}</p>
    </main>
</body>
</html>
