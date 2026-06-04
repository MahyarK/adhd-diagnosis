<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.task_breakdown') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="task" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['task']['eyebrow'] }}</p>
            <h1>{{ $tools['task']['title'] }}</h1>
            <p>{{ $tools['task']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="taskForm">
                <label>
                    <span>{{ $tools['task']['fields']['dump'] }}</span>
                    <textarea id="taskDump" rows="4" placeholder="{{ $tools['task']['placeholders']['dump'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['task']['fields']['focus'] }}</span>
                    <input id="taskFocus" type="text" required placeholder="{{ $tools['task']['placeholders']['focus'] }}">
                </label>
                <label>
                    <span>{{ $tools['task']['fields']['stuck'] }}</span>
                    <select id="taskStuck">
                        @foreach ($tools['task']['stuck'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['task']['fields']['done'] }}</span>
                    <input id="taskDone" type="text" placeholder="{{ $tools['task']['placeholders']['done'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['task']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="taskPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="taskDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="taskClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="taskOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['task']['output_eyebrow'] }}</p>
                <h2 id="taskOutputTitle">{{ $tools['task']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['task']['sections']['now'] }}</h3>
                        <ul id="taskNowList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['task']['sections']['microsteps'] }}</h3>
                        <ol id="taskMicrosteps"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['task']['sections']['hidden'] }}</h3>
                        <ul id="taskHiddenList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['task']['sections']['done'] }}</h3>
                        <p id="taskDoneText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['task']['sections']['reset'] }}</h3>
                        <p id="taskResetText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
