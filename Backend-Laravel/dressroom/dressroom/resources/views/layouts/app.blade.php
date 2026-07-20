<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $setting->site_name ?? 'Virtual Dress Room')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Hanken+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #D4AF37; /* Soft Gold */
            --brand-accent: #D4AF37; /* Soft Gold */
            --brand-secondary: #2F2F2F; /* Dark Charcoal */
            --bg-surface: #FAF9F6; /* Soft Ivory */
            --text-paragraph: #555555; /* Dark Gray */
            --font-editorial: 'Playfair Display', serif;
            --font-body: 'Hanken Grotesk', sans-serif;
            --transition-smooth: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-surface);
            color: var(--text-paragraph);
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Luxurious Typography */
        h1, h2, h3, h4, h5, h6, .display-font {
            font-family: var(--font-editorial);
            font-weight: 600;
        }

        .text-uppercase-tracking {
            font-family: var(--font-body);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 600;
        }

        /* Custom Buttons */
        .btn-luxury {
            background-color: var(--brand-primary);
            color: #ffffff;
            border: 1px solid var(--brand-primary);
            border-radius: 0;
            font-family: var(--font-body);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 600;
            padding: 0.9rem 1.8rem;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .btn-luxury::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(226, 180, 154, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-luxury:hover::before {
            left: 100%;
        }

        .btn-luxury:hover {
            background-color: #222;
            color: #ffffff;
            border-color: #222;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-luxury-outline {
            background-color: transparent;
            color: var(--brand-primary);
            border: 1.5px solid var(--brand-primary);
            border-radius: 0;
            font-family: var(--font-body);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 600;
            padding: 0.9rem 1.8rem;
            transition: var(--transition-smooth);
        }

        .btn-luxury-outline:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
        }

        /* Glassmorphism Panel */
        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* Brand Navbar */
        .navbar-brand {
            font-family: var(--font-editorial);
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--brand-primary) !important;
        }

        .navbar-luxury {
            background-color: #FFFFFF;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #EAEAEA;
            padding: 0.8rem 0;
            transition: var(--transition-smooth);
        }

        .navbar-luxury .nav-link {
            font-family: var(--font-body);
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #444748;
            padding: 0.5rem 1rem !important;
            transition: var(--transition-smooth);
        }

        .navbar-luxury .nav-link:hover, 
        .navbar-luxury .nav-link.active {
            color: var(--brand-primary);
            font-weight: 600;
        }

        .cart-badge, .wishlist-badge {
            background-color: var(--brand-accent);
            color: #000;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.25em 0.5em;
            border-radius: 50%;
            position: absolute;
            top: -2px;
            right: -2px;
        }

        /* Floating AI Sparkles */
        .ai-shimmer {
            background: linear-gradient(90deg, transparent, rgba(226, 180, 154, 0.2), transparent);
            background-size: 200% 100%;
            animation: shimmer 3s infinite linear;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Interactive Drawer */
        .offcanvas {
            border-left: 1px solid rgba(0, 0, 0, 0.08);
            background-color: var(--bg-surface);
        }
        
        .offcanvas-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .drawer-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .drawer-item img {
            width: 70px;
            height: 90px;
            object-fit: cover;
            margin-right: 1rem;
            border-radius: 4px;
        }

        /* Toast Container */
        .toast-container {
            z-index: 1090;
        }

        .toast {
            border-radius: 0;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        /* Loader Overlay */
        #loadingOverlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(253, 248, 248, 0.85);
            backdrop-filter: blur(8px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        #loadingOverlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        /* Elegant Circular Spinner */
        .luxury-spinner {
            width: 60px;
            height: 60px;
            border: 2px solid rgba(0, 0, 0, 0.05);
            border-top: 2px solid var(--brand-accent);
            border-radius: 50%;
            animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            margin-bottom: 1.5rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Card Hover Effects */
        .hover-card {
            transition: var(--transition-smooth);
        }

        .hover-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06) !important;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-luxury sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}" style="text-decoration: none;">
                <img src="{{ asset('virtual_dress_room_logo.png') }}" alt="Logo" style="height: 48px; width: auto; object-fit: contain; border-radius: 4px; flex-shrink: 0;">
                <span style="font-family: var(--font-editorial); font-size: 1.25rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--brand-primary); line-height: 1.2;">{{ $setting->site_name ?? 'Virtual Dress Room' }}</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="bi bi-list fs-2 text-dark"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                    </li>
                    @if(!Auth::check() || Auth::user()->role === 'user')
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('user/dashboard') ? 'active' : '' }}" href="{{ url('/user/dashboard') }}">Boutique</a>
                        </li>
                    @endif
                    @auth
                        @if(Auth::user()->role === 'user')
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('user/fitting-room') ? 'active' : '' }}" href="{{ url('/user/fitting-room') }}">Virtual Try-On</a>
                            </li>
                        @elseif(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">Admin Dashboard</a>
                            </li>
                        @endif
                    @endauth
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="text-uppercase-tracking text-decoration-none text-dark hover-opacity me-3">Log In</a>
                        <a href="{{ route('register') }}" class="btn btn-dark rounded-0 px-4 py-2 text-uppercase-tracking fs-7">Register</a>
                    @else
                        @if(Auth::user()->role === 'user')
                            <!-- Wishlist Toggle -->
                            <button class="btn border-0 position-relative p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#wishlistDrawer" aria-controls="wishlistDrawer" onclick="loadWishlist()">
                                <i class="bi bi-heart fs-5" style="color: var(--brand-primary);"></i>
                                <span class="wishlist-badge" id="wishlistCount">0</span>
                            </button>
                            
                            <!-- Cart Toggle -->
                            <button class="btn border-0 position-relative p-2 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" aria-controls="cartDrawer" onclick="loadCart()">
                                <i class="bi bi-cart3 fs-5" style="color: var(--brand-primary);"></i>
                                <span class="cart-badge" id="cartCount">0</span>
                            </button>
                        @endif
                        
                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="btn border-0 dropdown-toggle text-uppercase-tracking fs-7 px-0" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person fs-5 me-1 align-middle"></i> {{ Auth::user()->username }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end rounded-0 border shadow-sm" aria-labelledby="userMenuButton">
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item py-2 fs-7" href="{{ url('/admin/dashboard') }}"><i class="bi bi-sliders me-2"></i>Dashboard</a></li>
                                @else
                                    <li><a class="dropdown-item py-2 fs-7" href="{{ url('/user/dashboard') }}"><i class="bi bi-grid me-2"></i>Boutique</a></li>
                                    <li><a class="dropdown-item py-2 fs-7" href="{{ url('/user/fitting-room') }}"><i class="bi bi-magic me-2"></i>Try-On Room</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 fs-7 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white py-5 mt-auto border-top border-secondary" style="background-color: #2F2F2F;">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-md-4">
                    <h3 class="navbar-brand text-white mb-3">{{ $setting->site_name ?? 'Virtual Dress Room' }}</h3>
                    <p class="text-white-50 fs-7 pe-md-4">{{ $setting->tagline ?? 'Virtual Try-on & Fitting Suite' }}</p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-uppercase-tracking text-white-50 mb-3 fs-8">Suite</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}" class="text-white-50 text-decoration-none fs-7 hover-white">Home</a></li>
                        @auth
                            @if(Auth::user()->role === 'user')
                                <li><a href="{{ url('/user/dashboard') }}" class="text-white-50 text-decoration-none fs-7 hover-white">Boutique</a></li>
                                <li><a href="{{ url('/user/fitting-room') }}" class="text-white-50 text-decoration-none fs-7 hover-white">Try-On Room</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-uppercase-tracking text-white-50 mb-3 fs-8">AI-Driven Couture</h6>
                    <p class="text-white-50 fs-7">Experience premium virtual try-ons. High precision clothing rendering leveraging advanced diffusion models.</p>
                    <div class="text-white-50 fs-8 pt-2">© {{ date('Y') }} {{ $setting->site_name ?? 'Virtual Dress Room' }}. All rights reserved.</div>
                </div>
            </div>
        </div>
    </footer>

    @auth
        @if(Auth::user()->role === 'user')
            <!-- Shopping Bag Drawer (Offcanvas Right) -->
            <div class="offcanvas offcanvas-end rounded-start-0 border-0" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">
                <div class="offcanvas-header bg-white">
                    <h5 class="offcanvas-title text-uppercase-tracking" id="cartDrawerLabel"><i class="bi bi-cart3 me-2" style="color: var(--brand-primary);"></i>My Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column justify-content-between p-4" id="cartDrawerBody">
                    <div id="cartItemsContainer" style="overflow-y: auto; max-height: calc(100vh - 280px);">
                        <!-- Cart Items dynamically populated by JS -->
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-dark mb-2" role="status"></div>
                            <p class="fs-7">Syncing cart items...</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-uppercase-tracking fs-8 text-muted">Items Subtotal</span>
                            <span class="fs-7 fw-semibold" id="cartSubtotalPrice">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-uppercase-tracking fs-8 text-muted">Delivery Fee</span>
                            <span class="fs-7 fw-semibold" id="cartDeliveryFee">$0.00</span>
                        </div>
                        <div class="border-top pt-2 mb-3 d-flex justify-content-between">
                            <span class="text-uppercase-tracking fs-7 fw-bold">Total</span>
                            <span class="fs-5 fw-bold" id="cartTotalPrice">$0.00</span>
                        </div>
                        <button class="btn btn-luxury w-100" id="proceedCheckoutBtn" onclick="openPaymentModal()"><i class="bi bi-shield-lock me-2"></i>Proceed to Checkout</button>
                    </div>
                </div>
            </div>

            <!-- Wishlist Drawer (Offcanvas Right) -->
            <div class="offcanvas offcanvas-end rounded-start-0 border-0" tabindex="-1" id="wishlistDrawer" aria-labelledby="wishlistDrawerLabel">
                <div class="offcanvas-header bg-white">
                    <h5 class="offcanvas-title text-uppercase-tracking" id="wishlistDrawerLabel"><i class="bi bi-heart me-2"></i>Wishlist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-4">
                    <div id="wishlistItemsContainer" style="overflow-y: auto; max-height: calc(100vh - 120px);">
                        <!-- Wishlist Items dynamically populated by JS -->
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-dark mb-2" role="status"></div>
                            <p class="fs-7">Syncing wishlist...</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- ===== PAYMENT MODAL ===== -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-0 border-0 shadow-lg">
                <!-- Modal Header -->
                <div class="modal-header border-0 pb-0 px-4 pt-4" style="background: linear-gradient(135deg, #2F2F2F 0%, #1a1a1a 100%);">
                    <div>
                        <h4 class="modal-title text-white" id="paymentModalLabel" style="font-family: var(--font-editorial);"><i class="bi bi-shield-check me-2" style="color: var(--brand-primary);"></i>Secure Checkout</h4>
                        <p class="text-white-50 fs-8 mb-0">Your payment is encrypted and 100% secure</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Left: Form -->
                        <div class="col-lg-7 p-4">
                            <!-- Progress steps -->
                            <div class="d-flex align-items-center mb-4 gap-2">
                                <div class="d-flex align-items-center gap-2" id="stepIndicator1">
                                    <div style="width:28px;height:28px;border-radius:50%;background:var(--brand-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;">1</div>
                                    <span class="fs-8 fw-bold text-uppercase-tracking">Shipping</span>
                                </div>
                                <div style="flex:1;height:2px;background:#eee;"></div>
                                <div class="d-flex align-items-center gap-2" id="stepIndicator2">
                                    <div style="width:28px;height:28px;border-radius:50%;background:#ddd;color:#999;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;" id="step2Circle">2</div>
                                    <span class="fs-8 fw-bold text-uppercase-tracking text-muted" id="step2Label">Payment</span>
                                </div>
                            </div>

                            <form id="checkoutForm" novalidate>
                                <!-- Step 1: Shipping Info -->
                                <div id="shippingStep">
                                    <h6 class="text-uppercase-tracking mb-3" style="color:#2F2F2F;"><i class="bi bi-geo-alt me-2" style="color:var(--brand-primary);"></i>Shipping Details</h6>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label fs-8 text-muted text-uppercase-tracking">First Name *</label>
                                            <input type="text" id="payFirstName" class="form-control rounded-0 fs-7" placeholder="Sara" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fs-8 text-muted text-uppercase-tracking">Last Name *</label>
                                            <input type="text" id="payLastName" class="form-control rounded-0 fs-7" placeholder="Ahmed" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fs-8 text-muted text-uppercase-tracking">Email Address *</label>
                                            <div class="input-group">
                                                <span class="input-group-text rounded-0 bg-light border-end-0"><i class="bi bi-envelope" style="color:var(--brand-primary);"></i></span>
                                                <input type="email" id="payEmail" class="form-control rounded-0 fs-7 border-start-0" placeholder="sara@example.com" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fs-8 text-muted text-uppercase-tracking">Phone Number *</label>
                                            <div class="input-group">
                                                <span class="input-group-text rounded-0 bg-light border-end-0"><i class="bi bi-telephone" style="color:var(--brand-primary);"></i></span>
                                                <input type="tel" id="payPhone" class="form-control rounded-0 fs-7 border-start-0" placeholder="+92 300 1234567" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fs-8 text-muted text-uppercase-tracking">Delivery Address *</label>
                                            <div class="input-group">
                                                <span class="input-group-text rounded-0 bg-light border-end-0"><i class="bi bi-house" style="color:var(--brand-primary);"></i></span>
                                                <input type="text" id="payAddress" class="form-control rounded-0 fs-7 border-start-0" placeholder="Street, City, Province" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-luxury w-100" onclick="goToPaymentStep()"><i class="bi bi-arrow-right me-2"></i>Continue to Payment</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Payment Info -->
                                <div id="paymentStep" class="d-none">
                                    <h6 class="text-uppercase-tracking mb-3" style="color:#2F2F2F;"><i class="bi bi-credit-card me-2" style="color:var(--brand-primary);"></i>Payment Details</h6>

                                    <!-- Payment method tabs -->
                                    <div class="d-flex gap-2 mb-4">
                                        <button type="button" class="btn btn-sm rounded-0 flex-fill payment-method-btn active" id="cardMethodBtn" onclick="selectPayMethod('card')" style="border:2px solid var(--brand-primary);background:var(--brand-primary);color:#fff;font-size:0.7rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;padding:0.6rem;">
                                            <i class="bi bi-credit-card me-1"></i>Credit / Debit Card
                                        </button>
                                        <button type="button" class="btn btn-sm rounded-0 flex-fill payment-method-btn" id="cashMethodBtn" onclick="selectPayMethod('cash')" style="border:2px solid #ddd;background:#fff;color:#555;font-size:0.7rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;padding:0.6rem;">
                                            <i class="bi bi-cash-coin me-1"></i>Cash on Delivery
                                        </button>
                                    </div>

                                    <!-- Card fields -->
                                    <div id="cardFields">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fs-8 text-muted text-uppercase-tracking">Cardholder Name *</label>
                                                <input type="text" id="cardName" class="form-control rounded-0 fs-7" placeholder="SARA AHMED">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fs-8 text-muted text-uppercase-tracking">Card Number *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text rounded-0 bg-light border-end-0"><i class="bi bi-credit-card-2-front" style="color:var(--brand-primary);"></i></span>
                                                    <input type="text" id="cardNumber" class="form-control rounded-0 fs-7 border-start-0" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCardNumber(this)">
                                                    <span class="input-group-text rounded-0 bg-light" id="cardTypeIcon"><i class="bi bi-bank" style="color:#aaa;"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fs-8 text-muted text-uppercase-tracking">Expiry Date *</label>
                                                <input type="text" id="cardExpiry" class="form-control rounded-0 fs-7" placeholder="MM/YY" maxlength="5" oninput="formatExpiry(this)">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fs-8 text-muted text-uppercase-tracking">CVV *</label>
                                                <div class="input-group">
                                                    <input type="password" id="cardCvv" class="form-control rounded-0 fs-7" placeholder="•••" maxlength="4">
                                                    <span class="input-group-text rounded-0 bg-light"><i class="bi bi-lock" style="color:var(--brand-primary);"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cash on Delivery message -->
                                    <div id="cashFields" class="d-none">
                                        <div class="p-4 text-center" style="background:#f8f5f0;border:2px dashed var(--brand-primary);">
                                            <i class="bi bi-truck fs-1 mb-3 d-block" style="color:var(--brand-primary);"></i>
                                            <h6 class="fw-bold mb-1">Cash on Delivery</h6>
                                            <p class="text-muted fs-8 mb-0">You will pay in cash when your order arrives at your doorstep. No card needed.</p>
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-3">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-luxury-outline w-100" onclick="goToShippingStep()"><i class="bi bi-arrow-left me-2"></i>Back</button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-luxury w-100" id="placeOrderBtn" onclick="submitCheckout()"><i class="bi bi-bag-check me-2"></i>Place Order</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Right: Order Summary -->
                        <div class="col-lg-5" style="background:#f8f5f0;border-left:1px solid #eee;">
                            <div class="p-4">
                                <h6 class="text-uppercase-tracking mb-3" style="color:#2F2F2F;"><i class="bi bi-receipt me-2" style="color:var(--brand-primary);"></i>Order Summary</h6>
                                <div id="paymentModalOrderItems" style="max-height:300px;overflow-y:auto;">
                                    <div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div></div>
                                </div>
                                <div class="border-top pt-3 mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fs-8 text-muted">Subtotal</span>
                                        <span class="fs-7 fw-bold" id="modalSubtotal">$0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fs-8 text-muted">Delivery</span>
                                        <span class="fs-7 fw-bold" id="modalDelivery">$5.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                        <span class="fs-7 fw-bold text-uppercase-tracking">Total</span>
                                        <span class="fs-5 fw-bold" style="color:var(--brand-primary);" id="modalTotal">$0.00</span>
                                    </div>
                                </div>
                                <!-- Security badges -->
                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-shield-check fs-5" style="color:#28a745;"></i>
                                        <span class="fs-9 text-muted">256-bit SSL Encrypted</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-lock-fill fs-5" style="color:#28a745;"></i>
                                        <span class="fs-9 text-muted">Secure Payment Gateway</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-arrow-counterclockwise fs-5" style="color:#28a745;"></i>
                                        <span class="fs-9 text-muted">Easy 30-day Returns</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== THANK YOU OVERLAY ===== -->
    <div id="thankYouOverlay" style="position:fixed;inset:0;z-index:3000;background:rgba(250,249,246,0.97);backdrop-filter:blur(12px);display:none;align-items:center;justify-content:center;flex-direction:column;text-align:center;padding:2rem;">
        <div style="max-width:520px;width:100%;">
            <!-- Animated checkmark -->
            <div class="mb-4" style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#D4AF37,#f0d060);display:flex;align-items:center;justify-content:center;margin:0 auto;box-shadow:0 10px 40px rgba(212,175,55,0.3);animation:popIn 0.5s cubic-bezier(0.175,0.885,0.32,1.275);">
                <i class="bi bi-check-lg text-white" style="font-size:3rem;"></i>
            </div>
            <span class="text-uppercase-tracking text-muted d-block mb-2" style="font-size:0.7rem;letter-spacing:0.2em;">Order Confirmed</span>
            <h2 style="font-family:var(--font-editorial);font-size:2.5rem;color:#2F2F2F;margin-bottom:0.5rem;">Thank You for Shopping!</h2>
            <p class="text-muted mb-1" style="font-size:0.9rem;">Your order has been placed successfully.</p>
            <p class="text-muted mb-4" style="font-size:0.85rem;">A confirmation will be sent to <strong id="thankYouEmail"></strong></p>

            <!-- Order details card -->
            <div style="background:#fff;border:1px solid #eee;border-radius:4px;padding:1.5rem;margin-bottom:1.5rem;text-align:left;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-uppercase-tracking" style="font-size:0.7rem;color:#999;">Order Number</span>
                    <span id="thankYouOrderId" class="fw-bold" style="color:#2F2F2F;"></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-uppercase-tracking" style="font-size:0.7rem;color:#999;">Deliver To</span>
                    <span id="thankYouName" class="fw-bold" style="color:#2F2F2F;"></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-uppercase-tracking" style="font-size:0.7rem;color:#999;">Payment Method</span>
                    <span id="thankYouPayMethod" class="fw-bold" style="color:#2F2F2F;"></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-uppercase-tracking" style="font-size:0.7rem;color:#999;">Estimated Delivery</span>
                    <span class="fw-bold" style="color:var(--brand-primary);">5–7 Business Days</span>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <button class="btn btn-luxury px-5" onclick="closeThankYou()" style="padding:0.9rem 2rem;"><i class="bi bi-shop me-2"></i>Continue Shopping</button>
                <a href="{{ url('/user/dashboard') }}" class="btn btn-luxury-outline px-5" style="padding:0.9rem 2rem;"><i class="bi bi-clock-history me-2"></i>View My Orders</a>
            </div>
        </div>
    </div>
    <style>
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    </style>

    <!-- Reusable Toast Center -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="appToast" class="toast align-items-center text-white bg-dark border-0 rounded-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fs-7" id="toastMessage">
                    Action completed successfully.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Loading Screen Overlay -->
    <div id="loadingOverlay">
        <div class="luxury-spinner"></div>
        <h4 class="mb-1 fw-light" id="loaderTitle">VirtualFit AI Processing</h4>
        <p class="text-muted fs-7" id="loaderMessage">Running VirtualFit AI try-on engine. This may take up to 2 minutes...</p>
    </div>

    <!-- Bootstrap 5 JS Bundle (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global JS Logic -->
    <script>
        const userId = @json(Auth::id() ?? null);

        // Toast Helper
        const toastEl = document.getElementById('appToast');
        let toastObj = null;
        if (toastEl) {
            toastObj = new bootstrap.Toast(toastEl, { delay: 3500 });
        }

        function showNotification(message, bgClass = 'bg-dark') {
            const toastMsgEl = document.getElementById('toastMessage');
            if (toastMsgEl && toastObj) {
                toastMsgEl.textContent = message;
                toastEl.className = `toast align-items-center text-white ${bgClass} border-0 rounded-0`;
                toastObj.show();
            }
        }

        // Loader helper
        function showLoader(title = 'VirtualFit AI Processing', msg = 'Running VirtualFit AI try-on engine...') {
            document.getElementById('loaderTitle').textContent = title;
            document.getElementById('loaderMessage').textContent = msg;
            document.getElementById('loadingOverlay').classList.add('show');
        }

        function hideLoader() {
            document.getElementById('loadingOverlay').classList.remove('show');
        }

        // Add class active on nav item
        document.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.classList.add('active-scale');
                setTimeout(() => btn.classList.remove('active-scale'), 150);
            });
        });

        // Sync Counters and Load
        document.addEventListener('DOMContentLoaded', () => {
            if (userId) {
                syncCounters();
                
                // Auto-trigger actions if URL params exist
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('buy_now') && urlParams.get('buy_now')) {
                    setTimeout(() => buyNow(urlParams.get('buy_now')), 500);
                    window.history.replaceState({}, document.title, window.location.pathname);
                } else if (urlParams.has('add_to_cart') && urlParams.get('add_to_cart')) {
                    setTimeout(() => addToCart(urlParams.get('add_to_cart')), 500);
                    window.history.replaceState({}, document.title, window.location.pathname);
                } else if (urlParams.has('wishlist') && urlParams.get('wishlist')) {
                    setTimeout(() => addToWishlist(urlParams.get('wishlist')), 500);
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            }
        });

        async function syncCounters() {
            if (!userId) return;
            try {
                // Fetch Cart count
                const cartRes = await fetch(`{{ url('/api') }}/cart?user_id=${userId}`);
                const cartData = await cartRes.json();
                if (cartData.status === 'success') {
                    const count = cartData.cart.length;
                    document.getElementById('cartCount').textContent = count;
                }

                // Fetch Wishlist count
                const wishlistRes = await fetch(`{{ url('/api') }}/wishlist?user_id=${userId}`);
                const wishlistData = await wishlistRes.json();
                if (wishlistData.status === 'success') {
                    const count = wishlistData.wishlist.length;
                    document.getElementById('wishlistCount').textContent = count;
                }
            } catch (err) {
                console.error("Error syncing counters:", err);
            }
        }

        window.uncheckedCartItems = new Set();
        
        function handleCartCheckboxChange(cb, itemId) {
            if (cb.checked) window.uncheckedCartItems.delete(itemId);
            else window.uncheckedCartItems.add(itemId);
            updateCartTotals();
        }
        
        function updateCartTotals() {
            let total = 0;
            document.querySelectorAll('.cart-item-checkbox:checked').forEach(cb => {
                total += parseFloat(cb.dataset.price) * parseInt(cb.dataset.qty);
            });
            const deliveryFee = total > 0 ? 5.00 : 0.00;
            const grandTotal = total + deliveryFee;
            document.getElementById('cartSubtotalPrice').textContent = `$${total.toFixed(2)}`;
            document.getElementById('cartDeliveryFee').textContent = deliveryFee > 0 ? `$${deliveryFee.toFixed(2)}` : 'Free';
            document.getElementById('cartTotalPrice').textContent = `$${grandTotal.toFixed(2)}`;
        }

        // Cart Actions
        async function loadCart() {
            if (!userId) return;
            const container = document.getElementById('cartItemsContainer');
            container.innerHTML = `<div class="text-center py-5"><div class="spinner-border spinner-border-sm text-dark" role="status"></div></div>`;
            
            try {
                const res = await fetch(`{{ url('/api') }}/cart?user_id=${userId}`);
                const data = await res.json();
                
                if (data.status === 'success' && data.cart.length > 0) {
                    let html = '';
                    let total = 0;
                    data.cart.forEach(item => {
                        const product = item.product;
                        if (!product) return;
                        const price = parseFloat(product.price);
                        const qty = parseInt(item.quantity);
                        
                        let isChecked = !window.uncheckedCartItems.has(item.id) ? 'checked' : '';
                        if (isChecked) total += (price * qty);
                        
                        html += `
                            <div class="drawer-item" style="align-items: flex-start; gap: 10px;">
                                <div class="form-check mt-3 pt-1">
                                    <input class="form-check-input cart-item-checkbox" type="checkbox" value="${item.id}" ${isChecked} onchange="handleCartCheckboxChange(this, ${item.id})" data-price="${price}" data-qty="${qty}" style="cursor:pointer; width:1.2rem; height:1.2rem;">
                                </div>
                                <img src="${product.image_url}" alt="${product.name}" style="width:70px;height:90px;object-fit:cover;border-radius:6px;flex-shrink:0;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fs-7 fw-bold" style="color:#2F2F2F;">${product.name}</h6>
                                    <div class="text-muted fs-8 mb-2">${product.category || 'Garment'}</div>
                                    <div class="fs-7 fw-bold mb-2" style="color:#D4AF37;">$${price.toFixed(2)} each</div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-1">
                                            <button class="btn btn-sm border rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;padding:0;" onclick="updateCartQty(${item.id}, ${qty - 1})">
                                                <i class="bi bi-dash fs-8"></i>
                                            </button>
                                            <span class="px-2 fs-7 fw-bold" style="min-width:28px;text-align:center;color:#2F2F2F;">${qty}</span>
                                            <button class="btn btn-sm border rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;padding:0;" onclick="updateCartQty(${item.id}, ${qty + 1})">
                                                <i class="bi bi-plus fs-8"></i>
                                            </button>
                                        </div>
                                        <button class="btn btn-sm" style="color:#dc3545;padding:4px 8px;" onclick="removeFromCart(${item.id})" title="Remove item">
                                            <i class="bi bi-trash3 fs-7"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    const deliveryFee = total > 0 ? 5.00 : 0.00;
                    const grandTotal = total + deliveryFee;
                    container.innerHTML = html;
                    document.getElementById('cartSubtotalPrice').textContent = `$${total.toFixed(2)}`;
                    document.getElementById('cartDeliveryFee').textContent = deliveryFee > 0 ? `$${deliveryFee.toFixed(2)}` : 'Free';
                    document.getElementById('cartTotalPrice').textContent = `$${grandTotal.toFixed(2)}`;
                    document.getElementById('cartCount').textContent = data.cart.length;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-cart text-muted fs-1 mb-2 d-block"></i>
                            <p class="text-muted fs-7">Your cart is empty.</p>
                        </div>
                    `;
                    document.getElementById('cartTotalPrice').textContent = `$0.00`;
                    document.getElementById('cartCount').textContent = 0;
                }
            } catch (err) {
                container.innerHTML = `<div class="text-center py-5 text-danger fs-8">Failed to sync cart.</div>`;
            }
        }

        async function addToCart(productId, quantity = 1) {
            if (!productId) return;
            if (!userId) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('add_to_cart', productId);
                window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(currentUrl.toString());
                return;
            }
            try {
                const res = await fetch('{{ url('/api') }}/cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        product_id: productId,
                        quantity: quantity
                    })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    showNotification("Item added to your cart!", "bg-success");
                    syncCounters();
                    // If cart drawer is open, reload it
                    const cartDrawerEl = document.getElementById('cartDrawer');
                    if (cartDrawerEl && cartDrawerEl.classList.contains('show')) {
                        loadCart();
                    }
                } else {
                    showNotification(data.message || "Failed to add item.", "bg-danger");
                }
            } catch (err) {
                showNotification("Request error occurred.", "bg-danger");
            }
        }

        async function buyNow(productId) {
            if (!productId) return;
            if (!userId) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('buy_now', productId);
                window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(currentUrl.toString());
                return;
            }
            // Directly open modal with buy now mode
            openPaymentModal('buy_now', productId);
        }

        async function updateCartQty(cartId, newQuantity) {
            if (newQuantity < 1) {
                removeFromCart(cartId);
                return;
            }
            try {
                // Laravel PUT request via POST with _method
                const res = await fetch(`{{ url('/api') }}/cart/${cartId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        quantity: newQuantity
                    })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    loadCart();
                    syncCounters();
                } else {
                    showNotification(data.message || "Failed to update quantity.", "bg-danger");
                }
            } catch (err) {
                showNotification("Request error occurred.", "bg-danger");
            }
        }

        async function removeFromCart(cartId) {
            try {
                const res = await fetch(`{{ url('/api') }}/cart/${cartId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await res.json();
                if (data.status === 'success') {
                    showNotification("Item removed from your cart.", "bg-dark");
                    loadCart();
                    syncCounters();
                } else {
                    showNotification("Failed to remove item.", "bg-danger");
                }
            } catch (err) {
                showNotification("Request error occurred.", "bg-danger");
            }
        }

        // Wishlist Actions
        async function loadWishlist() {
            if (!userId) return;
            const container = document.getElementById('wishlistItemsContainer');
            container.innerHTML = `<div class="text-center py-5"><div class="spinner-border spinner-border-sm text-dark" role="status"></div></div>`;
            
            try {
                const res = await fetch(`{{ url('/api') }}/wishlist?user_id=${userId}`);
                const data = await res.json();
                
                if (data.status === 'success' && data.wishlist.length > 0) {
                    let html = '';
                    data.wishlist.forEach(item => {
                        const product = item.product;
                        if (!product) return;
                        html += `
                            <div class="drawer-item">
                                <img src="${product.image_url}" alt="${product.name}">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fs-7 fw-bold text-truncate" style="max-width: 160px;">${product.name}</h6>
                                    <div class="text-muted fs-8 mb-1">$${parseFloat(product.price).toFixed(2)}</div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-dark rounded-0 px-2 py-1 fs-9 text-uppercase-tracking" onclick="moveToCart(${item.id}, ${product.id})">
                                            Move To Cart
                                        </button>
                                        <button class="btn btn-sm text-danger p-0 fs-9" onclick="removeFromWishlist(${item.id})">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                    document.getElementById('wishlistCount').textContent = data.wishlist.length;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-heart text-muted fs-1 mb-2 d-block"></i>
                            <p class="text-muted fs-7">Your wishlist is empty.</p>
                        </div>
                    `;
                    document.getElementById('wishlistCount').textContent = 0;
                }
            } catch (err) {
                container.innerHTML = `<div class="text-center py-5 text-danger fs-8">Failed to sync wishlist.</div>`;
            }
        }

        async function addToWishlist(productId) {
            if (!productId) return;
            if (!userId) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('wishlist', productId);
                window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(currentUrl.toString());
                return;
            }
            try {
                const res = await fetch('{{ url('/api') }}/wishlist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        product_id: productId
                    })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    showNotification("Item added to your wishlist!", "bg-success");
                    syncCounters();
                    const wishlistDrawerEl = document.getElementById('wishlistDrawer');
                    if (wishlistDrawerEl && wishlistDrawerEl.classList.contains('show')) {
                        loadWishlist();
                    }
                } else {
                    showNotification(data.message || "Item is already in your wishlist.", "bg-dark");
                }
            } catch (err) {
                showNotification("Request error occurred.", "bg-danger");
            }
        }

        async function removeFromWishlist(wishlistId) {
            try {
                const res = await fetch(`{{ url('/api') }}/wishlist/${wishlistId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await res.json();
                if (data.status === 'success') {
                    showNotification("Removed from wishlist.", "bg-dark");
                    loadWishlist();
                    syncCounters();
                } else {
                    showNotification("Failed to remove item.", "bg-danger");
                }
            } catch (err) {
                showNotification("Request error occurred.", "bg-danger");
            }
        }

        async function moveToCart(wishlistId, productId) {
            // First add to cart
            await addToCart(productId, 1);
            // Then remove from wishlist
            await removeFromWishlist(wishlistId);
            // Reload drawers if open
            loadWishlist();
        }

        // ===== PAYMENT MODAL LOGIC =====
        let currentPayMethod = 'card';
        let currentCartTotal = 0;
        let checkoutMode = 'cart'; // 'cart' or 'buy_now'
        let buyNowProductId = null;
        let selectedCartIds = [];

        function openPaymentModal(mode = 'cart', productId = null) {
            if (!userId) {
                window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(window.location.href);
                return;
            }
            checkoutMode = mode;
            buyNowProductId = productId;
            selectedCartIds = [];
            
            if (mode === 'cart') {
                document.querySelectorAll('.cart-item-checkbox:checked').forEach(cb => {
                    selectedCartIds.push(cb.value);
                });
                if (selectedCartIds.length === 0) {
                    showNotification("Please select at least one item to checkout.", "bg-warning");
                    return;
                }
            }

            goToShippingStep();
            loadPaymentModalSummary();
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }

        async function loadPaymentModalSummary() {
            const container = document.getElementById('paymentModalOrderItems');
            container.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-dark"></div></div>';
            
            if (checkoutMode === 'buy_now' && buyNowProductId) {
                try {
                    const res = await fetch(`{{ url('/api') }}/products/${buyNowProductId}`);
                    const data = await res.json();
                    if (data.status === 'success' && data.product) {
                        const p = data.product;
                        const lineTotal = parseFloat(p.price);
                        const delivery = lineTotal > 0 ? 5.00 : 0;
                        currentCartTotal = lineTotal + delivery;
                        
                        container.innerHTML = `
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <img src="${p.image_url}" alt="${p.name}" style="width:50px;height:65px;object-fit:cover;border-radius:4px;flex-shrink:0;border:1px solid #eee;">
                                <div class="flex-grow-1">
                                    <div class="fs-8 fw-bold text-truncate" style="max-width:180px;color:#2F2F2F;">${p.name}</div>
                                    <div class="fs-9 text-muted">${p.category || 'Garment'} &times; 1</div>
                                </div>
                                <div class="fs-8 fw-bold" style="color:var(--brand-primary);white-space:nowrap;">$${lineTotal.toFixed(2)}</div>
                            </div>
                        `;
                        document.getElementById('modalSubtotal').textContent = `$${lineTotal.toFixed(2)}`;
                        document.getElementById('modalDelivery').textContent = delivery > 0 ? `$${delivery.toFixed(2)}` : 'Free';
                        document.getElementById('modalTotal').textContent = `$${currentCartTotal.toFixed(2)}`;
                    }
                } catch (e) {
                    container.innerHTML = '<p class="text-danger fs-8 text-center py-3">Error loading item.</p>';
                }
                return;
            }

            try {
                const res = await fetch(`{{ url('/api') }}/cart?user_id=${userId}`);
                const data = await res.json();
                if (data.status === 'success' && data.cart.length > 0) {
                    let html = '';
                    let subtotal = 0;
                    data.cart.forEach(item => {
                        if (!selectedCartIds.includes(item.id.toString())) return;
                        
                        const p = item.product;
                        if (!p) return;
                        const lineTotal = parseFloat(p.price) * parseInt(item.quantity);
                        subtotal += lineTotal;
                        html += `
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <img src="${p.image_url}" alt="${p.name}" style="width:50px;height:65px;object-fit:cover;border-radius:4px;flex-shrink:0;border:1px solid #eee;">
                                <div class="flex-grow-1">
                                    <div class="fs-8 fw-bold text-truncate" style="max-width:180px;color:#2F2F2F;">${p.name}</div>
                                    <div class="fs-9 text-muted">${p.category || 'Garment'} &times; ${item.quantity}</div>
                                </div>
                                <div class="fs-8 fw-bold" style="color:var(--brand-primary);white-space:nowrap;">$${lineTotal.toFixed(2)}</div>
                            </div>
                        `;
                    });
                    const delivery = subtotal > 0 ? 5.00 : 0;
                    currentCartTotal = subtotal + delivery;
                    container.innerHTML = html;
                    document.getElementById('modalSubtotal').textContent = `$${subtotal.toFixed(2)}`;
                    document.getElementById('modalDelivery').textContent = delivery > 0 ? `$${delivery.toFixed(2)}` : 'Free';
                    document.getElementById('modalTotal').textContent = `$${currentCartTotal.toFixed(2)}`;
                } else {
                    container.innerHTML = '<p class="text-muted fs-8 text-center py-3">Your cart is empty.</p>';
                }
            } catch (e) {
                container.innerHTML = '<p class="text-danger fs-8 text-center py-3">Error loading items.</p>';
            }
        }

        function goToShippingStep() {
            document.getElementById('shippingStep').classList.remove('d-none');
            document.getElementById('paymentStep').classList.add('d-none');
            // Update step indicators
            document.getElementById('step2Circle').style.background = '#ddd';
            document.getElementById('step2Circle').style.color = '#999';
            document.getElementById('step2Label').classList.add('text-muted');
        }

        function goToPaymentStep() {
            const firstName = document.getElementById('payFirstName').value.trim();
            const lastName = document.getElementById('payLastName').value.trim();
            const email = document.getElementById('payEmail').value.trim();
            const phone = document.getElementById('payPhone').value.trim();
            const address = document.getElementById('payAddress').value.trim();
            if (!firstName || !lastName || !email || !phone || !address) {
                showNotification('Please fill in all shipping details.', 'bg-warning');
                return;
            }
            document.getElementById('shippingStep').classList.add('d-none');
            document.getElementById('paymentStep').classList.remove('d-none');
            // Update step indicators
            document.getElementById('step2Circle').style.background = 'var(--brand-primary)';
            document.getElementById('step2Circle').style.color = '#fff';
            document.getElementById('step2Label').classList.remove('text-muted');
        }

        function selectPayMethod(method) {
            currentPayMethod = method;
            if (method === 'card') {
                document.getElementById('cardFields').classList.remove('d-none');
                document.getElementById('cashFields').classList.add('d-none');
                document.getElementById('cardMethodBtn').style.background = 'var(--brand-primary)';
                document.getElementById('cardMethodBtn').style.color = '#fff';
                document.getElementById('cardMethodBtn').style.borderColor = 'var(--brand-primary)';
                document.getElementById('cashMethodBtn').style.background = '#fff';
                document.getElementById('cashMethodBtn').style.color = '#555';
                document.getElementById('cashMethodBtn').style.borderColor = '#ddd';
            } else {
                document.getElementById('cardFields').classList.add('d-none');
                document.getElementById('cashFields').classList.remove('d-none');
                document.getElementById('cashMethodBtn').style.background = 'var(--brand-primary)';
                document.getElementById('cashMethodBtn').style.color = '#fff';
                document.getElementById('cashMethodBtn').style.borderColor = 'var(--brand-primary)';
                document.getElementById('cardMethodBtn').style.background = '#fff';
                document.getElementById('cardMethodBtn').style.color = '#555';
                document.getElementById('cardMethodBtn').style.borderColor = '#ddd';
            }
        }

        function formatCardNumber(input) {
            let v = input.value.replace(/\D/g, '').substring(0, 16);
            input.value = v.replace(/(\d{4})/g, '$1 ').trim();
        }

        function formatExpiry(input) {
            let v = input.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
            input.value = v;
        }

        async function submitCheckout() {
            if (!userId) return;

            // Validate payment info
            if (currentPayMethod === 'card') {
                const cardName = document.getElementById('cardName').value.trim();
                const cardNum = document.getElementById('cardNumber').value.trim();
                const expiry = document.getElementById('cardExpiry').value.trim();
                const cvv = document.getElementById('cardCvv').value.trim();
                if (!cardName || !cardNum || !expiry || !cvv) {
                    showNotification('Please fill in all card details.', 'bg-warning');
                    return;
                }
            }

            const btn = document.getElementById('placeOrderBtn');
            btn.disabled = true;
            btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2" role="status"></div>Processing...';

            try {
                const res = await fetch('{{ url('/api') }}/cart/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        user_id: userId,
                        checkout_mode: checkoutMode,
                        selected_cart_ids: selectedCartIds,
                        buy_now_product_id: buyNowProductId,
                        buy_now_quantity: 1
                    })
                });
                const data = await res.json();

                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-bag-check me-2"></i>Place Order';

                if (data.status === 'success') {
                    // Close modal
                    const modalEl = document.getElementById('paymentModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    // Close cart drawer
                    const cartDrawerEl = document.getElementById('cartDrawer');
                    if (cartDrawerEl) {
                        const cartDrawer = bootstrap.Offcanvas.getInstance(cartDrawerEl);
                        if (cartDrawer) cartDrawer.hide();
                    }
                    // Show thank you overlay
                    const firstName = document.getElementById('payFirstName').value.trim();
                    const lastName = document.getElementById('payLastName').value.trim();
                    const email = document.getElementById('payEmail').value.trim();
                    showThankYou(data.order_id, firstName + ' ' + lastName, email, currentPayMethod);
                    // Reload counters
                    loadCart();
                    syncCounters();
                } else {
                    showNotification(data.message || 'Checkout failed. Please try again.', 'bg-danger');
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-bag-check me-2"></i>Place Order';
                showNotification('Connection error. Please check your internet and try again.', 'bg-danger');
            }
        }

        function showThankYou(orderId, name, email, payMethod) {
            document.getElementById('thankYouOrderId').textContent = '#ORD-' + String(orderId).padStart(5, '0');
            document.getElementById('thankYouName').textContent = name;
            document.getElementById('thankYouEmail').textContent = email;
            document.getElementById('thankYouPayMethod').textContent = payMethod === 'card' ? 'Credit/Debit Card' : 'Cash on Delivery';
            const overlay = document.getElementById('thankYouOverlay');
            overlay.style.display = 'flex';
            // Trigger animation
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.4s ease';
            setTimeout(() => { overlay.style.opacity = '1'; }, 10);
        }

        function closeThankYou() {
            const overlay = document.getElementById('thankYouOverlay');
            overlay.style.opacity = '0';
            setTimeout(() => { overlay.style.display = 'none'; }, 400);
        }
    </script>
    @yield('scripts')
</body>
</html>
