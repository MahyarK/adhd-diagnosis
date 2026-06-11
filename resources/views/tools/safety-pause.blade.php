<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.safety_pause') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="safety" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['safety']['eyebrow'] }}</p>
            <h1>{{ $tools['safety']['title'] }}</h1>
            <p>{{ $tools['safety']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="safetyForm">
                <label>
                    <span>{{ $tools['safety']['fields']['state'] }}</span>
                    <select id="safetyState">
                        @foreach ($tools['safety']['states'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['safety']['fields']['intensity'] }}</span>
                    <select id="safetyIntensity">
                        @foreach ($tools['safety']['intensities'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['safety']['fields']['support'] }}</span>
                    <input id="safetySupport" type="text" placeholder="{{ $tools['safety']['placeholders']['support'] }}">
                </label>
                <label>
                    <span>{{ $tools['safety']['fields']['location'] }}</span>
                    <input id="safetyLocation" type="text" placeholder="{{ $tools['safety']['placeholders']['location'] }}">
                </label>
                <label>
                    <span>{{ $tools['safety']['fields']['next_step'] }}</span>
                    <input id="safetyNextStep" type="text" placeholder="{{ $tools['safety']['placeholders']['next_step'] }}">
                </label>
                <label>
                    <span>{{ $tools['safety']['fields']['barrier'] }}</span>
                    <input id="safetyBarrier" type="text" placeholder="{{ $tools['safety']['placeholders']['barrier'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['safety']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="safetyPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="safetyDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="safetyClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['safety']['output_eyebrow'] }}</p>
                <h2 id="safetyOutputTitle">{{ $tools['safety']['empty_title'] }}</h2>
                <div class="crisis-links">
                    <a href="tel:988">{{ $tools['safety']['links']['call'] }}</a>
                    <a href="sms:988">{{ $tools['safety']['links']['text'] }}</a>
                    <a href="https://988lifeline.org/chat/" target="_blank" rel="noreferrer">{{ $tools['safety']['links']['chat'] }}</a>
                    <a href="https://findahelpline.com/" target="_blank" rel="noreferrer">{{ $tools['safety']['links']['international'] }}</a>
                </div>
                <div class="output-grid">
                    <article class="wide urgent-output">
                        <h3>{{ $tools['safety']['sections']['immediate'] }}</h3>
                        <p id="safetyImmediateText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['safety']['sections']['first'] }}</h3>
                        <p id="safetyFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['safety']['sections']['script'] }}</h3>
                        <p id="safetyScriptText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['safety']['sections']['ground'] }}</h3>
                        <ul id="safetyGroundList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['safety']['sections']['steps'] }}</h3>
                        <ol id="safetyStepList"></ol>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['safety']['sections']['stop'] }}</h3>
                        <p id="safetyStopText"></p>
                    </article>
                </div>
                <p class="tool-disclaimer">{{ $tools['safety']['disclaimer'] }}</p>
            </section>
        </section>
    </main>
</body>
</html>
