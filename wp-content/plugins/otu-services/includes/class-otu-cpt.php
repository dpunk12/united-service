<?php
/**
 * Custom Post Types and Taxonomies for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_CPT
 *
 * Registers custom post types and taxonomies.
 */
class OTU_CPT {

	/**
	 * Default service category terms to create.
	 *
	 * @var array
	 */
	private $default_terms = array(
		'Business Formation',
		'Immigration',
		'Notary',
		'Tax',
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ), 5 );
		add_action( 'init', array( $this, 'register_post_types' ), 10 );
		add_action( 'init', array( $this, 'create_default_terms' ), 15 );
	}

	/**
	 * Register custom post types.
	 */
	public function register_post_types() {
		// Service CPT.
		$service_labels = array(
			'name'                  => _x( 'Services', 'Post type general name', 'otu' ),
			'singular_name'         => _x( 'Service', 'Post type singular name', 'otu' ),
			'menu_name'             => _x( 'Services', 'Admin Menu text', 'otu' ),
			'name_admin_bar'        => _x( 'Service', 'Add New on Toolbar', 'otu' ),
			'add_new'               => __( 'Add New', 'otu' ),
			'add_new_item'          => __( 'Add New Service', 'otu' ),
			'new_item'              => __( 'New Service', 'otu' ),
			'edit_item'             => __( 'Edit Service', 'otu' ),
			'view_item'             => __( 'View Service', 'otu' ),
			'all_items'             => __( 'All Services', 'otu' ),
			'search_items'          => __( 'Search Services', 'otu' ),
			'parent_item_colon'     => __( 'Parent Services:', 'otu' ),
			'not_found'             => __( 'No services found.', 'otu' ),
			'not_found_in_trash'    => __( 'No services found in Trash.', 'otu' ),
			'featured_image'        => _x( 'Service Cover Image', 'Overrides the "Featured Image" phrase.', 'otu' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase.', 'otu' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase.', 'otu' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase.', 'otu' ),
			'archives'              => _x( 'Service archives', 'The post type archive label used in nav menus.', 'otu' ),
			'insert_into_item'      => _x( 'Insert into service', 'Overrides the "Insert into post" phrase.', 'otu' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this service', 'Overrides the "Uploaded to this post" phrase.', 'otu' ),
			'filter_items_list'     => _x( 'Filter services list', 'Screen reader text for the filter links.', 'otu' ),
			'items_list_navigation' => _x( 'Services list navigation', 'Screen reader text for the pagination.', 'otu' ),
			'items_list'            => _x( 'Services list', 'Screen reader text for the items list.', 'otu' ),
		);

		$service_args = array(
			'labels'             => $service_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => 'services/%service_category%',
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-admin-tools',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'show_in_rest'       => true,
			'taxonomies'         => array( 'service_category' ),
		);

		register_post_type( 'service', $service_args );

		// Digital Product CPT.
		$dp_labels = array(
			'name'                  => _x( 'Digital Products', 'Post type general name', 'otu' ),
			'singular_name'         => _x( 'Digital Product', 'Post type singular name', 'otu' ),
			'menu_name'             => _x( 'Digital Products', 'Admin Menu text', 'otu' ),
			'name_admin_bar'        => _x( 'Digital Product', 'Add New on Toolbar', 'otu' ),
			'add_new'               => __( 'Add New', 'otu' ),
			'add_new_item'          => __( 'Add New Digital Product', 'otu' ),
			'new_item'              => __( 'New Digital Product', 'otu' ),
			'edit_item'             => __( 'Edit Digital Product', 'otu' ),
			'view_item'             => __( 'View Digital Product', 'otu' ),
			'all_items'             => __( 'All Digital Products', 'otu' ),
			'search_items'          => __( 'Search Digital Products', 'otu' ),
			'parent_item_colon'     => __( 'Parent Digital Products:', 'otu' ),
			'not_found'             => __( 'No digital products found.', 'otu' ),
			'not_found_in_trash'    => __( 'No digital products found in Trash.', 'otu' ),
			'featured_image'        => _x( 'Digital Product Cover Image', 'Overrides the "Featured Image" phrase.', 'otu' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase.', 'otu' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase.', 'otu' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase.', 'otu' ),
			'archives'              => _x( 'Digital Product archives', 'The post type archive label used in nav menus.', 'otu' ),
			'insert_into_item'      => _x( 'Insert into digital product', 'Overrides the "Insert into post" phrase.', 'otu' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this digital product', 'Overrides the "Uploaded to this post" phrase.', 'otu' ),
			'filter_items_list'     => _x( 'Filter digital products list', 'Screen reader text for the filter links.', 'otu' ),
			'items_list_navigation' => _x( 'Digital Products list navigation', 'Screen reader text for the pagination.', 'otu' ),
			'items_list'            => _x( 'Digital Products list', 'Screen reader text for the items list.', 'otu' ),
		);

		$dp_args = array(
			'labels'             => $dp_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'digital-products' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 6,
			'menu_icon'          => 'dashicons-download',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'show_in_rest'       => true,
		);

		register_post_type( 'digital_product', $dp_args );
	}

	/**
	 * Register custom taxonomies.
	 */
	public function register_taxonomies() {
		$cat_labels = array(
			'name'              => _x( 'Service Categories', 'taxonomy general name', 'otu' ),
			'singular_name'     => _x( 'Service Category', 'taxonomy singular name', 'otu' ),
			'search_items'      => __( 'Search Service Categories', 'otu' ),
			'all_items'         => __( 'All Service Categories', 'otu' ),
			'parent_item'       => __( 'Parent Service Category', 'otu' ),
			'parent_item_colon' => __( 'Parent Service Category:', 'otu' ),
			'edit_item'         => __( 'Edit Service Category', 'otu' ),
			'update_item'       => __( 'Update Service Category', 'otu' ),
			'add_new_item'      => __( 'Add New Service Category', 'otu' ),
			'new_item_name'     => __( 'New Service Category Name', 'otu' ),
			'menu_name'         => __( 'Categories', 'otu' ),
		);

		$cat_args = array(
			'hierarchical'      => true,
			'labels'            => $cat_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'service-category' ),
			'show_in_rest'      => true,
			'public'            => true,
		);

		register_taxonomy( 'service_category', array( 'service' ), $cat_args );
	}

	/**
	 * Create default service category terms.
	 */
	public function create_default_terms() {
		foreach ( $this->default_terms as $term_name ) {
			if ( ! term_exists( $term_name, 'service_category' ) ) {
				wp_insert_term( $term_name, 'service_category' );
			}
		}
	}
}
