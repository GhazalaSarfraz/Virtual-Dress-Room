@extends('layouts.app')

@section('title', ($setting->site_name ?? 'Virtual Dress Room') . ' | Reset Password')

@section('styles')
<style>
    body {
        background-color: var(--bg-surface);
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
</style>
@endsection

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="bg-white p-5 border shadow-sm">
                <header class="mb-4 text-center">
                    <h2 class="h2 font-editorial mt-2 mb-3">Reset Password</h2>
                    <p class="text-muted fs-7">
                        Please enter your email and a new password below.
                    </p>
                </header>

                @if ($errors->any())
                    <div class="alert alert-danger fs-7 mb-4 rounded-0 border-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="needs-validation" novalidate>
                    @csrf
                    
                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="text-uppercase-tracking text-muted mb-2 d-block">Email Address</label>
                        <input id="email" class="form-control form-control-luxury" type="email" name="email" value="{{ old('email', $email) }}" required autofocus placeholder="name@example.com" />
                        <div class="invalid-feedback fs-8">Please enter a valid email address.</div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="text-uppercase-tracking text-muted mb-2 d-block">New Password</label>
                        <div class="position-relative">
                            <input id="password" class="form-control form-control-luxury" type="password" name="password" required />
                            <i class="bi bi-eye position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d;" onclick="togglePassword('password', this)"></i>
                        </div>
                        <div class="invalid-feedback fs-8">Please enter a new password (min 8 characters).</div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="text-uppercase-tracking text-muted mb-2 d-block">Confirm Password</label>
                        <div class="position-relative">
                            <input id="password_confirmation" class="form-control form-control-luxury" type="password" name="password_confirmation" required />
                            <i class="bi bi-eye position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d;" onclick="togglePassword('password_confirmation', this)"></i>
                        </div>
                        <div class="invalid-feedback fs-8">Please confirm your new password.</div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end mt-4">
                        <button class="btn btn-luxury w-100" type="submit">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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

    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection
