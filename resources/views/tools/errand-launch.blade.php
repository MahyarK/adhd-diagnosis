<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.errand_launch') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="errand" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['errand']['eyebrow'] }}</p>
            <h1>{{ $tools['errand']['title'] }}</h1>
            <p>{{ $tools['errand']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="errandForm">
                <label>
                    <span>{{ $tools['errand']['fields']['destination'] }}</span>
                    <input id="errandDestination" type="text" placeholder="{{ $tools['errand']['placeholders']['destination'] }}">
                </label>
                <label>
                    <span>{{ $tools['errand']['fields']['kind'] }}</span>
                    <select id="errandKind">
                        @foreach ($tools['errand']['kinds'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['errand']['fields']['travel'] }}</span>
                    <select id="errandTravel">
                        @foreach ($tools['errand']['travel'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['errand']['fields']['deadline'] }}</span>
                    <input id="errandDeadline" type="text" placeholder="{{ $tools['errand']['placeholders']['deadline'] }}">
                </label>
                <label>
                    <span>{{ $tools['errand']['fields']['blocker'] }}</span>
                    <input id="errandBlocker" type="text" placeholder="{{ $tools['errand']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['errand']['fields']['bring'] }}</span>
                    <textarea id="errandBring" rows="4" placeholder="{{ $tools['errand']['placeholders']['bring'] }}"></textarea>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['errand']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="errandPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="errandDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="errandClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['errand']['output_eyebrow'] }}</p>
                <h2 id="errandOutputTitle">{{ $tools['errand']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['errand']['sections']['first'] }}</h3>
                        <p id="errandFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['errand']['sections']['steps'] }}</h3>
                        <ol id="errandStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['errand']['sections']['bring'] }}</h3>
                        <ul id="errandBringList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['errand']['sections']['late'] }}</h3>
                        <p id="errandLateText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['errand']['sections']['backup'] }}</h3>
                        <ul id="errandBackupList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['errand']['sections']['stop'] }}</h3>
                        <p id="errandStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
