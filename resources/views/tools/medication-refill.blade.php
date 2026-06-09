<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.medication_refill') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="meds" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['meds']['eyebrow'] }}</p>
            <h1>{{ $tools['meds']['title'] }}</h1>
            <p>{{ $tools['meds']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="medsForm">
                <label>
                    <span>{{ $tools['meds']['fields']['mode'] }}</span>
                    <select id="medsMode">
                        @foreach ($tools['meds']['modes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['meds']['fields']['medicine'] }}</span>
                    <input id="medsMedicine" type="text" placeholder="{{ $tools['meds']['placeholders']['medicine'] }}">
                </label>
                <label>
                    <span>{{ $tools['meds']['fields']['supply'] }}</span>
                    <input id="medsSupply" type="text" placeholder="{{ $tools['meds']['placeholders']['supply'] }}">
                </label>
                <label>
                    <span>{{ $tools['meds']['fields']['blocker'] }}</span>
                    <input id="medsBlocker" type="text" placeholder="{{ $tools['meds']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['meds']['fields']['contact'] }}</span>
                    <input id="medsContact" type="text" placeholder="{{ $tools['meds']['placeholders']['contact'] }}">
                </label>
                <label>
                    <span>{{ $tools['meds']['fields']['deadline'] }}</span>
                    <input id="medsDeadline" type="text" placeholder="{{ $tools['meds']['placeholders']['deadline'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['meds']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="medsPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="medsDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="medsClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['meds']['output_eyebrow'] }}</p>
                <h2 id="medsOutputTitle">{{ $tools['meds']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['meds']['sections']['first'] }}</h3>
                        <p id="medsFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['meds']['sections']['steps'] }}</h3>
                        <ol id="medsStepsList"></ol>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['meds']['sections']['script'] }}</h3>
                        <p id="medsScriptText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['meds']['sections']['questions'] }}</h3>
                        <ul id="medsQuestionsList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['meds']['sections']['safety'] }}</h3>
                        <p id="medsSafetyText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['meds']['sections']['stop'] }}</h3>
                        <p id="medsStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
