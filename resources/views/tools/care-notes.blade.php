<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.care_notes') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="care" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['care']['eyebrow'] }}</p>
            <h1>{{ $tools['care']['title'] }}</h1>
            <p>{{ $tools['care']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="careForm">
                <label>
                    <span>{{ $tools['care']['fields']['main'] }}</span>
                    <textarea id="careMain" rows="3" placeholder="{{ $tools['care']['placeholders']['main'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['care']['fields']['history'] }}</span>
                    <textarea id="careHistory" rows="3" placeholder="{{ $tools['care']['placeholders']['history'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['care']['fields']['settings'] }}</span>
                    <textarea id="careSettings" rows="3" placeholder="{{ $tools['care']['placeholders']['settings'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['care']['fields']['overlap'] }}</span>
                    <textarea id="careOverlap" rows="3" placeholder="{{ $tools['care']['placeholders']['overlap'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['care']['fields']['help'] }}</span>
                    <textarea id="careHelp" rows="3" placeholder="{{ $tools['care']['placeholders']['help'] }}"></textarea>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['care']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="carePrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="careDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="careClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['care']['output_eyebrow'] }}</p>
                <h2 id="careOutputTitle">{{ $tools['care']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['care']['sections']['opening'] }}</h3>
                        <p id="careOpening"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['care']['sections']['history'] }}</h3>
                        <ul id="careHistoryList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['care']['sections']['settings'] }}</h3>
                        <ul id="careSettingsList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['care']['sections']['overlap'] }}</h3>
                        <ul id="careOverlapList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['care']['sections']['ask'] }}</h3>
                        <ul id="careAskList"></ul>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
