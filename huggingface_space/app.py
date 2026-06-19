from flask import Flask, request, jsonify, send_file
from gradio_client import Client, handle_file
from werkzeug.middleware.proxy_fix import ProxyFix
import os
import time
import random
import base64
import urllib.request
import traceback
import shutil

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

VTON_SPACES = [
    "yisol/IDM-VTON",
    "wytwyt02/yisol-IDM-VTON",
    "hysts-duplicates/IDM-VTON",
]

clients = {}


def call_vton_api(human_path, cloth_path, description):
    hf_token = os.environ.get("HF_TOKEN")
    errors_list = []

    for space in VTON_SPACES:
        try:
            print(f"\n=================================")
            print(f"TRYING SPACE: {space}")
            print("=================================\n")

            # Lazy initialization and caching of Gradio Client
            if space not in clients:
                try:
                    if hf_token:
                        clients[space] = Client(space, token=hf_token)
                    else:
                        clients[space] = Client(space)
                except TypeError:
                    if hf_token:
                        clients[space] = Client(space, hf_token=hf_token)
                    else:
                        clients[space] = Client(space)

            client = clients[space]

            # Try Method 1 (Keyword arguments)
            try:
                print(f"Trying Method 1 (Keyword args) on {space}")
                result = client.predict(
                    dict={
                        "background": handle_file(human_path),
                        "layers": [],
                        "composite": None
                    },
                    garm_img=handle_file(cloth_path),
                    garment_des=description,
                    is_checked=True,
                    is_checked_crop=False,
                    denoise_steps=20,
                    seed=42,
                    api_name="/tryon"
                )

                result_file = result[0] if isinstance(result, (list, tuple)) else result
                if result_file and os.path.exists(result_file):
                    print(f"SUCCESS ON {space} USING METHOD 1")
                    return result_file
            except Exception as e1:
                print(f"METHOD 1 FAILED ON {space}: {e1}")
                errors_list.append(f"{space} (M1): {str(e1)}")

            # Try Method 2 (Positional arguments)
            try:
                print(f"Trying Method 2 (Positional args) on {space}")
                result = client.predict(
                    {
                        "background": handle_file(human_path),
                        "layers": [],
                        "composite": None
                    },
                    handle_file(cloth_path),
                    description,
                    True,
                    False,
                    20,
                    42,
                    api_name="/tryon"
                )

                result_file = result[0] if isinstance(result, (list, tuple)) else result
                if result_file and os.path.exists(result_file):
                    print(f"SUCCESS ON {space} USING METHOD 2")
                    return result_file
            except Exception as e2:
                print(f"METHOD 2 FAILED ON {space}: {e2}")
                errors_list.append(f"{space} (M2): {str(e2)}")

        except Exception as e:
            print(f"SPACE INITIALIZATION FAILED: {space}: {e}")
            if space in clients:
                del clients[space]
            errors_list.append(f"{space} (INIT): {str(e)}")

    raise Exception(" | ".join(errors_list))


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

        result_file = call_vton_api(
            human_path,
            cloth_path,
            description
        )

        shutil.copyfile(result_file, result_path)

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


@app.route('/')
def health():
    return "Virtual Try-On API Running Successfully ✅"


if __name__ == '__main__':
    app.run(
        host='0.0.0.0',
        port=int(os.environ.get("PORT", 7860))
    )