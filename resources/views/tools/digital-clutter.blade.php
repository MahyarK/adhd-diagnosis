<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.digital_clutter') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="digital" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['digital']['eyebrow'] }}</p>
            <h1>{{ $tools['digital']['title'] }}</h1>
            <p>{{ $tools['digital']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="digitalForm">
                <label>
                    <span>{{ $tools['digital']['fields']['mode'] }}</span>
                    <select id="digitalMode">
                        @foreach ($tools['digital']['modes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['digital']['fields']['target'] }}</span>
                    <input id="digitalTarget" type="text" placeholder="{{ $tools['digital']['placeholders']['target'] }}">
                </label>
                <label>
                    <span>{{ $tools['digital']['fields']['energy'] }}</span>
                    <select id="digitalEnergy">
                        @foreach ($tools['digital']['energies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['digital']['fields']['device'] }}</span>
                    <select id="digitalDevice">
                        @foreach ($tools['digital']['devices'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['digital']['fields']['blocker'] }}</span>
                    <input id="digitalBlocker" type="text" placeholder="{{ $tools['digital']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['digital']['fields']['deadline'] }}</span>
                    <input id="digitalDeadline" type="text" placeholder="{{ $tools['digital']['placeholders']['deadline'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['digital']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="digitalPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="digitalDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="digitalClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['digital']['output_eyebrow'] }}</p>
                <h2 id="digitalOutputTitle">{{ $tools['digital']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['digital']['sections']['first'] }}</h3>
                        <p id="digitalFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['digital']['sections']['steps'] }}</h3>
                        <ol id="digitalStepsList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['digital']['sections']['search'] }}</h3>
                        <p id="digitalSearchText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['digital']['sections']['shutdown'] }}</h3>
                        <p id="digitalShutdownText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['digital']['sections']['parking'] }}</h3>
                        <ul id="digitalParkingList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['digital']['sections']['stop'] }}</h3>
                        <p id="digitalStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
