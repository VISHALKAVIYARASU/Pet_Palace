# test_checkout_flow.py
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import re
import sys

# CONFIG
BASE = "http://localhost/PetPalace"
LOGIN_URL = f"{BASE}/auth/login.php"
HOME_URL  = f"{BASE}/pages/home.php"
CART_URL  = f"{BASE}/pages/cart.php"
PAY_URL   = f"{BASE}/pages/payment.php"
INVOICE_URL = f"{BASE}/pages/invoice.php"

USERNAME = "user"
PASSWORD = "user1@"

# Helpers
def to_number_from_rupee(text):
    # Extract digits and decimal from strings like "₹1,234.00" or "₹1234"
    s = text.replace("₹", "").replace(",", "").strip()
    m = re.search(r"([0-9]+(?:\.[0-9]+)?)", s)
    return float(m.group(1)) if m else None

def wait_clickable(driver, by, selector, timeout=15):
    return WebDriverWait(driver, timeout).until(EC.element_to_be_clickable((by, selector)))

def wait_visible(driver, by, selector, timeout=15):
    return WebDriverWait(driver, timeout).until(EC.visibility_of_element_located((by, selector)))

# --- Setup Chrome with webdriver_manager ---
opts = Options()
opts.add_argument("--start-maximized")
opts.add_argument("--disable-popup-blocking")
# for newer Chrome versions if you run into issues:
opts.add_argument("--remote-allow-origins=*")

driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=opts)
wait = WebDriverWait(driver, 20)

try:
    # 1) Go to login page and log in
    print("[1] Opening login page...")
    driver.get(LOGIN_URL)
    wait_visible(driver, By.ID, "username")

    print("[1] Entering credentials...")
    driver.find_element(By.ID, "username").send_keys(USERNAME)
    # password input has no ID, use name attr 'password'
    driver.find_element(By.NAME, "password").send_keys(PASSWORD)
    driver.find_element(By.ID, "submitBtn").click()

    # Wait until redirected to home (or the home heading renders)
    print("[1] Waiting for home page...")
    WebDriverWait(driver, 10).until(EC.url_contains("/pages/home.php"))
    print(" ✅ Logged in and on Home page.")

    # 2) On home page, click the first "Add to Cart" button
    print("[2] Finding an 'Add to Cart' button...")
    # Wait until product cards are present
    WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, "//button[contains(., 'Add to Cart')]")))
    add_buttons = driver.find_elements(By.XPATH, "//button[contains(., 'Add to Cart')]")
    if not add_buttons:
        raise Exception("No 'Add to Cart' buttons found on home page.")
    print("[2] Clicking first Add to Cart...")
    add_buttons[0].click()

    # After clicking, the page redirects to cart.php?add=...
    print("[2] Waiting for cart page to load...")
    WebDriverWait(driver, 10).until(EC.url_contains("/pages/cart.php"))
    print(" ✅ On cart page.")

    # 3) Verify item is in cart and click "Proceed to Pay"
    print("[3] Looking for 'Proceed to Pay' button...")
    proceed = wait_clickable(driver, By.LINK_TEXT, "Proceed to Pay", timeout=10)
    proceed.click()
    WebDriverWait(driver, 10).until(EC.url_contains("/pages/payment.php"))
    print(" ✅ Navigated to payment.php")

    # 4) On payment page, read total amount and click "Pay with Stripe"
    print("[4] Waiting for payment page to render total and checkout button...")
    total_elem = wait_visible(driver, By.XPATH, "//p/strong[contains(., 'Total Amount')]/.. | //p[strong[contains(., 'Total Amount')]]", timeout=10)
    total_text = total_elem.text if total_elem else ""
    # normalize: payment.php markup is: <p><strong>Total Amount:</strong> ₹<?= $totalAmount ?></p>
    # so total_text will be something like "Total Amount: ₹1234"
    total_amount = to_number_from_rupee(total_text)
    print(f"    Found total on payment page: {total_text} -> parsed {total_amount}")

    checkout_btn = wait_clickable(driver, By.ID, "checkout-button", timeout=10)

    # Prepare to detect new window (stripe popup) and success container
    before_handles = set(driver.window_handles)
    original_handle = driver.current_window_handle

    print("[4] Clicking 'Pay with Stripe' (this will open stripe popup and then simulate payment)...")
    checkout_btn.click()

    # Wait for either a new window OR payment success container visible
    print("[4] Waiting for either stripe popup or payment success (this can take a few seconds)...")
    # Wait a max of 30s for success or popup
    success_shown = False
    try:
        # First wait short time for new popup
        WebDriverWait(driver, 8).until(lambda d: len(d.window_handles) > len(before_handles))
        new_handles = set(driver.window_handles) - before_handles
        if new_handles:
            new_handle = new_handles.pop()
            print(f"    Detected stripe popup window handle: {new_handle}. Switching and closing it.")
            driver.switch_to.window(new_handle)
            # optional small wait to let content load (not necessary)
            time.sleep(1)
            driver.close()
            # switch back
            driver.switch_to.window(original_handle)
    except Exception:
        # no popup detected quickly — JS may still open/close the popup; ignore
        pass

    # Now wait for the payment success container to become visible (JS should show it after simulation)
    print("[4] Waiting for payment success alert to appear (up to 30s)...")
    try:
        wait = WebDriverWait(driver, 30)
        success_container = wait.until(EC.visibility_of_element_located((By.ID, "payment-success")))
        # inside it is .alert-success
        alert = success_container.find_element(By.CLASS_NAME, "alert-success")
        print(" ✅ Payment success shown on payment.php.")
        success_shown = True
    except Exception as e:
        raise Exception("Payment success did not appear within timeout. " + str(e))

    # 5) (Optional) verify invoice: go to invoice.php and check the latest transaction amount matches
    print("[5] Navigating to invoice.php to verify transaction appears...")
    driver.get(INVOICE_URL)
    WebDriverWait(driver, 10).until(EC.url_contains("/pages/invoice.php"))

    # The invoice table lists transactions; the amount is in the 3rd <td> of the first <tbody><tr>.
    try:
        # Wait for table rows
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, "//table/tbody/tr")))
        first_row_amount_td = driver.find_element(By.XPATH, "//table/tbody/tr[1]/td[3]")
        invoice_amount_text = first_row_amount_td.text  # like "₹1,234.00"
        invoice_amount = to_number_from_rupee(invoice_amount_text)
        print(f"    Invoice top-row amount: {invoice_amount_text} -> parsed {invoice_amount}")

        if total_amount is not None:
            # amounts may be floats, compare with small tolerance
            if abs(invoice_amount - total_amount) < 0.01:
                print(" ✅ Invoice amount matches payment total.")
            else:
                raise Exception(f"Amount mismatch: payment page {total_amount} vs invoice {invoice_amount}")
        else:
            print(" ⚠️ Could not parse total from payment page earlier; invoice verification skipped.")
    except Exception as e:
        raise Exception("Failed verifying invoice table: " + str(e))

    print("\nALL STEPS PASSED: Checkout flow (login → add → cart → payment → invoice) succeeded.")

except Exception as err:
    print("\nTEST FAILED:", err)
    # Optionally show current URL for debugging
    try:
        print("Current URL:", driver.current_url)
    except:
        pass
    # Exit non-zero for CI
    sys.exit(1)

finally:
    print("Closing browser...")
    driver.quit()
