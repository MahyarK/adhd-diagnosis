@php
    $toolContext = \App\Support\Toolkit::context(request()->route()?->getName(), $locale);
    $kit = __('assessment.tools.toolkit');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>@yield('title') · {{ __('assessment.meta.title') }}</title>
    <link rel="preconnect" href="https://api.fontshare.com" crossorigin>
    <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap">
    @if ($isRtl)
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap">
    @endif
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body @class(['has-tool-context' => $toolContext])>
    <a class="skip-link" href="#main">{{ $kit['nav']['skip'] }}</a>
    <div class="ambient" aria-hidden="true"></div>

    @include('partials.navbar')

    @if ($toolContext)
        <nav class="crumbs" aria-label="{{ __('assessment.ui.tools') }}">
            <a href="{{ route('toolkit', ['lang' => $locale]) }}">{{ $kit['all_tools'] }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('toolkit', ['lang' => $locale]) }}#cat-{{ $toolContext['category']['key'] }}" class="crumb-category">
                <span class="pebble pebble-{{ $toolContext['category']['key'] }}" aria-hidden="true"></span>
                {{ $toolContext['category']['label'] }}
            </a>
        </nav>
    @endif

    @yield('content')

    @if ($toolContext && $toolContext['related'])
        <aside class="related" aria-labelledby="relatedTitle">
            <h2 id="relatedTitle">{{ str_replace(':category', $toolContext['category']['label'], $kit['more_in']) }}</h2>
            <ul>
                @foreach (array_slice($toolContext['related'], 0, 4) as $tool)
                    <li>
                        <a href="{{ $tool['href'] }}">
                            <strong>{{ $tool['title'] }}</strong>
                            <span>{{ $tool['body'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <a class="text-link" href="{{ route('toolkit', ['lang' => $locale]) }}">{{ $kit['all_tools'] }}</a>
        </aside>
    @endif

    @include('partials.footer')
</body>
</html>
