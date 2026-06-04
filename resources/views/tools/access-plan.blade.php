<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.access_plan') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="access" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['access']['eyebrow'] }}</p>
            <h1>{{ $tools['access']['title'] }}</h1>
            <p>{{ $tools['access']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="accessForm">
                <label>
                    <span>{{ $tools['access']['fields']['barrier'] }}</span>
                    <select id="accessBarrier">
                        @foreach ($tools['access']['barriers'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['access']['fields']['location'] }}</span>
                    <input id="accessLocation" type="text" placeholder="{{ $tools['access']['placeholders']['location'] }}">
                </label>
                <label>
                    <span>{{ $tools['access']['fields']['budget'] }}</span>
                    <input id="accessBudget" type="text" placeholder="{{ $tools['access']['placeholders']['budget'] }}">
                </label>
                <label>
                    <span>{{ $tools['access']['fields']['support'] }}</span>
                    <input id="accessSupport" type="text" placeholder="{{ $tools['access']['placeholders']['support'] }}">
                </label>
                <label>
                    <span>{{ $tools['access']['fields']['notes'] }}</span>
                    <textarea id="accessNotes" rows="3" placeholder="{{ $tools['access']['placeholders']['notes'] }}"></textarea>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['access']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="accessDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="accessClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="accessOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['access']['output_eyebrow'] }}</p>
                <h2 id="accessOutputTitle">{{ $tools['access']['output_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['access']['sections']['first'] }}</h3>
                        <p id="accessFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['access']['sections']['ask'] }}</h3>
                        <ul id="accessAskList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['access']['sections']['fallback'] }}</h3>
                        <ul id="accessFallbackList"></ul>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['access']['sections']['sources'] }}</h3>
                        <ul class="source-list">
                            @foreach ($tools['access']['sources'] as $source)
                                <li><a href="{{ $source['url'] }}" target="_blank" rel="noreferrer">{{ $source['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
