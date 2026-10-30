@extends('layouts.app')

@section('title', $tools['toolkit']['all_tools'])

@section('content')
    @php $kit = $tools['toolkit']; @endphp

    <main id="main" class="page toolkit-page">
        <section class="page-hero">
            <h1>{{ $kit['title'] }}</h1>
            <p class="lede">{{ $kit['intro'] }}</p>

            <div class="toolkit-search">
                <label for="toolSearch" class="sr-only">{{ $kit['search_label'] }}</label>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
                <input id="toolSearch" type="search" placeholder="{{ $kit['search_placeholder'] }}" autocomplete="off" aria-controls="toolkitGroups">
            </div>

            <nav class="category-jump" aria-label="{{ $kit['all_tools'] }}">
                @foreach ($groups as $group)
                    <a href="#cat-{{ $group['key'] }}"><span class="pebble pebble-{{ $group['key'] }}" aria-hidden="true"></span>{{ $group['label'] }}</a>
                @endforeach
            </nav>
        </section>

        <div class="toolkit-groups" id="toolkitGroups">
            @foreach ($groups as $group)
                <section class="toolkit-group reveal" id="cat-{{ $group['key'] }}" aria-labelledby="cat-{{ $group['key'] }}-title" data-toolkit-group>
                    <header>
                        <h2 id="cat-{{ $group['key'] }}-title"><span class="pebble pebble-{{ $group['key'] }}" aria-hidden="true"></span>{{ $group['label'] }}</h2>
                        <p>{{ $group['body'] }}</p>
                        <span class="toolkit-count">{{ str_replace(':count', count($group['tools']), $kit['count']) }}</span>
                    </header>
                    <ul class="tool-tiles">
                        @foreach ($group['tools'] as $tool)
                            <li data-search="{{ mb_strtolower($tool['title'].' '.$tool['body']) }}">
                                <a class="tool-tile" href="{{ $tool['href'] }}" data-tool-key="{{ $tool['key'] }}">
                                    <strong>{{ $tool['title'] }}</strong>
                                    <span>{{ $tool['body'] }}</span>
                                    <em class="saved-badge" hidden>{{ $kit['saved'] }}</em>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>

        <p class="toolkit-empty" id="toolSearchEmpty" role="status" hidden>
            {{ $kit['no_results'] }}
            <a class="text-link" href="{{ route('tools.navigator', ['lang' => $locale]) }}">{{ $kit['unsure_action'] }}</a>
        </p>
    </main>
@endsection
