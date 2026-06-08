<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.body_needs') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="body" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['body']['eyebrow'] }}</p>
            <h1>{{ $tools['body']['title'] }}</h1>
            <p>{{ $tools['body']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="bodyForm">
                <label>
                    <span>{{ $tools['body']['fields']['food'] }}</span>
                    <select id="bodyFood">
                        @foreach ($tools['body']['food'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['body']['fields']['water'] }}</span>
                    <select id="bodyWater">
                        @foreach ($tools['body']['water'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['body']['fields']['meds'] }}</span>
                    <select id="bodyMeds">
                        @foreach ($tools['body']['meds'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['body']['fields']['sleep'] }}</span>
                    <select id="bodySleep">
                        @foreach ($tools['body']['sleep'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['body']['fields']['signal'] }}</span>
                    <select id="bodySignal">
                        @foreach ($tools['body']['signals'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['body']['fields']['task'] }}</span>
                    <input id="bodyTask" type="text" placeholder="{{ $tools['body']['placeholders']['task'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['body']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="bodyPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="bodyDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="bodyClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['body']['output_eyebrow'] }}</p>
                <h2 id="bodyOutputTitle">{{ $tools['body']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['body']['sections']['first'] }}</h3>
                        <p id="bodyFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['body']['sections']['steps'] }}</h3>
                        <ol id="bodyStepsList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['body']['sections']['checks'] }}</h3>
                        <ul id="bodyChecksList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['body']['sections']['task'] }}</h3>
                        <p id="bodyTaskText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['body']['sections']['stop'] }}</h3>
                        <p id="bodyStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
