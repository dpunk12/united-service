# One Ten United Services

> Professional, modern, scalable **WordPress** website for One Ten United Services — a NYC-based multi-service business offering business formation, immigration document preparation, notary, tax, courses and digital products.

[![Platform](https://img.shields.io/badge/Platform-WordPress%206.0%2B-21759b)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb3)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-blue)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## ✨ What This Project Delivers

A complete, ready-to-zip-and-upload WordPress codebase consisting of:

1. A **custom theme** (`one-ten-united`) — fully responsive, branded NYC-business design.
2. A **custom plugin** (`otu-services`) — services CPT, per-service forms, booking, LMS integration, certificates, dashboard, emails.
3. **Documentation** for installation, requirements traceability, and admin usage.

Users can:

- 🛒 Purchase services (LLC formation, EIN, Immigration, Notary, Tax) via WooCommerce — **with mandatory pre-purchase forms**.
- 📅 Book appointments through the booking system.
- 🎓 Enroll in courses (Notary Exam, CAA Forensic Document Training) via Tutor LMS.
- 📥 Buy and instantly download digital products (PDF guides, MCQ banks, templates).
- 👤 Access a personal dashboard (orders, downloads, courses, progress, certificates, bookings).
- 🏆 Receive an auto-generated PDF **certificate** upon course completion.

---

## 🧰 Tech Stack

| Layer            | Technology |
|------------------|------------|
| CMS              | WordPress 6.0+ |
| Language         | PHP 7.4+ |
| E-commerce       | WooCommerce |
| LMS              | Tutor LMS |
| Theme            | Custom (`one-ten-united`) — no parent dependency |
| Plugin           | Custom (`otu-services`) — modular, OOP |
| PDF Certificates | TCPDF (via Composer) |
| Frontend         | Vanilla JS + IntersectionObserver, Poppins (Google Fonts) |

---

## 🎨 Brand & Design

| Token         | Value     |
|---------------|-----------|
| Primary       | **Maroon** `#800020` |
| Accent        | **Gold**   `#D4AF37` |
| Foreground    | **Black**  `#000000` |
| Background    | **White**  `#FFFFFF` |
| Typography    | Poppins (400 / 600 / 700) |
| Style         | Clean · Modern · Professional · NYC business feel |

Sticky floating CTAs (Call Now / WhatsApp / Book Appointment) are visible on every page.

---

## 📂 Folder Structure

```
/
├── README.md                        ← you are here
├── docs/
│   ├── REQUIREMENTS.md              ← full requirements traceability
│   ├── INSTALLATION.md              ← step-by-step install guide
│   └── USER-GUIDE.md                ← admin operations manual
└── wp-content/
    ├── themes/
    │   └── one-ten-united/
    │       ├── style.css            ← theme header
    │       ├── functions.php        ← enqueues, theme supports, helpers
    │       ├── header.php           ← top bar + sticky nav
    │       ├── footer.php           ← widgets + sticky Call/WhatsApp/Book
    │       ├── front-page.php       ← Hero → Services → Featured → Courses → Products → Why Us → CTA
    │       ├── index.php · page.php · single.php
    │       ├── single-service.php   ← Add to Cart + Book Appointment
    │       ├── archive-service.php
    │       ├── single-course.php
    │       ├── 404.php · searchform.php · sidebar.php
    │       └── assets/
    │           ├── css/main.css     ← ~2,900 lines
    │           ├── js/main.js
    │           └── images/logo.svg
    └── plugins/
        └── otu-services/
            ├── otu-services.php     ← main plugin file
            ├── includes/
            │   ├── class-otu-cpt.php          ← service + digital_product CPTs
            │   ├── class-otu-forms.php        ← LLC / EIN / Immigration forms
            │   ├── class-otu-woocommerce.php  ← WC hooks + order meta
            │   ├── class-otu-booking.php      ← booking CPT + [otu_booking_form]
            │   ├── class-otu-lms.php          ← Tutor LMS integration
            │   ├── class-otu-certificate.php  ← TCPDF certificate generator
            │   ├── class-otu-dashboard.php    ← [otu_dashboard]
            │   ├── class-otu-emails.php       ← all transactional emails
            │   └── class-otu-setup.php        ← activation: pages, menus, front page
            ├── admin/
            │   ├── class-otu-admin.php
            │   └── assets/{css,js}
            ├── assets/{css,js}
            ├── templates/certificate-template.php
            └── languages/otu.pot
```

---

## 🚀 Features

### Services Module (Hybrid Products)
- Custom Post Type `service` with taxonomy `service_category` (Business Formation / Immigration / Notary / Tax)
- Each service exposes **two action buttons**: *Add to Cart (Buy Service)* and *Book Appointment*
- **Mandatory pre-purchase forms** — LLC, EIN, Immigration — before checkout
- Form data saved as order meta and shown in WP admin order screen

### Appointment Booking
- Lightweight booking CPT (`otu_booking`) with date / time / service / user
- `[otu_booking_form]` shortcode
- Admin email notification on every booking

### LMS (Tutor LMS)
- Two courses scaffolded on activation: **Notary Exam Course**, **CAA Forensic Document Training**
- 70 % passing requirement triggers certificate
- Auto-generated **PDF certificate** with student name, course, completion date, unique Certificate ID
- Certificate emailed + accessible from dashboard

### Digital Products
- Custom Post Type `digital_product`
- Instant secure download after purchase (WooCommerce downloads API)

### User Dashboard
- `[otu_dashboard]` shortcode — login / register, orders, downloads, courses, progress, certificates, bookings, profile

### Admin Panel
- Custom OTU Services menu — settings (Stripe / PayPal placeholders, email config), bookings, certificates
- Customer form submissions visible on each WooCommerce order

### Email Automation
- Order confirmation · Form submission · Booking confirmation · Course completion + certificate

---

## 🔧 Installation (Quick Start)

```bash
# 1. Install WordPress on your server (LAMP / LEMP / managed host)
# 2. Install required plugins from wp-admin → Plugins → Add New:
#       - WooCommerce
#       - Tutor LMS
# 3. Copy this repo's wp-content/themes/one-ten-united/ to your site
# 4. Copy this repo's wp-content/plugins/otu-services/    to your site
# 5. (Optional) Inside otu-services/, run:
#       composer require tecnickcom/tcpdf
#    to enable PDF certificate generation
# 6. Activate the theme, then activate the plugin
# 7. The plugin's activation hook auto-creates pages, menus, and the front page
```

See **[docs/INSTALLATION.md](docs/INSTALLATION.md)** for the full step-by-step guide.

---

## 📸 Screenshots

> _Screenshots will be added after first deployment._

| Page              | Preview                  |
|-------------------|--------------------------|
| Home              | _placeholder_            |
| Services Archive  | _placeholder_            |
| Single Service    | _placeholder_            |
| Booking Form      | _placeholder_            |
| User Dashboard    | _placeholder_            |
| Certificate (PDF) | _placeholder_            |

---

## 📚 Documentation

- **[docs/REQUIREMENTS.md](docs/REQUIREMENTS.md)** — full requirements traceability matrix.
- **[docs/INSTALLATION.md](docs/INSTALLATION.md)** — server requirements, install flow, post-install configuration.
- **[docs/USER-GUIDE.md](docs/USER-GUIDE.md)** — admin guide for managing services, courses, products, orders, bookings.

---

## 🔐 Security & Standards

- All output escaped (`esc_html`, `esc_attr`, `esc_url`)
- All input sanitized (`sanitize_text_field`, `sanitize_email`, `absint`, …)
- Nonces on every form (`wp_nonce_field` / `wp_verify_nonce`)
- No hardcoded URLs (uses `home_url()`, `plugins_url()`, `get_template_directory_uri()`)
- All user-visible strings translatable via the `otu` text domain
- Defensive checks (`class_exists('WooCommerce')`, `function_exists('tutor')`) with admin notices on missing dependencies

---

## ⚠️ Legal Disclaimer

> _One Ten United Services is **not a law firm** and does **not provide legal advice**. We provide clerical and document preparation services only._

---

## 📜 License

Released under the [GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) license — consistent with WordPress core.
