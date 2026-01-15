@extends('layouts.app')

@section('content')
<div class="modern-container">
    <div class="modern-card">
        <div class="modern-card-header">
            <a href="{{ url('/') }}" class="modern-logo" style="justify-content: center;">
                <div class="modern-logo-icon">NL</div>
                <span class="modern-logo-text">NextLearning</span>
            </a>
            <h1>Welcome Back</h1>
            <p>Sign in to your account to continue</p>
        </div>

        @if (session('error'))
        <div class="modern-alert modern-alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="modern-form-group">
                <label for="email" class="modern-form-label">Email Address</label>
                <input id="email" type="email" 
                    class="modern-form-input @error('email') error @enderror"
                    name="email" value="{{ old('email') }}" 
                    required autocomplete="email" autofocus
                    placeholder="Enter your email">
                @error('email')
                    <span class="modern-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="modern-form-group">
                <label for="password" class="modern-form-label">Password</label>
                <input id="password" type="password"
                    class="modern-form-input @error('password') error @enderror" 
                    name="password"
                    required autocomplete="current-password"
                    placeholder="Enter your password">
                @error('password')
                    <span class="modern-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="modern-checkbox">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Remember Me</label>
            </div>

            <button type="submit" class="modern-btn modern-btn-primary">
                Sign In
            </button>

            @if (Route::has('password.request'))
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="{{ route('password.request') }}" class="modern-link">
                    Forgot Your Password?
                </a>
            </div>
            @endif

            <div class="modern-divider">
                <span>Don't have an account?</span>
            </div>

            @if (Route::has('register'))
            <a href="{{ route('register') }}" class="modern-btn modern-btn-secondary" style="text-align: center; display: block;">
                Create Account
            </a>
            @endif
        </form>
    </div>
</div>
@endsection