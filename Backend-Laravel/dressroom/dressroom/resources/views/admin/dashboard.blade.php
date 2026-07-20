@extends('layouts.app')

@section('title', ($setting->site_name ?? 'Virtual Dress Room') . ' | Admin Console')

@section('styles')
<style>
    body {
        background-color: var(--bg-surface);
    }
    
    .admin-wrapper {
        display: flex;
        min-height: calc(100vh - 70px);
    }

    .admin-sidebar {
        width: 250px;
        background: #ffffff;
        border-right: 1px solid rgba(0,0,0,0.08);
        padding: 2rem 0;
        flex-shrink: 0;
    }

    .admin-main {
        flex-grow: 1;
        padding: 2rem;
        overflow-x: hidden;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 2rem;
        color: #555;
        text-decoration: none;
        font-family: var(--font-body);
        font-weight: 500;
        font-size: 0.9rem;
        transition: var(--transition-smooth);
        border-left: 3px solid transparent;
    }

    .sidebar-link i {
        margin-right: 10px;
        font-size: 1.1rem;
    }

    .sidebar-link:hover, .sidebar-link.active {
        background: rgba(0,0,0,0.02);
        color: #000;
        border-left-color: #000;
    }

    .admin-card {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border-radius: 0;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        padding: 1.5rem;
        border: 1px solid rgba(0,0,0,0.05);
        background: #fafafa;
        text-align: center;
    }

    .stat-card h3 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .table-luxury {
        font-family: var(--font-body);
        font-size: 0.85rem;
    }

    .table-luxury th {
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.08em;
        color: #888;
        font-weight: 600;
        border-bottom-width: 1.5px;
        padding: 1rem;
    }

    .table-luxury td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom-color: rgba(0, 0, 0, 0.03);
    }
</style>
@endsection

