<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.wins_log') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="wins" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['wins']['eyebrow'] }}</p>
            <h1>{{ $tools['wins']['title'] }}</h1>
            <p>{{ $tools['wins']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="winsForm">
                <label>
                    <span>{{ $tools['wins']['fields']['win'] }}</span>
                    <input id="winsWin" type="text" placeholder="{{ $tools['wins']['placeholders']['win'] }}">
                </label>
                <label>
                    <span>{{ $tools['wins']['fields']['category'] }}</span>
                    <select id="winsCategory">
                        @foreach ($tools['wins']['categories'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['wins']['fields']['effort'] }}</span>
                    <select id="winsEffort">
                        @foreach ($tools['wins']['efforts'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['wins']['fields']['support'] }}</span>
                    <input id="winsSupport" type="text" placeholder="{{ $tools['wins']['placeholders']['support'] }}">
                </label>
                <label>
                    <span>{{ $tools['wins']['fields']['pattern'] }}</span>
                    <input id="winsPattern" type="text" placeholder="{{ $tools['wins']['placeholders']['pattern'] }}">
                </label>
                <label>
                    <span>{{ $tools['wins']['fields']['next'] }}</span>
                    <input id="winsNext" type="text" placeholder="{{ $tools['wins']['placeholders']['next'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['wins']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="winsPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="winsDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="winsClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['wins']['output_eyebrow'] }}</p>
                <h2 id="winsOutputTitle">{{ $tools['wins']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['wins']['sections']['reframe'] }}</h3>
                        <p id="winsReframeText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['wins']['sections']['evidence'] }}</h3>
                        <ul id="winsEvidenceList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['wins']['sections']['repeat'] }}</h3>
                        <p id="winsRepeatText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['wins']['sections']['share'] }}</h3>
                        <p id="winsShareText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['wins']['sections']['next'] }}</h3>
                        <p id="winsNextText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
