"""
VirtualFit AI — FastAPI Server for IDM-VTON
============================================
Deploy this on RunPod inside /workspace/IDM-VTON/
Run: uvicorn app:app --host 0.0.0.0 --port 8000
"""

import os
import sys
import base64
import io
import time
import traceback
from pathlib import Path

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from PIL import Image
import torch
import uvicorn

# ─── Add IDM-VTON to path ─────────────────────────────────────────────────────
IDMVTON_PATH = Path(__file__).parent
sys.path.insert(0, str(IDMVTON_PATH))

# ─── App Setup ────────────────────────────────────────────────────────────────
app = FastAPI(
    title="VirtualFit AI",
    description="IDM-VTON Virtual Try-On API — powered by RunPod GPU",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ─── Global pipeline (loaded once at startup) ─────────────────────────────────
pipeline      = None
unet_encoder  = None
DEVICE        = "cuda" if torch.cuda.is_available() else "cpu"


# ─── Request / Response Schemas ───────────────────────────────────────────────
class TryOnRequest(BaseModel):
    human_image: str        # base64 encoded human photo
    cloth_image: str        # base64 encoded cloth/garment photo
    description: str = "beautiful dress"


class TryOnResponse(BaseModel):
    status: str
    image: str              # base64 encoded result image
    message: str = ""


# ─── Helper: base64 → PIL Image ───────────────────────────────────────────────
def b64_to_pil(b64_string: str) -> Image.Image:
    if "," in b64_string:
        b64_string = b64_string.split(",")[1]
    image_bytes = base64.b64decode(b64_string)
    return Image.open(io.BytesIO(image_bytes)).convert("RGB")


# ─── Helper: PIL Image → base64 ───────────────────────────────────────────────
def pil_to_b64(image: Image.Image, fmt: str = "PNG") -> str:
    buffer = io.BytesIO()
    image.save(buffer, format=fmt)
    buffer.seek(0)
    return base64.b64encode(buffer.read()).decode("utf-8")


# ─── Load IDM-VTON Pipeline ───────────────────────────────────────────────────
def load_pipeline():
    global pipeline, unet_encoder
    print(f"[VirtualFit AI] Loading IDM-VTON pipeline on {DEVICE}...")
    try:
        from src.tryon_pipeline import StableDiffusionXLInpaintPipeline as TryonPipeline
        from src.unet_hacked_tryon import UNet2DConditionModel
        from src.unet_hacked_garmnet import UNet2DConditionModel as GarmentUNet
        from transformers import CLIPImageProcessor, CLIPVisionModelWithProjection
        from diffusers import AutoencoderKL

        base_path = str(IDMVTON_PATH)
        unet_path = os.path.join(base_path, "ckpt", "dresses")

        unet = UNet2DConditionModel.from_pretrained(
            unet_path, subfolder="unet",
            torch_dtype=torch.float16
        ).to(DEVICE)
        unet.requires_grad_(False)

        image_encoder = CLIPVisionModelWithProjection.from_pretrained(
            base_path, subfolder="ckpt/image_encoder",
            torch_dtype=torch.float16
        ).to(DEVICE)

        vae = AutoencoderKL.from_pretrained(
            "madebyollin/sdxl-vae-fp16-fix",
            torch_dtype=torch.float16
        ).to(DEVICE)

        pipe = TryonPipeline.from_pretrained(
            "yisol/IDM-VTON",
            unet=unet,
            vae=vae,
            feature_extractor=CLIPImageProcessor(),
            image_encoder=image_encoder,
            torch_dtype=torch.float16
        ).to(DEVICE)

        pipe.unet_encoder = GarmentUNet.from_pretrained(
            unet_path, subfolder="unet_encoder",
            torch_dtype=torch.float16
        ).to(DEVICE)

        pipeline = pipe
        print("[VirtualFit AI] Pipeline loaded successfully!")

    except Exception as e:
        print(f"[VirtualFit AI] Pipeline load failed: {e}")
        traceback.print_exc()
        pipeline = None


# ─── Run IDM-VTON Inference ───────────────────────────────────────────────────
def run_inference(human_img: Image.Image, cloth_img: Image.Image, prompt: str) -> Image.Image:
    human_img = human_img.resize((768, 1024))
    cloth_img = cloth_img.resize((768, 1024))

    full_prompt = f"a photo of a model wearing {prompt}, high quality, realistic"
    neg_prompt  = "monochrome, lowres, bad anatomy, worst quality, low quality"

    with torch.no_grad():
        result = pipeline(
            prompt=full_prompt,
            negative_prompt=neg_prompt,
            image=human_img,
            condition_image=cloth_img,
            num_inference_steps=30,
            guidance_scale=2.0,
            height=1024,
            width=768,
        )

    return result.images[0]


# ─── Routes ───────────────────────────────────────────────────────────────────

@app.get("/")
def root():
    return {
        "service":        "VirtualFit AI",
        "model":          "IDM-VTON",
        "device":         DEVICE,
        "pipeline_ready": pipeline is not None,
        "status":         "running"
    }


@app.get("/health")
def health():
    gpu_name = torch.cuda.get_device_name(0) if torch.cuda.is_available() else "CPU only"
    return {
        "status":         "ok",
        "gpu":            gpu_name,
        "pipeline_ready": pipeline is not None
    }


@app.post("/tryon", response_model=TryOnResponse)
async def virtual_tryon(req: TryOnRequest):
    if pipeline is None:
        raise HTTPException(
            status_code=503,
            detail="VirtualFit AI is warming up. Please retry in a moment."
        )

    try:
        start = time.time()

        human_img  = b64_to_pil(req.human_image)
        cloth_img  = b64_to_pil(req.cloth_image)

        print(f"[VirtualFit AI] Inference started — prompt: '{req.description}'")
        result_img = run_inference(human_img, cloth_img, req.description)
        result_b64 = pil_to_b64(result_img)

        elapsed = round(time.time() - start, 2)
        print(f"[VirtualFit AI] Done in {elapsed}s")

        return TryOnResponse(
            status="success",
            image=result_b64,
            message=f"Generated in {elapsed}s"
        )

    except Exception as e:
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=f"Inference error: {str(e)}")


# ─── Startup ──────────────────────────────────────────────────────────────────
@app.on_event("startup")
async def startup_event():
    load_pipeline()


# ─── Entry Point ──────────────────────────────────────────────────────────────
if __name__ == "__main__":
    uvicorn.run("app:app", host="0.0.0.0", port=8000, reload=False, workers=1)
