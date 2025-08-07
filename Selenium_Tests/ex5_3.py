from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
import time


# URLs to replace based on your app structure
base_url = "http://localhost/PetPalace/index.php"
register_url = "http://localhost/PetPalace/auth/register.php"
dashboard_url = "http://localhost/PetPalace/home.php"  # Adjust if needed
invoice_url = "http://localhost/PetPalace/pages/invoice.php"
home_url = "http://localhost/PetPalace/pages/home.php"
login_url = "http://localhost/PetPalace/auth/login.php"
header_url = "http://localhost/PetPalace/includes/header.php"


# Chrome options-> to hide logging
options = Options()
options.add_experimental_option('excludeSwitches', ['enable-logging'])  # Hide DevTools + GCM warnings
options.add_argument("--log-level=3")  # Suppress most logs


# Setup driver
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
wait = WebDriverWait(driver, 10)

def slow_type(element, text, delay=0.2):
    for char in text:
        element.send_keys(char)
        time.sleep(delay)


def open_browser_and_clear_cookies():
    driver.get(base_url)
    driver.delete_all_cookies()
    print("Browser opened and cookies deleted.")

def print_session_for_user(username, password):
    driver.get("http://localhost/PetPalace/auth/login.php")

    try:
        wait.until(EC.presence_of_element_located((By.NAME, "username"))).send_keys(username)
        driver.find_element(By.NAME, "password").send_keys(password + Keys.ENTER)
        time.sleep(2)
        print("✅ Logged in successfully.")
    except Exception as e:
        print("❌ Login failed:", e)
        return

    # Visit the session debug page
    driver.get("http://localhost/PetPalace/debug/session.php")
    time.sleep(1)

    # Print server-side session
    print("🧾 PHP Session Data:")
    print(driver.find_element(By.TAG_NAME, "pre").text)

import pickle
import os

cookie_file = "cookies.pkl"

# First session: Save cookies
def save_cookies():
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
    driver.get(base_url)
    driver.add_cookie({'name': 'test_cookie', 'value': 'testing'})
    pickle.dump(driver.get_cookies(), open(cookie_file, "wb"))
    driver.quit()

# Second session: Load cookies and check
def load_cookies_and_check():
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
    driver.get(base_url)
    
    if os.path.exists(cookie_file):
        cookies = pickle.load(open(cookie_file, "rb"))
        for cookie in cookies:
            driver.add_cookie(cookie)
        driver.refresh()  # Apply cookies by reloading

    cookies = driver.get_cookies()
    print("Session ended after closing:", not any(c['name'] == 'test_cookie' for c in cookies))
    driver.quit()

save_cookies()
load_cookies_and_check()


def set_window_size(width, height):
    driver.set_window_size(width, height)
    print(f"Window size set to {width}x{height}")

def register_user(username, password, role):
    driver.get(register_url)
    wait.until(EC.presence_of_element_located((By.NAME, "username"))).send_keys(username)
    driver.find_element(By.NAME, "password").send_keys(password)
    Select(driver.find_element(By.NAME, "role")).select_by_visible_text(role)
    driver.find_element(By.TAG_NAME, "button").click()
    time.sleep(1)
    print(f"Registration attempted for: {username} ({role})")


def test_invalid_login(username, password):
    driver.get("http://localhost/PetPalace/auth/login.php")
    time.sleep(2)

    try:
        wait = WebDriverWait(driver, 20)

        username_field = wait.until(EC.presence_of_element_located((By.NAME, "username")))
        username_field.clear()
        slow_type(username_field, username)

        password_field = wait.until(EC.presence_of_element_located((By.NAME, "password")))
        password_field.clear()
        slow_type(password_field, password)

        password_field.send_keys(Keys.ENTER)
        time.sleep(2)

        # Check if page contains any error indicators like 'Invalid' or 'Attempt'
        body_text = driver.find_element(By.TAG_NAME, "body").text.lower()
        if "invalid" in body_text or "attempt" in body_text:
            print("✅ Error message displayed correctly for invalid login.")
        else:
            print("❌ Error message NOT displayed properly.")

    except Exception as e:
        print(f"[ERROR] Exception during invalid login test: {str(e)}")
        with open("page_debug.html", "w", encoding="utf-8") as f:
            f.write(driver.page_source)
        print("[DEBUG] Page source written to page_debug.html")


