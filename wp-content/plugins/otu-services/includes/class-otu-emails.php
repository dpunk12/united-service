<?php
/**
 * Email Notifications for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_Emails
 *
 * Centralised email sending for bookings, certificates, orders and form submissions.
 */
class OTU_Emails {

	/**
	 * Site name used in email subjects and bodies.
	 *
	 * @var string
	 */
	private $site_name;

	/**
	 * Admin email address.
	 *
	 * @var string
	 */
	private $admin_email;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$otu_settings      = get_option( 'otu_settings', array() );
		$this->site_name   = ! empty( $otu_settings['general']['site_name'] ) ? sanitize_text_field( $otu_settings['general']['site_name'] ) : get_bloginfo( 'name' );
		$this->admin_email = ! empty( $otu_settings['email']['admin_email'] ) ? sanitize_email( $otu_settings['email']['admin_email'] ) : get_option( 'admin_email' );
	}

	/**
	 * Return HTML email headers.
	 *
	 * @return array
	 */
	private function get_html_headers() {
		$otu_settings = get_option( 'otu_settings', array() );
		$from_name    = ! empty( $otu_settings['email']['from_name'] ) ? sanitize_text_field( $otu_settings['email']['from_name'] ) : $this->site_name;
		$from_email   = ! empty( $otu_settings['email']['from_email'] ) ? sanitize_email( $otu_settings['email']['from_email'] ) : $this->admin_email;
		$bcc          = ! empty( $otu_settings['email']['bcc'] ) ? sanitize_email( $otu_settings['email']['bcc'] ) : '';

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $from_name . ' <' . $from_email . '>',
		);

		if ( $bcc ) {
			$headers[] = 'Bcc: ' . $bcc;
		}

		return $headers;
	}

	/**
	 * Wrap content in the standard OTU branded email template.
	 *
	 * @param string $content    Main email body (HTML).
	 * @param string $preheader  Short preview text.
	 * @return string
	 */
	private function wrap_email( $content, $preheader = '' ) {
		$site_url = esc_url( home_url( '/' ) );
		ob_start();
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<title><?php echo esc_html( $this->site_name ); ?></title>
		</head>
		<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
			<?php if ( $preheader ) : ?>
				<div style="display:none;max-height:0;overflow:hidden;color:#f4f4f4;font-size:1px;">
					<?php echo esc_html( $preheader ); ?>
				</div>
			<?php endif; ?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f4f4;">
				<tr>
					<td align="center" style="padding:20px 10px;">
						<table width="600" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
							<!-- Header -->
							<tr>
								<td style="background:#800020;padding:20px 30px;text-align:center;">
									<h1 style="margin:0;color:#D4AF37;font-size:22px;font-weight:bold;letter-spacing:1px;">
										<?php echo esc_html( strtoupper( $this->site_name ) ); ?>
									</h1>
									<p style="margin:4px 0 0;color:#ffffff;font-size:12px;">
										<a href="<?php echo $site_url; ?>" style="color:#D4AF37;text-decoration:none;">
											<?php echo esc_html( home_url() ); ?>
										</a>
									</p>
								</td>
							</tr>
							<!-- Body -->
							<tr>
								<td style="padding:30px 30px 20px;">
									<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</td>
							</tr>
							<!-- Footer -->
							<tr>
								<td style="background:#f8f8f8;padding:16px 30px;text-align:center;border-top:3px solid #D4AF37;">
									<p style="margin:0;font-size:12px;color:#888888;">
										<?php
										echo wp_kses_post(
											sprintf(
												/* translators: %s: site name */
												__( '&copy; %1$s %2$s. All rights reserved.', 'otu' ),
												esc_html( gmdate( 'Y' ) ),
												esc_html( $this->site_name )
											)
										);
										?>
									</p>
									<p style="margin:4px 0 0;font-size:11px;color:#aaaaaa;">
										<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:#800020;text-decoration:none;"><?php esc_html_e( 'Privacy Policy', 'otu' ); ?></a>
										&nbsp;|&nbsp;
										<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" style="color:#800020;text-decoration:none;"><?php esc_html_e( 'Contact Us', 'otu' ); ?></a>
									</p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * Send booking confirmation emails to the user and admin.
	 *
	 * @param int $booking_id OTU Booking post ID.
	 */
	public function send_booking_confirmation( $booking_id ) {
		$booking_id   = absint( $booking_id );
		$name         = get_post_meta( $booking_id, '_booking_user_name', true );
		$email        = get_post_meta( $booking_id, '_booking_user_email', true );
		$phone        = get_post_meta( $booking_id, '_booking_phone', true );
		$service      = get_post_meta( $booking_id, '_booking_service', true );
		$date         = get_post_meta( $booking_id, '_booking_date', true );
		$time         = get_post_meta( $booking_id, '_booking_time', true );
		$notes        = get_post_meta( $booking_id, '_booking_notes', true );
		$reference    = 'BK-' . str_pad( $booking_id, 5, '0', STR_PAD_LEFT );
		$headers      = $this->get_html_headers();

		// --- Email to user ---
		if ( is_email( $email ) ) {
			ob_start();
			?>
			<h2 style="color:#800020;"><?php esc_html_e( 'Your Appointment is Confirmed!', 'otu' ); ?></h2>
			<p><?php echo esc_html( sprintf( __( 'Hello %s,', 'otu' ), $name ) ); ?></p>
			<p><?php esc_html_e( 'Thank you for booking an appointment with us. Here are your appointment details:', 'otu' ); ?></p>
			<table style="width:100%;border-collapse:collapse;margin:16px 0;">
				<tr style="background:#f9f0f0;">
					<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Reference', 'otu' ); ?></td>
					<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $reference ); ?></td>
				</tr>
				<tr>
					<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Service', 'otu' ); ?></td>
					<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $service ); ?></td>
				</tr>
				<tr style="background:#f9f0f0;">
					<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Date', 'otu' ); ?></td>
					<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $date ); ?></td>
				</tr>
				<tr>
					<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Time', 'otu' ); ?></td>
					<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $time ); ?></td>
				</tr>
				<?php if ( ! empty( $notes ) ) : ?>
				<tr style="background:#f9f0f0;">
					<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Notes', 'otu' ); ?></td>
					<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $notes ); ?></td>
				</tr>
				<?php endif; ?>
			</table>
			<p><?php esc_html_e( 'A member of our team will follow up to confirm your appointment. If you need to reschedule or have questions, please contact us.', 'otu' ); ?></p>
			<p>
				<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"
					style="background:#800020;color:#D4AF37;padding:10px 24px;text-decoration:none;border-radius:4px;display:inline-block;font-weight:bold;">
					<?php esc_html_e( 'Contact Us', 'otu' ); ?>
				</a>
			</p>
			<?php
			$user_body = ob_get_clean();

			wp_mail(
				$email,
				/* translators: %s: reference number */
				sprintf( __( '[%s] Your Appointment is Confirmed — %s', 'otu' ), $this->site_name, $reference ),
				$this->wrap_email( $user_body, __( 'Your appointment is confirmed!', 'otu' ) ),
				$headers
			);
		}

		// --- Email to admin ---
		ob_start();
		?>
		<h2 style="color:#800020;"><?php echo esc_html( sprintf( __( 'New Booking: %s', 'otu' ), $reference ) ); ?></h2>
		<p><?php esc_html_e( 'A new appointment has been booked. Details below:', 'otu' ); ?></p>
		<table style="width:100%;border-collapse:collapse;margin:16px 0;">
			<tr style="background:#f9f0f0;">
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Reference', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $reference ); ?></td>
			</tr>
			<tr>
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Customer', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $name ); ?></td>
			</tr>
			<tr style="background:#f9f0f0;">
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Email', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $email ); ?></td>
			</tr>
			<tr>
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Phone', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $phone ); ?></td>
			</tr>
			<tr style="background:#f9f0f0;">
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Service', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $service ); ?></td>
			</tr>
			<tr>
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Date', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $date ); ?></td>
			</tr>
			<tr style="background:#f9f0f0;">
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Time', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $time ); ?></td>
			</tr>
			<?php if ( ! empty( $notes ) ) : ?>
			<tr>
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Notes', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $notes ); ?></td>
			</tr>
			<?php endif; ?>
		</table>
		<p>
			<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $booking_id . '&action=edit' ) ); ?>"
				style="background:#800020;color:#D4AF37;padding:10px 24px;text-decoration:none;border-radius:4px;display:inline-block;font-weight:bold;">
				<?php esc_html_e( 'View Booking in Admin', 'otu' ); ?>
			</a>
		</p>
		<?php
		$admin_body = ob_get_clean();

		wp_mail(
			$this->admin_email,
			/* translators: 1: site name, 2: reference number */
			sprintf( __( '[%1$s] New Booking: %2$s', 'otu' ), $this->site_name, $reference ),
			$this->wrap_email( $admin_body, __( 'New booking received.', 'otu' ) ),
			$headers
		);
	}

	/**
	 * Send certificate email to a student.
	 *
	 * @param int    $user_id        WordPress user ID.
	 * @param string $certificate_id Certificate unique ID.
	 * @param string $pdf_path       Absolute path to PDF file or empty.
	 */
	public function send_certificate_email( $user_id, $certificate_id, $pdf_path ) {
		$user_id        = absint( $user_id );
		$certificate_id = sanitize_text_field( $certificate_id );
		$user           = get_userdata( $user_id );

		if ( ! $user ) {
			return;
		}

		$student_name = trim( $user->first_name . ' ' . $user->last_name );
		if ( empty( $student_name ) ) {
			$student_name = $user->display_name;
		}

		$email = $user->user_email;
		if ( ! is_email( $email ) ) {
			return;
		}

		$dashboard_page = get_page_by_path( 'dashboard' );
		$cert_url       = $dashboard_page ? add_query_arg( 'otu_tab', 'certificates', get_permalink( $dashboard_page->ID ) ) : home_url( '/' );
		$headers        = $this->get_html_headers();
		$attachments    = array();

		if ( ! empty( $pdf_path ) && file_exists( $pdf_path ) ) {
			$attachments[] = $pdf_path;
		}

		ob_start();
		?>
		<h2 style="color:#800020;"><?php esc_html_e( '🏆 Congratulations! Your Certificate is Ready', 'otu' ); ?></h2>
		<p><?php echo esc_html( sprintf( __( 'Dear %s,', 'otu' ), $student_name ) ); ?></p>
		<p><?php esc_html_e( 'We are proud to inform you that you have successfully completed the course and your certificate has been issued.', 'otu' ); ?></p>
		<table style="width:100%;border-collapse:collapse;margin:16px 0;">
			<tr style="background:#f9f0f0;">
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Certificate ID', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $certificate_id ); ?></td>
			</tr>
			<tr>
				<td style="padding:10px;border:1px solid #eee;font-weight:bold;"><?php esc_html_e( 'Issued On', 'otu' ); ?></td>
				<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( gmdate( 'F j, Y' ) ); ?></td>
			</tr>
		</table>
		<?php if ( ! empty( $attachments ) ) : ?>
			<p><?php esc_html_e( 'Your certificate PDF is attached to this email. You can also download it from your dashboard.', 'otu' ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( 'Your certificate is available for download from your dashboard.', 'otu' ); ?></p>
		<?php endif; ?>
		<p>
			<a href="<?php echo esc_url( $cert_url ); ?>"
				style="background:#800020;color:#D4AF37;padding:10px 24px;text-decoration:none;border-radius:4px;display:inline-block;font-weight:bold;">
				<?php esc_html_e( 'View My Certificates', 'otu' ); ?>
			</a>
		</p>
		<?php
		$body = ob_get_clean();

		wp_mail(
			$email,
			/* translators: %s: site name */
			sprintf( __( '[%s] Your Certificate of Completion is Ready!', 'otu' ), $this->site_name ),
			$this->wrap_email( $body, __( 'Your certificate is ready!', 'otu' ) ),
			$headers,
			$attachments
		);
	}

	/**
	 * Send a custom OTU-branded order confirmation email.
	 *
	 * @param int $order_id WooCommerce order ID.
	 */
	public function send_order_confirmation( $order_id ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$order_id = absint( $order_id );
		$order    = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$email    = $order->get_billing_email();
		$name     = $order->get_billing_first_name();
		$headers  = $this->get_html_headers();

		ob_start();
		?>
		<h2 style="color:#800020;"><?php esc_html_e( 'Order Confirmed!', 'otu' ); ?></h2>
		<p><?php echo esc_html( sprintf( __( 'Hi %s,', 'otu' ), $name ) ); ?></p>
		<p>
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: order number */
					__( 'Thank you for your order. Your order #%s has been received and is being processed.', 'otu' ),
					$order->get_order_number()
				)
			);
			?>
		</p>
		<table style="width:100%;border-collapse:collapse;margin:16px 0;">
			<thead>
				<tr style="background:#800020;color:#ffffff;">
					<th style="padding:10px;text-align:left;"><?php esc_html_e( 'Product', 'otu' ); ?></th>
					<th style="padding:10px;text-align:right;"><?php esc_html_e( 'Total', 'otu' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $order->get_items() as $item ) : ?>
					<tr>
						<td style="padding:10px;border:1px solid #eee;"><?php echo esc_html( $item->get_name() ); ?> &times; <?php echo esc_html( $item->get_quantity() ); ?></td>
						<td style="padding:10px;border:1px solid #eee;text-align:right;"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
					</tr>
				<?php endforeach; ?>
				<tr style="background:#f9f0f0;">
					<td style="padding:10px;font-weight:bold;"><?php esc_html_e( 'Order Total', 'otu' ); ?></td>
					<td style="padding:10px;font-weight:bold;text-align:right;"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
				</tr>
			</tbody>
		</table>
		<p>
			<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>"
				style="background:#800020;color:#D4AF37;padding:10px 24px;text-decoration:none;border-radius:4px;display:inline-block;font-weight:bold;">
				<?php esc_html_e( 'View My Order', 'otu' ); ?>
			</a>
		</p>
		<?php
		$body = ob_get_clean();

		wp_mail(
			$email,
			/* translators: 1: site name, 2: order number */
			sprintf( __( '[%1$s] Order Confirmed — #%2$s', 'otu' ), $this->site_name, $order->get_order_number() ),
			$this->wrap_email( $body, __( 'Your order is confirmed!', 'otu' ) ),
			$headers
		);
	}

	/**
	 * Send admin notification when a service form (LLC/EIN/Immigration) is submitted.
	 *
	 * @param string $service_type LLC, EIN, or Immigration.
	 * @param array  $form_data    Sanitised form field key/value pairs.
	 * @param int    $order_id     WooCommerce order ID.
	 */
	public function send_form_submission_notification( $service_type, $form_data, $order_id ) {
		$service_type = sanitize_text_field( $service_type );
		$order_id     = absint( $order_id );
		$headers      = $this->get_html_headers();

		ob_start();
		?>
		<h2 style="color:#800020;">
			<?php
			echo esc_html(
				sprintf(
					/* translators: 1: service type, 2: order ID */
					__( 'New %1$s Service Form Submission — Order #%2$s', 'otu' ),
					strtoupper( $service_type ),
					$order_id
				)
			);
			?>
		</h2>
		<p><?php esc_html_e( 'A new service form has been submitted with the following details:', 'otu' ); ?></p>
		<table style="width:100%;border-collapse:collapse;margin:16px 0;">
			<?php
			$row_alt = false;
			foreach ( $form_data as $key => $value ) :
				$bg = $row_alt ? 'background:#f9f0f0;' : '';
				$row_alt = ! $row_alt;
				?>
				<tr style="<?php echo esc_attr( $bg ); ?>">
					<td style="padding:10px;border:1px solid #eee;font-weight:bold;width:40%;">
						<?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?>
					</td>
					<td style="padding:10px;border:1px solid #eee;">
						<?php echo esc_html( $value ); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php if ( $order_id && class_exists( 'WooCommerce' ) ) : ?>
			<p>
				<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ); ?>"
					style="background:#800020;color:#D4AF37;padding:10px 24px;text-decoration:none;border-radius:4px;display:inline-block;font-weight:bold;">
					<?php esc_html_e( 'View Order in Admin', 'otu' ); ?>
				</a>
			</p>
		<?php endif; ?>
		<?php
		$body = ob_get_clean();

		wp_mail(
			$this->admin_email,
			/* translators: 1: site name, 2: service type, 3: order ID */
			sprintf( __( '[%1$s] New %2$s Form Submission — Order #%3$s', 'otu' ), $this->site_name, strtoupper( $service_type ), $order_id ),
			$this->wrap_email( $body, __( 'New service form submission received.', 'otu' ) ),
			$headers
		);
	}
}