@section('content')
<div class="admin-wrapper">
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="px-4 mb-4">
            <span class="text-uppercase-tracking text-muted fs-8">Admin Panel</span>
            <h5 class="font-editorial mt-1 mb-0">Control Console</h5>
        </div>
        
        <div class="nav flex-column nav-pills" id="admin-sidebar-tabs" role="tablist" aria-orientation="vertical">
            
            <button class="nav-link sidebar-link active rounded-0 text-start w-100 bg-transparent" id="v-pills-dashboard-tab" data-bs-toggle="pill" data-bs-target="#v-pills-dashboard" type="button" role="tab">
                <i class="bi bi-graph-up"></i> Dashboard
            </button>
            
            <button class="nav-link sidebar-link rounded-0 text-start w-100 bg-transparent" id="v-pills-users-tab" data-bs-toggle="pill" data-bs-target="#v-pills-users" type="button" role="tab">
                <i class="bi bi-people"></i> Users
            </button>
            
            <button class="nav-link sidebar-link rounded-0 text-start w-100 bg-transparent" id="v-pills-products-tab" data-bs-toggle="pill" data-bs-target="#v-pills-products" type="button" role="tab">
                <i class="bi bi-bag"></i> Products
            </button>

            <button class="nav-link sidebar-link rounded-0 text-start w-100 bg-transparent" id="v-pills-orders-tab" data-bs-toggle="pill" data-bs-target="#v-pills-orders" type="button" role="tab">
                <i class="bi bi-box-seam"></i> Orders
            </button>

            <button class="nav-link sidebar-link rounded-0 text-start w-100 bg-transparent" id="v-pills-tryon-tab" data-bs-toggle="pill" data-bs-target="#v-pills-tryon" type="button" role="tab">
                <i class="bi bi-magic"></i> Try-On History
            </button>

            <button class="nav-link sidebar-link rounded-0 text-start w-100 bg-transparent" id="v-pills-wishlist-tab" data-bs-toggle="pill" data-bs-target="#v-pills-wishlist" type="button" role="tab">
                <i class="bi bi-heart"></i> Wishlist ❤️
            </button>

            <button class="nav-link sidebar-link rounded-0 text-start w-100 bg-transparent" id="v-pills-cart-tab" data-bs-toggle="pill" data-bs-target="#v-pills-cart" type="button" role="tab">
                <i class="bi bi-cart"></i> Cart 🛒
            </button>

            <button class="nav-link sidebar-link rounded-0 text-start w-100 bg-transparent" id="v-pills-reviews-tab" data-bs-toggle="pill" data-bs-target="#v-pills-reviews" type="button" role="tab">
                <i class="bi bi-star"></i> Reviews
            </button>

            <button class="nav-link sidebar-link rounded-0 text-start w-100 bg-transparent" id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settings" type="button" role="tab">
                <i class="bi bi-gear"></i> Settings
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-main">
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-0 fs-7 mb-4 py-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="tab-content" id="v-pills-tabContent">
            
            <!-- Dashboard / Reports -->
            <div class="tab-pane fade show active" id="v-pills-dashboard" role="tabpanel">
                <h3 class="font-editorial mb-4">Analytics Overview</h3>
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h3>{{ $totalUsers }}</h3>
                            <span class="text-uppercase-tracking fs-8 text-muted">Total Users</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h3>{{ $totalOrders }}</h3>
                            <span class="text-uppercase-tracking fs-8 text-muted">Total Orders</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h3>{{ $products->count() }}</h3>
                            <span class="text-uppercase-tracking fs-8 text-muted">Products</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h3>{{ $tryOnHistories->count() }}</h3>
                            <span class="text-uppercase-tracking fs-8 text-muted">Try-Ons</span>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="admin-card">
                            <h5 class="mb-4 font-editorial">Most Wishlisted Products</h5>
                            <ul class="list-group list-group-flush">
                                @forelse($topWishlisted as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset(ltrim($item->image_url, '/')) }}" style="width:40px; height:40px; object-fit:cover;">
                                            <span>{{ $item->name }}</span>
                                        </div>
                                        <span class="badge bg-dark rounded-pill">{{ $item->wishlists_count }} ❤️</span>
                                    </li>
                                @empty
                                    <li class="list-group-item px-0 text-muted">No wishlist data available.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="admin-card">
                            <h5 class="mb-4 font-editorial">Most Added to Cart</h5>
                            <ul class="list-group list-group-flush">
                                @forelse($topCartItems as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset(ltrim($item->image_url, '/')) }}" style="width:40px; height:40px; object-fit:cover;">
                                            <span>{{ $item->name }}</span>
                                        </div>
                                        <span class="badge bg-dark rounded-pill">{{ $item->carts_count }} 🛒</span>
                                    </li>
                                @empty
                                    <li class="list-group-item px-0 text-muted">No cart data available.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users -->
            <div class="tab-pane fade" id="v-pills-users" role="tabpanel">
                <div class="admin-card">
                    <h3 class="font-editorial mb-4">Registered Users</h3>
                    <div class="table-responsive">
                        <table class="table table-luxury">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>#{{ $user->id }}</td>
                                    <td class="fw-bold">{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge bg-{{ $user->role == 'admin' ? 'dark' : 'secondary' }} rounded-0">{{ strtoupper($user->role) }}</span></td>
                                    <td>{{ $user->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="tab-pane fade" id="v-pills-products" role="tabpanel">
                <div class="admin-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h3 class="font-editorial mb-0">Garments Catalog</h3>
                        <button class="btn btn-luxury" type="button" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Product
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-luxury align-middle">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 80px;">Garment</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Price</th>
                                    <th scope="col" class="text-end" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="adminProductsTableBody">
                                @forelse($products as $product)
                                    <tr id="product-row-{{ $product->id }}">
                                        <td>
                                            <img src="{{ asset(ltrim($product->image_url, '/')) }}" class="rounded" style="width:45px;height:60px;object-fit:cover;">
                                        </td>
                                        <td class="fw-bold">{{ $product->name }}</td>
                                        <td><span class="badge bg-light text-dark border rounded-0 fs-9 text-uppercase">{{ $product->category ?? 'Garment' }}</span></td>
                                        <td class="fw-semibold">${{ number_format($product->price, 2) }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-dark rounded-0 me-1" onclick="openEditModal({{ $product->id }}, '{{ $product->name }}', '{{ $product->category }}', {{ $product->price }}, '{{ addslashes($product->description) }}', '{{ $product->image_url }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger rounded-0" onclick="deleteProduct({{ $product->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <!-- Orders -->
            <div class="tab-pane fade" id="v-pills-orders" role="tabpanel">
                <div class="admin-card">
                    <h3 class="font-editorial mb-4">Customer Orders</h3>
                    <div class="table-responsive">
                        <table class="table table-luxury">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Total Amount</th>
                                    <th>Payment Status</th>
                                    <th>Order Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr id="order-row-{{ $order->id }}">
                                    <td class="fw-bold">#ORD-{{ sprintf('%05d', $order->id) }}</td>
                                    <td>{{ $order->user->username ?? 'Unknown User' }}</td>
                                    <td class="fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                    <td><span class="badge bg-success rounded-0">{{ $order->payment_status }}</span></td>
                                    <td>
                                        @php
                                            $statusColor = match(strtolower($order->order_status)) {
                                                'processing' => 'bg-warning text-dark',
                                                'shipped'    => 'bg-info text-dark',
                                                'delivered'  => 'bg-success',
                                                'cancelled'  => 'bg-danger',
                                                default      => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge rounded-0 {{ $statusColor }}" id="status-badge-{{ $order->id }}">{{ $order->order_status }}</span>
                                    </td>
                                    <td>{{ $order->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>
                                        <select class="form-select form-select-sm rounded-0" style="min-width:130px;" onchange="updateOrderStatus({{ $order->id }}, this.value)">
                                            <option value="Processing" {{ $order->order_status == 'Processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="Shipped"    {{ $order->order_status == 'Shipped'    ? 'selected' : '' }}>Shipped</option>
                                            <option value="Delivered"  {{ $order->order_status == 'Delivered'  ? 'selected' : '' }}>Delivered</option>
                                            <option value="Cancelled"  {{ $order->order_status == 'Cancelled'  ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No orders placed yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Try-On History -->
            <div class="tab-pane fade" id="v-pills-tryon" role="tabpanel">
                <div class="admin-card">
                    <h3 class="font-editorial mb-4">AI Try-On Usage Log</h3>
                    <div class="table-responsive">
                        <table class="table table-luxury align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Garment</th>
                                    <th>Garment Image</th>
                                    <th>Human Photo</th>
                                    <th>Result</th>
                                    <th>Prompt</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tryOnHistories as $history)
                                @php
                                    $productImgUrl = $history->product?->image_url
                                        ? (str_starts_with($history->product->image_url, 'http')
                                            ? $history->product->image_url
                                            : asset(ltrim($history->product->image_url, '/')))
                                        : null;

                                    $humanUrl = $history->human_image_url
                                        ? url('/try-on-image/' . basename($history->human_image_url))
                                        : null;

                                    $resultUrl = $history->result_image_url
                                        ? url('/try-on-image/' . basename($history->result_image_url))
                                        : null;
                                @endphp
                                <tr>
                                    <td>#{{ $history->id }}</td>
                                    <td>{{ $history->user->username ?? 'Unknown' }}</td>
                                    <td>{{ $history->product->name ?? 'Deleted Garment' }}</td>

                                    {{-- Garment/Product Image --}}
                                    <td>
                                        @if($productImgUrl)
                                            <img src="{{ $productImgUrl }}"
                                                 style="height:55px;width:45px;object-fit:cover;border-radius:4px;"
                                                 onerror="this.src='https://placehold.co/45x55?text=N/A'">
                                        @else
                                            <span class="text-muted fs-9">—</span>
                                        @endif
                                    </td>

                                    {{-- Human Image --}}
                                    <td>
                                        @if($humanUrl)
                                            <img src="{{ $humanUrl }}"
                                                 style="height:55px;width:45px;object-fit:cover;border-radius:4px;"
                                                 onerror="this.src='https://placehold.co/45x55?text=No+Img'">
                                        @else
                                            <span class="text-muted fs-9">—</span>
                                        @endif
                                    </td>

                                    {{-- Result Image --}}
                                    <td>
                                        @if($resultUrl)
                                            <img src="{{ $resultUrl }}"
                                                 style="height:55px;width:45px;object-fit:cover;border-radius:4px;"
                                                 onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                                            <span class="badge bg-warning text-dark fs-9" style="display:none;">Not saved</span>
                                        @else
                                            <span class="badge bg-secondary fs-9">No result</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="text-muted fs-9 fst-italic">
                                            {{ $history->ai_prompt_used ?? '—' }}
                                        </span>
                                    </td>
                                    <td>{{ $history->created_at?->format('M d, g:i A') ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No Try-Ons generated yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Wishlist -->
            <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel">
                <div class="admin-card">
                    <h3 class="font-editorial mb-4">User Wishlists</h3>
                    <div class="table-responsive">
                        <table class="table table-luxury align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Product Name</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wishlists as $wishlist)
                                <tr>
                                    <td>#{{ $wishlist->id }}</td>
                                    <td class="fw-bold">{{ $wishlist->user->username ?? 'Unknown User' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($wishlist->product)
                                                <img src="{{ asset(ltrim($wishlist->product->image_url, '/')) }}" style="width:30px;height:30px;object-fit:cover;">
                                                {{ $wishlist->product->name }}
                                            @else
                                                <span class="text-danger">Product Deleted</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $wishlist->created_at?->format('M d, Y - g:i A') ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No items in any wishlist.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Cart -->
            <div class="tab-pane fade" id="v-pills-cart" role="tabpanel">
                <div class="admin-card">
                    <h3 class="font-editorial mb-4">Active & Past Carts</h3>
                    <div class="table-responsive">
                        <table class="table table-luxury align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($carts as $cart)
                                <tr>
                                    <td>#{{ $cart->id }}</td>
                                    <td class="fw-bold">{{ $cart->user->username ?? 'Unknown User' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($cart->product)
                                                <img src="{{ asset(ltrim($cart->product->image_url, '/')) }}" style="width:30px;height:30px;object-fit:cover;">
                                                {{ $cart->product->name }}
                                            @else
                                                <span class="text-danger">Product Deleted</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $cart->quantity }}</td>
                                    <td>
                                        @if($cart->status == 'Active')
                                            <span class="badge bg-success rounded-0">ACTIVE</span>
                                        @elseif($cart->status == 'Ordered')
                                            <span class="badge bg-primary rounded-0">ORDERED</span>
                                        @else
                                            <span class="badge bg-secondary rounded-0">REMOVED</span>
                                        @endif
                                    </td>
                                    <td>{{ $cart->created_at?->format('M d, Y - g:i A') ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No cart records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <div class="tab-pane fade" id="v-pills-reviews" role="tabpanel">
                <div class="admin-card">
                    <h3 class="font-editorial mb-4">Product Reviews</h3>
                    
                    <div class="table-responsive">
                        <table class="table table-luxury align-middle">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Garment</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="adminReviewsTableBody">
                                @forelse($reviews ?? [] as $review)
                                    <tr id="review-row-{{ $review->id }}">
                                        <td class="fw-bold">{{ $review->user->username ?? 'Unknown User' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($review->product)
                                                    <img src="{{ asset(ltrim($review->product->image_url, '/')) }}" style="width:30px;height:40px;object-fit:cover;border-radius:4px;">
                                                    {{ $review->product->name }}
                                                @else
                                                    <span class="text-danger">Deleted Garment</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-warning">
                                                {!! str_repeat('★', $review->rating) !!}{!! str_repeat('☆', 5 - $review->rating) !!}
                                            </span>
                                        </td>
                                        <td class="text-wrap" style="max-width: 300px;">{{ $review->comment }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteReview({{ $review->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No reviews submitted yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="tab-pane fade" id="v-pills-settings" role="tabpanel">
                <div class="admin-card">
                    <h3 class="font-editorial mb-4">Site Customization</h3>
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="text-uppercase-tracking text-muted mb-2 d-block">Site Name</label>
                                    <input type="text" name="site_name" class="form-control" style="border-radius:0;" value="{{ old('site_name', $setting->site_name ?? 'Virtual Dress Room') }}" required>
                                </div>
                                <div class="mb-4">
                                    <label class="text-uppercase-tracking text-muted mb-2 d-block">Site Tagline</label>
                                    <input type="text" name="tagline" class="form-control" style="border-radius:0;" value="{{ old('tagline', $setting->tagline ?? '') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="text-uppercase-tracking text-muted mb-2 d-block">Landing Welcome Header</label>
                                    <input type="text" name="welcome_title" class="form-control" style="border-radius:0;" value="{{ old('welcome_title', $setting->welcome_title ?? '') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="text-uppercase-tracking text-muted mb-2 d-block">Landing Welcome Description</label>
                                    <textarea name="welcome_description" class="form-control" style="border-radius:0;" rows="4">{{ old('welcome_description', $setting->welcome_description ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="text-uppercase-tracking text-muted mb-2 d-block">Editorial Small Badge Text</label>
                                    <input type="text" name="editorial_small_text" class="form-control" style="border-radius:0;" value="{{ old('editorial_small_text', $setting->editorial_small_text ?? '') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="text-uppercase-tracking text-muted mb-2 d-block">Editorial Heading</label>
                                    <input type="text" name="editorial_heading" class="form-control" style="border-radius:0;" value="{{ old('editorial_heading', $setting->editorial_heading ?? '') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="text-uppercase-tracking text-muted mb-2 d-block">Editorial Image URL</label>
                                    <input type="text" name="editorial_image_url" class="form-control" style="border-radius:0;" value="{{ old('editorial_image_url', $setting->editorial_image ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-4 mt-2 text-end">
                            <button type="submit" class="btn btn-luxury px-5">Save Configuration</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL: ADD PRODUCT -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title font-editorial">Add Garment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="text-uppercase-tracking text-muted fs-8 mb-1">Name</label>
                        <input type="text" name="name" class="form-control" style="border-radius:0;" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-uppercase-tracking text-muted fs-8 mb-1">Category</label>
                            <input type="text" name="category" class="form-control" style="border-radius:0;" required>
                        </div>
                        <div class="col-6">
                            <label class="text-uppercase-tracking text-muted fs-8 mb-1">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" style="border-radius:0;" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-uppercase-tracking text-muted fs-8 mb-1">Description</label>
                        <textarea name="description" class="form-control" style="border-radius:0;" rows="3"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="text-uppercase-tracking text-muted fs-8 mb-1">Image File</label>
                        <input type="file" name="image" class="form-control" style="border-radius:0;" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-luxury w-100">Publish to Catalog</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: EDIT PRODUCT -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title font-editorial">Edit Garment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editProductForm" enctype="multipart/form-data">
                    <input type="hidden" id="editProductId" name="id">
                    <div class="mb-3">
                        <label class="text-uppercase-tracking text-muted fs-8 mb-1">Name</label>
                        <input type="text" id="editProductName" name="name" class="form-control" style="border-radius:0;" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-uppercase-tracking text-muted fs-8 mb-1">Category</label>
                            <input type="text" id="editProductCategory" name="category" class="form-control" style="border-radius:0;" required>
                        </div>
                        <div class="col-6">
                            <label class="text-uppercase-tracking text-muted fs-8 mb-1">Price</label>
                            <input type="number" step="0.01" id="editProductPrice" name="price" class="form-control" style="border-radius:0;" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-uppercase-tracking text-muted fs-8 mb-1">Description</label>
                        <textarea id="editProductDescription" name="description" class="form-control" style="border-radius:0;" rows="3"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="text-uppercase-tracking text-muted fs-8 mb-1">Image (Optional)</label>
                        <input type="file" name="image" class="form-control" style="border-radius:0;" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-luxury w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let editModalObj = null;
    document.addEventListener('DOMContentLoaded', () => {
        editModalObj = new bootstrap.Modal(document.getElementById('editProductModal'));
    });

    function openEditModal(id, name, category, price, description, imageUrl) {
        document.getElementById('editProductId').value = id;
        document.getElementById('editProductName').value = name;
        document.getElementById('editProductCategory').value = category;
        document.getElementById('editProductPrice').value = price;
        document.getElementById('editProductDescription').value = description;
        editModalObj.show();
    }

    document.getElementById('addProductForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        showLoader("Publishing Garment", "Saving...");
        const formData = new FormData(this);
        try {
            const res = await fetch(`{{ url('/api') }}/products`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: formData
            });
            const data = await res.json();
            if (data.status === 'success') window.location.reload();
            else showNotification("Failed.", "bg-danger");
        } catch (err) { hideLoader(); }
    });

    document.getElementById('editProductForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('editProductId').value;
        showLoader("Updating Garment", "Applying...");
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        try {
            const res = await fetch(`{{ url('/api') }}/products/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: formData
            });
            const data = await res.json();
            if (data.status === 'success') window.location.reload();
            else showNotification("Failed.", "bg-danger");
        } catch (err) { hideLoader(); }
    });

    async function deleteProduct(id) {
        if (!confirm("Are you sure?")) return;
        showLoader("Removing Garment", "Purging...");
        try {
            const res = await fetch(`{{ url('/api') }}/products/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            });
            const data = await res.json();
            if (data.status === 'success') document.getElementById(`product-row-${id}`).remove();
            hideLoader();
        } catch (err) { hideLoader(); }
    }

    async function deleteReview(id) {
        if (!confirm("Are you sure you want to delete this review?")) return;
        showLoader("Removing Review", "Deleting...");
        try {
            const res = await fetch(`{{ url('/admin/reviews') }}/${id}`, {
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
            const data = await res.json();
            if (data.status === 'success') {
                document.getElementById(`review-row-${id}`).remove();
                showNotification("Review deleted successfully.", "bg-success");
            } else {
                showNotification(data.message || "Failed.", "bg-danger");
            }
            hideLoader();
        } catch (err) {
            hideLoader();
            showNotification("Error occurred.", "bg-danger");
        }
    }

    async function updateOrderStatus(orderId, newStatus) {
        try {
            const res = await fetch(`{{ url('/admin/orders') }}/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            });
            const data = await res.json();
            if (data.status === 'success') {
                const badge = document.getElementById(`status-badge-${orderId}`);
                if (badge) {
                    badge.textContent = newStatus;
                    badge.className = 'badge rounded-0 ' + {
                        'Processing': 'bg-warning text-dark',
                        'Shipped':    'bg-info text-dark',
                        'Delivered':  'bg-success',
                        'Cancelled':  'bg-danger'
                    }[newStatus] || 'bg-secondary';
                }
                showNotification(`Order status updated to ${newStatus}`, 'bg-success');
            } else {
                showNotification(data.message || 'Failed to update status.', 'bg-danger');
            }
        } catch (err) {
            showNotification('Error occurred while updating order status.', 'bg-danger');
        }
    }
</script>
@endsection
