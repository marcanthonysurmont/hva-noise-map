import serial
import sys
import time
import requests
from urllib3.exceptions import InsecureRequestWarning

# Disable SSL verification warnings
requests.packages.urllib3.disable_warnings(category=InsecureRequestWarning)

# Config
PORT = 'COM5'
BAUD_RATE = 9600
BASE_URL = 'https://noisemapper.ddev.site'
API_URL = f'{BASE_URL}/api/store-noise-level'  # Remove /api/ prefix

# Simplified session setup
session = requests.Session()
session.verify = False

try:
    # Remove CSRF token handling since using API route
    headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'  # Add this line
    }

    ser = serial.Serial(PORT, BAUD_RATE, timeout=1)
    print(f"Connected to Arduino on {PORT}")

    while True:
        if ser.in_waiting:
            value = float(ser.readline().decode().strip())
            print(f"Reading: {value}")

            response = session.post(
                API_URL,
                json={'decibel_value': value},  # Changed from noise_level to decibel_value
                headers=headers
            )
            response.raise_for_status()
            print(f"Sent noise level {value}")
        time.sleep(0.1)

except requests.exceptions.RequestException as e:
    print(f"API Error: {e}", file=sys.stderr)
    sys.exit(1)
except serial.SerialException as e:
    print(f"Arduino Error: {e}", file=sys.stderr)
    sys.exit(1)
except Exception as e:
    print(f"Error: {e}", file=sys.stderr)
    sys.exit(1)
finally:
    if 'ser' in locals() and ser:
        ser.close()
