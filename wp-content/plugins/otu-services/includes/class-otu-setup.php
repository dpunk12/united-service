<?php
/**
 * Plugin Setup — Activation and Deactivation for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_Setup
 *
 * Handles activation tasks (page creation, menus, rewrites) and deactivation cleanup.
 */
class OTU_Setup {

	/**
	 * Service posts to seed on activation.
	 *
	 * key   => post slug
	 * value => array( title, excerpt, category )
	 *
	 * @var array
	 */
	private static $services = array(
		'llc-formation'      => array(
			'title'    => 'LLC Formation',
			'excerpt'  => 'Start your business the right way. We handle all filings with the NY Secretary of State.',
			'category' => 'Business Formation',
		),
		'ein-application'    => array(
			'title'    => 'EIN Application',
			'excerpt'  => 'Obtain your Employer Identification Number quickly and accurately from the IRS.',
			'category' => 'Business Formation',
		),
		'immigration-forms'  => array(
			'title'    => 'Immigration Forms',
			'excerpt'  => 'Authorized immigration form preparation assistance. USCIS-compliant, accurate filings.',
			'category' => 'Immigration',
		),
		'notary-services'    => array(
			'title'    => 'Notary Services',
			'excerpt'  => 'Licensed New York notary public available for all your official document needs.',
			'category' => 'Notary',
		),
		'tax-services'       => array(
			'title'    => 'Tax Services',
			'excerpt'  => 'Personal and business tax preparation, filing, and planning by experienced professionals.',
			'category' => 'Tax',
		),
		'document-preparation' => array(
			'title'    => 'Document Preparation',
			'excerpt'  => 'Professional preparation of legal, business, and personal documents with accuracy and care.',
			'category' => '',
		),
	);

