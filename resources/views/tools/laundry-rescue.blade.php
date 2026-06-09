<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.laundry_rescue') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="laundry" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['laundry']['eyebrow'] }}</p>
            <h1>{{ $tools['laundry']['title'] }}</h1>
            <p>{{ $tools['laundry']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="laundryForm">
                <label>
                    <span>{{ $tools['laundry']['fields']['mode'] }}</span>
                    <select id="laundryMode">
                        @foreach ($tools['laundry']['modes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['laundry']['fields']['needed'] }}</span>
                    <input id="laundryNeeded" type="text" placeholder="{{ $tools['laundry']['placeholders']['needed'] }}">
                </label>
                <label>
                    <span>{{ $tools['laundry']['fields']['energy'] }}</span>
                    <select id="laundryEnergy">
                        @foreach ($tools['laundry']['energies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['laundry']['fields']['machine'] }}</span>
                    <select id="laundryMachine">
                        @foreach ($tools['laundry']['machines'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['laundry']['fields']['blocker'] }}</span>
                    <input id="laundryBlocker" type="text" placeholder="{{ $tools['laundry']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['laundry']['fields']['deadline'] }}</span>
                    <input id="laundryDeadline" type="text" placeholder="{{ $tools['laundry']['placeholders']['deadline'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['laundry']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="laundryPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="laundryDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="laundryClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['laundry']['output_eyebrow'] }}</p>
                <h2 id="laundryOutputTitle">{{ $tools['laundry']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['laundry']['sections']['first'] }}</h3>
                        <p id="laundryFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['laundry']['sections']['steps'] }}</h3>
                        <ol id="laundryStepsList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['laundry']['sections']['drying'] }}</h3>
                        <p id="laundryDryingText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['laundry']['sections']['emergency'] }}</h3>
                        <p id="laundryEmergencyText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['laundry']['sections']['minimums'] }}</h3>
                        <ul id="laundryMinimumsList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['laundry']['sections']['stop'] }}</h3>
                        <p id="laundryStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
