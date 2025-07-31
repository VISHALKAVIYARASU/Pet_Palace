from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
import time

# Setup Chrome driver
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
driver.maximize_window()

# Set base URL for your app (adjust if necessary)
base_url = "http://localhost/PetPalace/auth/login.php"

# List of login credentials to test
test_users = [
    {"username": "admin", "password": "admin1@"},
    {"username": "user", "password": "user1@"}
]

for user in test_users:
    driver.get(base_url)

    try:
        # Wait for form elements to appear
        wait = WebDriverWait(driver, 10)
        username_input = wait.until(EC.presence_of_element_located((By.NAME, "username")))
        password_input = driver.find_element(By.NAME, "password")

        # Enter login credentials
        username_input.clear()
        password_input.clear()
        username_input.send_keys(user["username"])
        password_input.send_keys(user["password"])

        # Submit the form
        password_input.submit()

        # Wait for page to redirect or show success
        time.sleep(2)  # can be replaced with WebDriverWait for dynamic checks

        # Validation: check if login redirected to 'home.php'
        current_url = driver.current_url
        if "home.php" in current_url:
            print(f"[PASS] Login successful for user '{user['username']}'")
        else:
            print(f"[FAIL] Login failed for user '{user['username']}' — not redirected")

    except Exception as e:
        print(f"[ERROR] Test failed for user '{user['username']}':", e)

    finally:
        # Logout or reset session if needed
        driver.delete_all_cookies()

driver.quit()
