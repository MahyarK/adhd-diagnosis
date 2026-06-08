<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.food_rescue') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="food" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['food']['eyebrow'] }}</p>
            <h1>{{ $tools['food']['title'] }}</h1>
            <p>{{ $tools['food']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="foodForm">
                <label>
                    <span>{{ $tools['food']['fields']['energy'] }}</span>
                    <select id="foodEnergy">
                        @foreach ($tools['food']['energies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['food']['fields']['appetite'] }}</span>
                    <select id="foodAppetite">
                        @foreach ($tools['food']['appetites'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['food']['fields']['kitchen'] }}</span>
                    <select id="foodKitchen">
                        @foreach ($tools['food']['kitchens'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['food']['fields']['budget'] }}</span>
                    <select id="foodBudget">
                        @foreach ($tools['food']['budgets'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['food']['fields']['available'] }}</span>
                    <input id="foodAvailable" type="text" placeholder="{{ $tools['food']['placeholders']['available'] }}">
                </label>
                <label>
                    <span>{{ $tools['food']['fields']['next_thing'] }}</span>
                    <input id="foodNextThing" type="text" placeholder="{{ $tools['food']['placeholders']['next_thing'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['food']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="foodPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="foodDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="foodClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['food']['output_eyebrow'] }}</p>
                <h2 id="foodOutputTitle">{{ $tools['food']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['food']['sections']['first'] }}</h3>
                        <p id="foodFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['food']['sections']['options'] }}</h3>
                        <ol id="foodOptionsList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['food']['sections']['shopping'] }}</h3>
                        <ul id="foodShoppingList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['food']['sections']['next'] }}</h3>
                        <p id="foodNextText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['food']['sections']['rules'] }}</h3>
                        <ul id="foodRulesList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['food']['sections']['stop'] }}</h3>
                        <p id="foodStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
