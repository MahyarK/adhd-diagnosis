@extends('layouts.app')

@section('title', __('assessment.ui.transition_rescue'))

@section('content')
    <main id="main" class="tool-shell" data-tool="transition" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <h1>{{ $tools['transition']['title'] }}</h1>
            <p>{{ $tools['transition']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="transitionForm">
                <label>
                    <span>{{ $tools['transition']['fields']['transition'] }}</span>
                    <input id="transitionName" type="text" placeholder="{{ $tools['transition']['placeholders']['transition'] }}">
                </label>
                <label>
                    <span>{{ $tools['transition']['fields']['mode'] }}</span>
                    <select id="transitionMode">
                        @foreach ($tools['transition']['modes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['transition']['fields']['time'] }}</span>
                    <input id="transitionTime" type="text" placeholder="{{ $tools['transition']['placeholders']['time'] }}">
                </label>
                <label>
                    <span>{{ $tools['transition']['fields']['anchor'] }}</span>
                    <input id="transitionAnchor" type="text" placeholder="{{ $tools['transition']['placeholders']['anchor'] }}">
                </label>
                <label>
                    <span>{{ $tools['transition']['fields']['blocker'] }}</span>
                    <input id="transitionBlocker" type="text" placeholder="{{ $tools['transition']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['transition']['fields']['support'] }}</span>
                    <input id="transitionSupport" type="text" placeholder="{{ $tools['transition']['placeholders']['support'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['transition']['buttons']['build'] }}</button>
                </div>
                <details class="tool-extra-actions">
                    <summary>{{ $tools['shared']['extra_actions'] }}</summary>
                    <div>
                            <button type="button" class="secondary-button" id="transitionPrintButton">{{ $tools['shared']['print'] }}</button>
                            <button type="button" class="secondary-button" id="transitionDownloadButton">{{ $tools['shared']['download'] }}</button>
                            <button type="button" class="secondary-button" id="transitionClearButton">{{ $tools['shared']['clear'] }}</button>
                    </div>
                </details>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['transition']['output_eyebrow'] }}</p>
                <h2 id="transitionOutputTitle">{{ $tools['transition']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['transition']['sections']['first'] }}</h3>
                        <p id="transitionFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['transition']['sections']['steps'] }}</h3>
                        <ol id="transitionStepsList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['transition']['sections']['parking'] }}</h3>
                        <ol id="transitionParkingList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['transition']['sections']['anchor'] }}</h3>
                        <p id="transitionAnchorText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['transition']['sections']['script'] }}</h3>
                        <p id="transitionScriptText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['transition']['sections']['stop'] }}</h3>
                        <p id="transitionStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
@endsection
