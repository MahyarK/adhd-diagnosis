@php
    $navItems = [
        ['route' => 'dashboard', 'label' => __('assessment.ui.dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'M12 3v2m0 14v2M5.6 5.6 7 7m10 10 1.4 1.4M3 12h2m14 0h2M5.6 18.4 7 17m10-10 1.4-1.4M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z'],
        ['route' => 'assessment.show', 'label' => __('assessment.tools.toolkit.nav.screening'), 'active' => request()->routeIs('assessment.*'), 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4'],
        ['route' => 'toolkit', 'label' => __('assessment.ui.tools'), 'active' => request()->routeIs('toolkit', 'tools.*'), 'icon' => 'M4 5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Zm10 0a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V5ZM4 15a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4Zm10 0a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-4Z'],
        ['route' => 'resources', 'label' => __('assessment.ui.resource_library'), 'active' => request()->routeIs('resources'), 'icon' => 'M12 6.5C10.5 5 8.3 4.5 4 4.5v13c4.3 0 6.5.5 8 2 1.5-1.5 3.7-2 8-2v-13c-4.3 0-6.5.5-8 2Zm0 0v13'],
    ];
@endphp

<header class="site-header">
    <div class="nav-pill">
        <a class="brand" href="{{ route('dashboard', ['lang' => $locale]) }}">
            <span class="brand-mark" aria-hidden="true"></span>
            <span class="brand-name">{{ __('assessment.meta.title') }}</span>
        </a>

        <nav class="nav-links" aria-label="{{ __('assessment.tools.toolkit.nav.main') }}">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route'], ['lang' => $locale]) }}" @if ($item['active']) aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $item['icon'] }}"/></svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="nav-tools">
            <details class="nav-menu lang-dropdown">
                <summary aria-label="{{ __('assessment.ui.language') }}: {{ $locales[$locale] }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3Z"/></svg>
                    <span>{{ strtoupper($locale) }}</span>
                </summary>
                <div class="menu-panel">
                    @foreach ($locales as $code => $label)
                        <a href="{{ request()->url() }}?lang={{ $code }}" lang="{{ $code }}" hreflang="{{ $code }}" @if ($locale === $code) aria-current="true" @endif>{{ $label }}</a>
                    @endforeach
                </div>
            </details>

            @auth
                <details class="nav-menu account-nav">
                    <summary aria-label="{{ $ui['account'] ?? __('assessment.ui.account') }}">
                        <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    </summary>
                    <div class="menu-panel">
                        <p class="menu-name">{{ auth()->user()->name }}</p>
                        <a href="{{ route('dashboard', ['lang' => $locale]) }}#yourSpace">{{ __('assessment.ui.usage_dashboard') }}</a>
                        <form method="POST" action="{{ route('logout', ['lang' => $locale]) }}">
                            @csrf
                            <button type="submit">{{ __('assessment.ui.sign_out') }}</button>
                        </form>
                    </div>
                </details>
            @else
                <a class="nav-signin" href="{{ route('login', ['lang' => $locale]) }}" @if (request()->routeIs('login', 'register')) aria-current="page" @endif>{{ __('assessment.ui.sign_in') }}</a>
            @endauth
        </div>
    </div>
</header>
