<?php
/**
 * WooCommerce Integration for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_WooCommerce
 *
 * Handles WooCommerce-specific integrations.
 */
class OTU_WooCommerce {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		add_filter( 'woocommerce_cart_item_name', array( $this, 'append_service_type_badge' ), 10, 3 );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'add_checkout_fields' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_checkout_fields' ) );
		add_action( 'woocommerce_email_order_meta', array( $this, 'include_otu_form_data_in_email' ), 10, 3 );
		add_action( 'woocommerce_before_single_product', array( $this, 'show_service_note' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'custom_thankyou_message' ), 5 );
		add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this, 'add_order_service_column' ) );
		add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this, 'render_order_service_column' ), 10, 2 );
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_service_column' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_order_service_column_legacy' ), 10, 2 );
	}

	/**
	 * Show an admin notice if WooCommerce is not active.
	 */
	public function woocommerce_missing_notice() {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: WooCommerce plugin link */
						__( '<strong>OTU Services</strong> requires %s to be installed and active.', 'otu' ),
						'<a href="' . esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ) . '">WooCommerce</a>'
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Append service type badge to cart item name.
	 *
	 * @param string $name          Product name.
	 * @param array  $cart_item     Cart item data.
	 * @param string $cart_item_key Cart item key.
	 * @return string
	 */
	public function append_service_type_badge( $name, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['otu_form_data']['service_type'] ) ) {
			$service_type = sanitize_text_field( $cart_item['otu_form_data']['service_type'] );
			$badge        = '<span class="otu-service-badge otu-badge-' . esc_attr( $service_type ) . '">' . esc_html( strtoupper( $service_type ) ) . '</span>';
			$name        .= ' ' . $badge;
		}
		return $name;
	}

	/**
	 * Add custom checkout fields.
	 *
	 * @param array $fields Checkout fields.
	 * @return array
	 */
	public function add_checkout_fields( $fields ) {
		$fields['order']['otu_how_did_you_hear'] = array(
			'type'     => 'select',
			'label'    => __( 'How did you hear about us?', 'otu' ),
			'required' => false,
			'class'    => array( 'form-row-wide' ),
			'options'  => array(
				''              => __( '-- Select an option --', 'otu' ),
				'google'        => __( 'Google Search', 'otu' ),
				'social_media'  => __( 'Social Media', 'otu' ),
				'referral'      => __( 'Referral / Word of Mouth', 'otu' ),
				'advertisement' => __( 'Advertisement', 'otu' ),
				'returning'     => __( 'Returning Customer', 'otu' ),
				'other'         => __( 'Other', 'otu' ),
			),
		);
		return $fields;
	}

	/**
	 * Save custom checkout fields to order meta.
	 *
	 * @param int $order_id Order ID.
	 */
	public function save_checkout_fields( $order_id ) {
		if ( ! empty( $_POST['otu_how_did_you_hear'] ) ) {
			$allowed = array( 'google', 'social_media', 'referral', 'advertisement', 'returning', 'other' );
			$value   = sanitize_key( wp_unslash( $_POST['otu_how_did_you_hear'] ) );
			if ( in_array( $value, $allowed, true ) ) {
				$order = wc_get_order( $order_id );
				if ( $order ) {
					$order->update_meta_data( '_otu_how_did_you_hear', $value );
					$order->save();
				}
			}
		}
	}

	/**
	 * Include OTU form data in order email.
	 *
	 * @param WC_Order $order         Order object.
	 * @param bool     $sent_to_admin Whether sent to admin.
	 * @param bool     $plain_text    Whether plain text.
	 */
	public function include_otu_form_data_in_email( $order, $sent_to_admin, $plain_text ) {
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$service_type = $item->get_meta( '_otu_service_type' );
			if ( empty( $service_type ) ) {
				continue;
			}

			if ( $plain_text ) {
				echo "\n" . esc_html__( 'OTU Service Information', 'otu' ) . "\n";
				echo esc_html( str_repeat( '-', 30 ) ) . "\n";
			} else {
				echo '<h3 style="color:#800020;">' . esc_html__( 'OTU Service Information', 'otu' ) . '</h3>';
				echo '<table cellspacing="0" cellpadding="6" style="width:100%;border:1px solid #eee;" border="1" bordercolor="#eee"><tbody>';
			}

			$display_keys = array(
				'full_name', 'phone', 'email', 'business_name', 'business_address',
				'state', 'owner_full_name', 'owner_address', 'owner_dob',
				'responsible_party', 'ein_type', 'dob', 'passport_number', 'service_sub_type',
			);

			foreach ( $display_keys as $key ) {
				$value = $item->get_meta( '_otu_' . $key );
				if ( empty( $value ) ) {
					continue;
				}
				$label = ucwords( str_replace( '_', ' ', $key ) );
				if ( $plain_text ) {
					echo esc_html( $label ) . ': ' . esc_html( $value ) . "\n";
				} else {
					echo '<tr><th style="text-align:left;padding:6px;background:#f8f8f8;">' . esc_html( $label ) . '</th><td style="text-align:left;padding:6px;">' . esc_html( $value ) . '</td></tr>';
				}
			}

			if ( ! $plain_text ) {
				echo '</tbody></table>';
			}
		}
	}

	/**
	 * Show service-specific note on single product page.
	 */
	public function show_service_note() {
		global $product;
		if ( ! $product ) {
			return;
		}

		$service_type = sanitize_key( get_post_meta( $product->get_id(), '_otu_service_type', true ) );
		if ( empty( $service_type ) ) {
			return;
		}

		$notes = array(
			'llc'         => __( '✅ Our LLC formation service includes state filing, registered agent consultation, and operating agreement template. Processing time: 5–10 business days.', 'otu' ),
			'ein'         => __( '✅ Our EIN application service includes IRS form preparation and submission. You will receive your EIN within 2–5 business days.', 'otu' ),
			'immigration' => __( '✅ Our immigration document preparation service is provided by certified document preparers. Please note: we are not attorneys and do not provide legal advice.', 'otu' ),
		);

		if ( isset( $notes[ $service_type ] ) ) {
			echo '<div class="otu-service-note otu-service-note-' . esc_attr( $service_type ) . '">' . esc_html( $notes[ $service_type ] ) . '</div>';
		}
	}

	/**
	 * Custom thank-you message with next steps.
	 *
	 * @param int $order_id Order ID.
	 */
	public function custom_thankyou_message( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		echo '<div class="otu-thankyou-message">';
		echo '<h2>' . esc_html__( 'What Happens Next?', 'otu' ) . '</h2>';
		echo '<ol class="otu-next-steps">';
		echo '<li>' . esc_html__( 'You will receive a confirmation email with your order details shortly.', 'otu' ) . '</li>';
		echo '<li>' . esc_html__( 'Our team will review your submitted information within 1 business day.', 'otu' ) . '</li>';
		echo '<li>' . esc_html__( 'A specialist will contact you at the email or phone number you provided.', 'otu' ) . '</li>';
		echo '<li>' . esc_html__( 'You can track your order status from your Dashboard at any time.', 'otu' ) . '</li>';
		echo '</ol>';

		$dashboard_page = get_page_by_path( 'dashboard' );
		if ( $dashboard_page ) {
			echo '<p><a href="' . esc_url( get_permalink( $dashboard_page->ID ) ) . '" class="otu-btn otu-btn-primary">' . esc_html__( 'Go to My Dashboard', 'otu' ) . '</a></p>';
		}
		echo '</div>';
	}

	/**
	 * Add service type column to orders list (HPOS).
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function add_order_service_column( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;
			if ( 'order_status' === $key ) {
				$new_columns['otu_service_type'] = __( 'Service Type', 'otu' );
			}
		}
		return $new_columns;
	}

	/**
	 * Render service type column for HPOS orders.
	 *
	 * @param string   $column   Column name.
	 * @param WC_Order $order    Order object.
	 */
	public function render_order_service_column( $column, $order ) {
		if ( 'otu_service_type' !== $column ) {
			return;
		}
		$this->output_service_type_for_order( $order );
	}

	/**
	 * Render service type column for legacy orders.
	 *
	 * @param string $column   Column name.
	 * @param int    $post_id  Post ID.
	 */
	public function render_order_service_column_legacy( $column, $post_id ) {
		if ( 'otu_service_type' !== $column ) {
			return;
		}
		$order = wc_get_order( $post_id );
		if ( $order ) {
			$this->output_service_type_for_order( $order );
		}
	}

	/**
	 * Output service type badges for an order.
	 *
	 * @param WC_Order $order Order object.
	 */
	private function output_service_type_for_order( $order ) {
		$types = array();
		foreach ( $order->get_items() as $item ) {
			$st = $item->get_meta( '_otu_service_type' );
			if ( ! empty( $st ) && ! in_array( $st, $types, true ) ) {
				$types[] = sanitize_text_field( $st );
			}
		}
		if ( ! empty( $types ) ) {
			foreach ( $types as $type ) {
				echo '<span class="otu-service-badge otu-badge-' . esc_attr( $type ) . '">' . esc_html( strtoupper( $type ) ) . '</span> ';
			}
		} else {
			echo '<span class="otu-badge-none">—</span>';
		}
	}
}
