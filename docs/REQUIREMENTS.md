# Requirements — One Ten United Services

This document is a faithful, structured copy of the source requirements document used to scope the project, with cross-references to where each requirement is implemented in this codebase.

---

## 1. Project Overview

Develop a professional, modern, scalable WordPress website where users can:

- Purchase services (LLC, EIN, Immigration form filing, Notary, Tax)
- Book appointments
- Enroll in courses
- Buy digital products
- Access their dashboard (orders, downloads, courses, certificates)

> **Implemented by:** entire `wp-content/` tree.

---

## 2. Design Requirements

| Requirement       | Value / Implementation |
|-------------------|------------------------|
| Color scheme      | Maroon `#800020`, Black `#000000`, White `#FFFFFF`, Gold accents `#D4AF37` — see `wp-content/themes/one-ten-united/assets/css/main.css` (CSS custom properties) |
| Style             | Clean, modern, professional NYC business feel |
| Typography        | Poppins (Google Fonts), bold headings — enqueued in `functions.php` |
| Mobile responsive | 3 breakpoints (768px, 992px, 1200px) |
| Speed             | Lazy loading via IntersectionObserver, minified-friendly CSS/JS, properly enqueued assets |

---

## 3. Website Structure

**Main pages** (auto-created on plugin activation by `class-otu-setup.php`):

1. Home
2. Services
3. Courses
4. Digital Products
5. Blog / Tutorials
6. About Us
7. Contact Us

**Footer pages:**

- Privacy Policy
- Terms & Conditions
- Refund Policy
- Disclaimer

---

## 4. Home Page Sections (`front-page.php`)

1. Hero (headline + CTAs: Book Appointment / Get Started)
2. Services Overview (cards with icons)
3. Featured Services
4. Courses Preview
5. Digital Products Preview
6. Why Choose Us
7. Final CTA (Call / WhatsApp / Book)

---

## 5. Services Module (Core Feature)

Each service must have **two options**:

1. **Add to Cart** (Buy Service)
2. **Book Appointment**

### Service Purchase Flow (Mandatory)

1. User clicks **Add to Cart** → required form is shown.
2. User submits form → data saved as order meta + admin notified by email.
3. User proceeds → Add to Cart → Checkout → Payment.

### Form Fields

**LLC Formation**
- Full Name, Phone, Email, Business Name, Business Address, State, Owner Details

**EIN Application**
- Full Name, Business Name, Responsible Party, SSN/ITIN (with sensitive-data note)

**Immigration Services**
- Full Name, DOB, Passport Number, Service Type

> **Implemented by:** `includes/class-otu-cpt.php`, `includes/class-otu-forms.php`, `includes/class-otu-woocommerce.php`.

---

## 6. Appointment Booking System

- **Book Appointment** button on every service page (`single-service.php`).
- Booking allows: service selection, date & time selection, user details (name, phone, email).
- Admin receives booking notification email.

> **Implemented by:** `includes/class-otu-booking.php`, shortcode `[otu_booking_form]`.

---

## 7. Courses Module

**Courses scaffolded on plugin activation:**

1. Notary Exam Course — lessons (video/text), practice quizzes, paid access
2. CAA Forensic Document Training — structured modules, final exam, 70 % passing requirement

### Certificate System (Mandatory)

After course completion:

- Auto-generate digital certificate (PDF)
- Includes: Student Name, Course Name, Completion Date, Certificate ID
- Available in user dashboard + emailed

> **Implemented by:** `includes/class-otu-lms.php`, `includes/class-otu-certificate.php`, `templates/certificate-template.php`.

---

## 8. Digital Products Module

**Products:**

1. Notary Exam Practice MCQs (200+)
2. Notary Exam Guide
3. Rental Lease Template
4. Future products (scalable)

**Features:** instant download after purchase, secure file access, PDF / DOCX formats.

> **Implemented by:** `digital_product` CPT in `class-otu-cpt.php` + WooCommerce downloadable products.

---

## 9. User Dashboard

Users can: login/register, view orders, download products, access courses, track progress, download certificates.

> **Implemented by:** `[otu_dashboard]` shortcode in `class-otu-dashboard.php`.

---

## 10. Email Automation

System sends:

- Order confirmation
- Form submission details
- Booking confirmation
- Course completion + certificate

> **Implemented by:** `includes/class-otu-emails.php`.

---

## 11. Admin Panel Features

Admin can: view all orders, access customer-submitted form data, manage bookings, upload/edit products, create/edit courses, manage users.

> **Implemented by:** WP core admin + `admin/class-otu-admin.php` (custom OTU Services menu, settings, bookings, certificates).

---

## 12. UI/UX Requirements

**Each service card includes:** Title · Short description · Price · Buttons (Add to Cart / Book Appointment).

**Sticky floating buttons** (visible on every page):

- 📞 Call Now
- 💬 WhatsApp
- 📅 Book

> **Implemented by:** sticky `.otu-floating-cta` in `footer.php` + CSS in `assets/css/main.css`.

---

## 13. Legal Pages

Disclaimer (mandatory wording):

> _We are not a law firm and do not provide legal advice. We provide clerical and document preparation services only._

> **Implemented by:** Disclaimer page auto-created with this exact wording in `class-otu-setup.php`.

---

## 14. SEO Requirements

- SEO-friendly URLs (rewrite rules flushed on activation)
- Meta titles/descriptions (compatible with RankMath/Yoast)
- Fast loading
- Mobile optimized
- Google indexing ready

---

## 15. Security Requirements

- SSL certificate (server-level)
- Secure handling of sensitive data (forms only collect what's necessary, with security notes)
- Spam protection on forms (nonces; honeypot/recaptcha can be layered)
- Backup system (recommend UpdraftPlus)
- All output escaped, all input sanitized, nonces on every form

---

## 16. Recommended Plugins

| Purpose            | Plugin |
|--------------------|--------|
| Page builder       | Elementor (optional — theme works standalone) |
| E-commerce         | **WooCommerce** (required) |
| Forms              | Built into `otu-services` plugin |
| Courses            | **Tutor LMS** (required) — or LearnDash |
| Booking            | Built into `otu-services` plugin — or Amelia/Bookly |
| SEO                | RankMath or Yoast |
| Speed              | WP Rocket or LiteSpeed |
| Backup             | UpdraftPlus |

---

## 17. Future Scalability

The codebase supports:

- Adding tax services later (just add `service` posts under the *Tax* taxonomy)
- Adding more courses (Tutor LMS native)
- Adding digital tools / AI tools (extend `digital_product` CPT)
- Expanding e-commerce (WooCommerce native)

---

## 18. Final Deliverables

- ✅ Fully functional codebase (theme + plugin)
- ✅ Mobile optimized
- ✅ Admin login (configured during WP install)
- ✅ Documentation (this folder)
- ✅ Speed-conscious assets

---

## ⚠️ Final Notes

- Services work as **Hybrid Products** (Form + Checkout).
- Forms are **mandatory** before purchase.
- All user data is saved and accessible to admins.
- The system is **scalable** and easy to manage.
