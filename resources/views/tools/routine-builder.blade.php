@extends('layouts.app')

@section('title', __('assessment.ui.routine_builder'))

@section('content')
    <main id="main" class="tool-shell" data-tool="routine" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <h1>{{ $tools['routine']['title'] }}</h1>
            <p>{{ $tools['routine']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="routineForm">
                <label>
                    <span>{{ $tools['routine']['fields']['kind'] }}</span>
                    <select id="routineKind">
                        @foreach ($tools['routine']['kinds'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['routine']['fields']['anchor'] }}</span>
                    <input id="routineAnchor" type="text" placeholder="{{ $tools['routine']['placeholders']['anchor'] }}">
                </label>
                <label>
                    <span>{{ $tools['routine']['fields']['must'] }}</span>
                    <textarea id="routineMust" rows="3" placeholder="{{ $tools['routine']['placeholders']['must'] }}"></textarea>
                </label>
                <label>
                    <span>{{ $tools['routine']['fields']['friction'] }}</span>
                    <input id="routineFriction" type="text" placeholder="{{ $tools['routine']['placeholders']['friction'] }}">
                </label>
                <label>
                    <span>{{ $tools['routine']['fields']['fallback'] }}</span>
                    <input id="routineFallback" type="text" placeholder="{{ $tools['routine']['placeholders']['fallback'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['routine']['buttons']['build'] }}</button>
                </div>
                <details class="tool-extra-actions">
                    <summary>{{ $tools['shared']['extra_actions'] }}</summary>
                    <div>
                            <button type="button" class="secondary-button" id="routinePrintButton">{{ $tools['shared']['print'] }}</button>
                            <button type="button" class="secondary-button" id="routineDownloadButton">{{ $tools['shared']['download'] }}</button>
                            <button type="button" class="secondary-button" id="routineClearButton">{{ $tools['shared']['clear'] }}</button>
                    </div>
                </details>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['routine']['output_eyebrow'] }}</p>
                <h2 id="routineOutputTitle">{{ $tools['routine']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article>
                        <h3>{{ $tools['routine']['sections']['anchor'] }}</h3>
                        <p id="routineAnchorText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['routine']['sections']['steps'] }}</h3>
                        <ol id="routineStepList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['routine']['sections']['prep'] }}</h3>
                        <ul id="routinePrepList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['routine']['sections']['fallback'] }}</h3>
                        <p id="routineFallbackText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['routine']['sections']['reset'] }}</h3>
                        <p id="routineResetText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
@endsection
