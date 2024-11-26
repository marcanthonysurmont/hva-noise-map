import serial
import sys
import time
import requests
import logging
from datetime import datetime
from statistics import mean

# Setup logging
logging.basicConfig(level=logging.INFO)

# Config
PORT = 'COM5'
BAUD_RATE = 9600
BASE_URL = 'https://noisemapper.ddev.site'
API_URL = f'{BASE_URL}/api/store-noise-level'
INTERVAL_MINUTES = 2

readings_buffer = []
start_time = datetime.now()

# Calculate minutes until next hour
minutes_to_next_hour = 60 - start_time.minute
logging.info(f"Minutes until next hour: {minutes_to_next_hour}")

# Session setup
session = requests.Session()
session.verify = False
headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
}

# scripts/read_com.py
try:
    ser = serial.Serial(PORT, BAUD_RATE, timeout=1)
    logging.info(f"Connected to Arduino on {PORT}")

    while True:
        if ser.in_waiting:
            line = ser.readline().decode().strip()

            # Skip empty lines
            if not line:
                continue

            try:
                value = float(line)
                now = datetime.now()
                elapsed = (now - start_time).total_seconds()
                target_minutes = 60 - datetime.now().minute  # Gets remaining minutes until next hour
                logging.info(f"""
                Reading: {value}
                Time: {now.strftime('%H:%M:%S')}
                Elapsed: {elapsed:.0f}s
                Target: {target_minutes * 60}s
                Buffer size: {len(readings_buffer)}
                """)

                readings_buffer.append(value)

                # First interval: minutes until next hour
                # After that: full hours
                target_minutes = minutes_to_next_hour if now.hour == start_time.hour else 60

                # Check if interval has passed
                if elapsed >= target_minutes * 60:
                    period_average = round(mean(readings_buffer), 2)
                    logging.info(f"""
                    SAVING:
                    Time: {now.strftime('%H:%M:%S')}
                    Average: {period_average}
                    Readings: {len(readings_buffer)}
                    Period: {target_minutes}min
                    """)

                    response = requests.post(
                        API_URL,
                        json={'decibel_value': period_average},
                        headers={
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        verify=False
                    )

                    # Reset for next period
                    readings_buffer = []
                    start_time = now
                    # Update minutes_to_next_hour for next iteration
                    minutes_to_next_hour = 60 - start_time.minute

            except ValueError as e:
                logging.error(f"Invalid reading: {line}")
                continue

        time.sleep(0.1)

except KeyboardInterrupt:
    logging.info("\nScript terminated by user")
except requests.exceptions.RequestException as e:
    logging.error(f"API Error: {e}")
except serial.SerialException as e:
    logging.error(f"Arduino Error: {e}")
finally:
    if 'ser' in locals() and ser:
        ser.close()
