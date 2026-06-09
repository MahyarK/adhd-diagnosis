<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.work_school_support') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="workschool" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['workschool']['eyebrow'] }}</p>
            <h1>{{ $tools['workschool']['title'] }}</h1>
            <p>{{ $tools['workschool']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="workSchoolForm">
                <label>
                    <span>{{ $tools['workschool']['fields']['setting'] }}</span>
                    <select id="workSchoolSetting">
                        @foreach ($tools['workschool']['settings'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['workschool']['fields']['challenge'] }}</span>
                    <select id="workSchoolChallenge">
                        @foreach ($tools['workschool']['challenges'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['workschool']['fields']['support_style'] }}</span>
                    <select id="workSchoolSupportStyle">
                        @foreach ($tools['workschool']['support_styles'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['workschool']['fields']['friction'] }}</span>
                    <textarea id="workSchoolFriction" rows="3" placeholder="{{ $tools['workschool']['placeholders']['friction'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['workschool']['fields']['person'] }}</span>
                    <input id="workSchoolPerson" type="text" placeholder="{{ $tools['workschool']['placeholders']['person'] }}">
                </label>
                <label>
                    <span>{{ $tools['workschool']['fields']['trial'] }}</span>
                    <input id="workSchoolTrial" type="text" placeholder="{{ $tools['workschool']['placeholders']['trial'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['workschool']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="workSchoolPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="workSchoolDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="workSchoolClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['workschool']['output_eyebrow'] }}</p>
                <h2 id="workSchoolOutputTitle">{{ $tools['workschool']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['workschool']['sections']['first'] }}</h3>
                        <p id="workSchoolFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['workschool']['sections']['options'] }}</h3>
                        <ul id="workSchoolOptionList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['workschool']['sections']['script'] }}</h3>
                        <p id="workSchoolScriptText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['workschool']['sections']['trial'] }}</h3>
                        <p id="workSchoolTrialText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['workschool']['sections']['review'] }}</h3>
                        <ul id="workSchoolReviewList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['workschool']['sections']['boundary'] }}</h3>
                        <p id="workSchoolBoundaryText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
