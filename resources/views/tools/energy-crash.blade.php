@extends('layouts.app')

@section('title', __('assessment.ui.energy_crash'))

@section('content')
    <main id="main" class="tool-shell" data-tool="energy" data-locale="{{ $locale }}" data-tools='@json($tools)' data-ai-url="{{ route('tools.energy.ai', ['lang' => $locale]) }}">
        <section class="tool-hero">
            <h1>{{ $tools['energy']['title'] }}</h1>
            <p>{{ $tools['energy']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="energyForm">
                <label>
                    <span>{{ $tools['energy']['fields']['state'] }}</span>
                    <select id="energyState">
                        @foreach ($tools['energy']['states'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['energy']['fields']['must'] }}</span>
                    <input id="energyMust" type="text" placeholder="{{ $tools['energy']['placeholders']['must'] }}">
                </label>
                <label>
                    <span>{{ $tools['energy']['fields']['body'] }}</span>
                    <select id="energyBody">
                        @foreach ($tools['energy']['body_needs'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['energy']['fields']['drop'] }}</span>
                    <input id="energyDrop" type="text" placeholder="{{ $tools['energy']['placeholders']['drop'] }}">
                </label>
                <label>
                    <span>{{ $tools['energy']['fields']['support'] }}</span>
                    <input id="energySupport" type="text" placeholder="{{ $tools['energy']['placeholders']['support'] }}">
                </label>
                <label>
                    <span>{{ $tools['energy']['fields']['time'] }}</span>
                    <select id="energyTime">
                        @foreach ($tools['energy']['times'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['energy']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="energyAiButton">{{ $tools['energy']['buttons']['ai'] }}</button>
                </div>
                <details class="tool-extra-actions">
                    <summary>{{ $tools['shared']['extra_actions'] }}</summary>
                    <div>
                            <button type="button" class="secondary-button" id="energyPrintButton">{{ $tools['shared']['print'] }}</button>
                            <button type="button" class="secondary-button" id="energyDownloadButton">{{ $tools['shared']['download'] }}</button>
                            <button type="button" class="secondary-button" id="energyClearButton">{{ $tools['shared']['clear'] }}</button>
                    </div>
                </details>
                <p class="map-note" id="energyAiStatus">{{ $tools['energy']['ai_status']['idle'] }}</p>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['energy']['output_eyebrow'] }}</p>
                <h2 id="energyOutputTitle">{{ $tools['energy']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['energy']['sections']['first'] }}</h3>
                        <p id="energyFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['energy']['sections']['body'] }}</h3>
                        <p id="energyBodyText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['energy']['sections']['steps'] }}</h3>
                        <ol id="energyStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['energy']['sections']['drop'] }}</h3>
                        <p id="energyDropText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['energy']['sections']['script'] }}</h3>
                        <p id="energyScriptText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['energy']['sections']['stop'] }}</h3>
                        <p id="energyStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
@endsection
