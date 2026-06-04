<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.decision_priority') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="decision" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['decision']['eyebrow'] }}</p>
            <h1>{{ $tools['decision']['title'] }}</h1>
            <p>{{ $tools['decision']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="decisionForm">
                <label>
                    <span>{{ $tools['decision']['fields']['options'] }}</span>
                    <textarea id="decisionOptions" rows="5" placeholder="{{ $tools['decision']['placeholders']['options'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['decision']['fields']['urgency'] }}</span>
                    <select id="decisionUrgency">
                        @foreach ($tools['decision']['urgencies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['decision']['fields']['energy'] }}</span>
                    <select id="decisionEnergy">
                        @foreach ($tools['decision']['energy'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['decision']['fields']['consequence'] }}</span>
                    <select id="decisionConsequence">
                        @foreach ($tools['decision']['consequences'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['decision']['fields']['relief'] }}</span>
                    <input id="decisionRelief" type="text" placeholder="{{ $tools['decision']['placeholders']['relief'] }}">
                </label>
                <label>
                    <span>{{ $tools['decision']['fields']['support'] }}</span>
                    <input id="decisionSupport" type="text" placeholder="{{ $tools['decision']['placeholders']['support'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['decision']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="decisionPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="decisionDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="decisionClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['decision']['output_eyebrow'] }}</p>
                <h2 id="decisionOutputTitle">{{ $tools['decision']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['decision']['sections']['chosen'] }}</h3>
                        <p id="decisionChosenText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['decision']['sections']['why'] }}</h3>
                        <p id="decisionWhyText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['decision']['sections']['first'] }}</h3>
                        <p id="decisionFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['decision']['sections']['steps'] }}</h3>
                        <ol id="decisionStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['decision']['sections']['parked'] }}</h3>
                        <ul id="decisionParkedList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['decision']['sections']['script'] }}</h3>
                        <p id="decisionScriptText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['decision']['sections']['stop'] }}</h3>
                        <p id="decisionStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
