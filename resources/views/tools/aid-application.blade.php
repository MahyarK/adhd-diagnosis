<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.aid_application') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="aid" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['aid']['eyebrow'] }}</p>
            <h1>{{ $tools['aid']['title'] }}</h1>
            <p>{{ $tools['aid']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="aidForm">
                <label>
                    <span>{{ $tools['aid']['fields']['program'] }}</span>
                    <input id="aidProgram" type="text" placeholder="{{ $tools['aid']['placeholders']['program'] }}">
                </label>
                <label>
                    <span>{{ $tools['aid']['fields']['category'] }}</span>
                    <select id="aidCategory">
                        @foreach ($tools['aid']['categories'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['aid']['fields']['urgency'] }}</span>
                    <select id="aidUrgency">
                        @foreach ($tools['aid']['urgencies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['aid']['fields']['deadline'] }}</span>
                    <input id="aidDeadline" type="text" placeholder="{{ $tools['aid']['placeholders']['deadline'] }}">
                </label>
                <label>
                    <span>{{ $tools['aid']['fields']['blocker'] }}</span>
                    <input id="aidBlocker" type="text" placeholder="{{ $tools['aid']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['aid']['fields']['contact'] }}</span>
                    <input id="aidContact" type="text" placeholder="{{ $tools['aid']['placeholders']['contact'] }}">
                </label>
                <label>
                    <span>{{ $tools['aid']['fields']['document'] }}</span>
                    <input id="aidDocument" type="text" placeholder="{{ $tools['aid']['placeholders']['document'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['aid']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="aidPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="aidDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="aidClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['aid']['output_eyebrow'] }}</p>
                <h2 id="aidOutputTitle">{{ $tools['aid']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['aid']['sections']['first'] }}</h3>
                        <p id="aidFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['aid']['sections']['documents'] }}</h3>
                        <ul id="aidDocumentList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['aid']['sections']['script'] }}</h3>
                        <p id="aidScriptText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['aid']['sections']['questions'] }}</h3>
                        <ul id="aidQuestionList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['aid']['sections']['status'] }}</h3>
                        <ol id="aidStatusList"></ol>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['aid']['sections']['stop'] }}</h3>
                        <p id="aidStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
