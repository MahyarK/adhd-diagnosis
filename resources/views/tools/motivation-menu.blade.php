@extends('layouts.app')

@section('title', __('assessment.ui.motivation_menu'))

@section('content')
    <main id="main" class="tool-shell" data-tool="motivation" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <h1>{{ $tools['motivation']['title'] }}</h1>
            <p>{{ $tools['motivation']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="motivationForm">
                <label>
                    <span>{{ $tools['motivation']['fields']['task'] }}</span>
                    <input id="motivationTask" type="text" placeholder="{{ $tools['motivation']['placeholders']['task'] }}">
                </label>
                <label>
                    <span>{{ $tools['motivation']['fields']['mood'] }}</span>
                    <input id="motivationMood" type="text" placeholder="{{ $tools['motivation']['placeholders']['mood'] }}">
                </label>
                <label>
                    <span>{{ $tools['motivation']['fields']['reward'] }}</span>
                    <select id="motivationReward">
                        @foreach ($tools['motivation']['reward_types'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['motivation']['fields']['stimulation'] }}</span>
                    <select id="motivationStimulation">
                        @foreach ($tools['motivation']['stimulation_types'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['motivation']['fields']['friction'] }}</span>
                    <input id="motivationFriction" type="text" placeholder="{{ $tools['motivation']['placeholders']['friction'] }}">
                </label>
                <label>
                    <span>{{ $tools['motivation']['fields']['cost'] }}</span>
                    <input id="motivationCost" type="text" placeholder="{{ $tools['motivation']['placeholders']['cost'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['motivation']['buttons']['build'] }}</button>
                </div>
                <details class="tool-extra-actions">
                    <summary>{{ $tools['shared']['extra_actions'] }}</summary>
                    <div>
                            <button type="button" class="secondary-button" id="motivationPrintButton">{{ $tools['shared']['print'] }}</button>
                            <button type="button" class="secondary-button" id="motivationDownloadButton">{{ $tools['shared']['download'] }}</button>
                            <button type="button" class="secondary-button" id="motivationClearButton">{{ $tools['shared']['clear'] }}</button>
                    </div>
                </details>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['motivation']['output_eyebrow'] }}</p>
                <h2 id="motivationOutputTitle">{{ $tools['motivation']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['motivation']['sections']['starter'] }}</h3>
                        <p id="motivationStarterText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['motivation']['sections']['menu'] }}</h3>
                        <ol id="motivationMenuList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['motivation']['sections']['pairing'] }}</h3>
                        <p id="motivationPairingText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['motivation']['sections']['friction'] }}</h3>
                        <p id="motivationFrictionText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['motivation']['sections']['rules'] }}</h3>
                        <ul id="motivationRulesList"></ul>
                    </article>
                </div>
            </section>
        </section>
    </main>
@endsection