	/**
	 * Pages to create on activation.
	 *
	 * key   => page slug
	 * value => array( title, content, template )
	 *
	 * @var array
	 */
	private static $pages = array(
		'home'               => array(
			'title'    => 'Home',
			'content'  => '',
			'template' => 'front-page',
		),
		'services'           => array(
			'title'   => 'Services',
			'content' => '[otu_services_grid]',
		),
		'courses'            => array(
			'title'   => 'Courses',
			'content' => '',
		),
		'digital-products'   => array(
			'title'   => 'Digital Products',
			'content' => '',
		),
		'blog'               => array(
			'title'   => 'Blog',
			'content' => '',
		),
		'about-us'           => array(
			'title'   => 'About Us',
			'content' => '<h2>Who We Are</h2>
<p>One Ten United Services is a professional document preparation and clerical services company proudly serving individuals and businesses across New York City. We are not a law firm and do not provide legal advice — we provide clerical and document preparation services only.</p>

<h2>Our Mission</h2>
<p>Our mission is to make essential business and personal document preparation accessible, affordable, and stress-free for every New Yorker, regardless of background or language.</p>

<h2>What We Do</h2>
<p>We specialize in LLC formation, EIN applications, immigration form preparation, notary services, tax document preparation, and more. Our experienced team guides you through every step of the document preparation process.</p>

<h2>Why Choose Us</h2>
<ul>
<li>Certified document preparers with years of experience</li>
<li>Multilingual staff serving diverse communities</li>
<li>Transparent pricing — no hidden fees</li>
<li>Convenient in-person and remote appointments</li>
<li>Secure handling of all sensitive documents</li>
</ul>

<p>Ready to get started? <a href="/book-appointment">Book a free consultation</a> today.</p>',
		),
		'contact-us'         => array(
			'title'   => 'Contact Us',
			'content' => "<h2>Get In Touch</h2>
<p>We'd love to hear from you. Book an appointment, send us a message, or drop by our office.</p>

<div class=\"contact-info-grid\">
<div>
<h3>Phone</h3>
<p><a href=\"tel:+17185550100\">(718) 555-0100</a></p>

<h3>Email</h3>
<p><a href=\"mailto:info@onetenunited.com\">info@onetenunited.com</a></p>

<h3>Hours</h3>
<p>Monday \xe2\x80\x93 Friday: 9am \xe2\x80\x93 6pm<br>Saturday: 10am \xe2\x80\x93 4pm<br>Sunday: Closed</p>
</div>
</div>

<h2>Book an Appointment</h2>
[otu_booking_form]",
		),
		'privacy-policy'     => array(
			'title'   => 'Privacy Policy',
			'content' => '<p>One Ten United Services ("we," "us," or "our") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information.</p>

<h2>Information We Collect</h2>
<p>We collect information you provide directly to us, including: name, email address, phone number, mailing address, date of birth, and government identification numbers required to complete document preparation services.</p>

<h2>How We Use Your Information</h2>
<p>We use your information to: prepare requested documents, communicate with you about your service requests, send confirmation and status update emails, and improve our services.</p>

<h2>Data Security</h2>
<p>We implement industry-standard security measures to protect your personal information. Sensitive data such as Social Security Numbers (SSN) and ITIN numbers are transmitted over encrypted connections and are never stored in plain text.</p>

<h2>Third-Party Sharing</h2>
<p>We do not sell, trade, or rent your personal information to third parties. We may share information with government agencies as required to complete your document preparation services (e.g., IRS, Secretary of State).</p>

<h2>Contact Us</h2>
<p>If you have questions about this Privacy Policy, please contact us at <a href="mailto:info@onetenunited.com">info@onetenunited.com</a> or call (718) 555-0100.</p>',
		),
		'terms-conditions'   => array(
			'title'   => 'Terms &amp; Conditions',
			'content' => '<p>Please read these Terms and Conditions carefully before using the One Ten United Services website and services.</p>

<h2>Services</h2>
<p>One Ten United Services provides clerical and document preparation services only. We are not a law firm. We do not provide legal advice. Using our services does not create an attorney-client relationship.</p>

<h2>Payment</h2>
<p>Payment is required at the time of service. All fees are listed on our services pages. We accept major credit cards and other electronic payment methods via our secure checkout.</p>

<h2>Refund Policy</h2>
<p>Please see our <a href="/refund-policy">Refund Policy</a> for details on cancellations and refunds.</p>

<h2>Accuracy of Information</h2>
<p>You are responsible for providing accurate and complete information required for document preparation. We are not liable for errors resulting from inaccurate information provided by you.</p>

<h2>Limitation of Liability</h2>
<p>Our liability is limited to the fees paid for the specific service in question. We are not responsible for any government rejections, delays, or decisions related to submitted documents.</p>

<h2>Changes to Terms</h2>
<p>We reserve the right to modify these Terms at any time. Continued use of our services constitutes acceptance of the updated Terms.</p>

<h2>Contact</h2>
<p>For questions, contact us at <a href="mailto:info@onetenunited.com">info@onetenunited.com</a>.</p>',
		),
		'refund-policy'      => array(
			'title'   => 'Refund Policy',
			'content' => '<p>At One Ten United Services, we strive to deliver high-quality document preparation services. Please review our refund policy below.</p>

<h2>Refund Eligibility</h2>
<ul>
<li><strong>Before work begins:</strong> Full refund within 24 hours of payment, before we have started preparing your documents.</li>
<li><strong>Work in progress:</strong> A partial refund (50%) may be issued if work has begun but has not been submitted to the relevant agency.</li>
<li><strong>After submission:</strong> No refund is available once documents have been submitted to a government agency on your behalf.</li>
</ul>

<h2>Non-Refundable Items</h2>
<p>Government filing fees, third-party fees, and expedited processing fees are non-refundable under any circumstances.</p>

<h2>How to Request a Refund</h2>
<p>To request a refund, contact us at <a href="mailto:info@onetenunited.com">info@onetenunited.com</a> or call (718) 555-0100 within the applicable timeframe. Include your order number and reason for the refund request.</p>

<h2>Processing Time</h2>
<p>Approved refunds are processed within 5–10 business days to the original payment method.</p>',
		),
		'disclaimer'         => array(
			'title'   => 'Disclaimer',
			'content' => '<p><strong>We are not a law firm and do not provide legal advice. We provide clerical and document preparation services only.</strong></p>

<p>One Ten United Services is a document preparation company. We prepare documents and provide general information based on the information you provide to us. We are not licensed attorneys and cannot provide legal advice, represent you in court, or act as your attorney in any manner.</p>

<p>By using our services, you acknowledge that:</p>

<ul>
<li>We are not a law firm and do not provide legal advice of any kind.</li>
<li>We provide clerical and document preparation services only.</li>
<li>The use of our services does not create an attorney-client relationship.</li>
<li>Any information provided by us is general in nature and should not be relied upon as legal advice.</li>
<li>For legal advice specific to your situation, you should consult a licensed attorney.</li>
<li>For immigration matters, we are document preparers, not attorneys, and are not authorized to practice immigration law.</li>
</ul>

<p>If you require legal advice or representation, we recommend consulting a licensed attorney.</p>',
		),
		'dashboard'          => array(
			'title'   => 'Dashboard',
			'content' => '[otu_dashboard]',
		),
		'book-appointment'   => array(
			'title'   => 'Book an Appointment',
			'content' => '[otu_booking_form]',
		),
	);

