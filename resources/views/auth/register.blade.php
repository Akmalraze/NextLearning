@extends('layouts.app')

@section('content')
<div class="modern-container">
    <div class="modern-card">
        <div class="modern-card-header">
            <a href="{{ url('/') }}" class="modern-logo" style="justify-content: center;">
                <div class="modern-logo-icon">NL</div>
                <span class="modern-logo-text">NextLearning</span>
            </a>
            <h1>Create Account</h1>
            <p>Join us and start your learning journey</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="modern-form-group">
                <label for="name" class="modern-form-label">Full Name</label>
                <input id="name" type="text" 
                    class="modern-form-input @error('name') error @enderror" 
                    name="name" value="{{ old('name') }}" 
                    required autocomplete="name" autofocus
                    placeholder="Enter your full name">
                @error('name')
                    <span class="modern-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="modern-form-group">
                <label for="email" class="modern-form-label">Email Address</label>
                <input id="email" type="email" 
                    class="modern-form-input @error('email') error @enderror" 
                    name="email" value="{{ old('email') }}" 
                    required autocomplete="email"
                    placeholder="Enter your email">
                @error('email')
                    <span class="modern-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="modern-form-group">
                <label for="role" class="modern-form-label">Role</label>
                <select id="role" name="role" class="modern-form-input @error('role') error @enderror" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select your role</option>
                    <option value="Learner" {{ old('role') === 'Learner' ? 'selected' : '' }}>Learner</option>
                    <option value="Educator" {{ old('role') === 'Educator' ? 'selected' : '' }}>Educator</option>
                </select>
                @error('role')
                    <span class="modern-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="modern-form-group">
                <label for="password" class="modern-form-label">Password</label>
                <input id="password" type="password" 
                    class="modern-form-input @error('password') error @enderror" 
                    name="password" 
                    required autocomplete="new-password"
                    placeholder="Create a password">
                @error('password')
                    <span class="modern-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="modern-form-group">
                <label for="password-confirm" class="modern-form-label">Confirm Password</label>
                <input id="password-confirm" type="password" 
                    class="modern-form-input" 
                    name="password_confirmation" 
                    required autocomplete="new-password"
                    placeholder="Confirm your password">
            </div>

            <button type="submit" class="modern-btn modern-btn-primary">
                Create Account
            </button>

            <div class="modern-divider">
                <span>Already have an account?</span>
            </div>

            @if (Route::has('login'))
            <a href="{{ route('login') }}" class="modern-btn modern-btn-secondary" style="text-align: center; display: block;">
                Sign In Instead
            </a>
            @endif
        </form>
    </div>
</div>
@endsection
