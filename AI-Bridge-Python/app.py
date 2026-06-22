from flask import Flask, request, jsonify, send_file
from werkzeug.middleware.proxy_fix import ProxyFix
import os
import time
import random
import base64
import urllib.request
import traceback
import shutil
import requests

app = Flask(__name__)

app.wsgi_app = ProxyFix(
    app.wsgi_app,
    x_for=1,
    x_proto=1,
    x_host=1,
    x_prefix=1
)

os.makedirs("uploads", exist_ok=True)
os.makedirs("results", exist_ok=True)

# RunPod FastAPI Server - Permanent URL (never expires)
RUNPOD_API_URL = "https://4a6iw76624t7v5-7860.proxy.runpod.net"


def call_segmind_api(human_path, cloth_path, description, api_key, result_path):
    try:
        print("[Segmind] Triggering Segmind IDM-VTON API...")
        
        # Read human and cloth images and convert to base64
        with open(human_path, "rb") as f:
            human_base64 = base64.b64encode(f.read()).decode("utf-8")
        with open(cloth_path, "rb") as f:
            cloth_base64 = base64.b64encode(f.read()).decode("utf-8")
            
        url = "https://api.segmind.com/v1/idm-vton"
        headers = {
            "x-api-key": api_key,
            "Content-Type": "application/json"
        }
        payload = {
            "human_img": human_base64,
            "garm_img": cloth_base64,
            "garment_des": description,
            "category": "upper_body"
        }
        
        response = requests.post(url, json=payload, headers=headers, timeout=120)
        
        if response.status_code == 200:
            content_type = response.headers.get("Content-Type", "")
            if "json" in content_type:
                res_data = response.json()
                img_url = res_data.get("image") or res_data.get("output")
                if img_url:
                    urllib.request.urlretrieve(img_url, result_path)
                    return result_path
                else:
                    raise Exception(f"Unexpected JSON format from Segmind: {res_data}")
            else:
                # Save raw bytes directly to output
                with open(result_path, "wb") as f:
                    f.write(response.content)
                return result_path
        else:
            raise Exception(f"Segmind API returned status {response.status_code}: {response.text}")
            
    except Exception as e:
        print(f"[Segmind] Failed: {e}")
        raise


def call_runpod_api(human_path, cloth_path, description, result_path):
    """Direct FastAPI call to RunPod server - no Gradio, no version conflicts!"""
    print(f"\n=================================")
    print(f"CALLING RunPod FastAPI: {RUNPOD_API_URL}")
    print("=================================\n")

    with open(human_path, "rb") as hf, open(cloth_path, "rb") as cf:
        files = {
            "human_image": ("human.jpg", hf, "image/jpeg"),
            "garment_image": ("garment.jpg", cf, "image/jpeg"),
        }
        data = {
            "description": description,
            "denoise_steps": 30,
            "seed": 42
        }
        response = requests.post(
            f"{RUNPOD_API_URL}/tryon",
            files=files,
            data=data,
            timeout=180
        )

    if response.status_code != 200:
        raise Exception(f"RunPod server returned {response.status_code}: {response.text}")

    result = response.json()
    if not result.get("success"):
        raise Exception(f"RunPod inference failed: {result.get('error', 'Unknown error')}")

    img_data = base64.b64decode(result["result_image"])
    with open(result_path, "wb") as f:
        f.write(img_data)

    print(f"SUCCESS! Result saved to {result_path}")
    return result_path


@app.route('/tryon', methods=['POST'])
def tryon():

    human_path = None
    cloth_path = None

    try:

        data = request.json

        human_b64 = data.get("human_image")
        cloth_b64 = data.get("cloth_image")
        cloth_url = data.get("cloth_image_url")
        description = data.get("description", "beautiful dress")

        if not human_b64:
            return jsonify({
                "status": "error",
                "message": "human_image missing"
            }), 400

        if not cloth_b64 and not cloth_url:
            return jsonify({
                "status": "error",
                "message": "cloth image missing"
            }), 400

        timestamp = f"{int(time.time())}_{random.randint(1000,9999)}"

        human_path = f"uploads/human_{timestamp}.jpg"
        cloth_path = f"uploads/cloth_{timestamp}.jpg"

        result_filename = f"result_{timestamp}.png"
        result_path = f"results/{result_filename}"

        with open(human_path, "wb") as f:
            f.write(base64.b64decode(human_b64))

        if cloth_b64:
            with open(cloth_path, "wb") as f:
                f.write(base64.b64decode(cloth_b64))
        else:
            urllib.request.urlretrieve(cloth_url, cloth_path)

        # Check for Segmind API Key
        segmind_api_key = os.environ.get("SEGMIND_API_KEY")
        result_file = None
        used_segmind = False

        if segmind_api_key:
            try:
                result_file = call_segmind_api(
                    human_path,
                    cloth_path,
                    description,
                    segmind_api_key,
                    result_path
                )
                used_segmind = True
            except Exception as se:
                print(f"Segmind API failed, falling back to Hugging Face: {se}")

        if not used_segmind:
            call_runpod_api(human_path, cloth_path, description, result_path)
            result_file = result_path

        image_url = (
            request.host_url.rstrip("/")
            + "/results/"
            + result_filename
        )

        return jsonify({
            "status": "success",
            "image": image_url
        })

    except Exception as e:

        traceback.print_exc()

        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500

    finally:

        if human_path and os.path.exists(human_path):
            os.remove(human_path)

        if cloth_path and os.path.exists(cloth_path):
            os.remove(cloth_path)


@app.route('/results/<filename>')
def get_result(filename):
    path = os.path.join("results", filename)

    if os.path.exists(path):
        return send_file(path, mimetype="image/png")

    return "File not found", 404


@app.route('/uploads/<filename>')
def get_upload(filename):
    path = os.path.join("uploads", filename)

    if os.path.exists(path):
        mime = "image/jpeg"
        if filename.endswith(".png"):
            mime = "image/png"
        return send_file(path, mimetype=mime)

    return "File not found", 404


@app.route('/')
def health():
    return "Virtual Try-On API Running Successfully ✅"


if __name__ == '__main__':
    app.run(
        host='0.0.0.0',
        port=int(os.environ.get("PORT", 7860))
    )