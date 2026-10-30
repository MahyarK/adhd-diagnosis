@extends('layouts.app')

@section('title', $mode === 'register' ? $ui['create_account'] : $ui['sign_in'])

@section('content')
    <main id="main" class="auth-shell">
        <section class="auth-panel">
            <p class="kicker">{{ $ui['account_eyebrow'] }}</p>
            <h1>{{ $mode === 'register' ? $ui['create_account_title'] : $ui['sign_in_title'] }}</h1>
            <p>{{ $mode === 'register' ? $ui['create_account_intro'] : $ui['sign_in_intro'] }}</p>

            <form method="POST" action="{{ $mode === 'register' ? route('register.store', ['lang' => $locale]) : route('login.authenticate', ['lang' => $locale]) }}" class="auth-form">
                @csrf

                @if ($mode === 'register')
                    <label>
                        <span>{{ $ui['name'] }}</span>
                        <input name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>
                        @error('name')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </label>
                @endif

                <label>
                    <span>{{ $ui['email'] }}</span>
                    <input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                    @error('email')
                        <strong>{{ $message }}</strong>
                    @enderror
                </label>

                <label>
                    <span>{{ $ui['password'] }}</span>
                    <input name="password" type="password" autocomplete="{{ $mode === 'register' ? 'new-password' : 'current-password' }}" required>
                    @error('password')
                        <strong>{{ $message }}</strong>
                    @enderror
                </label>

                @if ($mode === 'register')
                    <label>
                        <span>{{ $ui['confirm_password'] }}</span>
                        <input name="password_confirmation" type="password" autocomplete="new-password" required>
                    </label>
                @else
                    <label class="auth-check">
                        <input name="remember" type="checkbox" value="1">
                        <span>{{ $ui['remember_me'] }}</span>
                    </label>
                @endif

                <button type="submit" class="primary-button">{{ $mode === 'register' ? $ui['create_account'] : $ui['sign_in'] }}</button>
            </form>

            <p class="auth-switch">
                @if ($mode === 'register')
                    {{ $ui['already_have_account'] }}
                    <a href="{{ route('login', ['lang' => $locale]) }}">{{ $ui['sign_in'] }}</a>
                @else
                    {{ $ui['need_account'] }}
                    <a href="{{ route('register', ['lang' => $locale]) }}">{{ $ui['create_account'] }}</a>
                @endif
            </p>
        </section>
    </main>
@endsection
