@extends('layouts.app')

@section('title', __('assessment.ui.communication_repair'))

@section('content')
    <main id="main" class="tool-shell" data-tool="communication" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <h1>{{ $tools['communication']['title'] }}</h1>
            <p>{{ $tools['communication']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="communicationForm">
                <label>
                    <span>{{ $tools['communication']['fields']['situation'] }}</span>
                    <select id="communicationSituation">
                        @foreach ($tools['communication']['situations'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['communication']['fields']['person'] }}</span>
                    <input id="communicationPerson" type="text" placeholder="{{ $tools['communication']['placeholders']['person'] }}">
                </label>
                <label>
                    <span>{{ $tools['communication']['fields']['context'] }}</span>
                    <textarea id="communicationContext" rows="3" placeholder="{{ $tools['communication']['placeholders']['context'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['communication']['fields']['need'] }}</span>
                    <input id="communicationNeed" type="text" placeholder="{{ $tools['communication']['placeholders']['need'] }}">
                </label>
                <label>
                    <span>{{ $tools['communication']['fields']['tone'] }}</span>
                    <select id="communicationTone">
                        @foreach ($tools['communication']['tones'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['communication']['buttons']['build'] }}</button>
                </div>
                <details class="tool-extra-actions">
                    <summary>{{ $tools['shared']['extra_actions'] }}</summary>
                    <div>
                            <button type="button" class="secondary-button" id="communicationPrintButton">{{ $tools['shared']['print'] }}</button>
                            <button type="button" class="secondary-button" id="communicationDownloadButton">{{ $tools['shared']['download'] }}</button>
                            <button type="button" class="secondary-button" id="communicationClearButton">{{ $tools['shared']['clear'] }}</button>
                    </div>
                </details>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['communication']['output_eyebrow'] }}</p>
                <h2 id="communicationOutputTitle">{{ $tools['communication']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['communication']['sections']['message'] }}</h3>
                        <p id="communicationMessageText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['communication']['sections']['before'] }}</h3>
                        <ul id="communicationBeforeList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['communication']['sections']['repair'] }}</h3>
                        <ul id="communicationRepairList"></ul>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['communication']['sections']['boundary'] }}</h3>
                        <p id="communicationBoundaryText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
@endsection
