# Ankabit Farm — Developer & Architecture Documentation

Welcome to the **Ankabit Farm** B2B Commercial Egg Supply Web Application. This documentation explains how the system is architected, how the 2-step progressive lead capture engine works, how email notifications are dispatched, and how future developers can work on, maintain, and deploy the codebase.

---

## 📋 1. Project Overview & Business Vision

**Ankabit Farm** is a commercial egg producer and B2B egg distribution supplier operating in Nigeria.

### Core Business Rules:
1. **Minimum Wholesale Order Quantity**: Strictly **100 Crates** (3,000 fresh eggs, ~200 kg).
2. **2-Step Progressive Lead Capture**:
   - **Step 1**: Captures Full Name, Business Email, WhatsApp Phone Number, and Security Math Captcha. Saves contact details to disk immediately and dispatches email alerts so leads are captured even if the user exits early.
   - **Step 2**: Unveils order quantity selection (minimum 100 crates enforced), destination state/LGA in Nigeria, and logistics notes, updating the lead record and sending a complete order receipt.
3. **Resend Email Notifications**:
   - Admin Recipient: `olaomansur@gmail.com`
   - Customer Confirmation: Auto-responder copy sent directly to the buyer's email.

---

## 🛠️ 2. Technology Stack & Design System

| Layer | Technology Used |
| :--- | :--- |
| **Backend Engine** | Pure Server-Side Rendered (SSR) **PHP 8.x+** |
| **Styling & System** | **Tailwind CSS** + Custom Design Token CSS (`public/css/styles.css`) |
| **Typography** | Headings: **`Cormorant Garamond`** (Serif) \| Body: **`DM Sans`** (Sans-Serif) |
| **Data Storage** | Lightweight JSON File Persistence (`data/leads.json`) |
| **Email Service** | **Resend API** (`POST https://api.resend.com/emails`) via PHP cURL |
| **Location Data** | Complete dataset of all 36 Nigerian States + FCT (Abuja) and 774 LGAs (`public/js/nigeria-data.js`) |
| **URL Routing** | Apache `.htaccess` Rewrite Rules + PHP CLI `router.php` |
| **Accessibility & SEO** | 100% WCAG 2.1 AA Compliant, JSON-LD Schema (`WholesaleStore`), OpenGraph, `sitemap.xml`, `robots.txt` |

---

## 📂 3. Complete Directory Structure

```
ankabit-farm/
├── config.php            # Global PHP configuration (Admin Email, Resend Key, Minimum Crates)
├── index.php             # Main Server-Rendered Landing Page (About, Service, Contact, Lead Form)
├── order.php             # Standalone Wholesale Order Inquiry Page
├── privacy.php           # Standalone Privacy Policy Page (NDPR Compliant)
├── terms.php             # Standalone Terms & Conditions Page
├── process-lead.php      # 2-Step Lead Processing API Handler (Step 1 & Step 2)
├── captcha.php           # Dynamic Math Captcha Challenge Generator
├── router.php            # Local PHP CLI Web Server Router (serves MIME types & clean URLs)
├── .htaccess             # Apache Web Server URL Rewriting Rules
├── data/
│   ├── leads.json        # Persisted Lead Database (JSON)
│   └── captchas/         # Active Captcha Challenge Cache
├── public/               # Web Root Static Assets
│   ├── css/
│   │   └── styles.css    # Typography, Buttons, Sliders, Modals & Material UI Form Styles
│   ├── images/
│   │   ├── hero-egg-bg.png
│   │   └── farm-distributor.png
│   ├── js/
│   │   ├── main.js       # Client AJAX 2-Step Form Engine, Sliders, Modals & Controls
│   │   └── nigeria-data.js # Complete 36 Nigerian States + 774 LGAs Dataset
│   ├── robots.txt        # Search Engine Crawling Instructions
│   └── sitemap.xml       # SEO Sitemap Index
└── README.md             # Developer & Architecture Documentation
```

---

## 🔄 4. How the System Works (System Architecture & Data Flow)

```
                       ┌─────────────────────────┐
                       │  User Submits Step 1    │
                       │ (Name, Email, Phone)    │
                       └────────────┬────────────┘
                                    │
                                    ▼
                       ┌─────────────────────────┐
                       │   process-lead.php      │
                       │  (Validates Captcha)    │
                       └────────────┬────────────┘
                                    │
           ┌────────────────────────┴────────────────────────┐
           ▼                                                 ▼
┌──────────────────────┐                          ┌──────────────────────┐
│ Save Lead to Disk    │                          │ Resend Email Dispatch│
│ (data/leads.json)    │                          │  - Admin Alert       │
└──────────────────────┘                          │  - Customer Copy     │
           │                                      └──────────────────────┘
           ▼
┌──────────────────────┐
│ Unveil Step 2 Form   │
│ (Quantity >= 100)    │
└──────────────────────┘
```

### Key Subsystems:

### A. Dynamic Security Captcha (`captcha.php`)
- Generates random addition math challenges server-side (e.g. *"What is 7 + 4?"*).
- Stores the expected answer in PHP `$_SESSION['captcha_answer']` and `data/captchas/<id>.json`.
- Returns `{ "captchaId": "...", "question": "What is 7 + 4?" }`.

### B. Progressive 2-Step Lead Processor (`process-lead.php`)
- **Step 1 (`?action=step1`)**:
  - Validates `fullName`, `email`, `phone`, and `captchaAnswer`.
  - Assigns a unique lead reference ID (e.g. `ABF-MS7KJJKW-928`).
  - Appends the lead record to `data/leads.json` with status `STEP1_CONTACT_CAPTURED`.
  - Triggers `sendResendEmail()` to notify **`olaomansur@gmail.com`** and sends a confirmation email to the customer.
- **Step 2 (`?action=step2`)**:
  - Enforces minimum order quantity ($\ge 100$ crates).
  - Updates the lead record in `data/leads.json` with state, LGA, and logistics notes, updating status to `WHOLESALE_ORDER_COMPLETED`.
  - Dispatches the complete order summary to **`olaomansur@gmail.com`**.

### C. Resend Email Dispatcher (`config.php`)
- Uses PHP cURL to send HTTP `POST` requests to `https://api.resend.com/emails`.
- Configured with authorization header `Authorization: Bearer <RESEND_API_KEY>`.

### D. Dynamic State & LGA Selector (`public/js/nigeria-data.js` & `main.js`)
- `NIGERIA_STATES_AND_LGAS` contains a key-value mapping of all 36 States + FCT and their 774 Local Government Areas.
- Changing `#deliveryState` instantly repopulates `#deliveryLGA` dropdown with the corresponding LGAs.

---

## ⚙️ 5. Developer Guide: Setting Up Locally

### Prerequisites:
- **PHP 8.0 or higher** (Included with XAMPP, WAMP, Laragon, or standalone PHP binary).

### Running Locally with PHP Built-in Server:

1. Clone or navigate to the project directory:
   ```bash
   cd C:\Users\USER\.gemini\antigravity\scratch\ankabit-farm
   ```

2. Start the local PHP development server using `router.php`:
   ```bash
   php -S localhost:8000 router.php
   ```
   *(If PHP is installed at XAMPP: `C:\xampp\php\php.exe -S localhost:8000 router.php`)*

3. Open your browser and navigate to:
   ```
   http://localhost:8000
   ```

---

## 🔑 6. Configuration & Customization Guide

All global application settings are located in **`config.php`**:

```php
<?php
// 1. Admin Email Recipient (Change to your preferred admin email)
define('ADMIN_EMAIL', 'olaomansur@gmail.com');

// 2. Sender Email Address (Configured in Resend dashboard)
define('FROM_EMAIL', 'Ankabit Farm <onboarding@resend.dev>');

// 3. Minimum Wholesale Order Quantity
define('MIN_WHOLESALE_CRATES', 100);

// 4. Resend API Key (Paste your API key here or set environment variable)
define('RESEND_API_KEY', getenv('RESEND_API_KEY') ?: 're_123456789_your_resend_key_here');
?>
```

### How to set your live Resend API Key:
- **Option A (PHP File)**: Edit `config.php` and replace `'re_123456789_your_resend_key_here'` with your live key from [Resend Dashboard](https://resend.com).
- **Option B (Environment Variable)**: Set `RESEND_API_KEY=re_your_live_key` in your server environment variables.

---

## 🚀 7. Deployment Instructions (cPanel / Apache / Nginx / Web Hosting)

Deploying this PHP application to any web hosting provider (Namecheap, Hostinger, Bluehost, cPanel, AWS, DigitalOcean) is simple:

1. **Upload Files**: Upload all files from the project directory into your web hosting server's `public_html` directory.
2. **Set File Permissions**: Ensure `data/` directory has write permissions (`0755` or `0777`) so `data/leads.json` can be updated.
3. **Verify `.htaccess`**: Ensure Apache `mod_rewrite` is enabled (enabled by default on all cPanel hosts). `.htaccess` automatically routes clean URLs (`/order`, `/privacy`, `/terms`, `/api/captcha`).
4. **Update `config.php`**: Paste your live Resend API key into `config.php`.

---

## 🧪 8. Testing & Verification

To test lead capture and email dispatches locally:
- Fill out Step 1 with a test name and valid captcha. Check `data/leads.json` to verify the lead ID was saved.
- Proceed to Step 2, enter `150` crates, select state and LGA, and click Submit.
- Check `data/leads.json` to verify the status updated to `WHOLESALE_ORDER_COMPLETED`.

---

## 📞 9. Support & Maintenance

For questions or updates, contact the Ankabit Farm Trade Desk at `trade@ankabitfarm.com.ng` or `olaomansur@gmail.com`.
