<?php
/**
 * Front page template.
 *
 * @package one-ten-united
 */

get_header();
?>

<!-- ======================================================
     SECTION 1: HERO
     ====================================================== -->
<section class="hero" id="hero" aria-label="<?php esc_attr_e( 'Welcome', 'otu' ); ?>">
	<div class="hero__overlay"></div>
	<div class="container hero__inner">
		<div class="hero__content">
			<span class="hero__eyebrow"><?php esc_html_e( "New York City's Trusted Services", 'otu' ); ?></span>
			<h1 class="hero__title"><?php esc_html_e( 'One Ten United Services', 'otu' ); ?></h1>
			<p class="hero__tagline">
				<?php esc_html_e( 'Your Trusted NYC Partner for Business, Immigration & More', 'otu' ); ?>
			</p>
			<div class="hero__actions">
				<a href="#booking" class="btn btn-gold hero__btn">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
					<?php esc_html_e( 'Book Appointment', 'otu' ); ?>
				</a>
				<a href="<?php echo esc_url( home_url( '/services' ) ); ?>" class="btn btn-secondary hero__btn">
					<?php esc_html_e( 'Get Started', 'otu' ); ?>
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
				</a>
			</div>
		</div>
	</div>
	<div class="hero__wave" aria-hidden="true">
		<svg viewBox="0 0 1440 80" preserveAspectRatio="none"><path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="#ffffff"/></svg>
	</div>
</section>

<!-- ======================================================
     SECTION 2: SERVICES OVERVIEW
     ====================================================== -->
