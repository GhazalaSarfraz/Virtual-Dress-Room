@extends('layouts.app')

@section('title', ($setting->site_name ?? 'Virtual Dress Room') . ' | Forgot Password')

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
                    <h2 class="h2 font-editorial mt-2 mb-3">Forgot Password</h2>
                    <p class="text-muted fs-7">
                        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
                    </p>
                </header>

                @if (session('status'))
                    <div class="alert alert-success fs-7 mb-4 rounded-0 border-0" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger fs-7 mb-4 rounded-0 border-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="text-uppercase-tracking text-muted mb-2 d-block">Email Address</label>
                        <input id="email" class="form-control form-control-luxury" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com" />
                        <div class="invalid-feedback fs-8">Please enter a valid email address.</div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <a class="text-muted text-decoration-none hover-dark fs-8 text-uppercase-tracking" href="{{ route('login') }}">
                            <i class="bi bi-arrow-left me-1"></i> Back to Login
                        </a>

                        <button class="btn btn-luxury" type="submit">
                            Send Reset Link
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
</script>
@endsection
