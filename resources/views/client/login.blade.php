@extends('layouts.app')

@section('title', 'Sign In - Chinese Learning App')

@section('content')
<div class="container">
    <div class="auth-card">
        <!-- Left Side - Desktop Only -->
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="chinese-big">登录</div>
                <h1>Welcome Back!</h1>
                <p>Continue your journey to master Chinese language with personalized learning experience</p>
                
                <div class="auth-left-features">
                    <div class="auth-left-feature">
                        <div class="icon">📊</div>
                        <div class="text">
                            <h3>Track Progress</h3>
                            <p>Monitor your learning achievements</p>
                        </div>
                    </div>
                    <div class="auth-left-feature">
                        <div class="icon">💬</div>
                        <div class="text">
                            <h3>AI Chatbot</h3>
                            <p>Practice conversation anytime</p>
                        </div>
                    </div>
                    <div class="auth-left-feature">
                        <div class="icon">⭐</div>
                        <div class="text">
                            <h3>Daily Streaks</h3>
                            <p>Build consistency and earn rewards</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-right">
            <div class="auth-right-content">
                <div class="auth-header">
                    <div class="chinese-decoration">登录</div>
                    <h1>🇨🇳 Chinese Learning</h1>
                    <p>Sign in to continue your learning journey</p>
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

                <form id="loginForm">
                    @csrf
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
                            placeholder="Enter your password"
                            required
                        >
                        <div id="password-error" class="error-message"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span class="btn-icon">🔐</span>
                        Sign In
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="{{ route('client.register') }}">Sign up now</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize login form
    auth.handleLoginForm();
</script>
@endpush
