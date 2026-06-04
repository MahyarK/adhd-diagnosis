<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.weekly_reset') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="weekly" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['weekly']['eyebrow'] }}</p>
            <h1>{{ $tools['weekly']['title'] }}</h1>
            <p>{{ $tools['weekly']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="weeklyForm">
                <label>
                    <span>{{ $tools['weekly']['fields']['wins'] }}</span>
                    <textarea id="weeklyWins" rows="3" placeholder="{{ $tools['weekly']['placeholders']['wins'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['weekly']['fields']['loose'] }}</span>
                    <textarea id="weeklyLoose" rows="3" placeholder="{{ $tools['weekly']['placeholders']['loose'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['weekly']['fields']['must'] }}</span>
                    <textarea id="weeklyMust" rows="3" placeholder="{{ $tools['weekly']['placeholders']['must'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['weekly']['fields']['support'] }}</span>
                    <input id="weeklySupport" type="text" placeholder="{{ $tools['weekly']['placeholders']['support'] }}">
                </label>
                <label>
                    <span>{{ $tools['weekly']['fields']['reset'] }}</span>
                    <select id="weeklyResetMode">
                        @foreach ($tools['weekly']['reset_modes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['weekly']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="weeklyPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="weeklyDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="weeklyClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="weeklyOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['weekly']['output_eyebrow'] }}</p>
                <h2>{{ $tools['weekly']['output_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['weekly']['sections']['wins'] }}</h3>
                        <ul id="weeklyWinsList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['weekly']['sections']['loose'] }}</h3>
                        <ul id="weeklyLooseList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['weekly']['sections']['must'] }}</h3>
                        <ul id="weeklyMustList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['weekly']['sections']['support'] }}</h3>
                        <p id="weeklySupportText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['weekly']['sections']['reset'] }}</h3>
                        <ol id="weeklyResetList"></ol>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
