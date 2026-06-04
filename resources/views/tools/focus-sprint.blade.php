<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.focus_sprint') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="focus" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['focus']['eyebrow'] }}</p>
            <h1>{{ $tools['focus']['title'] }}</h1>
            <p>{{ $tools['focus']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="focusForm">
                <label>
                    <span>{{ $tools['focus']['fields']['task'] }}</span>
                    <input id="focusTask" type="text" placeholder="{{ $tools['focus']['placeholders']['task'] }}">
                </label>
                <label>
                    <span>{{ $tools['focus']['fields']['mode'] }}</span>
                    <select id="focusMode">
                        @foreach ($tools['focus']['modes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['focus']['fields']['minutes'] }}</span>
                    <select id="focusMinutes">
                        @foreach ($tools['focus']['minutes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['focus']['fields']['distraction'] }}</span>
                    <input id="focusDistraction" type="text" placeholder="{{ $tools['focus']['placeholders']['distraction'] }}">
                </label>
                <label>
                    <span>{{ $tools['focus']['fields']['support'] }}</span>
                    <select id="focusSupport">
                        @foreach ($tools['focus']['supports'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['focus']['fields']['reward'] }}</span>
                    <input id="focusReward" type="text" placeholder="{{ $tools['focus']['placeholders']['reward'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['focus']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="focusPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="focusDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="focusClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['focus']['output_eyebrow'] }}</p>
                <h2 id="focusOutputTitle">{{ $tools['focus']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['focus']['sections']['start'] }}</h3>
                        <p id="focusStartText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['focus']['sections']['steps'] }}</h3>
                        <ol id="focusStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['focus']['sections']['distraction'] }}</h3>
                        <p id="focusDistractionText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['focus']['sections']['support'] }}</h3>
                        <p id="focusSupportText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['focus']['sections']['reward'] }}</h3>
                        <p id="focusRewardText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['focus']['sections']['stop'] }}</h3>
                        <p id="focusStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