	/**
	 * Plugin activation routine.
	 */
	public static function activate() {
		// Register CPTs and taxonomies early so flush_rewrite_rules works.
		$cpt = new OTU_CPT();
		$cpt->register_taxonomies();
		$cpt->register_post_types();

		$booking = new OTU_Booking();
		$booking->register_post_type();

		$certificate = new OTU_Certificate();
		$certificate->register_post_type();

		// Seed default service category terms before seeding posts.
		$cpt->create_default_terms();

		// Seed default service posts.
		self::seed_services();

		// Create pages.
		$page_ids = self::create_pages();

		// Set front page and blog page.
		self::set_front_page( $page_ids );

		// Create and assign navigation menus.
		self::create_nav_menus( $page_ids );

		// Scaffold LMS courses if Tutor LMS is active.
		if ( function_exists( 'tutor' ) ) {
			OTU_LMS::scaffold_courses();
		}

		// Ensure the certificate upload directory exists.
		$upload_dir  = wp_upload_dir();
		$cert_dir    = trailingslashit( $upload_dir['basedir'] ) . 'otu-certificates/';
		$cert_dir_ok = true;
		if ( ! file_exists( $cert_dir ) ) {
			if ( ! wp_mkdir_p( $cert_dir ) ) {
				$cert_dir_ok = false;
				set_transient(
					'otu_cert_dir_error',
					sprintf(
						/* translators: %s: directory path */
						__( 'OTU Services: Could not create certificate directory at %s. Certificate PDFs will not be generated until this is resolved.', 'otu' ),
						$cert_dir
					),
					DAY_IN_SECONDS
				);
			}
		}

		// Add .htaccess to protect certificate directory (Apache).
		$htaccess = $cert_dir . '.htaccess';
		if ( $cert_dir_ok && ! file_exists( $htaccess ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents(
				$htaccess,
				"Options -Indexes\nDeny from all\n"
			);
		}

		flush_rewrite_rules();
		set_transient( 'otu_activated', true, 30 );
	}

	/**
	 * Plugin deactivation routine.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Seed default service CPT posts so that homepage cards resolve to real
	 * permalinks instead of falling back to the archive or returning 404s.
	 * Idempotent — skips any post whose slug already exists.
	 * Public so it can be invoked from upgrade routines.
	 */
	public static function seed_services() {
		foreach ( self::$services as $slug => $data ) {
			$existing = get_page_by_path( $slug, OBJECT, 'service' );
			if ( $existing ) {
				continue;
			}

			$post_id = wp_insert_post( array(
				'post_type'    => 'service',
				'post_title'   => $data['title'],
				'post_name'    => $slug,
				'post_excerpt' => $data['excerpt'],
				'post_content' => '<p>' . $data['excerpt'] . '</p>',
				'post_status'  => 'publish',
			) );

			if ( ! is_wp_error( $post_id ) && $post_id && ! empty( $data['category'] ) ) {
				$term = get_term_by( 'name', $data['category'], 'service_category' );
				if ( $term ) {
					wp_set_object_terms( $post_id, $term->term_id, 'service_category' );
				}
			}
		}
	}

	/**
	 * Create all required pages if they don't already exist.
	 *
	 * @return array Map of slug => post ID for created/existing pages.
	 */
	private static function create_pages() {
		$page_ids = array();

		foreach ( self::$pages as $slug => $data ) {
			$existing = get_page_by_path( $slug );
			if ( $existing ) {
				$page_ids[ $slug ] = $existing->ID;
				continue;
			}

			$page_args = array(
				'post_type'      => 'page',
				'post_title'     => $data['title'],
				'post_name'      => $slug,
				'post_content'   => isset( $data['content'] ) ? $data['content'] : '',
				'post_status'    => 'publish',
				'comment_status' => 'closed',
			);

			$page_id = wp_insert_post( $page_args );

			if ( ! is_wp_error( $page_id ) && $page_id ) {
				if ( ! empty( $data['template'] ) ) {
					update_post_meta( $page_id, '_wp_page_template', $data['template'] . '.php' );
				}
				$page_ids[ $slug ] = $page_id;
			}
		}

		return $page_ids;
	}

	/**
	 * Set WordPress front page and posts page.
	 *
	 * @param array $page_ids Map of slug => post ID.
	 */
	private static function set_front_page( $page_ids ) {
		if ( 'page' !== get_option( 'show_on_front' ) ) {
			update_option( 'show_on_front', 'page' );
		}

		if ( ! empty( $page_ids['home'] ) && ! get_option( 'page_on_front' ) ) {
			update_option( 'page_on_front', $page_ids['home'] );
		}

		if ( ! empty( $page_ids['blog'] ) && ! get_option( 'page_for_posts' ) ) {
			update_option( 'page_for_posts', $page_ids['blog'] );
		}
	}

	/**
	 * Create Primary and Footer navigation menus and assign pages.
	 *
	 * @param array $page_ids Map of slug => post ID.
	 */
	private static function create_nav_menus( $page_ids ) {
		// Primary menu.
		$primary_menu_id = 0;
		$primary_term    = get_term_by( 'name', 'Primary Menu', 'nav_menu' );
		if ( ! $primary_term ) {
			$created = wp_create_nav_menu( 'Primary Menu' );
			if ( ! is_wp_error( $created ) && $created ) {
				$primary_menu_id = (int) $created;
			}
		} else {
			$primary_menu_id = $primary_term->term_id;
		}

		// Footer menu.
		$footer_menu_id = 0;
		$footer_term    = get_term_by( 'name', 'Footer Menu', 'nav_menu' );
		if ( ! $footer_term ) {
			$created = wp_create_nav_menu( 'Footer Menu' );
			if ( ! is_wp_error( $created ) && $created ) {
				$footer_menu_id = (int) $created;
			}
		} else {
			$footer_menu_id = $footer_term->term_id;
		}

		// Assign pages to primary menu (only if menu has no items yet).
		$primary_items = $primary_menu_id ? wp_get_nav_menu_items( $primary_menu_id ) : array();
		if ( $primary_menu_id && empty( $primary_items ) ) {
			$primary_pages = array( 'home', 'services', 'courses', 'digital-products', 'about-us', 'book-appointment', 'contact-us', 'dashboard' );
			foreach ( $primary_pages as $slug ) {
				if ( ! empty( $page_ids[ $slug ] ) ) {
					wp_update_nav_menu_item(
						$primary_menu_id,
						0,
						array(
							'menu-item-title'     => get_the_title( $page_ids[ $slug ] ),
							'menu-item-object-id' => $page_ids[ $slug ],
							'menu-item-object'    => 'page',
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						)
					);
				}
			}
		}

		// Assign pages to footer menu.
		$footer_items = $footer_menu_id ? wp_get_nav_menu_items( $footer_menu_id ) : array();
		if ( $footer_menu_id && empty( $footer_items ) ) {
			$footer_pages = array( 'privacy-policy', 'terms-conditions', 'refund-policy', 'disclaimer', 'contact-us' );
			foreach ( $footer_pages as $slug ) {
				if ( ! empty( $page_ids[ $slug ] ) ) {
					wp_update_nav_menu_item(
						$footer_menu_id,
						0,
						array(
							'menu-item-title'     => get_the_title( $page_ids[ $slug ] ),
							'menu-item-object-id' => $page_ids[ $slug ],
							'menu-item-object'    => 'page',
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						)
					);
				}
			}
		}

		// Assign menus to theme locations if the theme registers them.
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		if ( $primary_menu_id && empty( $locations['primary'] ) ) {
			$locations['primary'] = $primary_menu_id;
		}
		if ( $footer_menu_id && empty( $locations['footer'] ) ) {
			$locations['footer'] = $footer_menu_id;
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
