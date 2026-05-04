<?php
/**
 * Certificate Generation for OTU Services.
 *
 * Requires TCPDF: composer require tecnickcom/tcpdf
 * or bundle in /vendor/tcpdf/tcpdf.php
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_Certificate
 *
 * Handles certificate generation, storage, and delivery.
 */
class OTU_Certificate {

	/**
	 * Upload directory for certificates.
	 *
	 * @var string
	 */
	private static $cert_dir = '';

	/**
	 * Upload URL for certificates.
	 *
	 * @var string
	 */
	private static $cert_url = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_shortcode( 'otu_certificate', array( $this, 'certificate_shortcode' ) );
		add_action( 'wp_ajax_otu_download_certificate', array( $this, 'handle_certificate_download' ) );
		add_action( 'wp_ajax_nopriv_otu_download_certificate', array( $this, 'handle_certificate_download' ) );

		$upload_dir       = wp_upload_dir();
		self::$cert_dir   = trailingslashit( $upload_dir['basedir'] ) . 'otu-certificates/';
		self::$cert_url   = trailingslashit( $upload_dir['baseurl'] ) . 'otu-certificates/';
	}

	/**
	 * Register the otu_certificate CPT.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Certificates', 'Post type general name', 'otu' ),
			'singular_name'      => _x( 'Certificate', 'Post type singular name', 'otu' ),
			'menu_name'          => _x( 'Certificates', 'Admin Menu text', 'otu' ),
			'add_new'            => __( 'Add New', 'otu' ),
			'add_new_item'       => __( 'Add New Certificate', 'otu' ),
			'edit_item'          => __( 'Edit Certificate', 'otu' ),
			'all_items'          => __( 'All Certificates', 'otu' ),
			'search_items'       => __( 'Search Certificates', 'otu' ),
			'not_found'          => __( 'No certificates found.', 'otu' ),
			'not_found_in_trash' => __( 'No certificates found in Trash.', 'otu' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'otu-services',
			'query_var'       => false,
			'capability_type' => 'post',
			'has_archive'     => false,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'custom-fields' ),
		);

		register_post_type( 'otu_certificate', $args );
	}

	/**
	 * Generate a certificate for a user and course.
	 *
	 * @param int $user_id   WordPress user ID.
	 * @param int $course_id Course post ID.
	 * @return int|WP_Error  Certificate post ID or WP_Error.
	 */
	public static function generate( $user_id, $course_id ) {
		$user_id   = absint( $user_id );
		$course_id = absint( $course_id );

		// Check if certificate already exists.
		$existing = get_posts(
			array(
				'post_type'   => 'otu_certificate',
				'post_status' => 'publish',
				'meta_query'  => array(
					'relation' => 'AND',
					array(
						'key'   => '_student_id',
						'value' => $user_id,
					),
					array(
						'key'   => '_course_id',
						'value' => $course_id,
					),
				),
				'numberposts' => 1,
			)
		);

		if ( ! empty( $existing ) ) {
			return $existing[0]->ID;
		}

		$user        = get_userdata( $user_id );
		$course_name = get_the_title( $course_id );

		if ( ! $user || ! $course_name ) {
			return new WP_Error( 'invalid_data', __( 'Invalid user or course.', 'otu' ) );
		}

		// Generate unique certificate ID, retry if collision (extremely unlikely).
		$certificate_id = '';
		for ( $attempt = 0; $attempt < 5; $attempt++ ) {
			$candidate = 'OTU-' . strtoupper( wp_generate_password( 8, false, false ) );
			$existing_id = get_posts(
				array(
					'post_type'      => 'otu_certificate',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_key'       => '_certificate_id',
					'meta_value'     => $candidate,
				)
			);
			if ( empty( $existing_id ) ) {
				$certificate_id = $candidate;
				break;
			}
		}
		if ( '' === $certificate_id ) {
			return new WP_Error( 'cert_id_collision', __( 'Could not generate a unique certificate ID. Please try again.', 'otu' ) );
		}
		$issue_date     = gmdate( 'F j, Y' );
		$student_name   = trim( $user->first_name . ' ' . $user->last_name );
		if ( empty( $student_name ) ) {
			$student_name = $user->display_name;
		}

		$pdf_path = '';

		// Attempt to generate PDF.
		$tcpdf_path = OTU_PLUGIN_DIR . 'vendor/tcpdf/tcpdf.php';
		if ( file_exists( $tcpdf_path ) ) {
			$pdf_path = self::generate_pdf(
				$user_id,
				$course_id,
				$certificate_id,
				$student_name,
				$course_name,
				$issue_date
			);
		} else {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'OTU Services: TCPDF library not found. Certificate generated as text record only. Install TCPDF via: composer require tecnickcom/tcpdf' );
		}

		// Create certificate post.
		$cert_post_id = wp_insert_post(
			array(
				'post_type'   => 'otu_certificate',
				'post_title'  => sprintf(
					/* translators: 1: student name, 2: course name */
					__( 'Certificate — %1$s — %2$s', 'otu' ),
					$student_name,
					$course_name
				),
				'post_status' => 'publish',
			)
		);

		if ( is_wp_error( $cert_post_id ) ) {
			return $cert_post_id;
		}

		update_post_meta( $cert_post_id, '_student_id', $user_id );
		update_post_meta( $cert_post_id, '_course_id', $course_id );
		update_post_meta( $cert_post_id, '_certificate_id', $certificate_id );
		update_post_meta( $cert_post_id, '_issue_date', $issue_date );
		update_post_meta( $cert_post_id, '_pdf_path', $pdf_path );

		// Send certificate email.
		if ( class_exists( 'OTU_Emails' ) ) {
			$emails = new OTU_Emails();
			$emails->send_certificate_email( $user_id, $certificate_id, $pdf_path );
		}

		return $cert_post_id;
	}

	/**
	 * Generate a PDF certificate using TCPDF.
	 *
	 * @param int    $user_id        User ID.
	 * @param int    $course_id      Course ID.
	 * @param string $certificate_id Certificate unique ID string.
	 * @param string $student_name   Student full name.
	 * @param string $course_name    Course name.
	 * @param string $issue_date     Issue date string.
	 * @return string  Path to generated PDF or empty string on failure.
	 */
	private static function generate_pdf( $user_id, $course_id, $certificate_id, $student_name, $course_name, $issue_date ) {
		$tcpdf_path = OTU_PLUGIN_DIR . 'vendor/tcpdf/tcpdf.php';
		if ( ! file_exists( $tcpdf_path ) ) {
			return '';
		}

		require_once $tcpdf_path;

		// Ensure upload directory exists.
		if ( ! file_exists( self::$cert_dir ) ) {
			wp_mkdir_p( self::$cert_dir );
		}

		$filename = 'certificate-' . $user_id . '-' . $course_id . '-' . sanitize_title( $certificate_id ) . '.pdf';
		$filepath = self::$cert_dir . $filename;

		try {
			$pdf = new TCPDF( 'L', 'mm', 'A4', true, 'UTF-8', false );
			$pdf->SetCreator( 'OTU Services' );
			$pdf->SetAuthor( 'One Ten United Services' );
			$pdf->SetTitle( __( 'Certificate of Completion', 'otu' ) );
			$pdf->SetSubject( $course_name );
			$pdf->setPrintHeader( false );
			$pdf->setPrintFooter( false );
			$pdf->SetMargins( 15, 15, 15 );
			$pdf->SetAutoPageBreak( false, 0 );
			$pdf->AddPage();

			// Maroon header band.
			$pdf->SetFillColor( 128, 0, 32 );
			$pdf->Rect( 0, 0, 297, 35, 'F' );

			// Gold accent bar.
			$pdf->SetFillColor( 212, 175, 55 );
			$pdf->Rect( 0, 35, 297, 4, 'F' );

			// Organisation name in gold on maroon.
			$pdf->SetFont( 'helvetica', 'B', 22 );
			$pdf->SetTextColor( 212, 175, 55 );
			$pdf->SetXY( 0, 8 );
			$pdf->Cell( 297, 12, 'ONE TEN UNITED SERVICES', 0, 1, 'C' );

			$pdf->SetFont( 'helvetica', '', 12 );
			$pdf->SetTextColor( 255, 255, 255 );
			$pdf->SetXY( 0, 22 );
			$pdf->Cell( 297, 8, 'onetenunited.com', 0, 1, 'C' );

			// Certificate of Completion title.
			$pdf->SetFont( 'times', 'B', 28 );
			$pdf->SetTextColor( 128, 0, 32 );
			$pdf->SetXY( 0, 55 );
			$pdf->Cell( 297, 16, __( 'Certificate of Completion', 'otu' ), 0, 1, 'C' );

			// Decorative line.
			$pdf->SetDrawColor( 212, 175, 55 );
			$pdf->SetLineWidth( 0.8 );
			$pdf->Line( 50, 74, 247, 74 );

			// This certifies that.
			$pdf->SetFont( 'times', 'I', 14 );
			$pdf->SetTextColor( 80, 80, 80 );
			$pdf->SetXY( 0, 78 );
			$pdf->Cell( 297, 10, __( 'This certifies that', 'otu' ), 0, 1, 'C' );

			// Student name.
			$pdf->SetFont( 'times', 'BI', 32 );
			$pdf->SetTextColor( 128, 0, 32 );
			$pdf->SetXY( 0, 88 );
			$pdf->Cell( 297, 18, $student_name, 0, 1, 'C' );

			// has successfully completed.
			$pdf->SetFont( 'times', 'I', 14 );
			$pdf->SetTextColor( 80, 80, 80 );
			$pdf->SetXY( 0, 108 );
			$pdf->Cell( 297, 10, __( 'has successfully completed', 'otu' ), 0, 1, 'C' );

			// Course name.
			$pdf->SetFont( 'times', 'B', 20 );
			$pdf->SetTextColor( 40, 40, 40 );
			$pdf->SetXY( 0, 120 );
			$pdf->Cell( 297, 14, $course_name, 0, 1, 'C' );

			// Second decorative line.
			$pdf->Line( 50, 137, 247, 137 );

			// Date and certificate ID.
			$pdf->SetFont( 'helvetica', '', 11 );
			$pdf->SetTextColor( 80, 80, 80 );
			$pdf->SetXY( 50, 145 );
			$pdf->Cell( 90, 8, __( 'Date of Completion', 'otu' ), 0, 0, 'C' );
			$pdf->SetXY( 157, 145 );
			$pdf->Cell( 90, 8, __( 'Certificate ID', 'otu' ), 0, 1, 'C' );

			$pdf->SetFont( 'helvetica', 'B', 12 );
			$pdf->SetTextColor( 128, 0, 32 );
			$pdf->SetXY( 50, 153 );
			$pdf->Cell( 90, 8, $issue_date, 0, 0, 'C' );
			$pdf->SetXY( 157, 153 );
			$pdf->Cell( 90, 8, $certificate_id, 0, 1, 'C' );

			// Signature line.
			$pdf->SetDrawColor( 80, 80, 80 );
			$pdf->SetLineWidth( 0.3 );
			$pdf->Line( 80, 168, 217, 168 );
			$pdf->SetFont( 'helvetica', 'I', 10 );
			$pdf->SetTextColor( 100, 100, 100 );
			$pdf->SetXY( 0, 170 );
			$pdf->Cell( 297, 8, __( 'Authorized Signature — One Ten United Services', 'otu' ), 0, 1, 'C' );

			// Footer band.
			$pdf->SetFillColor( 128, 0, 32 );
			$pdf->Rect( 0, 186, 297, 6, 'F' );
			$pdf->SetFillColor( 212, 175, 55 );
			$pdf->Rect( 0, 182, 297, 4, 'F' );

			$pdf->Output( $filepath, 'F' );

			return $filepath;

		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'OTU Services: PDF generation failed — ' . $e->getMessage() );
			return '';
		}
	}

	/**
	 * Certificate shortcode — displays a certificate card.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function certificate_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'otu_certificate'
		);

		$cert_id = absint( $atts['id'] );
		if ( ! $cert_id ) {
			return '';
		}

		$cert = get_post( $cert_id );
		if ( ! $cert || 'otu_certificate' !== $cert->post_type ) {
			return '';
		}

		$certificate_id = get_post_meta( $cert_id, '_certificate_id', true );
		$issue_date     = get_post_meta( $cert_id, '_issue_date', true );
		$course_id      = absint( get_post_meta( $cert_id, '_course_id', true ) );
		$course_name    = get_the_title( $course_id );
		$student_id     = absint( get_post_meta( $cert_id, '_student_id', true ) );
		$student        = get_userdata( $student_id );
		if ( $student ) {
			$student_name = trim( $student->first_name . ' ' . $student->last_name );
			if ( '' === $student_name ) {
				$student_name = $student->display_name;
			}
		} else {
			$student_name = __( 'Unknown Student', 'otu' );
		}
		$pdf_path       = get_post_meta( $cert_id, '_pdf_path', true );
		$has_pdf        = ! empty( $pdf_path ) && file_exists( $pdf_path );

		$download_url = add_query_arg(
			array(
				'action'   => 'otu_download_certificate',
				'cert_id'  => $cert_id,
				'nonce'    => wp_create_nonce( 'otu_download_cert_' . $cert_id ),
			),
			admin_url( 'admin-ajax.php' )
		);

		ob_start();
		?>
		<div class="otu-certificate-card">
			<div class="otu-certificate-header">
				<h3><?php esc_html_e( 'Certificate of Completion', 'otu' ); ?></h3>
				<span class="otu-certificate-id"><?php echo esc_html( $certificate_id ); ?></span>
			</div>
			<div class="otu-certificate-body">
				<p class="otu-cert-student"><?php echo esc_html( $student_name ); ?></p>
				<p class="otu-cert-course"><?php echo esc_html( $course_name ); ?></p>
				<p class="otu-cert-date">
					<?php esc_html_e( 'Issued:', 'otu' ); ?> <?php echo esc_html( $issue_date ); ?>
				</p>
			</div>
			<div class="otu-certificate-footer">
				<?php if ( $has_pdf ) : ?>
					<a href="<?php echo esc_url( $download_url ); ?>"
						class="otu-btn otu-btn-download otu-download-certificate"
						data-cert-id="<?php echo esc_attr( $cert_id ); ?>"
						data-nonce="<?php echo esc_attr( wp_create_nonce( 'otu_download_cert_' . $cert_id ) ); ?>">
						<?php esc_html_e( '⬇ Download Certificate (PDF)', 'otu' ); ?>
					</a>
				<?php else : ?>
					<p class="otu-cert-no-pdf"><?php esc_html_e( 'PDF certificate is being generated. Please check back soon.', 'otu' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * AJAX handler to serve certificate PDF download.
	 */
	public function handle_certificate_download() {
		$cert_id = isset( $_GET['cert_id'] ) ? absint( $_GET['cert_id'] ) : 0;
		$nonce   = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';

		if ( ! $cert_id || ! wp_verify_nonce( $nonce, 'otu_download_cert_' . $cert_id ) ) {
			wp_die( esc_html__( 'Invalid or expired download link.', 'otu' ), 403 );
		}

		$cert = get_post( $cert_id );
		if ( ! $cert || 'otu_certificate' !== $cert->post_type ) {
			wp_die( esc_html__( 'Certificate not found.', 'otu' ), 404 );
		}

		$student_id = absint( get_post_meta( $cert_id, '_student_id', true ) );
		$current_user_id = get_current_user_id();

		if ( $current_user_id && $current_user_id !== $student_id && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to download this certificate.', 'otu' ), 403 );
		}

		$pdf_path = get_post_meta( $cert_id, '_pdf_path', true );

		if ( empty( $pdf_path ) || ! file_exists( $pdf_path ) ) {
			wp_die( esc_html__( 'Certificate PDF is not available yet.', 'otu' ), 404 );
		}

		// Path-traversal containment: ensure the PDF lives inside the certificate dir.
		$upload_dir = wp_upload_dir();
		$cert_dir   = trailingslashit( $upload_dir['basedir'] ) . 'otu-certificates/';
		$real_pdf   = realpath( $pdf_path );
		$real_dir   = realpath( $cert_dir );
		if ( false === $real_pdf || false === $real_dir || 0 !== strpos( $real_pdf, trailingslashit( $real_dir ) ) ) {
			wp_die( esc_html__( 'Invalid certificate file location.', 'otu' ), 403 );
		}

		$certificate_id = get_post_meta( $cert_id, '_certificate_id', true );
		$filename       = 'OTU-Certificate-' . sanitize_file_name( $certificate_id ) . '.pdf';

		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Content-Length: ' . filesize( $pdf_path ) );
		header( 'Cache-Control: private, max-age=0, must-revalidate' );
		header( 'Pragma: public' );

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		echo file_get_contents( $pdf_path );
		exit;
	}

	/**
	 * Get all certificates for a user.
	 *
	 * @param int $user_id User ID.
	 * @return array Array of certificate post objects.
	 */
	public static function get_user_certificates( $user_id ) {
		$user_id = absint( $user_id );

		return get_posts(
			array(
				'post_type'   => 'otu_certificate',
				'post_status' => 'publish',
				'numberposts' => -1,
				'meta_query'  => array(
					array(
						'key'   => '_student_id',
						'value' => $user_id,
					),
				),
			)
		);
	}
}
