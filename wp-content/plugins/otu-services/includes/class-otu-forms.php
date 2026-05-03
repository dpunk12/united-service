<?php
/**
 * Service Forms (LLC, EIN, Immigration) for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_Forms
 *
 * Handles service-specific forms integrated with WooCommerce product pages.
 */
class OTU_Forms {

	/**
	 * US States array.
	 *
	 * @var array
	 */
	private $us_states = array(
		'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas',
		'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware',
		'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho',
		'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas',
		'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
		'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi',
		'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
		'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
		'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma',
		'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
		'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah',
		'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia',
		'WI' => 'Wisconsin', 'WY' => 'Wyoming', 'DC' => 'District of Columbia',
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'render_service_form' ) );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_service_form' ), 10, 3 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'save_form_data_to_cart' ), 10, 3 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'save_form_data_to_order' ), 10, 4 );
		add_action( 'woocommerce_after_order_itemmeta', array( $this, 'display_form_data_in_admin' ), 10, 3 );
	}

	/**
	 * Get the service type for the current product.
	 *
	 * @param int $product_id Product ID.
	 * @return string Service type slug or empty string.
	 */
	private function get_service_type( $product_id ) {
		return sanitize_key( get_post_meta( $product_id, '_otu_service_type', true ) );
	}

	/**
	 * Render the appropriate service form before the add-to-cart button.
	 */
	public function render_service_form() {
		global $product;
		if ( ! $product ) {
			return;
		}

		$service_type = $this->get_service_type( $product->get_id() );

		if ( empty( $service_type ) ) {
			return;
		}

		switch ( $service_type ) {
			case 'llc':
				$this->render_llc_form();
				break;
			case 'ein':
				$this->render_ein_form();
				break;
			case 'immigration':
				$this->render_immigration_form();
				break;
		}
	}

	/**
	 * Render the LLC Formation form.
	 */
	private function render_llc_form() {
		?>
		<div class="otu-service-form otu-llc-form">
			<h3 class="otu-form-title"><?php esc_html_e( 'LLC Formation Information', 'otu' ); ?></h3>
			<p class="otu-form-description"><?php esc_html_e( 'Please provide the following details to process your LLC formation.', 'otu' ); ?></p>
			<?php wp_nonce_field( 'otu_llc_form_nonce', 'otu_llc_nonce' ); ?>
			<input type="hidden" name="otu_service_type" value="llc" />

			<div class="otu-form-row">
				<label for="otu_llc_full_name"><?php esc_html_e( 'Full Name *', 'otu' ); ?></label>
				<input type="text" id="otu_llc_full_name" name="otu_llc_full_name"
					value="<?php echo esc_attr( isset( $_POST['otu_llc_full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_llc_full_name'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_phone"><?php esc_html_e( 'Phone Number *', 'otu' ); ?></label>
				<input type="tel" id="otu_llc_phone" name="otu_llc_phone"
					value="<?php echo esc_attr( isset( $_POST['otu_llc_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_llc_phone'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_email"><?php esc_html_e( 'Email Address *', 'otu' ); ?></label>
				<input type="email" id="otu_llc_email" name="otu_llc_email"
					value="<?php echo esc_attr( isset( $_POST['otu_llc_email'] ) ? sanitize_email( wp_unslash( $_POST['otu_llc_email'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_business_name"><?php esc_html_e( 'Business Name *', 'otu' ); ?></label>
				<input type="text" id="otu_llc_business_name" name="otu_llc_business_name"
					value="<?php echo esc_attr( isset( $_POST['otu_llc_business_name'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_llc_business_name'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_business_address"><?php esc_html_e( 'Business Address *', 'otu' ); ?></label>
				<input type="text" id="otu_llc_business_address" name="otu_llc_business_address"
					value="<?php echo esc_attr( isset( $_POST['otu_llc_business_address'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_llc_business_address'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_state"><?php esc_html_e( 'State of Formation *', 'otu' ); ?></label>
				<select id="otu_llc_state" name="otu_llc_state" required>
					<option value=""><?php esc_html_e( '-- Select State --', 'otu' ); ?></option>
					<?php
					$selected_state = isset( $_POST['otu_llc_state'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_llc_state'] ) ) : '';
					foreach ( $this->us_states as $abbr => $name ) {
						printf(
							'<option value="%s"%s>%s</option>',
							esc_attr( $abbr ),
							selected( $selected_state, $abbr, false ),
							esc_html( $name )
						);
					}
					?>
				</select>
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_owner_full_name"><?php esc_html_e( 'Owner Full Name *', 'otu' ); ?></label>
				<input type="text" id="otu_llc_owner_full_name" name="otu_llc_owner_full_name"
					value="<?php echo esc_attr( isset( $_POST['otu_llc_owner_full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_llc_owner_full_name'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_owner_address"><?php esc_html_e( 'Owner Address *', 'otu' ); ?></label>
				<input type="text" id="otu_llc_owner_address" name="otu_llc_owner_address"
					value="<?php echo esc_attr( isset( $_POST['otu_llc_owner_address'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_llc_owner_address'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_owner_dob"><?php esc_html_e( 'Owner Date of Birth *', 'otu' ); ?></label>
				<input type="date" id="otu_llc_owner_dob" name="otu_llc_owner_dob"
					value="<?php echo esc_attr( isset( $_POST['otu_llc_owner_dob'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_llc_owner_dob'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_llc_owner_ssn"><?php esc_html_e( 'Owner SSN *', 'otu' ); ?></label>
				<input type="password" id="otu_llc_owner_ssn" name="otu_llc_owner_ssn"
					autocomplete="off" required />
				<p class="otu-security-note">
					<span class="otu-lock-icon">&#x1F512;</span>
					<?php esc_html_e( 'Your SSN is transmitted over a secure, encrypted connection and never stored in plain text.', 'otu' ); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the EIN Application form.
	 */
	private function render_ein_form() {
		$ein_types = array(
			'llc'            => __( 'LLC', 'otu' ),
			'corp'           => __( 'Corporation', 'otu' ),
			'partnership'    => __( 'Partnership', 'otu' ),
			'sole_proprietor' => __( 'Sole Proprietor', 'otu' ),
		);
		?>
		<div class="otu-service-form otu-ein-form">
			<h3 class="otu-form-title"><?php esc_html_e( 'EIN Application Information', 'otu' ); ?></h3>
			<p class="otu-form-description"><?php esc_html_e( 'Please provide the following details to process your EIN application.', 'otu' ); ?></p>
			<?php wp_nonce_field( 'otu_ein_form_nonce', 'otu_ein_nonce' ); ?>
			<input type="hidden" name="otu_service_type" value="ein" />

			<div class="otu-form-row">
				<label for="otu_ein_full_name"><?php esc_html_e( 'Full Name *', 'otu' ); ?></label>
				<input type="text" id="otu_ein_full_name" name="otu_ein_full_name"
					value="<?php echo esc_attr( isset( $_POST['otu_ein_full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_ein_full_name'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_ein_business_name"><?php esc_html_e( 'Business Name *', 'otu' ); ?></label>
				<input type="text" id="otu_ein_business_name" name="otu_ein_business_name"
					value="<?php echo esc_attr( isset( $_POST['otu_ein_business_name'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_ein_business_name'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_ein_responsible_party"><?php esc_html_e( 'Responsible Party Name *', 'otu' ); ?></label>
				<input type="text" id="otu_ein_responsible_party" name="otu_ein_responsible_party"
					value="<?php echo esc_attr( isset( $_POST['otu_ein_responsible_party'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_ein_responsible_party'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_ein_type"><?php esc_html_e( 'Entity Type *', 'otu' ); ?></label>
				<select id="otu_ein_type" name="otu_ein_type" required>
					<option value=""><?php esc_html_e( '-- Select Entity Type --', 'otu' ); ?></option>
					<?php
					$selected_type = isset( $_POST['otu_ein_type'] ) ? sanitize_key( wp_unslash( $_POST['otu_ein_type'] ) ) : '';
					foreach ( $ein_types as $value => $label ) {
						printf(
							'<option value="%s"%s>%s</option>',
							esc_attr( $value ),
							selected( $selected_type, $value, false ),
							esc_html( $label )
						);
					}
					?>
				</select>
			</div>

			<div class="otu-form-row">
				<label for="otu_ein_ssn_itin"><?php esc_html_e( 'SSN / ITIN *', 'otu' ); ?></label>
				<input type="password" id="otu_ein_ssn_itin" name="otu_ein_ssn_itin"
					autocomplete="off" required />
				<p class="otu-security-note">
					<span class="otu-lock-icon">&#x1F512;</span>
					<?php esc_html_e( 'Secured & Encrypted — Your SSN/ITIN is transmitted securely and never stored in plain text.', 'otu' ); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Immigration Services form.
	 */
	private function render_immigration_form() {
		$service_types = array(
			'I-485'          => __( 'I-485 (Adjustment of Status)', 'otu' ),
			'I-130'          => __( 'I-130 (Petition for Alien Relative)', 'otu' ),
			'I-90'           => __( 'I-90 (Renew/Replace Green Card)', 'otu' ),
			'I-765'          => __( 'I-765 (Employment Authorization)', 'otu' ),
			'DACA_Renewal'   => __( 'DACA Renewal', 'otu' ),
			'Other'          => __( 'Other', 'otu' ),
		);
		?>
		<div class="otu-service-form otu-immigration-form">
			<h3 class="otu-form-title"><?php esc_html_e( 'Immigration Services Information', 'otu' ); ?></h3>
			<p class="otu-form-description"><?php esc_html_e( 'Please provide the following details to process your immigration service request.', 'otu' ); ?></p>
			<?php wp_nonce_field( 'otu_immigration_form_nonce', 'otu_immigration_nonce' ); ?>
			<input type="hidden" name="otu_service_type" value="immigration" />

			<div class="otu-form-row">
				<label for="otu_imm_full_name"><?php esc_html_e( 'Full Name *', 'otu' ); ?></label>
				<input type="text" id="otu_imm_full_name" name="otu_imm_full_name"
					value="<?php echo esc_attr( isset( $_POST['otu_imm_full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_imm_full_name'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_imm_dob"><?php esc_html_e( 'Date of Birth *', 'otu' ); ?></label>
				<input type="date" id="otu_imm_dob" name="otu_imm_dob"
					value="<?php echo esc_attr( isset( $_POST['otu_imm_dob'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_imm_dob'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_imm_passport_number"><?php esc_html_e( 'Passport Number *', 'otu' ); ?></label>
				<input type="text" id="otu_imm_passport_number" name="otu_imm_passport_number"
					value="<?php echo esc_attr( isset( $_POST['otu_imm_passport_number'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_imm_passport_number'] ) ) : '' ); ?>"
					required />
			</div>

			<div class="otu-form-row">
				<label for="otu_imm_service_type"><?php esc_html_e( 'Immigration Service Type *', 'otu' ); ?></label>
				<select id="otu_imm_service_type" name="otu_imm_service_type" required>
					<option value=""><?php esc_html_e( '-- Select Service Type --', 'otu' ); ?></option>
					<?php
					$selected_svc = isset( $_POST['otu_imm_service_type'] ) ? sanitize_text_field( wp_unslash( $_POST['otu_imm_service_type'] ) ) : '';
					foreach ( $service_types as $value => $label ) {
						printf(
							'<option value="%s"%s>%s</option>',
							esc_attr( $value ),
							selected( $selected_svc, $value, false ),
							esc_html( $label )
						);
					}
					?>
				</select>
			</div>
		</div>
		<?php
	}

	/**
	 * Validate service form data before adding to cart.
	 *
	 * @param bool $passed    Whether validation has passed.
	 * @param int  $product_id Product ID.
	 * @param int  $quantity  Quantity.
	 * @return bool
	 */
	public function validate_service_form( $passed, $product_id, $quantity ) {
		$service_type = $this->get_service_type( $product_id );

		if ( empty( $service_type ) ) {
			return $passed;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		switch ( $service_type ) {
			case 'llc':
				if ( ! isset( $_POST['otu_llc_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['otu_llc_nonce'] ) ), 'otu_llc_form_nonce' ) ) {
					wc_add_notice( __( 'Security check failed. Please try again.', 'otu' ), 'error' );
					return false;
				}
				$required = array(
					'otu_llc_full_name'       => __( 'Full Name', 'otu' ),
					'otu_llc_phone'           => __( 'Phone Number', 'otu' ),
					'otu_llc_email'           => __( 'Email Address', 'otu' ),
					'otu_llc_business_name'   => __( 'Business Name', 'otu' ),
					'otu_llc_business_address' => __( 'Business Address', 'otu' ),
					'otu_llc_state'           => __( 'State of Formation', 'otu' ),
					'otu_llc_owner_full_name' => __( 'Owner Full Name', 'otu' ),
					'otu_llc_owner_address'   => __( 'Owner Address', 'otu' ),
					'otu_llc_owner_dob'       => __( 'Owner Date of Birth', 'otu' ),
					'otu_llc_owner_ssn'       => __( 'Owner SSN', 'otu' ),
				);
				break;

			case 'ein':
				if ( ! isset( $_POST['otu_ein_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['otu_ein_nonce'] ) ), 'otu_ein_form_nonce' ) ) {
					wc_add_notice( __( 'Security check failed. Please try again.', 'otu' ), 'error' );
					return false;
				}
				$required = array(
					'otu_ein_full_name'        => __( 'Full Name', 'otu' ),
					'otu_ein_business_name'    => __( 'Business Name', 'otu' ),
					'otu_ein_responsible_party' => __( 'Responsible Party Name', 'otu' ),
					'otu_ein_type'             => __( 'Entity Type', 'otu' ),
					'otu_ein_ssn_itin'         => __( 'SSN / ITIN', 'otu' ),
				);
				break;

			case 'immigration':
				if ( ! isset( $_POST['otu_immigration_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['otu_immigration_nonce'] ) ), 'otu_immigration_form_nonce' ) ) {
					wc_add_notice( __( 'Security check failed. Please try again.', 'otu' ), 'error' );
					return false;
				}
				$required = array(
					'otu_imm_full_name'      => __( 'Full Name', 'otu' ),
					'otu_imm_dob'            => __( 'Date of Birth', 'otu' ),
					'otu_imm_passport_number' => __( 'Passport Number', 'otu' ),
					'otu_imm_service_type'   => __( 'Immigration Service Type', 'otu' ),
				);
				break;

			default:
				return $passed;
		}

		foreach ( $required as $field => $label ) {
			if ( empty( $_POST[ $field ] ) ) {
				/* translators: %s: field label */
				wc_add_notice( sprintf( __( '%s is required.', 'otu' ), $label ), 'error' );
				$passed = false;
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return $passed;
	}

	/**
	 * Save form data to cart item.
	 *
	 * @param array $cart_item_data Existing cart item data.
	 * @param int   $product_id     Product ID.
	 * @param int   $variation_id   Variation ID.
	 * @return array
	 */
	public function save_form_data_to_cart( $cart_item_data, $product_id, $variation_id ) {
		$service_type = $this->get_service_type( $product_id );

		if ( empty( $service_type ) ) {
			return $cart_item_data;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$form_data = array( 'service_type' => $service_type );

		switch ( $service_type ) {
			case 'llc':
				$form_data['full_name']        = sanitize_text_field( wp_unslash( $_POST['otu_llc_full_name'] ?? '' ) );
				$form_data['phone']            = sanitize_text_field( wp_unslash( $_POST['otu_llc_phone'] ?? '' ) );
				$form_data['email']            = sanitize_email( wp_unslash( $_POST['otu_llc_email'] ?? '' ) );
				$form_data['business_name']    = sanitize_text_field( wp_unslash( $_POST['otu_llc_business_name'] ?? '' ) );
				$form_data['business_address'] = sanitize_text_field( wp_unslash( $_POST['otu_llc_business_address'] ?? '' ) );
				$form_data['state']            = sanitize_text_field( wp_unslash( $_POST['otu_llc_state'] ?? '' ) );
				$form_data['owner_full_name']  = sanitize_text_field( wp_unslash( $_POST['otu_llc_owner_full_name'] ?? '' ) );
				$form_data['owner_address']    = sanitize_text_field( wp_unslash( $_POST['otu_llc_owner_address'] ?? '' ) );
				$form_data['owner_dob']        = sanitize_text_field( wp_unslash( $_POST['otu_llc_owner_dob'] ?? '' ) );
				// SSN stored as sha256 hash — never store plaintext SSN.
				$raw_ssn = wp_unslash( $_POST['otu_llc_owner_ssn'] ?? '' );
				$form_data['owner_ssn_hash']   = ! empty( $raw_ssn ) ? hash( 'sha256', $raw_ssn ) : '';
				break;

			case 'ein':
				$form_data['full_name']          = sanitize_text_field( wp_unslash( $_POST['otu_ein_full_name'] ?? '' ) );
				$form_data['business_name']      = sanitize_text_field( wp_unslash( $_POST['otu_ein_business_name'] ?? '' ) );
				$form_data['responsible_party']  = sanitize_text_field( wp_unslash( $_POST['otu_ein_responsible_party'] ?? '' ) );
				$form_data['ein_type']           = sanitize_key( wp_unslash( $_POST['otu_ein_type'] ?? '' ) );
				$raw_ssn_itin = wp_unslash( $_POST['otu_ein_ssn_itin'] ?? '' );
				$form_data['ssn_itin_hash']      = ! empty( $raw_ssn_itin ) ? hash( 'sha256', $raw_ssn_itin ) : '';
				break;

			case 'immigration':
				$form_data['full_name']        = sanitize_text_field( wp_unslash( $_POST['otu_imm_full_name'] ?? '' ) );
				$form_data['dob']              = sanitize_text_field( wp_unslash( $_POST['otu_imm_dob'] ?? '' ) );
				$form_data['passport_number']  = sanitize_text_field( wp_unslash( $_POST['otu_imm_passport_number'] ?? '' ) );
				$form_data['service_sub_type'] = sanitize_text_field( wp_unslash( $_POST['otu_imm_service_type'] ?? '' ) );
				break;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$cart_item_data['otu_form_data'] = $form_data;
		return $cart_item_data;
	}

	/**
	 * Save form data to order line item meta.
	 *
	 * @param WC_Order_Item_Product $item          Order item.
	 * @param string                $cart_item_key  Cart item key.
	 * @param array                 $values         Cart item values.
	 * @param WC_Order              $order          Order.
	 */
	public function save_form_data_to_order( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['otu_form_data'] ) && is_array( $values['otu_form_data'] ) ) {
			$form_data = $values['otu_form_data'];
			foreach ( $form_data as $key => $value ) {
				$item->update_meta_data( '_otu_' . sanitize_key( $key ), sanitize_text_field( (string) $value ) );
			}
		}
	}

	/**
	 * Display form data in admin order screen.
	 *
	 * @param int    $item_id   Order item ID.
	 * @param object $item      Order item.
	 * @param object $product   Product.
	 */
	public function display_form_data_in_admin( $item_id, $item, $product ) {
		$service_type = $item->get_meta( '_otu_service_type' );

		if ( empty( $service_type ) ) {
			return;
		}

		$meta_keys = array(
			'full_name', 'phone', 'email', 'business_name', 'business_address',
			'state', 'owner_full_name', 'owner_address', 'owner_dob',
			'responsible_party', 'ein_type', 'dob', 'passport_number', 'service_sub_type',
		);

		echo '<div class="otu-order-form-data">';
		/* translators: %s: service type label */
		echo '<strong>' . esc_html( sprintf( __( 'OTU Service Form Data (%s)', 'otu' ), strtoupper( $service_type ) ) ) . '</strong><br/>';
		foreach ( $meta_keys as $key ) {
			$value = $item->get_meta( '_otu_' . $key );
			if ( ! empty( $value ) ) {
				echo '<span><strong>' . esc_html( ucwords( str_replace( '_', ' ', $key ) ) ) . ':</strong> ' . esc_html( $value ) . '</span><br/>';
			}
		}
		echo '</div>';
	}
}
