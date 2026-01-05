@extends('layouts.app')

@section('title', 'Sign Up - Chinese Learning App')

@section('content')
<div class="container">
    <div class="auth-card">
        <!-- Left Side - Desktop Only -->
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="chinese-big">注册</div>
                <h1>Join Us Today!</h1>
                <p>Start your Chinese learning adventure with thousands of students worldwide</p>
                
                <div class="auth-left-features">
                    <div class="auth-left-feature">
                        <div class="icon">🎓</div>
                        <div class="text">
                            <h3>Structured Learning</h3>
                            <p>Follow HSK-aligned curriculum</p>
                        </div>
                    </div>
                    <div class="auth-left-feature">
                        <div class="icon">🌟</div>
                        <div class="text">
                            <h3>Gamified Experience</h3>
                            <p>Learn through fun and engaging activities</p>
                        </div>
                    </div>
                    <div class="auth-left-feature">
                        <div class="icon">🏆</div>
                        <div class="text">
                            <h3>Achievements</h3>
                            <p>Earn badges and unlock rewards</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-right">
            <div class="auth-right-content">
                <div class="auth-header">
                    <div class="chinese-decoration">注册</div>
                    <h1>🇨🇳 Chinese Learning</h1>
                    <p>Create an account to start learning Chinese</p>
                </div>

                <div id="alert" class="alert"></div>

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <form id="registerForm">
                    @csrf
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-control" 
                            placeholder="Enter your full name"
                            value="{{ old('name') }}"
                            required
                        >
                        <div id="name-error" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            required
                        >
                        <div id="email-error" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Enter password (minimum 8 characters)"
                            required
                        >
                        <div id="password-error" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            class="form-control" 
                            placeholder="Re-enter password"
                            required
                        >
                        <div id="password_confirmation-error" class="error-message"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">✨</span>
                        Create Account
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Already have an account? <a href="{{ route('client.login') }}">Sign in now</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Check if already logged in
    auth.requireGuest();
    
    // Initialize register form
    auth.handleRegisterForm();
</script>
@endpush
