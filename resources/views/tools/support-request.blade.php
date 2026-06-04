<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.support_request') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="support" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['support']['eyebrow'] }}</p>
            <h1>{{ $tools['support']['title'] }}</h1>
            <p>{{ $tools['support']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="supportForm">
                <label>
                    <span>{{ $tools['support']['fields']['audience'] }}</span>
                    <select id="supportAudience">
                        @foreach ($tools['support']['audiences'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['support']['fields']['situation'] }}</span>
                    <input id="supportSituation" type="text" placeholder="{{ $tools['support']['placeholders']['situation'] }}">
                </label>
                <label>
                    <span>{{ $tools['support']['fields']['barrier'] }}</span>
                    <textarea id="supportBarrier" rows="3" placeholder="{{ $tools['support']['placeholders']['barrier'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['support']['fields']['request'] }}</span>
                    <textarea id="supportRequest" rows="3" placeholder="{{ $tools['support']['placeholders']['request'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['support']['fields']['trial'] }}</span>
                    <input id="supportTrial" type="text" placeholder="{{ $tools['support']['placeholders']['trial'] }}">
                </label>
                <label>
                    <span>{{ $tools['support']['fields']['tone'] }}</span>
                    <select id="supportTone">
                        @foreach ($tools['support']['tones'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['support']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="supportPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="supportDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="supportClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['support']['output_eyebrow'] }}</p>
                <h2 id="supportOutputTitle">{{ $tools['support']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['support']['sections']['message'] }}</h3>
                        <p id="supportMessage"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['support']['sections']['ask'] }}</h3>
                        <ul id="supportAskList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['support']['sections']['experiment'] }}</h3>
                        <p id="supportExperiment"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['support']['sections']['follow_up'] }}</h3>
                        <p id="supportFollowUp"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
