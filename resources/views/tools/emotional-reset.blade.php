<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.emotional_reset') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="emotion" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['emotion']['eyebrow'] }}</p>
            <h1>{{ $tools['emotion']['title'] }}</h1>
            <p>{{ $tools['emotion']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="emotionForm">
                <label>
                    <span>{{ $tools['emotion']['fields']['trigger'] }}</span>
                    <input id="emotionTrigger" type="text" placeholder="{{ $tools['emotion']['placeholders']['trigger'] }}">
                </label>
                <label>
                    <span>{{ $tools['emotion']['fields']['intensity'] }}</span>
                    <select id="emotionIntensity">
                        @foreach ($tools['emotion']['intensities'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['emotion']['fields']['body'] }}</span>
                    <select id="emotionBody">
                        @foreach ($tools['emotion']['body_states'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['emotion']['fields']['story'] }}</span>
                    <input id="emotionStory" type="text" placeholder="{{ $tools['emotion']['placeholders']['story'] }}">
                </label>
                <label>
                    <span>{{ $tools['emotion']['fields']['next'] }}</span>
                    <input id="emotionNext" type="text" placeholder="{{ $tools['emotion']['placeholders']['next'] }}">
                </label>
                <label>
                    <span>{{ $tools['emotion']['fields']['support'] }}</span>
                    <input id="emotionSupport" type="text" placeholder="{{ $tools['emotion']['placeholders']['support'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['emotion']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="emotionPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="emotionDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="emotionClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['emotion']['output_eyebrow'] }}</p>
                <h2 id="emotionOutputTitle">{{ $tools['emotion']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['emotion']['sections']['first'] }}</h3>
                        <p id="emotionFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['emotion']['sections']['ground'] }}</h3>
                        <ol id="emotionGroundList"></ol>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['emotion']['sections']['reframe'] }}</h3>
                        <p id="emotionReframeText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['emotion']['sections']['next'] }}</h3>
                        <p id="emotionNextText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['emotion']['sections']['script'] }}</h3>
                        <p id="emotionScriptText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['emotion']['sections']['stop'] }}</h3>
                        <p id="emotionStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
