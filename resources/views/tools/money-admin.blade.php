<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.money_admin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="money" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['money']['eyebrow'] }}</p>
            <h1>{{ $tools['money']['title'] }}</h1>
            <p>{{ $tools['money']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="moneyForm">
                <label>
                    <span>{{ $tools['money']['fields']['task'] }}</span>
                    <input id="moneyTask" type="text" placeholder="{{ $tools['money']['placeholders']['task'] }}">
                </label>
                <label>
                    <span>{{ $tools['money']['fields']['category'] }}</span>
                    <select id="moneyCategory">
                        @foreach ($tools['money']['categories'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['money']['fields']['urgency'] }}</span>
                    <select id="moneyUrgency">
                        @foreach ($tools['money']['urgencies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['money']['fields']['blocker'] }}</span>
                    <input id="moneyBlocker" type="text" placeholder="{{ $tools['money']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['money']['fields']['contact'] }}</span>
                    <input id="moneyContact" type="text" placeholder="{{ $tools['money']['placeholders']['contact'] }}">
                </label>
                <label>
                    <span>{{ $tools['money']['fields']['outcome'] }}</span>
                    <input id="moneyOutcome" type="text" placeholder="{{ $tools['money']['placeholders']['outcome'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['money']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="moneyPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="moneyDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="moneyClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['money']['output_eyebrow'] }}</p>
                <h2 id="moneyOutputTitle">{{ $tools['money']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['money']['sections']['first'] }}</h3>
                        <p id="moneyFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['money']['sections']['steps'] }}</h3>
                        <ol id="moneyStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['money']['sections']['script'] }}</h3>
                        <p id="moneyScriptText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['money']['sections']['questions'] }}</h3>
                        <ul id="moneyQuestionList"></ul>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['money']['sections']['stop'] }}</h3>
                        <p id="moneyStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
