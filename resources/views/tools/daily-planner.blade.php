<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.daily_planner') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="planner" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['planner']['eyebrow'] }}</p>
            <h1>{{ $tools['planner']['title'] }}</h1>
            <p>{{ $tools['planner']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="plannerForm">
                <label>
                    <span>{{ $tools['planner']['fields']['must'] }}</span>
                    <textarea id="planMust" rows="2" placeholder="{{ $tools['planner']['placeholders']['must'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['planner']['fields']['should'] }}</span>
                    <textarea id="planShould" rows="2" placeholder="{{ $tools['planner']['placeholders']['should'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['planner']['fields']['body'] }}</span>
                    <input id="planBody" type="text" placeholder="{{ $tools['planner']['placeholders']['body'] }}">
                </label>
                <label>
                    <span>{{ $tools['planner']['fields']['scary'] }}</span>
                    <input id="planScary" type="text" placeholder="{{ $tools['planner']['placeholders']['scary'] }}">
                </label>
                <label>
                    <span>{{ $tools['planner']['fields']['recovery'] }}</span>
                    <input id="planRecovery" type="text" placeholder="{{ $tools['planner']['placeholders']['recovery'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['planner']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="plannerPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="plannerDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="plannerClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="plannerOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['planner']['output_eyebrow'] }}</p>
                <h2>{{ $tools['planner']['output_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['planner']['sections']['must'] }}</h3>
                        <ul id="plannerMustList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['planner']['sections']['should'] }}</h3>
                        <ul id="plannerShouldList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['planner']['sections']['body'] }}</h3>
                        <p id="plannerBodyText">{{ $tools['planner']['empty_copy'] }}</p>
                    </article>
                    <article>
                        <h3>{{ $tools['planner']['sections']['scary'] }}</h3>
                        <p id="plannerScaryText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['planner']['sections']['recovery'] }}</h3>
                        <p id="plannerRecoveryText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
