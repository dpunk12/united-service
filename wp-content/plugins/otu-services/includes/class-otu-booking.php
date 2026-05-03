<?php
/**
 * Booking System for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_Booking
 *
 * Handles appointment bookings via CPT and shortcode.
 */
class OTU_Booking {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_booking_meta_box' ) );
		add_action( 'save_post_otu_booking', array( $this, 'save_booking_meta' ) );
		add_shortcode( 'otu_booking_form', array( $this, 'booking_form_shortcode' ) );
		add_action( 'wp_ajax_otu_submit_booking', array( $this, 'handle_booking_submission' ) );
		add_action( 'wp_ajax_nopriv_otu_submit_booking', array( $this, 'handle_booking_submission' ) );
	}

	/**
	 * Register the otu_booking CPT.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Bookings', 'Post type general name', 'otu' ),
			'singular_name'      => _x( 'Booking', 'Post type singular name', 'otu' ),
			'menu_name'          => _x( 'Bookings', 'Admin Menu text', 'otu' ),
			'name_admin_bar'     => _x( 'Booking', 'Add New on Toolbar', 'otu' ),
			'add_new'            => __( 'Add New', 'otu' ),
			'add_new_item'       => __( 'Add New Booking', 'otu' ),
			'new_item'           => __( 'New Booking', 'otu' ),
			'edit_item'          => __( 'Edit Booking', 'otu' ),
			'view_item'          => __( 'View Booking', 'otu' ),
			'all_items'          => __( 'All Bookings', 'otu' ),
			'search_items'       => __( 'Search Bookings', 'otu' ),
			'not_found'          => __( 'No bookings found.', 'otu' ),
			'not_found_in_trash' => __( 'No bookings found in Trash.', 'otu' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'otu-services',
			'query_var'       => true,
			'capability_type' => 'post',
			'has_archive'     => false,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'custom-fields' ),
		);

		register_post_type( 'otu_booking', $args );
	}

	/**
	 * Add meta box to booking post type.
	 */
	public function add_booking_meta_box() {
		add_meta_box(
			'otu_booking_details',
			__( 'Booking Details', 'otu' ),
			array( $this, 'render_booking_meta_box' ),
			'otu_booking',
			'normal',
			'high'
		);
	}

	/**
	 * Render booking meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_booking_meta_box( $post ) {
		wp_nonce_field( 'otu_booking_meta_nonce', 'otu_booking_meta_nonce_field' );

		$fields = array(
			'booking_user_name'  => __( 'Customer Name', 'otu' ),
			'booking_user_email' => __( 'Customer Email', 'otu' ),
			'booking_phone'      => __( 'Phone', 'otu' ),
			'booking_service'    => __( 'Service', 'otu' ),
			'booking_date'       => __( 'Preferred Date', 'otu' ),
			'booking_time'       => __( 'Preferred Time', 'otu' ),
			'booking_notes'      => __( 'Notes', 'otu' ),
		);

		echo '<table class="form-table otu-booking-meta-table">';
		foreach ( $fields as $key => $label ) {
			$value = get_post_meta( $post->ID, '_' . $key, true );
			echo '<tr><th>' . esc_html( $label ) . '</th><td><strong>' . esc_html( $value ) . '</strong></td></tr>';
		}

		$status = get_post_meta( $post->ID, '_booking_status', true );
		if ( empty( $status ) ) {
			$status = 'pending';
		}

		echo '<tr><th>' . esc_html__( 'Status', 'otu' ) . '</th><td>';
		echo '<select name="otu_booking_status">';
		$statuses = array(
			'pending'   => __( 'Pending', 'otu' ),
			'confirmed' => __( 'Confirmed', 'otu' ),
			'cancelled' => __( 'Cancelled', 'otu' ),
		);
		foreach ( $statuses as $val => $label ) {
			echo '<option value="' . esc_attr( $val ) . '"' . selected( $status, $val, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></td></tr>';
		echo '</table>';
	}

	/**
	 * Save booking meta box data.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_booking_meta( $post_id ) {
		if ( ! isset( $_POST['otu_booking_meta_nonce_field'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['otu_booking_meta_nonce_field'] ) ), 'otu_booking_meta_nonce' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['otu_booking_status'] ) ) {
			$allowed_statuses = array( 'pending', 'confirmed', 'cancelled' );
			$status           = sanitize_key( wp_unslash( $_POST['otu_booking_status'] ) );
			if ( in_array( $status, $allowed_statuses, true ) ) {
				update_post_meta( $post_id, '_booking_status', $status );
			}
		}
	}

	/**
	 * Get time slots for the booking form.
	 *
	 * @return array
	 */
	private function get_time_slots() {
		$slots = array();
		for ( $hour = 9; $hour <= 17; $hour++ ) {
			$time_24   = sprintf( '%02d:00', $hour );
			$period    = $hour < 12 ? 'AM' : 'PM';
			$hour_12   = $hour > 12 ? $hour - 12 : ( 0 === $hour ? 12 : $hour );
			$time_12   = sprintf( '%d:00 %s', $hour_12, $period );
			$slots[ $time_24 ] = $time_12;
		}
		return $slots;
	}

	/**
	 * Booking form shortcode.
	 *
	 * @return string Rendered HTML.
	 */
	public function booking_form_shortcode() {
		ob_start();

		$services_query = new WP_Query(
			array(
				'post_type'      => 'service',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$services = array();
		if ( $services_query->have_posts() ) {
			while ( $services_query->have_posts() ) {
				$services_query->the_post();
				$services[ get_the_ID() ] = get_the_title();
			}
			wp_reset_postdata();
		}
		?>
		<div class="otu-booking-form-wrap">
			<h2 class="otu-booking-title"><?php esc_html_e( 'Book an Appointment', 'otu' ); ?></h2>
			<div id="otu-booking-messages"></div>
			<form id="otu-booking-form" class="otu-booking-form" method="post" novalidate>
				<?php wp_nonce_field( 'otu_booking_form_nonce', 'otu_booking_nonce' ); ?>

				<div class="otu-form-row">
					<label for="otu_booking_name"><?php esc_html_e( 'Full Name *', 'otu' ); ?></label>
					<input type="text" id="otu_booking_name" name="booking_name"
						placeholder="<?php esc_attr_e( 'Enter your full name', 'otu' ); ?>" required />
					<span class="otu-field-error"></span>
				</div>

				<div class="otu-form-row">
					<label for="otu_booking_email"><?php esc_html_e( 'Email Address *', 'otu' ); ?></label>
					<input type="email" id="otu_booking_email" name="booking_email"
						placeholder="<?php esc_attr_e( 'Enter your email', 'otu' ); ?>" required />
					<span class="otu-field-error"></span>
				</div>

				<div class="otu-form-row">
					<label for="otu_booking_phone"><?php esc_html_e( 'Phone Number *', 'otu' ); ?></label>
					<input type="tel" id="otu_booking_phone" name="booking_phone"
						placeholder="<?php esc_attr_e( 'Enter your phone number', 'otu' ); ?>" required />
					<span class="otu-field-error"></span>
				</div>

				<div class="otu-form-row">
					<label for="otu_booking_service"><?php esc_html_e( 'Select Service *', 'otu' ); ?></label>
					<select id="otu_booking_service" name="booking_service" required>
						<option value=""><?php esc_html_e( '-- Select a Service --', 'otu' ); ?></option>
						<?php foreach ( $services as $id => $title ) : ?>
							<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="otu-field-error"></span>
				</div>

				<div class="otu-form-row">
					<label for="otu_booking_date"><?php esc_html_e( 'Preferred Date *', 'otu' ); ?></label>
					<input type="date" id="otu_booking_date" name="booking_date"
						min="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( '+1 day' ) ) ); ?>" required />
					<span class="otu-field-error"></span>
				</div>

				<div class="otu-form-row">
					<label for="otu_booking_time"><?php esc_html_e( 'Preferred Time *', 'otu' ); ?></label>
					<select id="otu_booking_time" name="booking_time" required>
						<option value=""><?php esc_html_e( '-- Select a Time --', 'otu' ); ?></option>
						<?php foreach ( $this->get_time_slots() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="otu-field-error"></span>
				</div>

				<div class="otu-form-row">
					<label for="otu_booking_notes"><?php esc_html_e( 'Additional Notes', 'otu' ); ?></label>
					<textarea id="otu_booking_notes" name="booking_notes" rows="4"
						placeholder="<?php esc_attr_e( 'Any additional information about your request...', 'otu' ); ?>"></textarea>
				</div>

				<div class="otu-form-row">
					<button type="submit" class="otu-btn otu-btn-primary otu-booking-submit">
						<?php esc_html_e( 'Book Appointment', 'otu' ); ?>
					</button>
					<span class="otu-spinner" style="display:none;"></span>
				</div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Handle AJAX booking form submission.
	 */
	public function handle_booking_submission() {
		check_ajax_referer( 'otu_booking_form_nonce', 'otu_booking_nonce' );

		$name    = isset( $_POST['booking_name'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_name'] ) ) : '';
		$email   = isset( $_POST['booking_email'] ) ? sanitize_email( wp_unslash( $_POST['booking_email'] ) ) : '';
		$phone   = isset( $_POST['booking_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_phone'] ) ) : '';
		$service = isset( $_POST['booking_service'] ) ? absint( $_POST['booking_service'] ) : 0;
		$date    = isset( $_POST['booking_date'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_date'] ) ) : '';
		$time    = isset( $_POST['booking_time'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_time'] ) ) : '';
		$notes   = isset( $_POST['booking_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['booking_notes'] ) ) : '';

		$errors = array();

		if ( empty( $name ) ) {
			$errors[] = __( 'Full Name is required.', 'otu' );
		}
		if ( empty( $email ) || ! is_email( $email ) ) {
			$errors[] = __( 'A valid email address is required.', 'otu' );
		}
		if ( empty( $phone ) ) {
			$errors[] = __( 'Phone number is required.', 'otu' );
		}
		if ( empty( $service ) ) {
			$errors[] = __( 'Please select a service.', 'otu' );
		}
		if ( empty( $date ) ) {
			$errors[] = __( 'Please select a preferred date.', 'otu' );
		} elseif ( strtotime( $date ) <= strtotime( 'today' ) ) {
			$errors[] = __( 'Please select a future date for your appointment.', 'otu' );
		}
		if ( empty( $time ) ) {
			$errors[] = __( 'Please select a preferred time.', 'otu' );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'messages' => $errors ) );
		}

		$service_name = get_the_title( $service );

		$booking_id = wp_insert_post(
			array(
				'post_type'   => 'otu_booking',
				'post_title'  => sprintf(
					/* translators: 1: customer name, 2: date */
					__( 'Booking — %1$s — %2$s', 'otu' ),
					$name,
					$date
				),
				'post_status' => 'publish',
			)
		);

		if ( is_wp_error( $booking_id ) ) {
			wp_send_json_error( array( 'messages' => array( __( 'Failed to create booking. Please try again.', 'otu' ) ) ) );
		}

		update_post_meta( $booking_id, '_booking_user_name', $name );
		update_post_meta( $booking_id, '_booking_user_email', $email );
		update_post_meta( $booking_id, '_booking_phone', $phone );
		update_post_meta( $booking_id, '_booking_service', $service_name );
		update_post_meta( $booking_id, '_booking_date', $date );
		update_post_meta( $booking_id, '_booking_time', $time );
		update_post_meta( $booking_id, '_booking_notes', $notes );
		update_post_meta( $booking_id, '_booking_status', 'pending' );

		// Send emails via OTU_Emails.
		if ( class_exists( 'OTU_Emails' ) ) {
			$emails = new OTU_Emails();
			$emails->send_booking_confirmation( $booking_id );
		}

		wp_send_json_success(
			array(
				'message'    => __( 'Your appointment request has been submitted! You will receive a confirmation email shortly.', 'otu' ),
				'booking_id' => $booking_id,
			)
		);
	}

	/**
	 * Get bookings for a specific user by email.
	 *
	 * @param string $email User email.
	 * @return WP_Query
	 */
	public static function get_user_bookings( $email ) {
		return new WP_Query(
			array(
				'post_type'      => 'otu_booking',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'meta_value',
				'meta_key'       => '_booking_date',
				'order'          => 'ASC',
				'meta_query'     => array(
					array(
						'key'     => '_booking_user_email',
						'value'   => sanitize_email( $email ),
						'compare' => '=',
					),
				),
			)
		);
	}
}
