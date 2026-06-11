<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.job_hunt') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="job" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['job']['eyebrow'] }}</p>
            <h1>{{ $tools['job']['title'] }}</h1>
            <p>{{ $tools['job']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="jobForm">
                <label>
                    <span>{{ $tools['job']['fields']['target'] }}</span>
                    <input id="jobTarget" type="text" placeholder="{{ $tools['job']['placeholders']['target'] }}">
                </label>
                <label>
                    <span>{{ $tools['job']['fields']['stage'] }}</span>
                    <select id="jobStage">
                        @foreach ($tools['job']['stages'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['job']['fields']['energy'] }}</span>
                    <select id="jobEnergy">
                        @foreach ($tools['job']['energies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['job']['fields']['blocker'] }}</span>
                    <input id="jobBlocker" type="text" placeholder="{{ $tools['job']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['job']['fields']['contact'] }}</span>
                    <input id="jobContact" type="text" placeholder="{{ $tools['job']['placeholders']['contact'] }}">
                </label>
                <label>
                    <span>{{ $tools['job']['fields']['deadline'] }}</span>
                    <input id="jobDeadline" type="text" placeholder="{{ $tools['job']['placeholders']['deadline'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['job']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="jobPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="jobDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="jobClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['job']['output_eyebrow'] }}</p>
                <h2 id="jobOutputTitle">{{ $tools['job']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['job']['sections']['first'] }}</h3>
                        <p id="jobFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['job']['sections']['steps'] }}</h3>
                        <ol id="jobStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['job']['sections']['materials'] }}</h3>
                        <ul id="jobMaterialList"></ul>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['job']['sections']['script'] }}</h3>
                        <p id="jobScriptText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['job']['sections']['stop'] }}</h3>
                        <p id="jobStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
