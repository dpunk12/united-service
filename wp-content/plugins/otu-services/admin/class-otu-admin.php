<?php
/**
 * Admin Area for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_Admin
 *
 * Handles admin menus, settings pages, booking management and certificate management.
 */
class OTU_Admin {

	/**
	 * Settings option key.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'otu_settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_otu_update_booking_status', array( $this, 'ajax_update_booking_status' ) );
		add_action( 'wp_ajax_otu_regenerate_certificate', array( $this, 'ajax_regenerate_certificate' ) );
		add_action( 'admin_notices', array( $this, 'activation_notice' ) );
		add_action( 'wp_ajax_otu_dismiss_backup_notice', array( $this, 'ajax_dismiss_backup_notice' ) );
	}

	/**
	 * AJAX handler: dismiss the UpdraftPlus backup recommendation notice for 30 days.
	 */
	public function ajax_dismiss_backup_notice() {
		check_ajax_referer( 'otu_dismiss_backup_notice', 'nonce' );
		set_transient( 'otu_backup_notice_dismissed', true, 30 * DAY_IN_SECONDS );
		wp_send_json_success();
	}

	/**
	 * Show one-time activation notice.
	 */
	public function activation_notice() {
		if ( get_transient( 'otu_activated' ) ) {
			delete_transient( 'otu_activated' );
			?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: settings page link */
							__( '<strong>OTU Services</strong> has been activated. Visit the %s to configure the plugin.', 'otu' ),
							'<a href="' . esc_url( admin_url( 'admin.php?page=otu-settings' ) ) . '">' . esc_html__( 'Settings Page', 'otu' ) . '</a>'
						)
					);
					?>
				</p>
			</div>
			<?php
		}

		// Backup plugin recommendation notice (dismissible, shown once per day).
		if (
			! class_exists( 'UpdraftPlus' )
			&& current_user_can( 'install_plugins' )
			&& ! get_transient( 'otu_backup_notice_dismissed' )
		) {
			?>
			<div class="notice notice-warning is-dismissible" id="otu-backup-notice">
				<p>
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: link to install UpdraftPlus */
							__( '<strong>OTU Services recommends:</strong> Install %s to enable automated backups of your website (required by project security requirements).', 'otu' ),
							'<a href="' . esc_url( admin_url( 'plugin-install.php?s=updraftplus&tab=search&type=term' ) ) . '" target="_blank" rel="noopener noreferrer">UpdraftPlus Backup</a>'
						)
					);
					?>
				</p>
			</div>
			<script>
			(function($){
				$(document).on('click', '#otu-backup-notice .notice-dismiss', function(){
					$.post(ajaxurl, {
						action: 'otu_dismiss_backup_notice',
						nonce: '<?php echo esc_js( wp_create_nonce( 'otu_dismiss_backup_notice' ) ); ?>'
					});
				});
			})(jQuery);
			</script>
			<?php
		}
	}

	/**
	 * Register admin menus and submenus.
	 */
	public function add_admin_menus() {
		add_menu_page(
			__( 'OTU Services', 'otu' ),
			__( 'OTU Services', 'otu' ),
			'manage_options',
			'otu-services',
			array( $this, 'render_dashboard_page' ),
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#a0a5aa" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-4h2v2h-2zm0-10h2v8h-2z"/></svg>' ),
			30
		);

		add_submenu_page(
			'otu-services',
			__( 'Dashboard', 'otu' ),
			__( 'Dashboard', 'otu' ),
			'manage_options',
			'otu-services',
			array( $this, 'render_dashboard_page' )
		);

		add_submenu_page(
			'otu-services',
			__( 'Settings', 'otu' ),
			__( 'Settings', 'otu' ),
			'manage_options',
			'otu-settings',
			array( $this, 'render_settings_page' )
		);

		add_submenu_page(
			'otu-services',
			__( 'Bookings', 'otu' ),
			__( 'Bookings', 'otu' ),
			'manage_options',
			'otu-bookings',
			array( $this, 'render_bookings_page' )
		);

		add_submenu_page(
			'otu-services',
			__( 'Certificates', 'otu' ),
			__( 'Certificates', 'otu' ),
			'manage_options',
			'otu-certificates',
			array( $this, 'render_certificates_page' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'otu_settings_group',
			self::OPTION_KEY,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		// General section.
		add_settings_section( 'otu_general_section', __( 'General Settings', 'otu' ), null, 'otu-settings-general' );
		add_settings_field( 'otu_site_name', __( 'Site Name', 'otu' ), array( $this, 'field_site_name' ), 'otu-settings-general', 'otu_general_section' );
		add_settings_field( 'otu_contact_email', __( 'Contact Email', 'otu' ), array( $this, 'field_contact_email' ), 'otu-settings-general', 'otu_general_section' );
		add_settings_field( 'otu_phone', __( 'Phone', 'otu' ), array( $this, 'field_phone' ), 'otu-settings-general', 'otu_general_section' );
		add_settings_field( 'otu_address', __( 'Address', 'otu' ), array( $this, 'field_address' ), 'otu-settings-general', 'otu_general_section' );

		// Payment section.
		add_settings_section( 'otu_payment_section', __( 'Payment Settings', 'otu' ), array( $this, 'payment_section_desc' ), 'otu-settings-payment' );
		add_settings_field( 'otu_stripe_key', __( 'Stripe Publishable Key', 'otu' ), array( $this, 'field_stripe_key' ), 'otu-settings-payment', 'otu_payment_section' );
		add_settings_field( 'otu_paypal_client_id', __( 'PayPal Client ID', 'otu' ), array( $this, 'field_paypal_client_id' ), 'otu-settings-payment', 'otu_payment_section' );

		// Email section.
		add_settings_section( 'otu_email_section', __( 'Email Settings', 'otu' ), null, 'otu-settings-email' );
		add_settings_field( 'otu_admin_email', __( 'Admin Email', 'otu' ), array( $this, 'field_admin_email' ), 'otu-settings-email', 'otu_email_section' );
		add_settings_field( 'otu_from_name', __( 'From Name', 'otu' ), array( $this, 'field_from_name' ), 'otu-settings-email', 'otu_email_section' );
		add_settings_field( 'otu_from_email', __( 'From Email', 'otu' ), array( $this, 'field_from_email' ), 'otu-settings-email', 'otu_email_section' );
		add_settings_field( 'otu_bcc', __( 'BCC', 'otu' ), array( $this, 'field_bcc' ), 'otu-settings-email', 'otu_email_section' );

		// Notifications section.
		add_settings_section( 'otu_notifications_section', __( 'Notification Toggles', 'otu' ), null, 'otu-settings-notifications' );
		add_settings_field( 'otu_notify_booking', __( 'Booking Emails', 'otu' ), array( $this, 'field_notify_booking' ), 'otu-settings-notifications', 'otu_notifications_section' );
		add_settings_field( 'otu_notify_order', __( 'Order Emails', 'otu' ), array( $this, 'field_notify_order' ), 'otu-settings-notifications', 'otu_notifications_section' );
		add_settings_field( 'otu_notify_certificate', __( 'Certificate Emails', 'otu' ), array( $this, 'field_notify_certificate' ), 'otu-settings-notifications', 'otu_notifications_section' );
	}

	/**
	 * Sanitise settings before saving.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$output = array();

		// General.
		$output['general']['site_name']      = sanitize_text_field( $input['general']['site_name'] ?? '' );
		$output['general']['contact_email']  = sanitize_email( $input['general']['contact_email'] ?? '' );
		$output['general']['phone']          = sanitize_text_field( $input['general']['phone'] ?? '' );
		$output['general']['address']        = sanitize_textarea_field( $input['general']['address'] ?? '' );

		// Payment — only store publishable/client keys (never secret keys).
		$output['payment']['stripe_key']      = sanitize_text_field( $input['payment']['stripe_key'] ?? '' );
		$output['payment']['paypal_client_id'] = sanitize_text_field( $input['payment']['paypal_client_id'] ?? '' );

		// Email.
		$output['email']['admin_email'] = sanitize_email( $input['email']['admin_email'] ?? '' );
		$output['email']['from_name']   = sanitize_text_field( $input['email']['from_name'] ?? '' );
		$output['email']['from_email']  = sanitize_email( $input['email']['from_email'] ?? '' );
		$output['email']['bcc']         = sanitize_email( $input['email']['bcc'] ?? '' );

		// Notifications.
		$output['notifications']['booking']     = ! empty( $input['notifications']['booking'] ) ? 1 : 0;
		$output['notifications']['order']       = ! empty( $input['notifications']['order'] ) ? 1 : 0;
		$output['notifications']['certificate'] = ! empty( $input['notifications']['certificate'] ) ? 1 : 0;

		return $output;
	}

	// ---------- Settings Fields ----------

	/** Render site name field. */
	public function field_site_name() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['general']['site_name'] ?? get_bloginfo( 'name' );
		echo '<input type="text" name="otu_settings[general][site_name]" value="' . esc_attr( $val ) . '" class="regular-text" />';
	}

	/** Render contact email field. */
	public function field_contact_email() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['general']['contact_email'] ?? get_option( 'admin_email' );
		echo '<input type="email" name="otu_settings[general][contact_email]" value="' . esc_attr( $val ) . '" class="regular-text" />';
	}

	/** Render phone field. */
	public function field_phone() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['general']['phone'] ?? '';
		echo '<input type="text" name="otu_settings[general][phone]" value="' . esc_attr( $val ) . '" class="regular-text" />';
	}

	/** Render address field. */
	public function field_address() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['general']['address'] ?? '';
		echo '<textarea name="otu_settings[general][address]" rows="3" class="large-text">' . esc_textarea( $val ) . '</textarea>';
	}

	/** Render payment section description. */
	public function payment_section_desc() {
		echo '<p class="description" style="color:#d63638;">';
		esc_html_e( '⚠ Only enter publishable/client ID keys here — never secret keys. For full payment integration, configure keys within the WooCommerce gateway settings.', 'otu' );
		echo '</p>';
	}

	/** Render Stripe key field. */
	public function field_stripe_key() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['payment']['stripe_key'] ?? '';
		echo '<input type="text" name="otu_settings[payment][stripe_key]" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="pk_live_..." />';
		echo '<p class="description">' . esc_html__( 'Stripe Publishable Key only (starts with pk_).', 'otu' ) . '</p>';
	}

	/** Render PayPal client ID field. */
	public function field_paypal_client_id() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['payment']['paypal_client_id'] ?? '';
		echo '<input type="text" name="otu_settings[payment][paypal_client_id]" value="' . esc_attr( $val ) . '" class="regular-text" />';
	}

	/** Render admin email field. */
	public function field_admin_email() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['email']['admin_email'] ?? get_option( 'admin_email' );
		echo '<input type="email" name="otu_settings[email][admin_email]" value="' . esc_attr( $val ) . '" class="regular-text" />';
	}

	/** Render from name field. */
	public function field_from_name() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['email']['from_name'] ?? get_bloginfo( 'name' );
		echo '<input type="text" name="otu_settings[email][from_name]" value="' . esc_attr( $val ) . '" class="regular-text" />';
	}

	/** Render from email field. */
	public function field_from_email() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['email']['from_email'] ?? get_option( 'admin_email' );
		echo '<input type="email" name="otu_settings[email][from_email]" value="' . esc_attr( $val ) . '" class="regular-text" />';
	}

	/** Render BCC field. */
	public function field_bcc() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['email']['bcc'] ?? '';
		echo '<input type="email" name="otu_settings[email][bcc]" value="' . esc_attr( $val ) . '" class="regular-text" />';
		echo '<p class="description">' . esc_html__( 'Optional — blind carbon copy all emails to this address.', 'otu' ) . '</p>';
	}

	/** Render booking email toggle. */
	public function field_notify_booking() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['notifications']['booking'] ?? 1;
		echo '<label><input type="checkbox" name="otu_settings[notifications][booking]" value="1"' . checked( 1, $val, false ) . ' /> ' . esc_html__( 'Send booking confirmation and admin notification emails', 'otu' ) . '</label>';
	}

	/** Render order email toggle. */
	public function field_notify_order() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['notifications']['order'] ?? 1;
		echo '<label><input type="checkbox" name="otu_settings[notifications][order]" value="1"' . checked( 1, $val, false ) . ' /> ' . esc_html__( 'Send OTU custom order confirmation email', 'otu' ) . '</label>';
	}

	/** Render certificate email toggle. */
	public function field_notify_certificate() {
		$opts = get_option( self::OPTION_KEY, array() );
		$val  = $opts['notifications']['certificate'] ?? 1;
		echo '<label><input type="checkbox" name="otu_settings[notifications][certificate]" value="1"' . checked( 1, $val, false ) . ' /> ' . esc_html__( 'Send certificate completion email', 'otu' ) . '</label>';
	}

	// ---------- Admin Pages ----------

	/**
	 * Render the OTU admin dashboard page.
	 */
	public function render_dashboard_page() {
		$booking_count = wp_count_posts( 'otu_booking' )->publish ?? 0;
		$cert_count    = wp_count_posts( 'otu_certificate' )->publish ?? 0;
		?>
		<div class="wrap otu-admin-wrap">
			<h1><?php esc_html_e( 'OTU Services Dashboard', 'otu' ); ?></h1>
			<div class="otu-admin-stats">
				<div class="otu-admin-stat-card">
					<span class="otu-admin-stat-number"><?php echo esc_html( $booking_count ); ?></span>
					<span class="otu-admin-stat-label"><?php esc_html_e( 'Total Bookings', 'otu' ); ?></span>
				</div>
				<div class="otu-admin-stat-card">
					<span class="otu-admin-stat-number"><?php echo esc_html( $cert_count ); ?></span>
					<span class="otu-admin-stat-label"><?php esc_html_e( 'Certificates Issued', 'otu' ); ?></span>
				</div>
			</div>
			<div class="otu-admin-quick-links">
				<h2><?php esc_html_e( 'Quick Links', 'otu' ); ?></h2>
				<ul>
					<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=otu-settings' ) ); ?>"><?php esc_html_e( '⚙ Settings', 'otu' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=otu-bookings' ) ); ?>"><?php esc_html_e( '📅 Manage Bookings', 'otu' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=otu-certificates' ) ); ?>"><?php esc_html_e( '🏆 Manage Certificates', 'otu' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=service' ) ); ?>"><?php esc_html_e( '🛠 Manage Services', 'otu' ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the settings page with tabs.
	 */
	public function render_settings_page() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
		$tabs       = array(
			'general'       => __( 'General', 'otu' ),
			'payment'       => __( 'Payment', 'otu' ),
			'email'         => __( 'Email', 'otu' ),
			'notifications' => __( 'Notifications', 'otu' ),
		);
		?>
		<div class="wrap otu-admin-wrap">
			<h1><?php esc_html_e( 'OTU Services Settings', 'otu' ); ?></h1>

			<nav class="otu-settings-tabs nav-tab-wrapper">
				<?php foreach ( $tabs as $slug => $label ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=otu-settings&tab=' . $slug ) ); ?>"
						class="nav-tab<?php echo $active_tab === $slug ? ' nav-tab-active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<form method="post" action="options.php" class="otu-settings-form">
				<?php
				settings_fields( 'otu_settings_group' );
				do_settings_sections( 'otu-settings-' . $active_tab );
				submit_button( __( 'Save Settings', 'otu' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the bookings management page.
	 */
	public function render_bookings_page() {
		$status_filter = isset( $_GET['booking_status'] ) ? sanitize_key( wp_unslash( $_GET['booking_status'] ) ) : '';
		$meta_query    = array();
		if ( in_array( $status_filter, array( 'pending', 'confirmed', 'cancelled' ), true ) ) {
			$meta_query[] = array(
				'key'   => '_booking_status',
				'value' => $status_filter,
			);
		}

		$query = new WP_Query(
			array(
				'post_type'      => 'otu_booking',
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_query'     => $meta_query,
			)
		);
		?>
		<div class="wrap otu-admin-wrap">
			<h1><?php esc_html_e( 'Booking Management', 'otu' ); ?></h1>

			<div class="otu-filter-bar">
				<span><?php esc_html_e( 'Filter by status:', 'otu' ); ?></span>
				<?php
				$statuses = array( '' => __( 'All', 'otu' ), 'pending' => __( 'Pending', 'otu' ), 'confirmed' => __( 'Confirmed', 'otu' ), 'cancelled' => __( 'Cancelled', 'otu' ) );
				foreach ( $statuses as $val => $label ) :
					$url = admin_url( 'admin.php?page=otu-bookings' . ( $val ? '&booking_status=' . $val : '' ) );
					?>
					<a href="<?php echo esc_url( $url ); ?>"
						class="otu-filter-btn<?php echo $status_filter === $val ? ' otu-filter-active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</div>

			<?php if ( ! $query->have_posts() ) : ?>
				<p><?php esc_html_e( 'No bookings found.', 'otu' ); ?></p>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped otu-admin-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'ID', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Customer', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Email', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Service', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Date', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Time', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Status', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'otu' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							$bid     = get_the_ID();
							$status  = get_post_meta( $bid, '_booking_status', true );
							?>
							<tr id="otu-booking-row-<?php echo esc_attr( $bid ); ?>">
								<td><?php echo esc_html( $bid ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_user_name', true ) ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_user_email', true ) ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_service', true ) ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_date', true ) ); ?></td>
								<td><?php echo esc_html( get_post_meta( $bid, '_booking_time', true ) ); ?></td>
								<td>
									<select class="otu-booking-status-select"
										data-booking-id="<?php echo esc_attr( $bid ); ?>"
										data-nonce="<?php echo esc_attr( wp_create_nonce( 'otu_admin_nonce' ) ); ?>">
										<option value="pending"<?php selected( $status, 'pending' ); ?>><?php esc_html_e( 'Pending', 'otu' ); ?></option>
										<option value="confirmed"<?php selected( $status, 'confirmed' ); ?>><?php esc_html_e( 'Confirmed', 'otu' ); ?></option>
										<option value="cancelled"<?php selected( $status, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'otu' ); ?></option>
									</select>
								</td>
								<td>
									<a href="<?php echo esc_url( get_edit_post_link( $bid ) ); ?>" class="button button-small">
										<?php esc_html_e( 'Edit', 'otu' ); ?>
									</a>
								</td>
							</tr>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the certificates management page.
	 */
	public function render_certificates_page() {
		$query = new WP_Query(
			array(
				'post_type'      => 'otu_certificate',
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		?>
		<div class="wrap otu-admin-wrap">
			<h1><?php esc_html_e( 'Certificate Management', 'otu' ); ?></h1>

			<?php if ( ! $query->have_posts() ) : ?>
				<p><?php esc_html_e( 'No certificates issued yet.', 'otu' ); ?></p>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped otu-admin-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'ID', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Certificate ID', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Student', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Course', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Issued', 'otu' ); ?></th>
							<th><?php esc_html_e( 'PDF', 'otu' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'otu' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							$cid            = get_the_ID();
							$certificate_id = get_post_meta( $cid, '_certificate_id', true );
							$issue_date     = get_post_meta( $cid, '_issue_date', true );
							$course_id      = absint( get_post_meta( $cid, '_course_id', true ) );
							$course_name    = get_the_title( $course_id );
							$student_id     = absint( get_post_meta( $cid, '_student_id', true ) );
							$student        = get_userdata( $student_id );
							$student_name   = $student ? esc_html( $student->display_name ) : esc_html__( 'Unknown', 'otu' );
							$pdf_path       = get_post_meta( $cid, '_pdf_path', true );
							$has_pdf        = ! empty( $pdf_path ) && file_exists( $pdf_path );
							?>
							<tr id="otu-cert-row-<?php echo esc_attr( $cid ); ?>">
								<td><?php echo esc_html( $cid ); ?></td>
								<td><?php echo esc_html( $certificate_id ); ?></td>
								<td><?php echo esc_html( $student_name ); ?></td>
								<td><?php echo esc_html( $course_name ); ?></td>
								<td><?php echo esc_html( $issue_date ); ?></td>
								<td>
									<?php if ( $has_pdf ) : ?>
										<span style="color:green;">✓ <?php esc_html_e( 'Available', 'otu' ); ?></span>
									<?php else : ?>
										<span style="color:#888;"><?php esc_html_e( 'Not generated', 'otu' ); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<button type="button" class="button button-small otu-regen-cert"
										data-cert-id="<?php echo esc_attr( $cid ); ?>"
										data-nonce="<?php echo esc_attr( wp_create_nonce( 'otu_admin_nonce' ) ); ?>">
										<?php esc_html_e( 'Regenerate PDF', 'otu' ); ?>
									</button>
									<a href="<?php echo esc_url( get_delete_post_link( $cid ) ); ?>"
										class="button button-small button-link-delete"
										onclick="return confirm('<?php esc_attr_e( 'Delete this certificate?', 'otu' ); ?>');">
										<?php esc_html_e( 'Delete', 'otu' ); ?>
									</a>
								</td>
							</tr>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	// ---------- AJAX Handlers ----------

	/**
	 * AJAX: Update booking status.
	 */
	public function ajax_update_booking_status() {
		check_ajax_referer( 'otu_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'otu' ) ) );
		}

		$booking_id = isset( $_POST['booking_id'] ) ? absint( $_POST['booking_id'] ) : 0;
		$status     = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';

		if ( ! $booking_id || ! in_array( $status, array( 'pending', 'confirmed', 'cancelled' ), true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid data.', 'otu' ) ) );
		}

		$booking_post = get_post( $booking_id );
		if ( ! $booking_post || 'otu_booking' !== $booking_post->post_type ) {
			wp_send_json_error( array( 'message' => __( 'Invalid booking.', 'otu' ) ) );
		}

		update_post_meta( $booking_id, '_booking_status', $status );
		wp_send_json_success( array( 'message' => __( 'Status updated.', 'otu' ), 'status' => $status ) );
	}

	/**
	 * AJAX: Regenerate certificate PDF.
	 */
	public function ajax_regenerate_certificate() {
		check_ajax_referer( 'otu_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'otu' ) ) );
		}

		$cert_id = isset( $_POST['cert_id'] ) ? absint( $_POST['cert_id'] ) : 0;
		if ( ! $cert_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid certificate.', 'otu' ) ) );
		}

		$cert_post = get_post( $cert_id );
		if ( ! $cert_post || 'otu_certificate' !== $cert_post->post_type ) {
			wp_send_json_error( array( 'message' => __( 'Invalid certificate.', 'otu' ) ) );
		}

		$student_id = absint( get_post_meta( $cert_id, '_student_id', true ) );
		$course_id  = absint( get_post_meta( $cert_id, '_course_id', true ) );

		// Delete existing certificate and regenerate.
		wp_delete_post( $cert_id, true );
		$new_cert_id = OTU_Certificate::generate( $student_id, $course_id );

		if ( is_wp_error( $new_cert_id ) ) {
			wp_send_json_error( array( 'message' => $new_cert_id->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'Certificate regenerated successfully.', 'otu' ) ) );
	}
}
