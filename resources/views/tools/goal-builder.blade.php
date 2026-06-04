<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.goal_builder') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="goal" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['goal']['eyebrow'] }}</p>
            <h1>{{ $tools['goal']['title'] }}</h1>
            <p>{{ $tools['goal']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="goalForm">
                <label>
                    <span>{{ $tools['goal']['fields']['goal'] }}</span>
                    <textarea id="goalText" rows="3" required placeholder="{{ $tools['goal']['placeholders']['goal'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['goal']['fields']['why'] }}</span>
                    <input id="goalWhy" type="text" placeholder="{{ $tools['goal']['placeholders']['why'] }}">
                </label>
                <label>
                    <span>{{ $tools['goal']['fields']['blocker'] }}</span>
                    <input id="goalBlocker" type="text" placeholder="{{ $tools['goal']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['goal']['fields']['energy'] }}</span>
                    <select id="goalEnergy">
                        @foreach ($tools['goal']['energy'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['goal']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="goalPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="goalDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="goalClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="goalOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['goal']['output_eyebrow'] }}</p>
                <h2 id="goalOutputTitle">{{ $tools['goal']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['goal']['sections']['first_step'] }}</h3>
                        <p id="goalFirstStep">{{ $tools['goal']['empty_copy'] }}</p>
                    </article>
                    <article>
                        <h3>{{ $tools['goal']['sections']['microsteps'] }}</h3>
                        <ol id="goalMicrosteps"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['goal']['sections']['support'] }}</h3>
                        <p id="goalSupport"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['goal']['sections']['done'] }}</h3>
                        <p id="goalDone"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
