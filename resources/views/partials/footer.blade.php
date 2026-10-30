@php
    $footer = __('assessment.tools.toolkit.footer');
@endphp

<footer class="site-footer">
    @unless (request()->routeIs('tools.navigator', 'assessment.*', 'login', 'register'))
        <section class="footer-cta" aria-labelledby="footerCtaTitle">
            <h2 id="footerCtaTitle">{{ $footer['cta_title'] }}</h2>
            <p>{{ $footer['cta_body'] }}</p>
            <a class="primary-link" href="{{ route('tools.navigator', ['lang' => $locale]) }}">{{ __('assessment.tools.toolkit.unsure_action') }}</a>
        </section>
    @endunless

    <div class="footer-base">
        <p>{{ $footer['note'] }}</p>
        <nav aria-label="{{ __('assessment.tools.toolkit.nav.main') }}">
            <a href="{{ route('dashboard', ['lang' => $locale]) }}">{{ __('assessment.ui.dashboard') }}</a>
            <a href="{{ route('assessment.show', ['lang' => $locale]) }}">{{ __('assessment.tools.toolkit.nav.screening') }}</a>
            <a href="{{ route('toolkit', ['lang' => $locale]) }}">{{ __('assessment.ui.tools') }}</a>
            <a href="{{ route('resources', ['lang' => $locale]) }}">{{ __('assessment.ui.resource_library') }}</a>
        </nav>
    </div>
</footer>
