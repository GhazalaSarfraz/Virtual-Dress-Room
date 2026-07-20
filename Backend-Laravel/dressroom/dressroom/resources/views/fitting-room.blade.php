@extends('layouts.app')

@section('title', ($setting->site_name ?? 'Virtual Dress Room') . ' | Virtual Fitting Suite')

@section('styles')
<style>
    body {
        background-color: var(--bg-surface);
    }

    .fitting-suite-card {
        background: #ffffff;
        border: none;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.04);
        padding: 3rem;
    }

    .tryon-dropzone {
        border: 2px dashed rgba(0, 0, 0, 0.15);
        background-color: #ffffff;
        min-height: 280px;
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
        height: 280px;
        object-fit: cover;
        border-radius: 4px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .garment-select-card {
        cursor: pointer;
        transition: var(--transition-smooth);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .garment-select-card img {
        height: 140px;
        object-fit: cover;
    }

    .garment-select-card.active {
        border-color: var(--brand-primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .prompt-preset-badge {
        cursor: pointer;
        transition: var(--transition-smooth);
        border-radius: 0;
        font-size: 0.7rem;
        padding: 0.5rem 0.8rem;
    }

    .prompt-preset-badge:hover {
        background-color: var(--brand-accent) !important;
        color: #000000 !important;
    }

    .scrolling-wrapper {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 1rem;
        gap: 1rem;
        -webkit-overflow-scrolling: touch;
    }

    .scrolling-wrapper::-webkit-scrollbar {
        height: 6px;
    }

    .scrolling-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .scrolling-wrapper::-webkit-scrollbar-thumb {
        background: #d1d1d1;
    }
</style>
@endsection

@section('content')
<!-- Fitting Room Banner -->
<section class="py-5 bg-white border-bottom">
    <div class="container text-center py-3">
        <span class="text-uppercase-tracking text-muted"> VirtualFit  Suite</span>
        <h1 class="display-4 font-editorial mt-2 mb-3">AI Fitting Room</h1>
        <p class="text-muted fs-7 mx-auto" style="max-width: 600px;">Fit clothes onto your portrait using our high-fidelity generative AI model. Create perfect couture visuals.</p>
    </div>
</section>

<div class="container py-5">
    <div class="fitting-suite-card">
        
        <div class="row g-5">
            <!-- 1. Human Portrait Column -->
            <div class="col-lg-4">
                <h4 class="font-editorial mb-4">1. Portrait Photo</h4>
                
                <input type="file" id="humanImageUpload" accept="image/*" class="d-none" onchange="previewHuman(this)">
                
                <div class="tryon-dropzone mb-3" onclick="document.getElementById('humanImageUpload').click()">
                    <div id="dropzoneText">
                        <i class="bi bi-person-bounding-box display-5 text-muted mb-3 d-block"></i>
                        <h6 class="fw-bold fs-7 mb-1 text-uppercase-tracking">Upload Portrait</h6>
                        <p class="text-muted fs-8 mb-0">Drag and drop image or click to browse</p>
                    </div>
                    <img id="humanPreview" class="workspace-preview-img d-none" alt="Human Portrait Preview">
                </div>
                
                <p class="text-muted fs-8"><i class="bi bi-info-circle me-1"></i> For best results, use a high-quality portrait photo with solid lighting and front-facing posture.</p>
            </div>
            
            <!-- 2. Catalog selection Column -->
            <div class="col-lg-4">
                <h4 class="font-editorial mb-4">2. Select Garment</h4>
                
                <!-- Chosen Garment details -->
                <div class="p-3 bg-light border mb-4 text-center" id="chosenGarmentBlock">
                    <div id="noGarmentMessage">
                        <i class="bi bi-tag fs-3 text-muted mb-2 d-block"></i>
                        <p class="fs-8 text-muted mb-0">Choose a garment from the closet list below</p>
                    </div>
                    <div id="garmentInfo" class="d-none">
                        <img id="chosenGarmentImg" class="rounded mb-2" src="" alt="Selected garment" style="height: 120px; object-fit: cover;">
                        <h6 id="chosenGarmentName" class="fw-bold fs-7 mb-1 text-truncate">Garment Name</h6>
                        <span id="chosenGarmentCategory" class="badge bg-dark rounded-0 fs-9 text-uppercase mb-0">Category</span>
                        <input type="hidden" id="chosenGarmentUrl">
                    </div>
                </div>

                <label class="text-uppercase-tracking text-muted fs-8 mb-2 d-block">Store Closets</label>
                <!-- Horizontal list of selectable items -->
                <div class="scrolling-wrapper">
                    @forelse($products as $product)
                        <div class="card garment-select-card rounded-0 flex-shrink-0" style="width: 110px;" id="product-card-{{ $product->id }}"
                             onclick="selectGarment({{ $product->id }}, '{{ $product->name }}', '{{ $product->image_url }}', '{{ $product->category ?? 'Dress' }}')">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="card-img-top rounded-0">
                            <div class="card-body p-2 text-center bg-white">
                                <div class="fs-9 text-truncate fw-bold">{{ $product->name }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted fs-8 py-3 text-center w-100">Boutique catalog is empty.</div>
                    @endforelse
                </div>
            </div>
            
            <!-- 3. AI Custom settings Column -->
            <div class="col-lg-4 border-start-lg">
                <h4 class="font-editorial mb-4">3. AI Fitting Parameters</h4>
                
                <div class="mb-4">
                    <label for="aiPrompt" class="text-uppercase-tracking text-muted fs-8 mb-2 d-block">Garment Sizing Prompt</label>
                    <textarea id="aiPrompt" class="form-control rounded-0 fs-7" rows="3" placeholder="Specify custom styling, background scene or size details (e.g. elegant evening dress, standing in a minimalistic studio)..."></textarea>
                </div>

                <div class="mb-5">
                    <label class="text-uppercase-tracking text-muted fs-8 mb-2 d-block">Popular preset styling</label>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border prompt-preset-badge" onclick="applyPreset('beautiful dress, fitting loosely')">Casual Loose</span>
                        <span class="badge bg-light text-dark border prompt-preset-badge" onclick="applyPreset('formal fitting suite, high-end studio lighting')">Formal Studio</span>
                        <span class="badge bg-light text-dark border prompt-preset-badge" onclick="applyPreset('haute couture dress, minimalist architectural background')">Couture Editorial</span>
                        <span class="badge bg-light text-dark border prompt-preset-badge" onclick="applyPreset('elegant attire, outdoor street fashion')">Urban Chic</span>
                    </div>
                </div>
                
                <button class="btn btn-luxury w-100 py-3 text-uppercase-tracking" onclick="generateTryOn()">
                    <i class="bi bi-stars me-2"></i> Virtual Couture
                </button>
            </div>
        </div>

        <!-- Try-on generation Result Section -->
        <div class="mt-5 pt-5 border-top d-none" id="tryonResultContainer">
            <h3 class="font-editorial mb-4 text-center">Virtual Fitting Output</h3>
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="border p-2 bg-white mb-3 shadow-sm" id="resultImageContainer" style="display: none; min-height: 400px;">
                        <img id="resultImage" class="img-fluid" alt="Virtual AI Fitting Result" style="max-height: 550px; object-fit: contain;">
                    </div>
                    <div class="d-flex gap-3 justify-content-center">
                        <a id="downloadResultBtn" href="#" download="aura-virtual-fitting.png" class="btn btn-luxury px-4">
                            <i class="bi bi-download me-2"></i> Download Image
                        </a>
                        <button id="addGarmentBagBtn" class="btn btn-luxury-outline px-4">
                            <i class="bi bi-bag-check-fill me-2"></i> Save Garment to Bag
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    let humanBase64Data = null;
    let currentProductId = null;

    function previewHuman(input) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('humanPreview').src = e.target.result;
            document.getElementById('humanPreview').classList.remove('d-none');
            document.getElementById('dropzoneText').classList.add('d-none');
            humanBase64Data = e.target.result.split(',')[1];
        };
        reader.readAsDataURL(file);
    }

    function selectGarment(id, name, url, category) {
        currentProductId = id;
        document.getElementById('chosenGarmentImg').src = url;
        document.getElementById('chosenGarmentName').textContent = name;
        document.getElementById('chosenGarmentCategory').textContent = category;
        document.getElementById('chosenGarmentUrl').value = url;

        document.getElementById('garmentInfo').classList.remove('d-none');
        document.getElementById('noGarmentMessage').classList.add('d-none');

        // Mark active border
        document.querySelectorAll('.garment-select-card').forEach(card => {
            card.classList.remove('active');
        });
        document.getElementById(`product-card-${id}`).classList.add('active');

        showNotification(`Garment "${name}" loaded.`);
    }

    function applyPreset(prompt) {
        document.getElementById('aiPrompt').value = prompt;
    }

    async function generateTryOn() {
        if (!humanBase64Data) {
            showNotification("Please upload your portrait photo first.", "bg-warning");
            return;
        }

        const garmentUrl = document.getElementById('chosenGarmentUrl').value;
        if (!garmentUrl) {
            showNotification("Please select a garment from the closets list.", "bg-warning");
            return;
        }

        const prompt = document.getElementById('aiPrompt').value || "beautiful dress";

        showLoader("Virtual Couture Processing", "Generating virtual preview. This leverages high performance GPU containers and takes 1-2 minutes...");

        try {
            const response = await fetch('/api/tryon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    human_image: humanBase64Data,
                    cloth_image_url: garmentUrl,
                    description: prompt
                })
            });

            const data = await response.json();
            hideLoader();

            if (data.status === 'error') {
                showNotification("Generation Error: " + data.message, "bg-danger");
                return;
            }

            // Extract generated base64 / URL
            let imageSrc = '';
            if (data.image) {
                imageSrc = data.image.startsWith('http') ? data.image : `data:image/png;base64,${data.image}`;
            } else if (data.result) {
                imageSrc = data.result.startsWith('http') ? data.result : `data:image/png;base64,${data.result}`;
            } else {
                const keys = Object.keys(data);
                if (keys.length > 0 && typeof data[keys[0]] === 'string') {
                    const firstVal = data[keys[0]];
                    imageSrc = firstVal.startsWith('http') ? firstVal : `data:image/png;base64,${firstVal}`;
                } else {
                    showNotification("Try-on completed, but failed to render outcome image.", "bg-warning");
                    return;
                }
            }

            document.getElementById('resultImage').src = imageSrc;
            document.getElementById('downloadResultBtn').href = imageSrc;
            document.getElementById('tryonResultContainer').classList.remove('d-none');

            document.getElementById('addGarmentBagBtn').onclick = function() {
                if (currentProductId) {
                    addToCart(currentProductId);
                }
            };

            showNotification("Couture virtual try-on generated successfully!", "bg-success");
            document.getElementById('tryonResultContainer').scrollIntoView({ behavior: 'smooth' });

        } catch (err) {
            hideLoader();
            showNotification("Virtual AI server connection failed: " + err.message, "bg-danger");
        }
    }
</script>
@endsection