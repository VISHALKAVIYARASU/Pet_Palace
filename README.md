# 🐾 Pet Palace – E-commerce Website for Pet Products

Pet Palace is a PHP-based e-commerce application for selling pet-related products. It features user-friendly shopping, dynamic cart management, admin product controls, Stripe integration for payments, and Selenium-based automation testing.

---

## 📦 Implemented Features

### 👤 User Features
- User **registration** and **login** with validation
- Product listing with image, name, and price
- Add products to cart and update quantities
- **Real-time cart quantity control**
- **Stock reduction** after payment
- **Stripe payment gateway** integration
- Invoice redirection post-payment

### 🛠️ Admin Features
- Admin login panel
- Add, edit, update, and delete products
- View orders via invoice table (mock)

### 🔐 Validation Features
- Email format validation
- Password rule enforcement (length, symbols, numbers)

### 💳 Payment Integration
- Stripe Checkout session via JavaScript
- Secure API key via `.env` configuration

### 🧪 Automation Testing
- Selenium based testing of many features and login in a separate file

---

## 🛠️ Technologies Used

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 8+
- **Database**: MySQL
- **Payment**: Stripe API
- **Testing**: Python 3, Selenium WebDriver
- **Environment**: XAMPP (Apache + MySQL)

---

## ⚙️ Setup Instructions

### 🔧 Prerequisites
- Install [XAMPP](https://www.apachefriends.org/index.html)
- Install Python 3.11+
- Install Google Chrome
- Add [ChromeDriver](https://chromedriver.chromium.org/) to system PATH (must match your Chrome version)
- Install Selenium and WebDriver:
  ```bash
  pip install selenium webdriver-manager
  ```

---

## 🚀 How to Run the Project

### 1️⃣ Launch XAMPP
- Start **Apache** and **MySQL** from the XAMPP Control Panel

### 2️⃣ Create MySQL Database
- Go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Create a new database named:
  ```
  pet_palace
  ```
- Import the SQL structure from `init.sql` into this database

### 3️⃣ Setup Environment File
- Create a `.env` file in your root `PetPalace/` folder:
  ```
  STRIPE_SECRET_KEY=your_stripe_secret_key_here
  STRIPE_PUBLISHABLE_KEY=your_stripe_publishable_key_here
  ```
- Replace `your_stripe_secret_key_here` with your actual test/live Stripe secret key

### 4️⃣ Move Project to `htdocs`
- Copy or move your project folder into:
  ```
  C:\xampp\htdocs\PetPalace
  ```

### 5️⃣ Access in Browser
- Visit:
  ```
  http://localhost/PetPalace/index.php
  ```

---

## 🧪 Run Selenium Tests

### Folder Structure
```
PetPalace/
├── Selenium_Tests/
│   └── test_login.py
    └── ex5_3.py
```

### Execute Test
1. Make sure the app is running on `localhost`
2. Open command prompt:
   ```bash
   cd C:\xampp\htdocs\PetPalace\Selenium_Tests
   python test_login.py
   ```
3. The test will:
   - Open Chrome
   - Visit login page
   - Enter credentials
   - Submit form and print result

---

## ✅ Completed Modules

- [x] User Registration & Login
- [x] Product Display with Images
- [x] Cart Add, Quantity Update & Remove
- [x] Cart Stock Reduction Logic
- [x] Stripe Payment Gateway Integration
- [x] Admin Panel (CRUD)
- [x] Email & Password Validation
- [x] Stripe Checkout Success Redirect
- [x] .env Secure Key Management
- [x] Selenium Test Script

---

## 🧑‍💻 Developer

**VISHAL**  
Information Technology, PSG College of Technology

---

## 📜 License

This project is for educational and personal portfolio use only.
