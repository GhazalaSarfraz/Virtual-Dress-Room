import requests
import json

def get_info():
    try:
        response = requests.get("https://yisol-idm-vton.hf.space/info")
        if response.status_code == 200:
            print("Successfully fetched API info!")
            with open("yisol_info.json", "w") as f:
                json.dump(response.json(), f, indent=2)
            print("Saved to yisol_info.json")
        else:
            print(f"Failed with status: {response.status_code}")
            print(response.text[:500])
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    get_info()
