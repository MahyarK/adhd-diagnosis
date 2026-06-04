<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.reminders') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="reminders" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['reminders']['eyebrow'] }}</p>
            <h1>{{ $tools['reminders']['title'] }}</h1>
            <p>{{ $tools['reminders']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="reminderForm">
                <label>
                    <span>{{ $tools['reminders']['fields']['kind'] }}</span>
                    <select id="reminderKind">
                        @foreach ($tools['reminders']['kinds'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['reminders']['fields']['date'] }}</span>
                    <input id="reminderDate" type="date" required>
                </label>
                <label>
                    <span>{{ $tools['reminders']['fields']['time'] }}</span>
                    <input id="reminderTime" type="time" required>
                </label>
                <label>
                    <span>{{ $tools['reminders']['fields']['note'] }}</span>
                    <textarea id="reminderNote" rows="3" placeholder="{{ $tools['reminders']['placeholders']['note'] }}"></textarea>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['reminders']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="reminderDownloadButton">{{ $tools['reminders']['buttons']['download'] }}</button>
                    <button type="button" class="secondary-button" id="reminderClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="reminderOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['reminders']['output_eyebrow'] }}</p>
                <h2 id="reminderOutputTitle">{{ $tools['reminders']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['reminders']['sections']['when'] }}</h3>
                        <p id="reminderWhenText">{{ $tools['reminders']['empty_copy'] }}</p>
                    </article>
                    <article>
                        <h3>{{ $tools['reminders']['sections']['why'] }}</h3>
                        <p id="reminderWhyText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['reminders']['sections']['script'] }}</h3>
                        <p id="reminderScriptText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
