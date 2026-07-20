@extends('layouts.app')

@section('title', ($setting->site_name ?? 'Virtual Dress Room') . ' | Boutique')

@section('styles')
<style>
    .tryon-workspace {
        background: #f1edec;
        border: 1px solid rgba(0, 0, 0, 0.05);
        padding: 2.5rem;
        margin-bottom: 4rem;
        position: relative;
    }
    
    .tryon-dropzone {
        border: 2px dashed rgba(0, 0, 0, 0.15);
        background-color: #ffffff;
        min-height: 250px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition-smooth);
        padding: 1.5rem;
    }
    
    .tryon-dropzone:hover {
        border-color: var(--brand-accent);
        background-color: rgba(226, 180, 154, 0.05);
    }
    
    .workspace-preview-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 4px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .category-pill {
        border: 1.5px solid rgba(0, 0, 0, 0.08);
        background-color: transparent;
        color: #555;
        border-radius: 0;
        font-family: var(--font-body);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        transition: var(--transition-smooth);
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .category-pill:hover, .category-pill.active {
        background-color: var(--brand-primary);
        color: #ffffff;
        border-color: var(--brand-primary);
    }

    .search-input-luxury {
        border: 1.5px solid rgba(0, 0, 0, 0.08);
        border-radius: 0;
        padding: 0.8rem 1.2rem;
        font-family: var(--font-body);
        font-size: 0.85rem;
        transition: var(--transition-smooth);
    }

    .search-input-luxury:focus {
        border-color: #000000;
        box-shadow: none;
    }

    .product-card-img {
        height: 380px;
        object-fit: cover;
        transition: transform 1.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .product-card:hover .product-card-img {
        transform: scale(1.04);
    }

    .badge-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: rgba(255, 255, 255, 0.9);
        color: #000000;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 0.1em;
        font-weight: 600;
        padding: 0.4rem 0.8rem;
        z-index: 5;
    }

    .wishlist-toggle-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(255, 255, 255, 0.9);
        color: #000;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        transition: var(--transition-smooth);
    }

    .wishlist-toggle-btn:hover {
        background-color: #ffffff;
        transform: scale(1.1);
    }

    .btn-action-panel {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .btn-action-panel .btn {
        flex: 1;
        font-size: 0.7rem;
        padding: 0.6rem 0.5rem;
    }
</style>
@endsection

@section('content')
<!-- Boutique Hero -->
<section class="py-5 bg-white border-bottom">
    <div class="container text-center py-3">
        <span class="text-uppercase-tracking text-muted">{{ $setting->site_name ?? 'Virtual Dress Room' }} Collection</span>
        <h2 class="display-font mb-2">Step into the Virtual Suite</h2>
        <p class="text-muted fs-7 mx-auto" style="max-width: 600px;">Select your style, upload your portrait, and preview couture using <strong>VirtualFit AI</strong> rendering.</p>
    </div>
</section>

<div class="container py-5">
    
    <!-- VIRTUAL AI TRY-ON WORKSPACE -->
    @auth
    <div class="glass-panel p-md-5 p-4 mx-3 mb-5 position-relative overflow-hidden d-none" id="tryonWorkspace">
        <h3 class="font-editorial mb-4"><i class="bi bi-magic me-2 text-dark"></i> VirtualFit AI Fitting Suite</h3>
        
        <div class="row g-4">
            <!-- Column 1: Human Photo Upload -->
            <div class="col-lg-4">
                <label class="text-uppercase-tracking text-muted mb-2 d-block">1. Upload Portrait</label>
                
                <input type="file" id="humanImageUpload" accept="image/*" class="d-none" onchange="previewHumanImage(this)">
                
                <div class="tryon-dropzone" id="dropzoneContainer" onclick="document.getElementById('humanImageUpload').click()" style="min-height: 360px; padding: 0; overflow: hidden; position: relative;">
                    <div id="dropzoneDefault" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <i class="bi bi-cloud-arrow-up fs-1 text-muted mb-2"></i>
                        <p class="mb-1 fw-bold fs-7">Drop photograph here</p>
                        <p class="text-muted fs-8 mb-0">or click to browse local files</p>
                    </div>
                    <img id="humanPreview" class="d-none" alt="Human Portrait Preview" style="width: 100%; height: 360px; object-fit: cover; object-position: top; display: block;">
                </div>
            </div>
            
            <!-- Column 2: Garment Selection -->
            <div class="col-lg-4">
                <label class="text-uppercase-tracking text-muted mb-2 d-block">2. Selected Garment</label>
                
                <div class="tryon-dropzone bg-light" id="garmentZone">
                    <div id="garmentDefault" class="text-center">
                        <i class="bi bi-tag fs-1 text-muted mb-2"></i>
                        <p class="mb-1 fw-bold fs-7">No Garment Selected</p>
                        <p class="text-muted fs-8 mb-0">Select "Try On" on any product catalog card below</p>
                    </div>
                    <div id="garmentDetails" class="w-100 d-none text-center">
                        <img id="garmentPreview" class="workspace-preview-img mb-3" alt="Garment Photo Preview">
                        <h6 id="garmentName" class="fw-bold fs-7 mb-1 text-truncate">Dress Name</h6>
                        <span id="garmentCategory" class="badge bg-dark rounded-0 fs-9 text-uppercase mb-0">Category</span>
                        <input type="hidden" id="garmentUrlInput">
                    </div>
                </div>
            </div>
            
            <!-- Column 3: Custom Prompts and Actions -->
            <div class="col-lg-4">
                <label class="text-uppercase-tracking text-muted mb-2 d-block">3. Try-On Customizations</label>
                
                <div class="bg-white p-4 h-100 border d-flex flex-column justify-content-between" style="min-height: 250px;">
                    <div>
                        <div class="mb-3">
                            <label for="tryonPrompt" class="text-uppercase-tracking text-muted fs-8 mb-1 d-block">AI Style Instruction</label>
                            <textarea id="tryonPrompt" class="form-control rounded-0 fs-7" rows="3" placeholder="beautiful dress, fit loosely..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-uppercase-tracking text-muted fs-8 mb-1 d-block">Preset prompts</label>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-0 fs-9" onclick="setPresetPrompt('beautiful dress')">Beautiful Dress</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-0 fs-9" onclick="setPresetPrompt('formal fit, solid background')">Formal Fit</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-0 fs-9" onclick="setPresetPrompt('casual loose wear')">Casual Loose</button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="runTryonBtn" class="btn btn-luxury w-100 py-3 mt-3" onclick="triggerTryOn()">
                        <i class="bi bi-stars me-1"></i> Generate with VirtualFit AI
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Results Section (Initially Hidden) -->
        <div class="mt-4 pt-4 border-top d-none" id="tryonResultSection">
            <h4 class="font-editorial mb-3 text-center">✨ Your VirtualFit AI Result</h4>
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div id="resultPreviewContainer" class="bg-white border shadow-sm mb-3" style="min-height: 500px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px;">
                        <img id="tryonResultImage" alt="VirtualFit AI Result" style="width: 100%; max-height: 600px; object-fit: contain; display: block;">
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button id="tryonDownloadBtn" onclick="downloadResultImage()" class="btn btn-luxury px-4"><i class="bi bi-download me-1"></i> Download Image</button>
                        <button id="tryonAddBagBtn" class="btn btn-luxury-outline px-4"><i class="bi bi-cart-check-fill me-1"></i> Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth
    
    <!-- PRODUCT EXPLORER & FILTERS -->
    <div class="row mb-4 align-items-center">
        <!-- Search bar -->
        <div class="col-md-4 mb-3 mb-md-0">
            <input type="text" id="productSearchInput" class="form-control search-input-luxury" placeholder="Search product name..." onkeyup="filterProducts()">
        </div>
        
        <!-- Filters count -->
        <div class="col-md-8 text-md-end">
            <span class="fs-8 text-muted me-2" id="resultsCount">Showing {{ $products->count() }} products</span>
        </div>
    </div>
    
    <!-- Category pills -->
    <div class="d-flex flex-wrap mb-5">
        <button class="category-pill active" onclick="selectCategory('All', this)">All Collections</button>
        @foreach($categories as $category)
            <button class="category-pill" onclick="selectCategory('{{ $category }}', this)">{{ $category }}</button>
        @endforeach
    </div>

    <!-- Product Grid -->
    <div class="row g-4" id="productsGrid">
        @forelse($products as $product)
            <div class="col-md-4 col-sm-6 product-card-item" data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}" data-rawname="{{ addslashes($product->name) }}" data-url="{{ $product->image_url }}" data-category="{{ $product->category ?? 'Dress' }}">
                <div class="card h-100 rounded-0 border-0 shadow-sm hover-card position-relative overflow-hidden">
                    
                    <span class="badge-category">{{ $product->category ?? 'New Collection' }}</span>
                    
                    <!-- Wishlist trigger -->
                    <button class="wishlist-toggle-btn" onclick="addToWishlist({{ $product->id }})" title="Add to Wishlist">
                        <i class="bi bi-heart"></i>
                    </button>
                    
                    <div class="overflow-hidden">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="card-img-top rounded-0 product-card-img">
                    </div>
                    
                    <div class="card-body p-4 bg-white">
                        <h5 class="card-title font-editorial mb-1 text-truncate">{{ $product->name }}</h5>
                        <p class="text-muted fs-8 text-truncate mb-2">{{ $product->description ?? 'Premium luxury garments.' }}</p>
                        
                        <!-- Reviews Rating Stars -->
                        <div class="d-flex align-items-center mb-2 gap-1" style="cursor: pointer;" onclick="openReviewsModal({{ $product->id }}, '{{ addslashes($product->name) }}')" title="View Reviews">
                            @php
                                $avgRating = round($product->reviews_avg_rating ?? 0);
                                $reviewCount = $product->reviews_count ?? 0;
                            @endphp
                            <span class="text-warning fs-8">
                                {!! str_repeat('★', $avgRating) !!}{!! str_repeat('☆', 5 - $avgRating) !!}
                            </span>
                            <span class="text-muted fs-9">({{ $reviewCount }} {{ $reviewCount == 1 ? 'review' : 'reviews' }})</span>
                        </div>

                        <div class="fs-6 fw-bold mb-3 text-dark">${{ number_format($product->price, 2) }}</div>
                        
                        <div class="btn-action-panel border-top pt-3 flex-column gap-2">
                            <div class="d-flex gap-2">
                                <button class="btn btn-dark rounded-0 text-uppercase-tracking fs-8 w-50" onclick="selectForTryOn('{{ $product->name }}', '{{ $product->image_url }}', '{{ $product->category ?? 'Dress' }}', {{ $product->id }})">
                                    <i class="bi bi-magic me-1"></i> Try On
                                </button>
                                <button class="btn btn-outline-dark rounded-0 text-uppercase-tracking fs-8 w-50" onclick="addToCart({{ $product->id }})">
                                    <i class="bi bi-cart3 me-1"></i> Add to Cart
                                </button>
                            </div>
                            <button class="btn btn-luxury w-100 rounded-0 text-uppercase-tracking fs-8" onclick="buyNow({{ $product->id }})">
                                <i class="bi bi-lightning-fill me-1"></i> Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-tag text-muted display-4 mb-3 d-block"></i>
                <p class="text-muted">No products found in our collections.</p>
            </div>
        @endforelse
    </div>
    
    <!-- MY ORDERS & HISTORY -->
    @auth
    <div class="mt-5 pt-5 border-top">
        <h3 class="font-editorial mb-4">My Orders & History</h3>
        
        <ul class="nav nav-tabs rounded-0 mb-4" id="userHistoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-0 text-dark" data-bs-toggle="tab" data-bs-target="#ordersPane" type="button" role="tab">My Orders</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-0 text-dark" data-bs-toggle="tab" data-bs-target="#wardrobePane" type="button" role="tab">My AI Wardrobe</button>
            </li>
        </ul>

        <div class="tab-content" id="userHistoryContent">
            <!-- Orders Tab -->
            <div class="tab-pane fade show active" id="ordersPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table align-middle border">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase-tracking fs-8 text-muted py-3 px-4">Order ID</th>
                                <th class="text-uppercase-tracking fs-8 text-muted py-3 px-4">Total Amount</th>
                                <th class="text-uppercase-tracking fs-8 text-muted py-3 px-4">Items</th>
                                <th class="text-uppercase-tracking fs-8 text-muted py-3 px-4">Payment</th>
                                <th class="text-uppercase-tracking fs-8 text-muted py-3 px-4">Status</th>
                                <th class="text-uppercase-tracking fs-8 text-muted py-3 px-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders ?? [] as $order)
                            <tr>
                                <td class="px-4 fw-bold">#ORD-{{ sprintf('%05d', $order->id) }}</td>
                                <td class="px-4 fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        @foreach($order->items as $item)
                                            @if($item->product)
                                                @php
                                                    $rev = $userReviews->get($item->product_id);
                                                    $ratingText = match((int)($rev->rating ?? 0)) {
                                                        5 => 'Excellent',
                                                        4 => 'Good',
                                                        3 => 'Average',
                                                        2 => 'Poor',
                                                        1 => 'Terrible',
                                                        default => ''
                                                    };
                                                @endphp
                                                <div class="d-flex flex-column align-items-center me-3 mb-2">
                                                    <img src="{{ asset(ltrim($item->product->image_url, '/')) }}" title="{{ $item->product->name }} (x{{ $item->quantity }})" style="width:45px;height:60px;object-fit:cover;border:1px solid #eee;border-radius:4px;" class="shadow-sm">
                                                    @if($rev)
                                                        <div class="text-warning mt-1" style="font-size: 0.6rem; letter-spacing: 1px;">
                                                            {!! str_repeat('<i class="bi bi-star-fill"></i>', (int)$rev->rating) !!}{!! str_repeat('<i class="bi bi-star"></i>', 5 - (int)$rev->rating) !!}
                                                        </div>
                                                        <span class="text-muted fw-bold" style="font-size: 0.65rem;">{{ $ratingText }}</span>
                                                    @elseif(strtolower($order->order_status) === 'delivered')
                                                        <button type="button" class="btn btn-link text-muted p-0 mt-1 text-decoration-none" onclick="openReviewsModal({{ $item->product_id }}, '{{ addslashes($item->product->name) }}')" title="Rate Product">
                                                            <i class="bi bi-star fs-8"></i> <span class="fs-9 text-dark">Rate</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4"><span class="badge bg-success rounded-0">{{ $order->payment_status }}</span></td>
                                <td class="px-4">
                                    @php
                                        $sBadge = match(strtolower($order->order_status)) {
                                            'processing' => 'bg-warning text-dark',
                                            'shipped'    => 'bg-info text-dark',
                                            'delivered'  => 'bg-success',
                                            'cancelled'  => 'bg-danger',
                                            default      => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge rounded-0 {{ $sBadge }}">{{ $order->order_status }}</span>
                                </td>
                                <td class="px-4 text-muted fs-8">{{ $order->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">You have not placed any orders yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- AI Wardrobe Tab -->
            <div class="tab-pane fade" id="wardrobePane" role="tabpanel">
                <div class="row g-4">
                    @forelse($tryOnHistories ?? [] as $history)
                        @php
                            $resultImg = $history->result_image_url ?? null;
                            $productName = $history->product?->name ?? 'Garment';
                            $imgSrc = $resultImg
                                ? (str_starts_with($resultImg, 'http') ? $resultImg : url('/try-on-image/' . basename($resultImg)))
                                : asset('images/placeholder.png');
                        @endphp
                        <div class="col-md-3 col-sm-6">
                            <div class="card rounded-0 border-0 shadow-sm h-100 position-relative overflow-hidden">
                                <!-- Gold "AI Tried" badge -->
                                <span class="position-absolute top-0 start-0 m-2 badge fs-9 text-uppercase px-2 py-1"
                                      style="background:var(--brand-accent);color:#000;letter-spacing:.08em;z-index:5;">
                                    <i class="bi bi-stars me-1"></i>AI Tried
                                </span>
                                <img src="{{ $imgSrc }}"
                                     class="card-img-top rounded-0"
                                     style="height:250px;object-fit:contain;background-color:#faf9f6;"
                                     onerror="this.src='https://placehold.co/300x250?text=No+Image'">
                                <div class="card-body p-3 text-center">
                                    <h6 class="fs-8 fw-bold text-truncate mb-1">{{ $productName }}</h6>
                                    <p class="fs-9 text-muted mb-2">{{ $history->created_at?->format('M d, Y') ?? 'N/A' }}</p>
                                    @if($history->ai_prompt_used)
                                        <p class="fs-9 text-muted fst-italic mb-2" title="{{ $history->ai_prompt_used }}">"{{ Str::limit($history->ai_prompt_used, 40) }}"</p>
                                    @endif
                                    <div class="d-flex gap-1 mt-2">
                                        <a href="{{ $imgSrc }}" download="{{ $productName }}.jpg"
                                           class="btn btn-sm btn-outline-dark fs-9 rounded-0 flex-grow-1">
                                            <i class="bi bi-download me-1"></i>Download
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger fs-9 rounded-0 px-2"
                                                onclick="deleteWardrobeItem({{ $history->id }}, this)" title="Delete from Wardrobe">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-magic display-6 d-block mb-3"></i>
                            Your AI Wardrobe is empty. Try on some clothes above!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endauth

    <!-- ===== PRODUCT REVIEWS MODAL ===== -->
    <div class="modal fade" id="productReviewsModal" tabindex="-1" aria-labelledby="productReviewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title font-editorial text-dark" id="productReviewsModalLabel">Reviews & Ratings</h5>
                        <p class="text-muted fs-8 mb-0" id="reviewModalProductName">Garment Reviews</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <!-- Reviews List -->
                    <h6 class="text-uppercase-tracking mb-3" style="font-size:0.75rem;"><i class="bi bi-chat-left-text me-2"></i>Customer Feedback</h6>
                    <div id="reviewsListContainer" class="mb-4" style="max-height:220px; overflow-y:auto; border-bottom:1px solid #eee; padding-bottom:10px;">
                        <!-- Dynamically populated by JS -->
                    </div>
                    
                    <!-- Submit Form (only for logged-in users) -->
                    @auth
                    <form id="submitReviewForm" onsubmit="submitReview(event)">
                        @csrf
                        <input type="hidden" name="product_id" id="reviewProductId">
                        <h6 class="text-uppercase-tracking mb-3" style="font-size:0.75rem;"><i class="bi bi-star me-2"></i>Share Your Thoughts</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fs-8 text-muted text-uppercase-tracking d-block">Rating *</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="star-rating-input d-flex gap-1">
                                    <i class="bi bi-star-fill text-warning fs-5 cursor-pointer" data-rating="1" onclick="setRating(1)"></i>
                                    <i class="bi bi-star-fill text-warning fs-5 cursor-pointer" data-rating="2" onclick="setRating(2)"></i>
                                    <i class="bi bi-star-fill text-warning fs-5 cursor-pointer" data-rating="3" onclick="setRating(3)"></i>
                                    <i class="bi bi-star-fill text-warning fs-5 cursor-pointer" data-rating="4" onclick="setRating(4)"></i>
                                    <i class="bi bi-star-fill text-warning fs-5 cursor-pointer" data-rating="5" onclick="setRating(5)"></i>
                                </div>
                                <span id="reviewRatingText" class="badge bg-dark fw-normal rounded-0 text-uppercase-tracking fs-8 px-2 py-1">Excellent</span>
                            </div>
                            <input type="hidden" name="rating" id="reviewRating" value="5">
                        </div>
                        
                        <div class="mb-3" style="display: none;">
                            <textarea name="comment" id="reviewComment" class="form-control rounded-0 fs-7" rows="3" placeholder="Write your review here..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-luxury w-100 rounded-0 text-uppercase-tracking fs-8" id="submitReviewBtn">
                            <i class="bi bi-send me-1"></i> Submit Review
                        </button>
                    </form>
                    <div id="notPurchasedMsg" class="alert alert-light border text-center fs-8 mt-3 mb-0" style="display:none;">
                        <i class="bi bi-info-circle me-1"></i> You can only review products you have purchased.
                    </div>
                    @else
                    <div class="p-3 text-center bg-light border">
                        <p class="fs-8 text-muted mb-2">Please log in to submit a review.</p>
                        <a href="{{ route('login') }}" class="btn btn-sm btn-dark rounded-0 px-3 fs-8">Log In</a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setRating(rating) {
        document.getElementById('reviewRating').value = rating;
        const stars = document.querySelectorAll('.star-rating-input .bi');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('bi-star');
                star.classList.add('bi-star-fill', 'text-warning');
            } else {
                star.classList.remove('bi-star-fill', 'text-warning');
                star.classList.add('bi-star', 'text-muted');
            }
        });
        
        const textEl = document.getElementById('reviewRatingText');
        if (textEl) {
            const labels = ['Terrible', 'Poor', 'Average', 'Good', 'Excellent'];
            textEl.textContent = labels[rating - 1];
        }
    }

    let activeCategory = 'All';
    let humanBase64 = null;
    let selectedProductId = null;

    // Helper: Convert File to Base64
    function previewHumanImage(input) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('humanPreview').src = e.target.result;
            document.getElementById('humanPreview').classList.remove('d-none');
            document.getElementById('dropzoneDefault').classList.add('d-none');
            
            // Extract raw base64 string
            humanBase64 = e.target.result.split(',')[1];
        }
        reader.readAsDataURL(file);
    }

    // Set preset prompt
    function setPresetPrompt(preset) {
        document.getElementById('tryonPrompt').value = preset;
    }

    // Select Product for Virtual Try-on
    function selectForTryOn(name, url, category, id) {
        if (!userId) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('tryon', id);
            window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(currentUrl.toString());
            return;
        }
        selectedProductId = id;
        document.getElementById('garmentPreview').src = url;
        document.getElementById('garmentName').textContent = name;
        document.getElementById('garmentCategory').textContent = category;
        document.getElementById('garmentUrlInput').value = url;

        document.getElementById('garmentDetails').classList.remove('d-none');
        document.getElementById('garmentDefault').classList.add('d-none');
        
        // Unhide workspace
        const workspace = document.getElementById('tryonWorkspace');
        workspace.classList.remove('d-none');
        
        // Scroll smoothly to the workspace
        workspace.scrollIntoView({ behavior: 'smooth' });
        showNotification(`Garment "${name}" selected for try-on.`);
    }

    // Trigger HuggingFace Try-on API call
    async function triggerTryOn() {
        if (!userId) {
            window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(window.location.href);
            return;
        }
        if (!humanBase64) {
            showNotification("Please upload your portrait photo first.", "bg-warning");
            return;
        }
        
        const clothUrl = document.getElementById('humanPreview').src; // Check if garment url exists
        const garmentUrl = document.getElementById('garmentUrlInput').value;
        
        if (!garmentUrl) {
            showNotification("Please select a garment from the boutique catalog below.", "bg-warning");
            return;
        }

        const prompt = document.getElementById('tryonPrompt').value || "beautiful dress";

        // Show loading screen overlay
        showLoader("VirtualFit AI Processing", "Running VirtualFit AI try-on engine. This can take up to 2 minutes...");

        try {
            const response = await fetch('{{ url('/api') }}/tryon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    human_image: humanBase64,
                    cloth_image_url: garmentUrl,
                    description: prompt,
                    user_id: userId,
                    product_id: selectedProductId
                })
            });

            const data = await response.json();
            hideLoader();

            if (data.status === 'error') {
                showNotification("VirtualFit AI Error: " + data.message, "bg-danger");
                return;
            }

            // Priority: image_b64 (base64) > image (URL or base64) > result
            let resultSrc = '';
            if (data.image_b64) {
                // Direct base64 from Laravel — best for Flutter & Web
                resultSrc = data.image_b64.startsWith('data:') ? data.image_b64 : `data:image/png;base64,${data.image_b64}`;
            } else if (data.image) {
                resultSrc = data.image.startsWith('data:') ? data.image
                          : data.image.startsWith('http')  ? data.image
                          : `data:image/png;base64,${data.image}`;
            } else if (data.result) {
                resultSrc = data.result.startsWith('data:') ? data.result
                          : data.result.startsWith('http')  ? data.result
                          : `data:image/png;base64,${data.result}`;
            } else {
                showNotification("Try-on completed but image could not be extracted.", "bg-warning");
                console.log("Full API response:", data);
                return;
            }

            // Store globally for download
            window._tryonResultSrc = resultSrc;

            // Show result image
            const resultImg = document.getElementById('tryonResultImage');
            resultImg.src = resultSrc;
            document.getElementById('tryonResultSection').classList.remove('d-none');

            // Save to bag button
            document.getElementById('tryonAddBagBtn').onclick = function() {
                if (selectedProductId) addToCart(selectedProductId);
            };

            showNotification("✨ Your VirtualFit AI preview is ready!", "bg-success");
            document.getElementById('tryonResultSection').scrollIntoView({ behavior: 'smooth' });

        } catch (err) {
            hideLoader();
            showNotification("VirtualFit AI server error: " + err.message, "bg-danger");
        }
    }

    // Download result image properly (works for both URL and base64)
    function downloadResultImage() {
        const src = window._tryonResultSrc;
        if (!src) { showNotification('No image to download yet.', 'bg-warning'); return; }
        const link = document.createElement('a');
        link.download = 'virtualfit-ai-result.png';
        if (src.startsWith('data:')) {
            link.href = src;
        } else {
            // For URL — fetch and convert to blob
            fetch(src).then(r => r.blob()).then(blob => {
                link.href = URL.createObjectURL(blob);
                link.click();
            }).catch(() => { link.href = src; link.click(); });
            return;
        }
        link.click();
    }

    // Filter products dynamically
    function selectCategory(category, buttonEl) {
        activeCategory = category;
        
        // Update active class on pills
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.remove('active');
        });
        buttonEl.classList.add('active');
        
        filterProducts();
    }

    // Filter products
    function filterProducts() {
        const query = document.getElementById('productSearchInput').value.toLowerCase();
        const items = document.querySelectorAll('.product-card-item');
        let visibleCount = 0;
        
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            const category = item.getAttribute('data-category');
            
            const matchesQuery = name.includes(query);
            const matchesCategory = (activeCategory === 'All' || category === activeCategory);
            
            if (matchesQuery && matchesCategory) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        document.getElementById('resultsCount').textContent = `Showing ${visibleCount} products`;
    }

    let reviewsModalObj = null;
    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('productReviewsModal');
        if (modalEl) {
            reviewsModalObj = new bootstrap.Modal(modalEl);
        }

        // Auto-select tryon if URL has ?tryon=id
        const urlParams = new URLSearchParams(window.location.search);
        const tryonId = urlParams.get('tryon');
        if (tryonId && typeof userId !== 'undefined' && userId) {
            const productCard = document.querySelector(`.product-card-item[data-id="${tryonId}"]`);
            if (productCard) {
                const name = productCard.getAttribute('data-rawname');
                const url = productCard.getAttribute('data-url');
                const category = productCard.getAttribute('data-category');
                
                // Clear the query parameter so it doesn't stay in the URL
                window.history.replaceState({}, document.title, window.location.pathname);
                
                // Slight delay to ensure UI is ready
                setTimeout(() => {
                    selectForTryOn(name, url, category, tryonId);
                }, 500);
            }
        }
    });

    async function openReviewsModal(productId, productName) {
        document.getElementById('reviewModalProductName').textContent = productName;
        document.getElementById('reviewProductId').value = productId;
        
        const commentEl = document.getElementById('reviewComment');
        if (commentEl) commentEl.value = '';
        
        const container = document.getElementById('reviewsListContainer');
        container.innerHTML = `<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-dark" role="status"></div></div>`;
        
        if (reviewsModalObj) {
            reviewsModalObj.show();
        }
        
        try {
            const res = await fetch(`{{ url('/product') }}/${productId}/reviews`);
            const data = await res.json();
            
            if (data.status === 'success' && data.reviews.length > 0) {
                let html = '';
                data.reviews.forEach(r => {
                    const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
                    const date = new Date(r.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
                    html += `
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold fs-8 text-dark">${r.user ? r.user.username : 'Anonymous'}</span>
                                <span class="text-muted fs-9">${date}</span>
                            </div>
                            <div class="text-warning fs-9 mb-1">${stars}</div>
                            <p class="fs-8 mb-0 text-secondary text-wrap">${r.comment}</p>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = `<p class="text-muted fs-8 text-center py-4">No reviews yet for this product. Be the first to review!</p>`;
            }
            
            // Handle form visibility based on purchase history
            const reviewForm = document.getElementById('submitReviewForm');
            const notPurchasedMsg = document.getElementById('notPurchasedMsg');
            if (reviewForm) {
                if (data.has_purchased) {
                    reviewForm.style.display = 'block';
                    if (notPurchasedMsg) notPurchasedMsg.style.display = 'none';
                } else {
                    reviewForm.style.display = 'none';
                    if (notPurchasedMsg) notPurchasedMsg.style.display = 'block';
                }
            }
        } catch (err) {
            container.innerHTML = `<p class="text-danger fs-8 text-center py-4">Error loading reviews.</p>`;
        }
    }

    async function submitReview(event) {
        event.preventDefault();
        const form = event.target;
        const submitBtn = document.getElementById('submitReviewBtn');
        const productId = document.getElementById('reviewProductId').value;
        const productName = document.getElementById('reviewModalProductName').textContent;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Submitting...';
        
        try {
            const res = await fetch('{{ route('user.reviews.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    rating: document.getElementById('reviewRating').value
                })
            });
            
            const data = await res.json();
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Submit Review';
            
            if (res.ok) {
                showNotification("Review submitted successfully!", "bg-success");
                openReviewsModal(productId, productName);
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                showNotification(data.message || "Failed to submit review.", "bg-danger");
            }
        } catch (err) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Submit Review';
            showNotification("Request error occurred.", "bg-danger");
        }
    }

    async function deleteWardrobeItem(historyId, buttonEl) {
        if (!confirm('Are you sure you want to delete this try-on history from your wardrobe?')) {
            return;
        }
        
        buttonEl.disabled = true;
        buttonEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        try {
            const response = await fetch(`{{ url('/user/try-on-history') }}/${historyId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                showNotification("Item removed from your AI wardrobe.", "bg-success");
                const card = buttonEl.closest('.col-md-3');
                if (card) {
                    card.remove();
                }
                
                const row = document.querySelector('#wardrobePane .row');
                if (row && row.children.length === 0) {
                    row.innerHTML = `
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-magic display-6 d-block mb-3"></i>
                            Your AI Wardrobe is empty. Try on some clothes above!
                        </div>
                    `;
                }
            } else {
                buttonEl.disabled = false;
                buttonEl.innerHTML = '<i class="bi bi-trash"></i>';
                showNotification(data.message || "Failed to delete item.", "bg-danger");
            }
        } catch (err) {
            buttonEl.disabled = false;
            buttonEl.innerHTML = '<i class="bi bi-trash"></i>';
            showNotification("Request error occurred.", "bg-danger");
        }
    }
</script>
@endsection
