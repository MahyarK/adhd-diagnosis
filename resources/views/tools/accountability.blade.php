<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.accountability') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="accountability" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['accountability']['eyebrow'] }}</p>
            <h1>{{ $tools['accountability']['title'] }}</h1>
            <p>{{ $tools['accountability']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="accountabilityForm">
                <label>
                    <span>{{ $tools['accountability']['fields']['task'] }}</span>
                    <input id="accountabilityTask" type="text" placeholder="{{ $tools['accountability']['placeholders']['task'] }}">
                </label>
                <label>
                    <span>{{ $tools['accountability']['fields']['person'] }}</span>
                    <input id="accountabilityPerson" type="text" placeholder="{{ $tools['accountability']['placeholders']['person'] }}">
                </label>
                <label>
                    <span>{{ $tools['accountability']['fields']['format'] }}</span>
                    <select id="accountabilityFormat">
                        @foreach ($tools['accountability']['formats'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['accountability']['fields']['time'] }}</span>
                    <input id="accountabilityTime" type="text" placeholder="{{ $tools['accountability']['placeholders']['time'] }}">
                </label>
                <label>
                    <span>{{ $tools['accountability']['fields']['proof'] }}</span>
                    <input id="accountabilityProof" type="text" placeholder="{{ $tools['accountability']['placeholders']['proof'] }}">
                </label>
                <label>
                    <span>{{ $tools['accountability']['fields']['missed'] }}</span>
                    <input id="accountabilityMissed" type="text" placeholder="{{ $tools['accountability']['placeholders']['missed'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['accountability']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="accountabilityPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="accountabilityDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="accountabilityClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['accountability']['output_eyebrow'] }}</p>
                <h2 id="accountabilityOutputTitle">{{ $tools['accountability']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['accountability']['sections']['script'] }}</h3>
                        <p id="accountabilityScriptText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['accountability']['sections']['steps'] }}</h3>
                        <ol id="accountabilityStepsList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['accountability']['sections']['proof'] }}</h3>
                        <p id="accountabilityProofText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['accountability']['sections']['missed'] }}</h3>
                        <p id="accountabilityMissedText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['accountability']['sections']['rules'] }}</h3>
                        <ul id="accountabilityRulesList"></ul>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
