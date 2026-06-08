<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.time_block') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="time" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['time']['eyebrow'] }}</p>
            <h1>{{ $tools['time']['title'] }}</h1>
            <p>{{ $tools['time']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="timeForm">
                <label>
                    <span>{{ $tools['time']['fields']['start'] }}</span>
                    <input id="timeStart" type="time" value="{{ $tools['time']['defaults']['start'] }}">
                </label>
                <label>
                    <span>{{ $tools['time']['fields']['end'] }}</span>
                    <input id="timeEnd" type="time" value="{{ $tools['time']['defaults']['end'] }}">
                </label>
                <label>
                    <span>{{ $tools['time']['fields']['must'] }}</span>
                    <textarea id="timeMust" rows="3" placeholder="{{ $tools['time']['placeholders']['must'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['time']['fields']['fixed'] }}</span>
                    <textarea id="timeFixed" rows="2" placeholder="{{ $tools['time']['placeholders']['fixed'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['time']['fields']['buffer'] }}</span>
                    <select id="timeBuffer">
                        @foreach ($tools['time']['buffers'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['time']['fields']['energy'] }}</span>
                    <select id="timeEnergy">
                        @foreach ($tools['time']['energy'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['time']['fields']['recovery'] }}</span>
                    <input id="timeRecovery" type="text" placeholder="{{ $tools['time']['placeholders']['recovery'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['time']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="timePrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="timeDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="timeClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['time']['output_eyebrow'] }}</p>
                <h2 id="timeOutputTitle">{{ $tools['time']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['time']['sections']['blocks'] }}</h3>
                        <ol id="timeBlocksList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['time']['sections']['fixed'] }}</h3>
                        <ul id="timeFixedList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['time']['sections']['buffers'] }}</h3>
                        <ul id="timeBufferList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['time']['sections']['recovery'] }}</h3>
                        <p id="timeRecoveryText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['time']['sections']['fallback'] }}</h3>
                        <p id="timeFallbackText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
