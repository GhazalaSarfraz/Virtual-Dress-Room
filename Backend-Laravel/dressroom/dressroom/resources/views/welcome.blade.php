@extends('layouts.app')

@section('title', ($setting->site_name ?? 'Virtual Dress Room') . ' | Virtual Try-on Room')

@section('styles')
<style>
    .hero-section {
        min-height: 80vh;
        display: flex;
        align-items: center;
        background-color: var(--bg-surface);
        position: relative;
        overflow: hidden;
    }
    
    .editorial-img-container {
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }
    
    .editorial-img-container img {
        transition: transform 6s cubic-bezier(0.16, 1, 0.3, 1);
        width: 100%;
        height: auto;
        max-height: 600px;
        object-fit: cover;
    }
    
    .editorial-img-container:hover img {
        transform: scale(1.08);
    }
    
    .accent-bar {
        height: 1.5px;
        width: 80px;
        background-color: var(--brand-accent);
        margin: 2rem 0;
    }
    
    .feature-card {
        border: none;
        background: transparent;
        padding: 2rem 1.5rem;
        transition: var(--transition-smooth);
    }
    
    .feature-card .bi {
        color: var(--brand-accent);
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left content column -->
            <div class="col-lg-6" data-aos="fade-up">
                <p class="text-uppercase-tracking text-muted mb-2">
                    {{ $setting->editorial_small_text ?? 'AI-DRIVEN COUTURE' }}
                </p>
                <h1 class="display-3 mb-4 text-dark font-editorial leading-tight">
                    {{ $setting->welcome_title ?? 'The future of fitting rooms.' }}
                </h1>
                <div class="accent-bar"></div>
                <p class="lead text-muted mb-5 fs-6 pe-lg-4">
                    {{ $setting->welcome_description ?? 'Browse products and try them on virtually using our advanced AI technology. Instantly dress yourself and customize couture selections.' }}
                </p>
                <div class="d-flex flex-wrap gap-3">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ url('/admin/dashboard') }}" class="btn btn-luxury">
                                Control Console
                            </a>
                        @else
                            <a href="{{ url('/user/dashboard') }}" class="btn btn-luxury">
                                Explore Boutique
                            </a>
                            <a href="{{ url('/user/fitting-room') }}" class="btn btn-luxury-outline">
                                Virtual Try-On
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-luxury">
                            Enter Fitting Suite
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-luxury-outline">
                            Create Wardrobe
                        </a>
                    @endauth
                </div>
            </div>
            
            <!-- Right image column -->
            <div class="col-lg-6">
                <div class="editorial-img-container">
                    <img src="{{ $setting->editorial_image ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBBSSEpiXwE_TWupkDKt4WLQdDffVq7Vivn6pVSV6xY5aohPmYc-2zoYfzGrH-T1w1L0LKOrAF19XJD33vOnAl4jzxPM7V1hGRJRJsVuWHtad_OAFHBJuhJS2eMH7VWLc_AHyRVvG8BV1Rq1Vi94jQZCTwfGStMvzsmR8XrDVlw_ka6NJ6wDoaC4cNS72n_MPk1GsJ17h6vzcgEfVHv99_nCbeRzizc1iCxBgnRPIyAGwGwVa_RC1i0KPrLB1t0AlawXeHFMda840oL' }}" 
                         alt="{{ $setting->editorial_heading ?? 'Virtual Dress Room Editorial' }}" 
                         class="img-fluid">
                    <div class="position-absolute bottom-0 start-0 p-4 w-100 bg-gradient-dark text-white d-lg-none" style="background: linear-gradient(transparent, rgba(0,0,0,0.75))">
                        <h4 class="font-editorial mb-1">{{ $setting->editorial_heading ?? 'The future of fitting rooms.' }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 border-top border-bottom border-light" style="background-color: #E8DCCB;">
    <div class="container py-4">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="text-uppercase-tracking text-muted">Virtual Dress Room Suite Features</span>
            <h2 class="display-5 font-editorial mt-2">Revolutionizing boutique retail</h2>
        </div>
        
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-magic"></i>
                    <h4 class="font-editorial mb-3">Virtual Try-On</h4>
                    <p class="text-muted fs-7">Upload your photograph and watch garments digitally adjust to your size and fit instantly.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-bag-heart"></i>
                    <h4 class="font-editorial mb-3">Virtual Wardrobe</h4>
                    <p class="text-muted fs-7">Curate your personal collection. Save items to wishlist and manage your couture selections.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-sliders2-vertical"></i>
                    <h4 class="font-editorial mb-3">Custom Prompt Fit</h4>
                    <p class="text-muted fs-7">Describe how you want the dress to look. Tailor color accents and scene backgrounds in real-time.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to action Section -->
<section class="py-5" style="background-color: #E8DCCB;">
    <div class="container text-center py-5">
        <h2 class="display-4 font-editorial mb-3">Dress without boundaries.</h2>
        <p class="text-muted fs-6 mb-5 mx-auto" style="max-width: 600px;">Unlock the ultimate AI shopping companion. Experience real-time high-fidelity clothing rendering right in your browser.</p>
        <div>
            @auth
                <a href="{{ url('/user/dashboard') }}" class="btn btn-luxury">Enter Boutique</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-luxury px-5">Join Virtual Dress Room Wardrobe</a>
            @endauth
        </div>
    </div>
</section>
@endsection
