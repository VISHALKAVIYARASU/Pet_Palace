# 🐾 Pet Palace – E-commerce Website for Pet Products

Pet Palace is a PHP-based e-commerce application designed for selling pet-related products. It includes core features such as user registration/login, product browsing, add-to-cart, quantity control, mock payment gateway, admin-side CRUD operations, and more. The project also includes automation test cases using Selenium for quality assurance.

---

## 📁 Features

### 👤 User Features
- User registration and login with password validation
- Product listing with image, name, description, and price
- Add to cart, update quantities, remove items
- Checkout with a mock payment gateway

### 🛠️ Admin Features
- Login-based admin panel
- Add, update, and delete products
- View user orders (mock)

### ✅ Validation Features
- Email format check during registration
- Password validation: minimum length, special character, number

### 🧪 Automation Testing (Selenium)
- Login functionality test
- Registration form test
- Cart functionality test (coming soon)
- Admin login test (coming soon)

---

## 🛠️ Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Testing**: Python, Selenium WebDriver
- **Environment**: XAMPP (Apache + MySQL)

---

## ⚙️ Setup Instructions

### 🔧 Prerequisites
- XAMPP installed
- Python 3.11+ installed
- Google Chrome browser
- ChromeDriver (version matching your Chrome) in PATH
- Selenium installed via pip:
  ```bash
  pip install selenium
  ```

---

## 🚀 How to Run the Project

### 1️⃣ Launch PHP and MySQL
1. Open **XAMPP Control Panel**
2. Start **Apache** and **MySQL**

### 2️⃣ Set Up the Database
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Create a new database named:
   ```
   pet_palace
   ```
3. Import the `init.sql` file provided with the project into phpMyAdmin

### 3️⃣ Place Project in `htdocs`
1. Move the entire project folder `PetPalace` into:
   ```
   C:\xampp\htdocs\
   ```

### 4️⃣ Run the Application
- Open browser and go to:
  ```
  http://localhost/PetPalace
  ```

---

## 🧪 Running Selenium Test Cases

### 1️⃣ Folder Structure
Your test scripts should be placed in:

```
PetPalace/
├── Selenium_Tests/
│   └── test_login.py
```

### 2️⃣ Edit Script if Needed
Ensure the target elements (`name="email"`, etc.) exist on the respective pages. Update locators if changes were made in your HTML.

### 3️⃣ Run the Test
Open command prompt, navigate to `Selenium_Tests` folder and run:

```bash
python test_login.py
```

Test will:
- Open Chrome
- Visit the login page
- Enter credentials and submit
- Print login result to console

---

## ✅ Completed Modules So Far

- [x] User Registration
- [x] User Login
- [x] Product Display
- [x] Cart and Checkout
- [x] Admin Login
- [x] Admin Product CRUD
- [x] Password and Email Validation
- [x] Selenium Login Test (Basic)
- [ ] Selenium Registration and Cart Tests (WIP)
- [ ] Payment Gateway Integration (Mocked)

---

## 📂 Future Plans
- Add product categories
- Order history for users
- Real-time email alerts
- OTP verification
- Search functionality
- Selenium tests for full workflow

---

## 🧑‍💻 Developer
**VISHAL**  
Information Technology, PSG College of Technology

---

## 📜 License
This project is for educational purposes and personal portfolio use only.
