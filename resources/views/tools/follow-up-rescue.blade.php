<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.follow_up_rescue') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="followup" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['followup']['eyebrow'] }}</p>
            <h1>{{ $tools['followup']['title'] }}</h1>
            <p>{{ $tools['followup']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="followupForm">
                <label>
                    <span>{{ $tools['followup']['fields']['thing'] }}</span>
                    <input id="followupThing" type="text" placeholder="{{ $tools['followup']['placeholders']['thing'] }}">
                </label>
                <label>
                    <span>{{ $tools['followup']['fields']['kind'] }}</span>
                    <select id="followupKind">
                        @foreach ($tools['followup']['kinds'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['followup']['fields']['lateness'] }}</span>
                    <select id="followupLateness">
                        @foreach ($tools['followup']['lateness'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['followup']['fields']['person'] }}</span>
                    <input id="followupPerson" type="text" placeholder="{{ $tools['followup']['placeholders']['person'] }}">
                </label>
                <label>
                    <span>{{ $tools['followup']['fields']['blocker'] }}</span>
                    <input id="followupBlocker" type="text" placeholder="{{ $tools['followup']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['followup']['fields']['next_date'] }}</span>
                    <input id="followupNextDate" type="text" placeholder="{{ $tools['followup']['placeholders']['next_date'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['followup']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="followupPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="followupDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="followupClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['followup']['output_eyebrow'] }}</p>
                <h2 id="followupOutputTitle">{{ $tools['followup']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['followup']['sections']['first'] }}</h3>
                        <p id="followupFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['followup']['sections']['script'] }}</h3>
                        <p id="followupScriptText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['followup']['sections']['steps'] }}</h3>
                        <ol id="followupStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['followup']['sections']['questions'] }}</h3>
                        <ul id="followupQuestionList"></ul>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['followup']['sections']['stop'] }}</h3>
                        <p id="followupStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
