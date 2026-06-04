<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.provider_shortlist') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="providers" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['providers']['eyebrow'] }}</p>
            <h1>{{ $tools['providers']['title'] }}</h1>
            <p>{{ $tools['providers']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="providerForm">
                <label>
                    <span>{{ $tools['providers']['fields']['name'] }}</span>
                    <input id="providerName" type="text" required placeholder="{{ $tools['providers']['placeholders']['name'] }}">
                </label>
                <label>
                    <span>{{ $tools['providers']['fields']['kind'] }}</span>
                    <input id="providerKind" type="text" placeholder="{{ $tools['providers']['placeholders']['kind'] }}">
                </label>
                <label>
                    <span>{{ $tools['providers']['fields']['contact'] }}</span>
                    <input id="providerContact" type="text" placeholder="{{ $tools['providers']['placeholders']['contact'] }}">
                </label>
                <label>
                    <span>{{ $tools['providers']['fields']['cost'] }}</span>
                    <input id="providerCost" type="text" placeholder="{{ $tools['providers']['placeholders']['cost'] }}">
                </label>
                <label>
                    <span>{{ $tools['providers']['fields']['status'] }}</span>
                    <select id="providerStatus">
                        @foreach ($tools['providers']['statuses'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['providers']['fields']['notes'] }}</span>
                    <textarea id="providerNotes" rows="3" placeholder="{{ $tools['providers']['placeholders']['notes'] }}"></textarea>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['providers']['buttons']['save'] }}</button>
                    <button type="button" class="secondary-button" id="providersDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="providersClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" id="providersOutput" aria-live="polite">
                <p class="eyebrow">{{ $tools['providers']['output_eyebrow'] }}</p>
                <h2>{{ $tools['providers']['output_title'] }}</h2>
                <div class="provider-list" id="providersList"></div>
            </section>
        </section>
    </main>
</body>
</html>
