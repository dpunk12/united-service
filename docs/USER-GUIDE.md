# Admin User Guide

How to operate the **One Ten United Services** site day-to-day.

---

## 1. Logging In

Navigate to `https://yourdomain.com/wp-admin` and log in with your WordPress administrator account.

---

## 2. Adding a Service

Services are a custom post type *and* a WooCommerce product (one of each, linked by name). This pattern lets you display rich service content while still using WooCommerce for checkout.

### Step A — Create the Service post

1. `wp-admin → Services → Add New`.
2. Enter title (e.g. *LLC Formation*).
3. Add description in the editor.
4. Set a **Featured Image** (used in cards and on the single-service page).
5. Under **Service Categories**, choose one: Business Formation / Immigration / Notary / Tax.
6. Publish.

### Step B — Create the WooCommerce Product

1. `wp-admin → Products → Add New`.
2. Use the **same title** as the Service post.
3. Set price.
4. Set product type to **Simple product**.
5. In the **OTU Service Type** meta box (added by the plugin), select the matching form: `llc`, `ein`, `immigration`, or `none`.
6. Publish.

The pre-purchase form will appear automatically on the product page when a customer clicks Add to Cart.

---

## 3. Managing Service Form Submissions

When customers fill an LLC / EIN / Immigration form during checkout:

1. `wp-admin → WooCommerce → Orders → [open an order]`.
2. Form data is shown under **OTU Service Form Data** below the order items.
3. The same data is included in the admin order email.

> ⚠️ **Sensitive data:** SSN/ITIN values may be collected on EIN forms. Restrict admin access, enable HTTPS, and use SMTP with TLS.

---

## 4. Adding a Course

1. `wp-admin → Tutor LMS → Courses → Add New`.
2. Add lessons (video / text / file).
3. Add quizzes — for the final exam, set **Passing Grade** to `70`.
4. Configure pricing (free, one-time payment, or subscription).
5. Publish.

The OTU Services plugin will:

- Hook into Tutor LMS course completion.
- Generate a PDF certificate via TCPDF.
- Email the certificate to the student.
- Make it downloadable from `[otu_dashboard] → My Certificates`.

---

## 5. Managing Bookings

1. `wp-admin → OTU Services → Bookings`.
2. Each booking shows: customer name, email, phone, service, date/time, notes, status.
3. Update the **Status** dropdown:
   - `Pending` (new bookings)
   - `Confirmed`
   - `Cancelled`
4. Save — customer is notified by email if the toggle is on in **OTU Services → Settings → Notifications**.

You can also filter bookings by status from the list view.

---

## 6. Managing Certificates

1. `wp-admin → OTU Services → Certificates`.
2. Each certificate shows: student, course, certificate ID, issue date, PDF link.
3. From here you can:
   - **Re-email** a certificate to the student.
   - **Regenerate** the PDF (if the template was updated).
   - **Delete** an erroneous certificate.

---

## 7. Adding Digital Products

Digital products are sold as WooCommerce *Downloadable* products and tracked via the `digital_product` CPT.

1. `wp-admin → Digital Products → Add New` — create the descriptive post (title, image, description).
2. `wp-admin → Products → Add New`:
   - Match the title.
   - Tick **Downloadable** + **Virtual**.
   - Upload the PDF/DOCX/ZIP file.
   - Set download limit and expiry as needed.
   - Publish.

Customers receive an email with the download link after payment, and the file appears in `[otu_dashboard] → My Downloads`.

---

## 8. Managing Orders

`wp-admin → WooCommerce → Orders` — standard WooCommerce flow.

- View/edit any order.
- Process refunds.
- Resend customer emails.
- See the OTU service-form data attached to each order.

---

## 9. Managing Users

`wp-admin → Users` — standard WordPress flow.

- Roles: Administrator, Shop Manager (WooCommerce), Customer (default for site visitors).
- The dashboard shortcode auto-detects the logged-in user.

---

## 10. Email Notifications

All transactional emails are configured in `wp-admin → OTU Services → Settings → Email` and `… → Notifications`:

- Order confirmation
- Form submission notification (admin)
- Booking confirmation (user + admin)
- Course completion + certificate (user)

> 💡 Use a real SMTP provider (SendGrid, Mailgun, Postmark) via **WP Mail SMTP** for reliable delivery.

---

## 11. Editing Site Pages

Each auto-created page (Home, About, Privacy Policy, etc.) can be edited:

1. `wp-admin → Pages → All Pages`.
2. Click the page → edit content in the editor.
3. The Disclaimer page contains the legally required wording — **do not remove**:
   > _We are not a law firm and do not provide legal advice. We provide clerical and document preparation services only._

---

## 12. Editing the Header / Footer / Menus

- **Logo:** `wp-admin → Appearance → Customize → Site Identity`.
- **Menus:** `wp-admin → Appearance → Menus` — edit the **Primary** and **Footer** menus.
- **Widgets:** `wp-admin → Appearance → Widgets` — populate the *Sidebar* and three *Footer* widget areas.
- **Sticky Call / WhatsApp / Book buttons:** edit the phone and WhatsApp numbers in `wp-content/themes/one-ten-united/footer.php` (or via a child theme — recommended).

---

## 13. Backups & Maintenance

- Run **UpdraftPlus** at least weekly (full backup to remote storage).
- Keep WordPress core, themes, and plugins up to date.
- Monitor PHP error logs.
- Review failed login attempts (consider Wordfence or Limit Login Attempts).

---

## 14. Common Tasks Cheat-Sheet

| I want to…                                    | Where to go |
|-----------------------------------------------|-------------|
| Add a new service                             | Services → Add New + Products → Add New |
| Add a new course                              | Tutor LMS → Courses → Add New |
| Add a digital download                        | Digital Products → Add New + Products → Add New (Downloadable) |
| See a customer's LLC/EIN/Immigration form     | WooCommerce → Orders → [order] |
| Confirm a booking                             | OTU Services → Bookings |
| Resend a certificate                          | OTU Services → Certificates |
| Edit the Disclaimer text                      | Pages → Disclaimer |
| Change the logo                               | Appearance → Customize → Site Identity |
| Update payment gateways                       | WooCommerce → Settings → Payments |
| Update the SMTP / email sender                | OTU Services → Settings → Email (and WP Mail SMTP) |