# Function to login and print items on the home page
def login_and_print_items():
    driver.get(base_url)
    
    # Wait for product cards to load
    wait.until(EC.presence_of_all_elements_located((By.CLASS_NAME, "card")))
    time.sleep(1)

    products = driver.find_elements(By.CLASS_NAME, "card")

    if len(products) == 0:
        print("❌ No products displayed on the home page.")
    else:
        print(f"✅ {len(products)} product(s) displayed on the home page.")

    for product in products:
        try:
            name = product.find_element(By.CLASS_NAME, "card-title").text
            price = product.find_element(By.CLASS_NAME, "card-text").text
            print(f"🛒 {name} - {price}")
        except Exception as e:
            print("⚠️ Failed to fetch product info:", e)
        time.sleep(1)

# Function to login and print invoice
def login_and_print_invoice(username, password):
    driver.get(login_url)
    
    # Wait for login form fields to load
    try:
        wait.until(EC.presence_of_element_located((By.NAME, "username"))).send_keys(username)
        driver.find_element(By.NAME, "password").send_keys(password + Keys.ENTER)
    except Exception as e:
        print("❌ Login failed:", e)
        return

    time.sleep(2)  # wait for login redirect

    driver.get(invoice_url)
    print(f"User: {username}, Password: {password}")
    print("Invoice content preview (HTML source):\n", driver.page_source[:500])  # First 500 chars only

    try:
        print_btn = wait.until(EC.presence_of_element_located((By.CLASS_NAME, "print-button")))
        print_btn.click()
        print("✅ Window.print() triggered for invoice.")
    except Exception as e:
        print("❌ Invoice print button not found or error occurred:", e)


def check_logo_on_homepage():
    driver.get(header_url)
    try:
        logo = driver.find_element(By.TAG_NAME, "img")  # Adjust selector if needed
        if logo.is_displayed():
            print("Logo is present on homepage.")
    except:
        print("Logo not found!")

def handle_auto_suggestion():
    driver.get(home_url)
    try:
        # Locate the search input
        search_box = wait.until(EC.presence_of_element_located((By.NAME, "search")))
        search_box.clear()
        search_box.send_keys("Dog")  # Partial keyword to trigger datalist suggestions
        time.sleep(1)  # Small delay so the datalist can show

        # Fetch datalist suggestions
        datalist = driver.find_element(By.ID, "productSuggestions")
        options = datalist.find_elements(By.TAG_NAME, "option")
        suggestions = [opt.get_attribute("value") for opt in options]

        print("Suggestions shown:", suggestions)

        # Check if relevant suggestion exists
        if any("Dog" in suggestion for suggestion in suggestions):
            print("✅ Auto-suggestion for 'Dog' found.")
        else:
            print("❌ No matching auto-suggestion for 'Dog'.")

    except Exception as e:
        print("❌ Search bar or datalist not found:", e)

def check_dropdown_functionality():
    driver.get(register_url)
    dropdown = Select(wait.until(EC.presence_of_element_located((By.NAME, "role"))))
    dropdown.select_by_visible_text("Admin")
    print("Dropdown (single-select) working correctly.")

# Run tests
open_browser_and_clear_cookies()
time.sleep(2)

set_window_size(1280, 720)
time.sleep(2)

register_user("testuser1", "Test@123", "User")
time.sleep(2)

register_user("testadmin", "Admin@123", "Admin")
time.sleep(2)

test_invalid_login("invalid", "wrongpass")
time.sleep(2)

login_and_print_items()
time.sleep(2)

login_and_print_invoice("user", "user1@")
time.sleep(2)

print_session_for_user("user", "user1@")
time.sleep(2)

def does_closing_window_end_session():
    save_cookies()
    load_cookies_and_check()

time.sleep(2)

check_logo_on_homepage()
time.sleep(2)

handle_auto_suggestion()
time.sleep(2)

check_dropdown_functionality()


driver.quit()
