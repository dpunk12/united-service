<?php
/**
 * User Dashboard for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_Dashboard
 *
 * Renders a full-featured user dashboard via the [otu_dashboard] shortcode.
 */
class OTU_Dashboard {

	/**
	 * Dashboard tabs definition.
	 *
	 * @var array
	 */
	private $tabs = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->tabs = array(
			'overview'     => __( 'Overview', 'otu' ),
			'orders'       => __( 'My Orders', 'otu' ),
			'downloads'    => __( 'My Downloads', 'otu' ),
			'courses'      => __( 'My Courses', 'otu' ),
			'certificates' => __( 'My Certificates', 'otu' ),
			'bookings'     => __( 'My Bookings', 'otu' ),
			'profile'      => __( 'Profile', 'otu' ),
		);

		add_shortcode( 'otu_dashboard', array( $this, 'render_dashboard' ) );
		add_action( 'wp_ajax_otu_update_profile', array( $this, 'handle_profile_update' ) );
	}

	/**
	 * Render the full dashboard.
	 *
	 * @return string
	 */
	public function render_dashboard() {
		ob_start();

		if ( ! is_user_logged_in() ) {
			$this->render_login_prompt();
		} else {
			$this->render_logged_in_dashboard();
		}

		return ob_get_clean();
	}

	/**
	 * Show login form for unauthenticated users.
	 */
	private function render_login_prompt() {
		$register_url = wp_registration_url();
		?>
		<div class="otu-dashboard-login">
			<h2><?php esc_html_e( 'Please Log In', 'otu' ); ?></h2>
			<p><?php esc_html_e( 'You need to be logged in to access your dashboard.', 'otu' ); ?></p>
			<?php
			wp_login_form(
				array(
					'redirect'       => esc_url( get_permalink() ),
					'label_username' => __( 'Username or Email Address', 'otu' ),
					'label_password' => __( 'Password', 'otu' ),
					'label_remember' => __( 'Remember Me', 'otu' ),
					'label_log_in'   => __( 'Log In', 'otu' ),
				)
			);
			?>
			<p class="otu-register-link">
				<?php esc_html_e( "Don't have an account?", 'otu' ); ?>
				<a href="<?php echo esc_url( $register_url ); ?>"><?php esc_html_e( 'Register here', 'otu' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Render the logged-in user dashboard.
	 */
	private function render_logged_in_dashboard() {
		$user    = wp_get_current_user();
		$user_id = $user->ID;

		// Determine active tab.
		$active_tab = isset( $_GET['otu_tab'] ) ? sanitize_key( wp_unslash( $_GET['otu_tab'] ) ) : 'overview';
		if ( ! array_key_exists( $active_tab, $this->tabs ) ) {
			$active_tab = 'overview';
		}
		?>
		<div class="otu-dashboard" id="otu-dashboard">
			<div class="otu-dashboard-header">
				<h2>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: user display name */
							__( 'Welcome back, %s!', 'otu' ),
							$user->display_name
						)
					);
					?>
				</h2>
			</div>

			<nav class="otu-tab-nav" role="tablist">
				<?php foreach ( $this->tabs as $slug => $label ) : ?>
					<button
						class="otu-tab<?php echo $active_tab === $slug ? ' otu-tab-active' : ''; ?>"
						data-tab="<?php echo esc_attr( $slug ); ?>"
						role="tab"
						aria-selected="<?php echo $active_tab === $slug ? 'true' : 'false'; ?>"
						aria-controls="otu-tab-<?php echo esc_attr( $slug ); ?>">
						<?php echo esc_html( $label ); ?>
					</button>
				<?php endforeach; ?>
			</nav>

			<div class="otu-tab-panels">
				<div id="otu-tab-overview"
					class="otu-tab-content<?php echo 'overview' === $active_tab ? ' otu-tab-content-active' : ''; ?>"
					role="tabpanel">
					<?php $this->render_overview( $user_id ); ?>
				</div>

				<div id="otu-tab-orders"
					class="otu-tab-content<?php echo 'orders' === $active_tab ? ' otu-tab-content-active' : ''; ?>"
					role="tabpanel">
					<?php $this->render_orders( $user_id ); ?>
				</div>

				<div id="otu-tab-downloads"
					class="otu-tab-content<?php echo 'downloads' === $active_tab ? ' otu-tab-content-active' : ''; ?>"
					role="tabpanel">
					<?php $this->render_downloads( $user_id ); ?>
				</div>

				<div id="otu-tab-courses"
					class="otu-tab-content<?php echo 'courses' === $active_tab ? ' otu-tab-content-active' : ''; ?>"
					role="tabpanel">
					<?php $this->render_courses( $user_id ); ?>
				</div>

				<div id="otu-tab-certificates"
					class="otu-tab-content<?php echo 'certificates' === $active_tab ? ' otu-tab-content-active' : ''; ?>"
					role="tabpanel">
					<?php $this->render_certificates( $user_id ); ?>
				</div>

				<div id="otu-tab-bookings"
					class="otu-tab-content<?php echo 'bookings' === $active_tab ? ' otu-tab-content-active' : ''; ?>"
					role="tabpanel">
					<?php $this->render_bookings( $user_id ); ?>
				</div>

				<div id="otu-tab-profile"
					class="otu-tab-content<?php echo 'profile' === $active_tab ? ' otu-tab-content-active' : ''; ?>"
					role="tabpanel">
					<?php $this->render_profile( $user_id ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the overview tab.
	 *
	 * @param int $user_id User ID.
	 */
	private function render_overview( $user_id ) {
		$user = get_userdata( $user_id );

		// Quick stats.
		$order_count   = 0;
		$download_count = 0;
		if ( class_exists( 'WooCommerce' ) ) {
			$orders        = wc_get_orders( array( 'customer' => $user->user_email, 'limit' => -1, 'return' => 'ids' ) );
			$order_count   = count( $orders );
			foreach ( $orders as $order_id ) {
				$order = wc_get_order( $order_id );
				if ( $order ) {
					foreach ( $order->get_items( 'line_item' ) as $item ) {
						$product = $item->get_product();
						if ( $product && $product->is_downloadable() ) {
							$download_count++;
						}
					}
				}
			}
		}

		$course_count = 0;
		if ( function_exists( 'OTU_LMS::otu_get_user_courses' ) || class_exists( 'OTU_LMS' ) ) {
			$courses      = OTU_LMS::otu_get_user_courses( $user_id );
			$course_count = count( $courses );
		}

		$cert_count = class_exists( 'OTU_Certificate' ) ? count( OTU_Certificate::get_user_certificates( $user_id ) ) : 0;
		?>
		<div class="otu-overview">
			<div class="otu-stats-grid">
				<div class="otu-stat-card">
					<span class="otu-stat-number"><?php echo esc_html( $order_count ); ?></span>
					<span class="otu-stat-label"><?php esc_html_e( 'Orders', 'otu' ); ?></span>
				</div>
				<div class="otu-stat-card">
					<span class="otu-stat-number"><?php echo esc_html( $course_count ); ?></span>
					<span class="otu-stat-label"><?php esc_html_e( 'Courses', 'otu' ); ?></span>
				</div>
				<div class="otu-stat-card">
					<span class="otu-stat-number"><?php echo esc_html( $download_count ); ?></span>
					<span class="otu-stat-label"><?php esc_html_e( 'Downloads', 'otu' ); ?></span>
				</div>
				<div class="otu-stat-card">
					<span class="otu-stat-number"><?php echo esc_html( $cert_count ); ?></span>
					<span class="otu-stat-label"><?php esc_html_e( 'Certificates', 'otu' ); ?></span>
				</div>
			</div>

			<div class="otu-overview-welcome">
				<h3><?php esc_html_e( 'Quick Links', 'otu' ); ?></h3>
				<ul class="otu-quick-links">
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( '→ Browse Services', 'otu' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/courses/' ) ); ?>"><?php esc_html_e( '→ View Courses', 'otu' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/book-appointment/' ) ); ?>"><?php esc_html_e( '→ Book Appointment', 'otu' ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the orders tab.
	 *
	 * @param int $user_id User ID.
	 */
	private function render_orders( $user_id ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p>' . esc_html__( 'WooCommerce is not active.', 'otu' ) . '</p>';
			return;
		}

		$user   = get_userdata( $user_id );
		$orders = wc_get_orders(
			array(
				'customer'   => $user->user_email,
				'limit'      => 20,
				'orderby'    => 'date',
				'order'      => 'DESC',
			)
		);

		if ( empty( $orders ) ) {
			echo '<p class="otu-no-data">' . esc_html__( 'You have not placed any orders yet.', 'otu' ) . '</p>';
			return;
		}
		?>
		<div class="otu-orders-list">
			<h3><?php esc_html_e( 'My Orders', 'otu' ); ?></h3>
			<table class="otu-data-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Order #', 'otu' ); ?></th>
						<th><?php esc_html_e( 'Date', 'otu' ); ?></th>
						<th><?php esc_html_e( 'Status', 'otu' ); ?></th>
						<th><?php esc_html_e( 'Total', 'otu' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'otu' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $orders as $order ) : ?>
						<tr>
							<td><?php echo esc_html( '#' . $order->get_order_number() ); ?></td>
							<td><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></td>
							<td>
								<span class="otu-order-status otu-status-<?php echo esc_attr( $order->get_status() ); ?>">
									<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
								</span>
							</td>
							<td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
							<td>
								<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="otu-btn otu-btn-sm">
									<?php esc_html_e( 'View', 'otu' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render the downloads tab.
	 *
	 * @param int $user_id User ID.
	 */
	private function render_downloads( $user_id ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<p>' . esc_html__( 'WooCommerce is not active.', 'otu' ) . '</p>';
			return;
		}

		$user      = get_userdata( $user_id );
		$downloads = wc_get_customer_available_downloads( $user_id );

		if ( empty( $downloads ) ) {
			echo '<p class="otu-no-data">' . esc_html__( 'No downloadable files available yet.', 'otu' ) . '</p>';
			return;
		}
		?>
		<div class="otu-downloads-list">
			<h3><?php esc_html_e( 'My Downloads', 'otu' ); ?></h3>
			<table class="otu-data-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Product', 'otu' ); ?></th>
						<th><?php esc_html_e( 'Downloads Remaining', 'otu' ); ?></th>
						<th><?php esc_html_e( 'Expires', 'otu' ); ?></th>
						<th><?php esc_html_e( 'Download', 'otu' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $downloads as $download ) : ?>
						<tr>
							<td><?php echo esc_html( $download['product_name'] ); ?></td>
							<td>
								<?php
								if ( '' === $download['downloads_remaining'] ) {
									esc_html_e( 'Unlimited', 'otu' );
								} else {
									echo esc_html( $download['downloads_remaining'] );
								}
								?>
							</td>
							<td>
								<?php
								if ( $download['access_expires'] ) {
									echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) );
								} else {
									esc_html_e( 'Never', 'otu' );
								}
								?>
							</td>
							<td>
								<a href="<?php echo esc_url( $download['download_url'] ); ?>" class="otu-btn otu-btn-sm otu-btn-download">
									<?php esc_html_e( '⬇ Download', 'otu' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render the courses tab.
	 *
	 * @param int $user_id User ID.
	 */
	private function render_courses( $user_id ) {
		if ( ! function_exists( 'tutor' ) ) {
			echo '<p>' . esc_html__( 'Tutor LMS is required for course enrollment.', 'otu' ) . '</p>';
			return;
		}

		$courses = OTU_LMS::otu_get_user_courses( $user_id );

		if ( empty( $courses ) ) {
			echo '<p class="otu-no-data">' . esc_html__( "You haven't enrolled in any courses yet.", 'otu' ) . '</p>';
			$courses_url = home_url( '/courses/' );
			echo '<a href="' . esc_url( $courses_url ) . '" class="otu-btn otu-btn-primary">' . esc_html__( 'Browse Courses', 'otu' ) . '</a>';
			return;
		}
		?>
		<div class="otu-courses-list">
			<h3><?php esc_html_e( 'My Courses', 'otu' ); ?></h3>
			<div class="otu-courses-grid">
				<?php foreach ( $courses as $course ) : ?>
					<div class="otu-course-card">
						<?php if ( ! empty( $course['thumbnail'] ) ) : ?>
							<img src="<?php echo esc_url( $course['thumbnail'] ); ?>" alt="<?php echo esc_attr( $course['title'] ); ?>" class="otu-course-thumb" />
						<?php endif; ?>
						<div class="otu-course-info">
							<h4><?php echo esc_html( $course['title'] ); ?></h4>
							<div class="otu-progress-bar-wrap">
								<div class="otu-progress-bar" style="width:<?php echo esc_attr( $course['progress'] ); ?>%;" data-progress="<?php echo esc_attr( $course['progress'] ); ?>"></div>
							</div>
							<p class="otu-progress-label"><?php echo esc_html( $course['progress'] ); ?>% <?php esc_html_e( 'Complete', 'otu' ); ?></p>
							<a href="<?php echo esc_url( $course['permalink'] ); ?>" class="otu-btn otu-btn-sm">
								<?php esc_html_e( 'Continue Course', 'otu' ); ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the certificates tab.
	 *
	 * @param int $user_id User ID.
	 */
	private function render_certificates( $user_id ) {
		$certificates = class_exists( 'OTU_Certificate' ) ? OTU_Certificate::get_user_certificates( $user_id ) : array();

		if ( empty( $certificates ) ) {
			echo '<p class="otu-no-data">' . esc_html__( 'You have not earned any certificates yet. Complete a course to earn your certificate.', 'otu' ) . '</p>';
			return;
		}
		?>
		<div class="otu-certificates-list">
			<h3><?php esc_html_e( 'My Certificates', 'otu' ); ?></h3>
			<div class="otu-certificates-grid">
				<?php foreach ( $certificates as $cert ) : ?>
					<?php
					$cert_id        = $cert->ID;
					$certificate_id = get_post_meta( $cert_id, '_certificate_id', true );
					$issue_date     = get_post_meta( $cert_id, '_issue_date', true );
					$course_id      = absint( get_post_meta( $cert_id, '_course_id', true ) );
					$course_name    = get_the_title( $course_id );
					$pdf_path       = get_post_meta( $cert_id, '_pdf_path', true );
					$has_pdf        = ! empty( $pdf_path ) && file_exists( $pdf_path );
					$download_url   = add_query_arg(
						array(
							'action'  => 'otu_download_certificate',
							'cert_id' => $cert_id,
							'nonce'   => wp_create_nonce( 'otu_download_cert_' . $cert_id ),
						),
						admin_url( 'admin-ajax.php' )
					);
					?>
					<div class="otu-certificate-card">
						<div class="otu-certificate-header">
							<span class="otu-cert-icon">🏆</span>
							<h4><?php echo esc_html( $course_name ); ?></h4>
						</div>
						<div class="otu-certificate-body">
							<p><strong><?php esc_html_e( 'Certificate ID:', 'otu' ); ?></strong> <?php echo esc_html( $certificate_id ); ?></p>
							<p><strong><?php esc_html_e( 'Issued:', 'otu' ); ?></strong> <?php echo esc_html( $issue_date ); ?></p>
						</div>
						<div class="otu-certificate-footer">
							<?php if ( $has_pdf ) : ?>
								<a href="<?php echo esc_url( $download_url ); ?>"
									class="otu-btn otu-btn-download otu-download-certificate"
									data-cert-id="<?php echo esc_attr( $cert_id ); ?>"
									data-nonce="<?php echo esc_attr( wp_create_nonce( 'otu_download_cert_' . $cert_id ) ); ?>">
									<?php esc_html_e( '⬇ Download PDF', 'otu' ); ?>
								</a>
							<?php else : ?>
								<p class="otu-cert-no-pdf"><?php esc_html_e( 'PDF generating…', 'otu' ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the bookings tab.
	 *
	 * @param int $user_id User ID.
	 */
	private function render_bookings( $user_id ) {
		$user   = get_userdata( $user_id );
		$query  = class_exists( 'OTU_Booking' ) ? OTU_Booking::get_user_bookings( $user->user_email ) : new WP_Query( array( 'post__in' => array( 0 ) ) );

		$today    = gmdate( 'Y-m-d' );
		$upcoming = array();
		$past     = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$booking_id   = get_the_ID();
				$booking_date = get_post_meta( $booking_id, '_booking_date', true );
				if ( $booking_date >= $today ) {
					$upcoming[] = $booking_id;
				} else {
					$past[] = $booking_id;
				}
			}
			wp_reset_postdata();
		}
		?>
		<div class="otu-bookings-wrap">
			<h3><?php esc_html_e( 'My Bookings', 'otu' ); ?></h3>

			<h4><?php esc_html_e( 'Upcoming Appointments', 'otu' ); ?></h4>
			<?php if ( empty( $upcoming ) ) : ?>
				<p class="otu-no-data"><?php esc_html_e( 'No upcoming appointments.', 'otu' ); ?></p>
			<?php else : ?>
				<table class="otu-data-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Service', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Date', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Time', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Status', 'otu' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $upcoming as $bid ) : ?>
							<tr>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_service', true ) ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_date', true ) ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_time', true ) ); ?></td>
								<td>
									<span class="otu-booking-status otu-status-<?php echo esc_attr( get_post_meta( $bid, '_booking_status', true ) ); ?>">
										<?php echo esc_html( ucfirst( get_post_meta( $bid, '_booking_status', true ) ) ); ?>
									</span>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<h4><?php esc_html_e( 'Past Appointments', 'otu' ); ?></h4>
			<?php if ( empty( $past ) ) : ?>
				<p class="otu-no-data"><?php esc_html_e( 'No past appointments.', 'otu' ); ?></p>
			<?php else : ?>
				<table class="otu-data-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Service', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Date', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Time', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Status', 'otu' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $past as $bid ) : ?>
							<tr>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_service', true ) ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_date', true ) ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_time', true ) ); ?></td>
								<td>
									<span class="otu-booking-status otu-status-<?php echo esc_attr( get_post_meta( $bid, '_booking_status', true ) ); ?>">
										<?php echo esc_html( ucfirst( get_post_meta( $bid, '_booking_status', true ) ) ); ?>
									</span>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the profile tab.
	 *
	 * @param int $user_id User ID.
	 */
	private function render_profile( $user_id ) {
		$user  = get_userdata( $user_id );
		$phone = get_user_meta( $user_id, 'billing_phone', true );
		?>
		<div class="otu-profile-wrap">
			<h3><?php esc_html_e( 'My Profile', 'otu' ); ?></h3>
			<div id="otu-profile-messages"></div>
			<form id="otu-profile-form" class="otu-profile-form" method="post">
				<?php wp_nonce_field( 'otu_update_profile_nonce', 'otu_profile_nonce' ); ?>
				<input type="hidden" name="action" value="otu_update_profile" />

				<div class="otu-form-row">
					<label for="otu_first_name"><?php esc_html_e( 'First Name', 'otu' ); ?></label>
					<input type="text" id="otu_first_name" name="first_name"
						value="<?php echo esc_attr( $user->first_name ); ?>" />
				</div>

				<div class="otu-form-row">
					<label for="otu_last_name"><?php esc_html_e( 'Last Name', 'otu' ); ?></label>
					<input type="text" id="otu_last_name" name="last_name"
						value="<?php echo esc_attr( $user->last_name ); ?>" />
				</div>

				<div class="otu-form-row">
					<label for="otu_email"><?php esc_html_e( 'Email Address', 'otu' ); ?></label>
					<input type="email" id="otu_email" name="email"
						value="<?php echo esc_attr( $user->user_email ); ?>" />
				</div>

				<div class="otu-form-row">
					<label for="otu_phone"><?php esc_html_e( 'Phone Number', 'otu' ); ?></label>
					<input type="tel" id="otu_phone" name="phone"
						value="<?php echo esc_attr( $phone ); ?>" />
				</div>

				<div class="otu-form-row">
					<button type="submit" class="otu-btn otu-btn-primary">
						<?php esc_html_e( 'Update Profile', 'otu' ); ?>
					</button>
					<span class="otu-spinner" style="display:none;"></span>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle profile update AJAX request.
	 */
	public function handle_profile_update() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in.', 'otu' ) ) );
		}

		check_ajax_referer( 'otu_update_profile_nonce', 'otu_profile_nonce' );

		$user_id    = get_current_user_id();
		$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
		$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'otu' ) ) );
		}

		$update_args = array(
			'ID'         => $user_id,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'user_email' => $email,
		);

		$result = wp_update_user( $update_args );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		update_user_meta( $user_id, 'billing_phone', $phone );

		wp_send_json_success( array( 'message' => __( 'Profile updated successfully!', 'otu' ) ) );
	}
}