<section class="services-overview section" id="services" aria-label="<?php esc_attr_e( 'Services Overview', 'otu' ); ?>">
	<div class="container">
		<div class="section-header">
			<span class="section-eyebrow"><?php esc_html_e( 'What We Offer', 'otu' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Our Services', 'otu' ); ?></h2>
			<p class="section-subtitle">
				<?php esc_html_e( 'Professional services tailored to individuals and businesses across New York City.', 'otu' ); ?>
			</p>
		</div>

		<div class="services-grid">

			<!-- LLC Formation -->
			<a href="<?php echo esc_url( otu_service_url( 'llc-formation' ) ); ?>" class="service-card">
				<div class="service-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="40" height="40"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
				</div>
				<h3 class="service-card__title"><?php esc_html_e( 'LLC Formation', 'otu' ); ?></h3>
				<p class="service-card__desc"><?php esc_html_e( 'Start your business the right way. We handle all filings with the NY Secretary of State.', 'otu' ); ?></p>
				<span class="service-card__link"><?php esc_html_e( 'Learn More', 'otu' ); ?> &rarr;</span>
			</a>

			<!-- EIN Application -->
			<a href="<?php echo esc_url( otu_service_url( 'ein-application' ) ); ?>" class="service-card">
				<div class="service-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="40" height="40"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
				</div>
				<h3 class="service-card__title"><?php esc_html_e( 'EIN Application', 'otu' ); ?></h3>
				<p class="service-card__desc"><?php esc_html_e( 'Obtain your Employer Identification Number quickly and accurately from the IRS.', 'otu' ); ?></p>
				<span class="service-card__link"><?php esc_html_e( 'Learn More', 'otu' ); ?> &rarr;</span>
			</a>

			<!-- Immigration Forms -->
			<a href="<?php echo esc_url( otu_service_url( 'immigration-forms' ) ); ?>" class="service-card">
				<div class="service-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="40" height="40"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
				</div>
				<h3 class="service-card__title"><?php esc_html_e( 'Immigration Forms', 'otu' ); ?></h3>
				<p class="service-card__desc"><?php esc_html_e( 'Authorized immigration form preparation assistance. USCIS-compliant, accurate filings.', 'otu' ); ?></p>
				<span class="service-card__link"><?php esc_html_e( 'Learn More', 'otu' ); ?> &rarr;</span>
			</a>

			<!-- Notary Services -->
			<a href="<?php echo esc_url( otu_service_url( 'notary-services' ) ); ?>" class="service-card">
				<div class="service-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="40" height="40"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
				</div>
				<h3 class="service-card__title"><?php esc_html_e( 'Notary Services', 'otu' ); ?></h3>
				<p class="service-card__desc"><?php esc_html_e( 'Licensed New York notary public available for all your official document needs.', 'otu' ); ?></p>
				<span class="service-card__link"><?php esc_html_e( 'Learn More', 'otu' ); ?> &rarr;</span>
			</a>

			<!-- Tax Services -->
			<a href="<?php echo esc_url( otu_service_url( 'tax-services' ) ); ?>" class="service-card">
				<div class="service-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="40" height="40"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
				</div>
				<h3 class="service-card__title"><?php esc_html_e( 'Tax Services', 'otu' ); ?></h3>
				<p class="service-card__desc"><?php esc_html_e( 'Personal and business tax preparation, filing, and planning by experienced professionals.', 'otu' ); ?></p>
				<span class="service-card__link"><?php esc_html_e( 'Learn More', 'otu' ); ?> &rarr;</span>
			</a>

			<!-- Document Preparation -->
			<a href="<?php echo esc_url( otu_service_url( 'document-preparation' ) ); ?>" class="service-card">
				<div class="service-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="40" height="40"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
				</div>
				<h3 class="service-card__title"><?php esc_html_e( 'Document Preparation', 'otu' ); ?></h3>
				<p class="service-card__desc"><?php esc_html_e( 'Professional preparation of legal, business, and personal documents with accuracy and care.', 'otu' ); ?></p>
				<span class="service-card__link"><?php esc_html_e( 'Learn More', 'otu' ); ?> &rarr;</span>
			</a>

		</div><!-- .services-grid -->

		<div class="section-cta">
			<a href="<?php echo esc_url( home_url( '/services' ) ); ?>" class="btn btn-primary">
				<?php esc_html_e( 'View All Services', 'otu' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ======================================================
     SECTION 3: FEATURED SERVICES
     ====================================================== -->
<section class="featured-services section section--dark" aria-label="<?php esc_attr_e( 'Featured Services', 'otu' ); ?>">
	<div class="container">
		<div class="section-header section-header--light">
			<span class="section-eyebrow"><?php esc_html_e( 'Most Popular', 'otu' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Featured Services', 'otu' ); ?></h2>
		</div>

		<div class="featured-grid">

			<div class="featured-card">
				<div class="featured-card__number">01</div>
				<div class="featured-card__body">
					<h3 class="featured-card__title"><?php esc_html_e( 'Business Formation Package', 'otu' ); ?></h3>
					<p><?php esc_html_e( 'Complete LLC or Corporation formation including EIN, registered agent, and operating agreement.', 'otu' ); ?></p>
					<a href="<?php echo esc_url( otu_service_url( 'llc-formation' ) ); ?>" class="btn btn-gold btn-sm">
						<?php esc_html_e( 'Get This Service', 'otu' ); ?>
					</a>
				</div>
			</div>

			<div class="featured-card">
				<div class="featured-card__number">02</div>
				<div class="featured-card__body">
					<h3 class="featured-card__title"><?php esc_html_e( 'Immigration Assistance', 'otu' ); ?></h3>
					<p><?php esc_html_e( 'Comprehensive immigration form preparation for green cards, citizenship, work permits, and family petitions.', 'otu' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/services/immigration-forms' ) ); ?>" class="btn btn-gold btn-sm">
						<?php esc_html_e( 'Get This Service', 'otu' ); ?>
					</a>
				</div>
			</div>

			<div class="featured-card">
				<div class="featured-card__number">03</div>
				<div class="featured-card__body">
					<h3 class="featured-card__title"><?php esc_html_e( 'Tax Filing Service', 'otu' ); ?></h3>
					<p><?php esc_html_e( 'Stress-free personal and business tax filing with maximum refund guarantee and year-round support.', 'otu' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/services/tax-services' ) ); ?>" class="btn btn-gold btn-sm">
						<?php esc_html_e( 'Get This Service', 'otu' ); ?>
					</a>
				</div>
			</div>

		</div>
	</div>
</section>

<!-- ======================================================
     SECTION 4: COURSES PREVIEW
     ====================================================== -->
<section class="courses-preview section" id="courses" aria-label="<?php esc_attr_e( 'Courses Preview', 'otu' ); ?>">
	<div class="container">
		<div class="section-header">
			<span class="section-eyebrow"><?php esc_html_e( 'Education', 'otu' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Learn &amp; Grow', 'otu' ); ?></h2>
			<p class="section-subtitle">
				<?php esc_html_e( 'Professional development courses led by industry experts.', 'otu' ); ?>
			</p>
		</div>

		<div class="courses-grid">

			<!-- Course 1 -->
			<div class="course-card">
				<div class="course-card__badge"><?php esc_html_e( 'Certification', 'otu' ); ?></div>
				<div class="course-card__thumb course-card__thumb--notary">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="60" height="60"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
				</div>
				<div class="course-card__body">
					<h3 class="course-card__title"><?php esc_html_e( 'Notary Exam Course', 'otu' ); ?></h3>
					<p class="course-card__desc"><?php esc_html_e( 'Comprehensive prep for the NY notary public exam. Study materials, practice tests, and live Q&A sessions.', 'otu' ); ?></p>
					<div class="course-card__meta">
						<span>
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							<?php esc_html_e( '6 Hours', 'otu' ); ?>
						</span>
						<span>
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
							<?php esc_html_e( 'All Levels', 'otu' ); ?>
						</span>
					</div>
					<a href="<?php echo esc_url( home_url( '/courses/notary-exam' ) ); ?>" class="btn btn-primary course-card__btn">
						<?php esc_html_e( 'Enroll Now', 'otu' ); ?>
					</a>
				</div>
			</div>

			<!-- Course 2 -->
			<div class="course-card">
				<div class="course-card__badge course-card__badge--pro"><?php esc_html_e( 'Professional', 'otu' ); ?></div>
				<div class="course-card__thumb course-card__thumb--forensic">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="60" height="60"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
				</div>
				<div class="course-card__body">
					<h3 class="course-card__title"><?php esc_html_e( 'CAA Forensic Document Training', 'otu' ); ?></h3>
					<p class="course-card__desc"><?php esc_html_e( 'Advanced training for accredited forensic document examiners. Covers fraud detection, authentication, and expert witness testimony.', 'otu' ); ?></p>
					<div class="course-card__meta">
						<span>
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							<?php esc_html_e( '20 Hours', 'otu' ); ?>
						</span>
						<span>
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
							<?php esc_html_e( 'Advanced', 'otu' ); ?>
						</span>
					</div>
					<a href="<?php echo esc_url( home_url( '/courses/caa-forensic-document' ) ); ?>" class="btn btn-primary course-card__btn">
						<?php esc_html_e( 'Enroll Now', 'otu' ); ?>
					</a>
				</div>
			</div>

		</div><!-- .courses-grid -->

		<div class="section-cta">
			<a href="<?php echo esc_url( home_url( '/courses' ) ); ?>" class="btn btn-secondary">
				<?php esc_html_e( 'Browse All Courses', 'otu' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ======================================================
     SECTION 5: DIGITAL PRODUCTS PREVIEW
     ====================================================== -->
<section class="products-preview section section--light" id="resources" aria-label="<?php esc_attr_e( 'Digital Products', 'otu' ); ?>">
	<div class="container">
		<div class="section-header">
			<span class="section-eyebrow"><?php esc_html_e( 'Digital Downloads', 'otu' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Resources &amp; Templates', 'otu' ); ?></h2>
			<p class="section-subtitle">
				<?php esc_html_e( 'Professionally crafted templates and guides to help you succeed.', 'otu' ); ?>
			</p>
		</div>

		<div class="products-grid">

			<div class="product-card">
				<div class="product-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="48" height="48"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
				</div>
				<div class="product-card__body">
					<h3 class="product-card__title"><?php esc_html_e( 'LLC Operating Agreement Template', 'otu' ); ?></h3>
					<p class="product-card__desc"><?php esc_html_e( 'New York compliant operating agreement for single and multi-member LLCs.', 'otu' ); ?></p>
					<div class="product-card__price">$29<span>.99</span></div>
					<a href="<?php echo esc_url( home_url( '/product/llc-operating-agreement' ) ); ?>" class="btn btn-primary btn-sm">
						<?php esc_html_e( 'Buy Now', 'otu' ); ?>
					</a>
				</div>
			</div>

			<div class="product-card">
				<div class="product-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="48" height="48"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
				</div>
				<div class="product-card__body">
					<h3 class="product-card__title"><?php esc_html_e( 'Immigration Forms Guide eBook', 'otu' ); ?></h3>
					<p class="product-card__desc"><?php esc_html_e( 'Step-by-step guide for common USCIS forms: I-485, I-130, N-400, and more.', 'otu' ); ?></p>
					<div class="product-card__price">$19<span>.99</span></div>
					<a href="<?php echo esc_url( home_url( '/product/immigration-forms-guide' ) ); ?>" class="btn btn-primary btn-sm">
						<?php esc_html_e( 'Buy Now', 'otu' ); ?>
					</a>
				</div>
			</div>

			<div class="product-card">
				<div class="product-card__icon">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="48" height="48"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
				</div>
				<div class="product-card__body">
					<h3 class="product-card__title"><?php esc_html_e( 'Small Business Tax Checklist', 'otu' ); ?></h3>
					<p class="product-card__desc"><?php esc_html_e( 'Comprehensive tax preparation checklist for small business owners in New York.', 'otu' ); ?></p>
					<div class="product-card__price">$9<span>.99</span></div>
					<a href="<?php echo esc_url( home_url( '/product/tax-checklist' ) ); ?>" class="btn btn-primary btn-sm">
						<?php esc_html_e( 'Buy Now', 'otu' ); ?>
					</a>
				</div>
			</div>

		</div><!-- .products-grid -->

		<div class="section-cta">
			<a href="<?php echo esc_url( home_url( '/shop' ) ); ?>" class="btn btn-secondary">
				<?php esc_html_e( 'Visit Our Shop', 'otu' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- ======================================================
     SECTION 6: WHY CHOOSE US
     ====================================================== -->
<section class="why-us section" id="why-us" aria-label="<?php esc_attr_e( 'Why Choose Us', 'otu' ); ?>">
	<div class="container">
		<div class="section-header">
			<span class="section-eyebrow"><?php esc_html_e( 'Our Promise', 'otu' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Why Choose Us', 'otu' ); ?></h2>
		</div>

		<div class="why-grid">

			<div class="why-card">
				<div class="why-card__icon why-card__icon--1">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="36" height="36"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
				</div>
				<h3 class="why-card__title"><?php esc_html_e( 'Licensed Professionals', 'otu' ); ?></h3>
				<p class="why-card__desc">
					<?php esc_html_e( 'Our team includes licensed notaries, authorized immigration representatives, and certified tax professionals.', 'otu' ); ?>
				</p>
			</div>

			<div class="why-card">
				<div class="why-card__icon why-card__icon--2">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="36" height="36"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
				</div>
				<h3 class="why-card__title"><?php esc_html_e( 'NYC Based', 'otu' ); ?></h3>
				<p class="why-card__desc">
					<?php esc_html_e( 'Proudly serving all five boroughs of New York City with in-person and virtual appointments available.', 'otu' ); ?>
				</p>
			</div>

			<div class="why-card">
				<div class="why-card__icon why-card__icon--3">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="36" height="36"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				</div>
				<h3 class="why-card__title"><?php esc_html_e( 'Fast Turnaround', 'otu' ); ?></h3>
				<p class="why-card__desc">
					<?php esc_html_e( 'We understand time is critical. Most services are completed within 24–72 hours with rush options available.', 'otu' ); ?>
				</p>
			</div>

			<div class="why-card">
				<div class="why-card__icon why-card__icon--4">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="36" height="36"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
				</div>
				<h3 class="why-card__title"><?php esc_html_e( 'Secure &amp; Confidential', 'otu' ); ?></h3>
				<p class="why-card__desc">
					<?php esc_html_e( 'Your documents and personal information are handled with strict confidentiality and digital security.', 'otu' ); ?>
				</p>
			</div>

		</div><!-- .why-grid -->
	</div>
</section>

<!-- ======================================================
     SECTION 7: FINAL CTA
     ====================================================== -->
<section class="final-cta section section--maroon" id="booking" aria-label="<?php esc_attr_e( 'Get Started', 'otu' ); ?>">
	<div class="container final-cta__inner">
		<div class="final-cta__content">
			<h2 class="final-cta__title"><?php esc_html_e( 'Ready to Get Started?', 'otu' ); ?></h2>
			<p class="final-cta__subtitle">
				<?php esc_html_e( 'Contact us today and let our team guide you through every step of the process.', 'otu' ); ?>
			</p>
		</div>
		<div class="final-cta__actions">
			<a href="tel:+17185550100" class="btn btn-white final-cta__btn">
				<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
				<?php esc_html_e( 'Call Us', 'otu' ); ?>
			</a>
			<a href="https://wa.me/17185550100" class="btn btn-gold final-cta__btn" target="_blank" rel="noopener noreferrer">
				<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
				<?php esc_html_e( 'WhatsApp', 'otu' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/book-appointment' ) ); ?>" class="btn btn-secondary btn--outline-white final-cta__btn">
				<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
				<?php esc_html_e( 'Book Appointment', 'otu' ); ?>
			</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
