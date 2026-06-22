import subprocess
import time
import requests
import base64
import os
import signal

def run_test():
    # 1. Start Flask app locally on port 7890
    print("Starting Flask app locally on port 7890...")
    env = os.environ.copy()
    env["PORT"] = "7890"
    
    # Run the app.py as a subprocess
    process = subprocess.Popen(
        ["python", "app.py"],
        env=env,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True
    )
    
    # Wait for the server to spin up
    time.sleep(3)
    
    # 2. Check health endpoint
    try:
        health_res = requests.get("http://127.0.0.1:7890/", timeout=5)
        print(f"Health check status: {health_res.status_code}")
        
        # Find a product image in the Laravel storage to test base64 conversion
        products_dir = r"c:\Users\DELL\Downloads\dressroom (1)\dressroom\public\storage\products"
        image_file = None
        if os.path.exists(products_dir):
            files = [f for f in os.listdir(products_dir) if f.lower().endswith(('.png', '.jpg', '.jpeg'))]
            if files:
                image_file = os.path.join(products_dir, files[0])
                
        if image_file:
            print(f"Using image file for test: {image_file}")
            with open(image_file, "rb") as f:
                img_b64 = base64.b64encode(f.read()).decode("utf-8")
                
            payload = {
                "human_image": img_b64,
                "cloth_image": img_b64,
                "description": "test garment"
            }
            
            print("Sending POST request to /tryon...")
            # We set a low timeout because we expect it to hit the gradio space or fail with IndexError 
            # (which is acceptable for checking our app routing code)
            try:
                res = requests.post("http://127.0.0.1:7890/tryon", json=payload, timeout=20)
                print(f"Response status: {res.status_code}")
                print(f"Response JSON: {res.json()}")
            except Exception as req_err:
                print(f"Request finished (expected timeout/upstream error): {req_err}")
        else:
            print("No product image found for test, skipping POST test.")
            
    except Exception as e:
        print(f"Error during request: {e}")
    finally:
        # Kill the Flask subprocess
        print("Stopping local Flask app...")
        process.terminate()
        try:
            process.wait(timeout=5)
            print("Flask app stopped successfully.")
        except subprocess.TimeoutExpired:
            process.kill()
            print("Flask app forced to stop.")

if __name__ == "__main__":
    run_test()
