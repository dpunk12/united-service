# Installation Guide

Step-by-step instructions to deploy the **One Ten United Services** WordPress site.

---

## 1. Server Requirements

| Component   | Minimum         | Recommended       |
|-------------|-----------------|-------------------|
| PHP         | 7.4             | 8.1+              |
| MySQL       | 5.7             | 8.0+ (or MariaDB 10.4+) |
| Web server  | Apache or Nginx | Nginx + PHP-FPM   |
| Memory      | 256 MB          | 512 MB+           |
| HTTPS       | required        | Let's Encrypt / managed SSL |
| Composer    | optional        | required for PDF certificates |

---

## 2. Install WordPress

If you don't already have a WordPress install:

1. Download from <https://wordpress.org/download/>.
2. Upload to your server's web root.
3. Create a MySQL database + user.
4. Visit your domain in a browser and complete the **5-minute install**.
5. Log in to `wp-admin`.

---

## 3. Install Required Plugins

From `wp-admin → Plugins → Add New`, install and activate:

| Plugin        | Why                                |
|---------------|------------------------------------|
| **WooCommerce** | E-commerce + service products + downloadable digital products |
| **Tutor LMS**   | Courses, lessons, quizzes          |

> The OTU Services plugin will display an admin notice if either is missing — it degrades gracefully but key features need both.

Optionally install:

- **RankMath** or **Yoast SEO** — meta tags, sitemap, schema
- **WP Rocket** or **LiteSpeed Cache** — performance
- **UpdraftPlus** — backups

---

## 4. Install the Custom Theme

1. From this repo, copy the entire folder:
   ```
   wp-content/themes/one-ten-united/
   ```
   to your WordPress site's `wp-content/themes/` directory.
2. In `wp-admin → Appearance → Themes`, activate **One Ten United**.

Alternative: zip the folder, then `wp-admin → Appearance → Themes → Add New → Upload Theme`.

---

## 5. Install the Custom Plugin

1. From this repo, copy the entire folder:
   ```
   wp-content/plugins/otu-services/
   ```
   to your WordPress site's `wp-content/plugins/` directory.

2. **(Recommended)** Enable PDF certificate generation by installing TCPDF:
   ```bash
   cd wp-content/plugins/otu-services/
   composer require tecnickcom/tcpdf
   ```
   Without TCPDF the plugin still records certificate metadata, but PDF files won't be generated.

3. In `wp-admin → Plugins`, activate **OTU Services**.

### What activation does

The activation hook (`OTU_Setup::activate()`) automatically:

- Creates 12 pages: Home, Services, Courses, Digital Products, Blog, About Us, Contact Us, Privacy Policy, Terms & Conditions, Refund Policy, Disclaimer, Dashboard.
- Sets the static front page (`Home`) and posts page (`Blog`).
- Registers the **Primary** and **Footer** nav menus and assigns them.
- Scaffolds two courses (as drafts): *Notary Exam Course*, *CAA Forensic Document Training*.
- Creates the upload directory `wp-content/uploads/otu-certificates/`.
- Flushes rewrite rules for the new `service` and `digital_product` post types.

---

## 6. Run the Setup Wizard

After activation, visit:

```
wp-admin → OTU Services → Settings
```

Configure:

- **General:** site name, tagline, contact email, phone, address.
- **Payment:** Stripe publishable key, PayPal client ID (placeholder fields — actual gateways are configured inside WooCommerce → Settings → Payments).
- **Email:** admin email, from name, from email, BCC.
- **Notifications:** toggle booking / order / certificate emails.

Save changes.

---

## 7. WooCommerce Configuration

1. Run the WooCommerce Setup Wizard (`wp-admin → WooCommerce → Home`).
2. Configure payment gateways (Stripe, PayPal, etc.) in **WooCommerce → Settings → Payments**.
3. For each service you want to sell:
   - Create a `service` post (`wp-admin → Services → Add New`).
   - Create a corresponding WooCommerce **Product** with the same name and a price.
   - Tag the product with the service type meta to trigger the correct pre-purchase form (LLC / EIN / Immigration). See **USER-GUIDE.md → Adding Services**.

For digital products: create a `digital_product` post **and** a WooCommerce *Downloadable* product, attaching the PDF/DOCX file.

---

## 8. Tutor LMS Configuration

1. Run the Tutor LMS Setup Wizard (`wp-admin → Tutor LMS → Settings`).
2. The two scaffolded courses are saved as **drafts**. Open each:
   - Add real content for the lessons.
   - Configure quizzes (set passing grade to **70 %**).
   - Set price and publish.
3. Enable **certificate** support (handled automatically by the OTU Services plugin on course completion).

---

## 9. Menus & Navigation

The activation hook creates the menus, but verify:

- `wp-admin → Appearance → Menus`
- **Primary** menu should contain: Home, Services, Courses, Digital Products, About Us, Contact Us.
- **Footer** menu should contain: Privacy Policy, Terms & Conditions, Refund Policy, Disclaimer.

---

## 10. Final Checks

- [ ] Visit the homepage — Hero, Services, Courses, Products, Why Us, CTA all visible.
- [ ] Sticky **Call / WhatsApp / Book** buttons appear bottom-right on every page.
- [ ] Visit `/dashboard/` (logged out) — login form shown.
- [ ] Visit `/dashboard/` (logged in) — tabs visible.
- [ ] Submit a test booking via `[otu_booking_form]` — admin email arrives.
- [ ] Add a test service to cart — pre-purchase form appears.
- [ ] Place a test order — form data appears in admin order screen.
- [ ] Complete a test course — certificate is generated and emailed.

---

## Troubleshooting

| Symptom                                    | Fix |
|--------------------------------------------|-----|
| 404 on service / digital product pages     | Visit `Settings → Permalinks` and click **Save Changes** to flush rewrite rules. |
| "TCPDF not found" notice                   | Run `composer require tecnickcom/tcpdf` inside the plugin folder. |
| WooCommerce/Tutor LMS admin notice         | Install + activate the missing plugin. |
| Emails not arriving                        | Install **WP Mail SMTP** and configure a real SMTP provider. |
| Certificate PDF empty                      | Ensure the `wp-content/uploads/otu-certificates/` directory is writable (chmod 755 with PHP write permission). |
