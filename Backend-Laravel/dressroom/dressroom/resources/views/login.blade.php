@extends('layouts.app')

@section('title', ($setting->site_name ?? 'Virtual Dress Room') . ' | Login')

@section('styles')
<style>
    body {
        background-color: var(--bg-surface);
    }
    
    .login-container {
        min-height: calc(100vh - 80px);
        overflow: hidden;
    }
    
    .login-editorial {
        position: relative;
        overflow: hidden;
        background-color: #e5e2e1;
        min-height: 400px;
    }
    
    .login-editorial img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        inset: 0;
        filter: grayscale(10%);
        transition: transform 10s ease;
    }
    
    .login-editorial:hover img {
        transform: scale(1.06);
    }
    
    .editorial-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, transparent, rgba(253, 248, 248, 0.15));
        z-index: 1;
    }
    
    .editorial-content {
        position: absolute;
        bottom: 10%;
        left: 8%;
        z-index: 2;
        max-width: 80%;
    }
    
    .role-segmented-control {
        background-color: rgba(0, 0, 0, 0.04);
        padding: 4px;
        border-radius: 4px;
    }
    
    .role-btn {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.6rem;
        font-family: var(--font-body);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 600;
        border-radius: 3px;
        transition: var(--transition-smooth);
        color: #555;
    }
    
    .role-btn.active {
        background-color: #ffffff;
        color: #000000;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    
    .form-control-luxury {
        background-color: #ffffff;
        border: 1.5px solid rgba(0, 0, 0, 0.08);
        border-radius: 0;
        padding: 1rem 1.2rem;
        font-family: var(--font-body);
        font-size: 0.85rem;
        transition: var(--transition-smooth);
    }
    
    .form-control-luxury:focus {
        border-color: #000000;
        box-shadow: none;
        background-color: #ffffff;
    }
    
    .eye-toggle-btn {
        border: none;
        background: transparent;
        color: #888;
        padding: 0 1rem;
        height: 100%;
        transition: var(--transition-smooth);
    }
    
    .eye-toggle-btn:hover {
        color: #000;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 login-container">
        
        <!-- Left Side: Editorial Column (Desktop Only) -->
        <div class="col-lg-6 d-none d-lg-block login-editorial position-relative">
            <img src="{{ $setting->editorial_image ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBBSSEpiXwE_TWupkDKt4WLQdDffVq7Vivn6pVSV6xY5aohPmYc-2zoYfzGrH-T1w1L0LKOrAF19XJD33vOnAl4jzxPM7V1hGRJRJsVuWHtad_OAFHBJuhJS2eMH7VWLc_AHyRVvG8BV1Rq1Vi94jQZCTwfGStMvzsmR8XrDVlw_ka6NJ6wDoaC4cNS72n_MPk1GsJ17h6vzcgEfVHv99_nCbeRzizc1iCxBgnRPIyAGwGwVa_RC1i0KPrLB1t0AlawXeHFMda840oL' }}" 
                 alt="Virtual Dress Room couture fashion editorial">
            <div class="editorial-gradient"></div>
            <div class="editorial-content">
                <p class="text-uppercase-tracking text-white-50 mb-2">{{ $setting->editorial_small_text ?? 'AI-DRIVEN COUTURE' }}</p>
                <h2 class="display-4 text-white font-editorial mb-4 leading-tight">
                    {{ $setting->editorial_heading ?? 'The future of fitting rooms.' }}
                </h2>
                <div class="accent-bar bg-white"></div>
            </div>
        </div>
        
        <!-- Right Side: Login Form Column -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center py-5 px-4 px-sm-5 bg-white">
            <div class="w-100" style="max-width: 420px;">
                
                <header class="mb-5">
                    <span class="text-uppercase-tracking text-muted">{{ $setting->site_name ?? 'Virtual Dress Room' }} Wardrobe</span>
                    <h2 class="h1 font-editorial mt-2 mb-2">Welcome Back</h2>
                    <p class="text-muted fs-7">Sign in to your private fitting room and collections suite.</p>
                </header>
                
                <!-- Display Backend Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-0 fs-7 mb-4 py-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <form action="{{ route('login') }}" method="POST" class="needs-validation" novalidate id="loginForm">
                    @csrf
                    
                    <!-- Hidden field to hold selected role -->
                    <input type="hidden" name="role" id="roleInput" value="user">
                    
                    @if(request()->has('redirect'))
                        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                    @endif
                    
                    <!-- Role Segmented Control -->
                    <div class="mb-4">
                        <label class="text-uppercase-tracking text-muted mb-2 d-block">Account Type</label>
                        <div class="d-flex role-segmented-control">
                            <button type="button" class="role-btn active" id="roleUserBtn" onclick="setRole('user')">User</button>
                            <button type="button" class="role-btn" id="roleAdminBtn" onclick="setRole('admin')">Admin</button>
                        </div>
                    </div>
                    
                    <!-- Email field -->
                    <div class="mb-4">
                        <label for="email" class="text-uppercase-tracking text-muted mb-2 d-block">Email Address</label>
                        <input type="email" name="email" id="email" 
                               class="form-control form-control-luxury" 
                               placeholder="name@example.com" 
                               value="{{ old('email') }}"
                               required autocomplete="email" autofocus>
                        <div class="invalid-feedback fs-8">Please enter a valid email address.</div>
                    </div>
                    
                    <!-- Password field -->
                    <div class="mb-5">
                        <div class="d-flex justify-content-between mb-2">
                            <label for="password" class="text-uppercase-tracking text-muted">Password</label>
                            <a href="{{ url('/forgot-password') }}" class="text-muted fs-8 text-decoration-none hover-dark">Forgot?</a>
                        </div>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" 
                                   class="form-control form-control-luxury pe-5" 
                                   placeholder="••••••••" 
                                   required autocomplete="current-password">
                            <button type="button" class="eye-toggle-btn position-absolute top-50 end-0 translate-middle-y" onclick="togglePasswordVisibility()">
                                <i class="bi bi-eye fs-5" id="passwordEyeIcon"></i>
                            </button>
                            <div class="invalid-feedback fs-8 mt-1">Please enter your password.</div>
                        </div>
                    </div>
                    
                    <!-- Submit button -->
                    <div class="mb-4">
                        <button type="submit" class="btn btn-luxury w-100 py-3">
                            <span class="position-relative z-10">Sign In</span>
                        </button>
                    </div>
                </form>
                
                <footer class="text-center pt-4 border-top mt-5">
                    <p class="text-muted fs-7 mb-0">
                        New to {{ $setting->site_name ?? 'Virtual Dress Room' }}? 
                        <a href="{{ route('register') }}" class="text-dark fw-bold text-decoration-none border-bottom border-dark border-2 pb-1 ms-2">Create Account</a>
                    </p>
                </footer>
                
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Segmented role picker
    function setRole(role) {
        document.getElementById('roleInput').value = role;
        
        const userBtn = document.getElementById('roleUserBtn');
        const adminBtn = document.getElementById('roleAdminBtn');
        
        if (role === 'user') {
            userBtn.classList.add('active');
            adminBtn.classList.remove('active');
        } else {
            adminBtn.classList.add('active');
            userBtn.classList.remove('active');
        }
    }

    // Toggle Password Visibility
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('passwordEyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    }

    // Bootstrap Form Validation Client-side
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
@endsection