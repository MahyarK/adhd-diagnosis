<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.symptom_tracker') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="tracker" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['tracker']['eyebrow'] }}</p>
            <h1>{{ $tools['tracker']['title'] }}</h1>
            <p>{{ $tools['tracker']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="trackerForm">
                <label>
                    <span>{{ $tools['tracker']['fields']['date'] }}</span>
                    <input id="trackerDate" type="date" required>
                </label>
                <label>
                    <span>{{ $tools['tracker']['fields']['sleep'] }}</span>
                    <input id="trackerSleep" type="number" min="0" max="24" step="0.5" placeholder="{{ $tools['tracker']['placeholders']['sleep'] }}">
                </label>
                <label>
                    <span>{{ $tools['tracker']['fields']['focus'] }}</span>
                    <select id="trackerFocus">
                        @foreach ($tools['tracker']['scale'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['tracker']['fields']['stress'] }}</span>
                    <select id="trackerStress">
                        @foreach ($tools['tracker']['scale'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['tracker']['fields']['context'] }}</span>
                    <textarea id="trackerContext" rows="3" placeholder="{{ $tools['tracker']['placeholders']['context'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['tracker']['fields']['notes'] }}</span>
                    <textarea id="trackerNotes" rows="3" placeholder="{{ $tools['tracker']['placeholders']['notes'] }}"></textarea>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['tracker']['buttons']['save'] }}</button>
                    <button type="button" class="secondary-button" id="trackerDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="trackerClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="trackerOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['tracker']['output_eyebrow'] }}</p>
                <h2 id="trackerOutputTitle">{{ $tools['tracker']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['tracker']['sections']['scores'] }}</h3>
                        <p id="trackerScoresText">{{ $tools['tracker']['empty_copy'] }}</p>
                    </article>
                    <article>
                        <h3>{{ $tools['tracker']['sections']['sleep'] }}</h3>
                        <p id="trackerSleepText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['tracker']['sections']['context'] }}</h3>
                        <p id="trackerContextText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['tracker']['sections']['notes'] }}</h3>
                        <p id="trackerNotesText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
