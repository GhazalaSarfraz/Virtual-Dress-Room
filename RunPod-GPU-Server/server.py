import sys
sys.path.append('/workspace/IDM-VTON')
sys.path.append('/workspace/IDM-VTON/gradio_demo')

import os, io, base64, traceback
import torch
import numpy as np
from PIL import Image
from torchvision import transforms
from torchvision.transforms.functional import to_pil_image
from fastapi import FastAPI, File, UploadFile, Form
from fastapi.responses import JSONResponse
import uvicorn

# This is the reference script deployed to RunPod Server for RTX 3090 Inference.
app = FastAPI()

@app.get("/")
def root():
    return {"status": "IDM-VTON FastAPI Running!"}

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=7860)
