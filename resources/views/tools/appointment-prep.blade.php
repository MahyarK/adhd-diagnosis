<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.appointment_prep') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="appointment" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['appointment']['eyebrow'] }}</p>
            <h1>{{ $tools['appointment']['title'] }}</h1>
            <p>{{ $tools['appointment']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="appointmentForm">
                <label>
                    <span>{{ $tools['appointment']['fields']['provider'] }}</span>
                    <input id="appointmentProvider" type="text" placeholder="{{ $tools['appointment']['placeholders']['provider'] }}">
                </label>
                <label>
                    <span>{{ $tools['appointment']['fields']['symptoms'] }}</span>
                    <textarea id="appointmentSymptoms" rows="3" placeholder="{{ $tools['appointment']['placeholders']['symptoms'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['appointment']['fields']['impact'] }}</span>
                    <textarea id="appointmentImpact" rows="3" placeholder="{{ $tools['appointment']['placeholders']['impact'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['appointment']['fields']['questions'] }}</span>
                    <textarea id="appointmentQuestions" rows="3" placeholder="{{ $tools['appointment']['placeholders']['questions'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['appointment']['fields']['contact'] }}</span>
                    <select id="appointmentContact">
                        @foreach ($tools['appointment']['contact'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['appointment']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="appointmentPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="appointmentDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="appointmentClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="appointmentOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['appointment']['output_eyebrow'] }}</p>
                <h2 id="appointmentOutputTitle">{{ $tools['appointment']['output_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['appointment']['sections']['bring'] }}</h3>
                        <ul id="appointmentBringList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['appointment']['sections']['say'] }}</h3>
                        <p id="appointmentScript"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['appointment']['sections']['questions'] }}</h3>
                        <ul id="appointmentQuestionList"></ul>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['appointment']['sections']['notes'] }}</h3>
                        <p id="appointmentNotes"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
