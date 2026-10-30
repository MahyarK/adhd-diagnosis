@extends('layouts.app')

@section('title', __('assessment.ui.home_reset'))

@section('content')
    <main id="main" class="tool-shell" data-tool="home" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <h1>{{ $tools['home']['title'] }}</h1>
            <p>{{ $tools['home']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="homeForm">
                <label>
                    <span>{{ $tools['home']['fields']['space'] }}</span>
                    <input id="homeSpace" type="text" placeholder="{{ $tools['home']['placeholders']['space'] }}">
                </label>
                <label>
                    <span>{{ $tools['home']['fields']['mode'] }}</span>
                    <select id="homeMode">
                        @foreach ($tools['home']['modes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['home']['fields']['time'] }}</span>
                    <select id="homeTime">
                        @foreach ($tools['home']['times'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['home']['fields']['blocker'] }}</span>
                    <input id="homeBlocker" type="text" placeholder="{{ $tools['home']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['home']['fields']['reward'] }}</span>
                    <input id="homeReward" type="text" placeholder="{{ $tools['home']['placeholders']['reward'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['home']['buttons']['build'] }}</button>
                </div>
                <details class="tool-extra-actions">
                    <summary>{{ $tools['shared']['extra_actions'] }}</summary>
                    <div>
                            <button type="button" class="secondary-button" id="homePrintButton">{{ $tools['shared']['print'] }}</button>
                            <button type="button" class="secondary-button" id="homeDownloadButton">{{ $tools['shared']['download'] }}</button>
                            <button type="button" class="secondary-button" id="homeClearButton">{{ $tools['shared']['clear'] }}</button>
                    </div>
                </details>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['home']['output_eyebrow'] }}</p>
                <h2 id="homeOutputTitle">{{ $tools['home']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['home']['sections']['start'] }}</h3>
                        <p id="homeStartText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['home']['sections']['steps'] }}</h3>
                        <ol id="homeStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['home']['sections']['parking'] }}</h3>
                        <ul id="homeParkingList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['home']['sections']['stop'] }}</h3>
                        <p id="homeStopText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['home']['sections']['kind'] }}</h3>
                        <p id="homeKindText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
@endsection
