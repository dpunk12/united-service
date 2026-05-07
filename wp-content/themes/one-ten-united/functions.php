<?php
/**
 * One Ten United theme functions and definitions.
 *
 * @package one-ten-united
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OTU_VERSION', '1.0.0' );
define( 'OTU_DIR', get_template_directory() );
define( 'OTU_URI', get_template_directory_uri() );

/* =========================================================
 * THEME SETUP
 * ========================================================= */
function otu_setup() {
	load_theme_textdomain( 'otu', OTU_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array(
		'height'               => 80,
		'width'                => 240,
		'flex-height'          => true,
		'flex-width'           => true,
		'header-text'          => array( 'site-title', 'site-description' ),
		'unlink-homepage-logo' => true,
	) );

	// WooCommerce support.
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 400,
		'single_image_width'    => 600,
		'product_grid'          => array(
			'default_rows'    => 3,
			'min_rows'        => 1,
			'default_columns' => 3,
			'min_columns'     => 1,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc_product_gallery_zoom' );
	add_theme_support( 'wc_product_gallery_lightbox' );
	add_theme_support( 'wc_product_gallery_slider' );

	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Navigation', 'otu' ),
		'footer'  => esc_html__( 'Footer Navigation', 'otu' ),
	) );

	add_image_size( 'otu-card', 600, 400, true );
	add_image_size( 'otu-hero', 1920, 800, true );
}
add_action( 'after_setup_theme', 'otu_setup' );

/* =========================================================
 * ENQUEUE SCRIPTS & STYLES
 * ========================================================= */
function otu_scripts() {
	// Google Fonts – Poppins.
	wp_enqueue_style(
		'otu-google-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap',
		array(),
		null
	);

	// Main stylesheet.
	wp_enqueue_style(
		'otu-main',
		OTU_URI . '/assets/css/main.css',
		array( 'otu-google-fonts' ),
		OTU_VERSION
	);

	// Main JS.
	wp_enqueue_script(
		'otu-main',
		OTU_URI . '/assets/js/main.js',
		array( 'jquery' ),
		OTU_VERSION,
		true
	);

	// Localize script data.
	wp_localize_script( 'otu-main', 'otuData', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'otu_nonce' ),
		'homeUrl' => esc_url( home_url( '/' ) ),
	) );

	// Comment reply.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'otu_scripts' );

/* =========================================================
 * WIDGET AREAS
 * ========================================================= */
function otu_widgets_init() {
	$defaults = array(
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	);

	register_sidebar( array_merge( $defaults, array(
		'name'        => esc_html__( 'Main Sidebar', 'otu' ),
		'id'          => 'sidebar',
		'description' => esc_html__( 'Primary sidebar displayed on blog and pages.', 'otu' ),
	) ) );

	register_sidebar( array_merge( $defaults, array(
		'name'        => esc_html__( 'Footer Column 1', 'otu' ),
		'id'          => 'footer-1',
		'description' => esc_html__( 'First footer widget column.', 'otu' ),
	) ) );

	register_sidebar( array_merge( $defaults, array(
		'name'        => esc_html__( 'Footer Column 2', 'otu' ),
		'id'          => 'footer-2',
		'description' => esc_html__( 'Second footer widget column.', 'otu' ),
	) ) );

	register_sidebar( array_merge( $defaults, array(
		'name'        => esc_html__( 'Footer Column 3', 'otu' ),
		'id'          => 'footer-3',
		'description' => esc_html__( 'Third footer widget column.', 'otu' ),
	) ) );
}
add_action( 'widgets_init', 'otu_widgets_init' );

/* =========================================================
 * EXCERPT LENGTH
 * ========================================================= */
function otu_excerpt_length( $length ) {
	if ( is_admin() ) {
		return $length;
	}
	return 25;
}
add_filter( 'excerpt_length', 'otu_excerpt_length' );

function otu_excerpt_more( $more ) {
	if ( is_admin() ) {
		return $more;
	}
	return __( '&hellip;', 'otu' );
}
add_filter( 'excerpt_more', 'otu_excerpt_more' );

/* =========================================================
 * BODY CLASSES HELPER
 * ========================================================= */
function otu_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( is_active_sidebar( 'sidebar' ) && ( is_single() || is_page() ) ) {
		$classes[] = 'has-sidebar';
	} else {
		$classes[] = 'no-sidebar';
	}

	if ( is_front_page() ) {
		$classes[] = 'front-page';
	}

	return $classes;
}
add_filter( 'body_class', 'otu_body_classes' );

/* =========================================================
 * BREADCRUMBS
 * ========================================================= */
function otu_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$separator = '<span class="breadcrumb-sep" aria-hidden="true">/</span>';
	$home_text = esc_html__( 'Home', 'otu' );

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'otu' ) . '">';
	echo '<ol class="breadcrumb-list">';
	echo '<li class="breadcrumb-item"><a href="' . esc_url( home_url( '/' ) ) . '">' . $home_text . '</a></li>';

	if ( is_category() || is_single() ) {
		$categories = get_the_category();
		if ( $categories ) {
			$cat = $categories[0];
			echo '<li class="breadcrumb-item">' . $separator . '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
		}
		if ( is_single() ) {
			echo '<li class="breadcrumb-item breadcrumb-current">' . $separator . '<span>' . esc_html( get_the_title() ) . '</span></li>';
		}
	} elseif ( is_page() && ! is_front_page() ) {
		$ancestors = get_post_ancestors( get_the_ID() );
		$ancestors = array_reverse( $ancestors );
		foreach ( $ancestors as $ancestor ) {
			echo '<li class="breadcrumb-item">' . $separator . '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a></li>';
		}
		echo '<li class="breadcrumb-item breadcrumb-current">' . $separator . '<span>' . esc_html( get_the_title() ) . '</span></li>';
	} elseif ( is_tag() ) {
		echo '<li class="breadcrumb-item breadcrumb-current">' . $separator . '<span>' . esc_html__( 'Tag: ', 'otu' ) . esc_html( single_tag_title( '', false ) ) . '</span></li>';
	} elseif ( is_author() ) {
		echo '<li class="breadcrumb-item breadcrumb-current">' . $separator . '<span>' . esc_html__( 'Author: ', 'otu' ) . esc_html( get_the_author() ) . '</span></li>';
	} elseif ( is_search() ) {
		echo '<li class="breadcrumb-item breadcrumb-current">' . $separator . '<span>' . esc_html__( 'Search Results for: ', 'otu' ) . esc_html( get_search_query() ) . '</span></li>';
	} elseif ( is_404() ) {
		echo '<li class="breadcrumb-item breadcrumb-current">' . $separator . '<span>' . esc_html__( '404 Not Found', 'otu' ) . '</span></li>';
	} elseif ( is_archive() ) {
		echo '<li class="breadcrumb-item breadcrumb-current">' . $separator . '<span>' . esc_html( get_the_archive_title() ) . '</span></li>';
	}

	echo '</ol>';
	echo '</nav>';
}

/* =========================================================
 * LOGO HELPER
 * ========================================================= */
function otu_get_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
	} else {
		echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="otu-text-logo" rel="home">';
		echo '<img src="' . esc_url( OTU_URI . '/assets/images/logo.svg' ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" width="200" height="70" />';
		echo '</a>';
	}
}

/* =========================================================
 * WOOCOMMERCE HELPERS
 * ========================================================= */
// Remove default WooCommerce wrappers so theme controls layout.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

function otu_woo_wrapper_start() {
	echo '<main id="main-content" class="site-main woocommerce-main">';
	echo '<div class="container">';
}
add_action( 'woocommerce_before_main_content', 'otu_woo_wrapper_start', 10 );

function otu_woo_wrapper_end() {
	echo '</div>';
	echo '</main>';
}
add_action( 'woocommerce_after_main_content', 'otu_woo_wrapper_end', 10 );

/* =========================================================
 * SERVICE URL HELPER
 * =========================================================
 * Resolve a service "card" link to a real service permalink when a
 * `service` CPT post exists at the given slug; otherwise fall back to
 * the services archive. This prevents 404s when the homepage links to
 * services that have not yet been created in the admin.
 */
function otu_service_url( $slug ) {
	$slug = sanitize_title( $slug );

	if ( $slug && post_type_exists( 'service' ) ) {
		$service = get_page_by_path( $slug, OBJECT, 'service' );
		if ( $service && 'publish' === $service->post_status ) {
			return get_permalink( $service );
		}
	}

	// Fallback: services archive / page.
	if ( post_type_exists( 'service' ) ) {
		$archive = get_post_type_archive_link( 'service' );
		if ( $archive ) {
			return $archive;
		}
	}

	return home_url( '/services/' );
}

/* =========================================================
 * CUSTOM POST TYPE: SERVICE (register if no plugin handles it)
 * ========================================================= */
function otu_register_service_cpt() {
	// Fallback only — if the OTU Services plugin (or another plugin) already
	// registered the `service` post type, do nothing to avoid clobbering its
	// configuration (rewrite rules, capabilities, taxonomies, etc.).
	if ( post_type_exists( 'service' ) ) {
		return;
	}

	$labels = array(
		'name'               => esc_html_x( 'Services', 'post type general name', 'otu' ),
		'singular_name'      => esc_html_x( 'Service', 'post type singular name', 'otu' ),
		'add_new'            => esc_html__( 'Add New', 'otu' ),
		'add_new_item'       => esc_html__( 'Add New Service', 'otu' ),
		'edit_item'          => esc_html__( 'Edit Service', 'otu' ),
		'new_item'           => esc_html__( 'New Service', 'otu' ),
		'view_item'          => esc_html__( 'View Service', 'otu' ),
		'search_items'       => esc_html__( 'Search Services', 'otu' ),
		'not_found'          => esc_html__( 'No services found.', 'otu' ),
		'not_found_in_trash' => esc_html__( 'No services found in Trash.', 'otu' ),
	);

	register_post_type( 'service', array(
		'labels'             => $labels,
		'public'             => true,
		'has_archive'        => true,
		'rewrite'            => array( 'slug' => 'services' ),
		'menu_icon'          => 'dashicons-portfolio',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'show_in_rest'       => true,
		'taxonomies'         => array( 'service_category' ),
	) );

	if ( ! taxonomy_exists( 'service_category' ) ) {
		register_taxonomy( 'service_category', array( 'service' ), array(
			'labels'            => array(
				'name'          => esc_html_x( 'Service Categories', 'taxonomy general name', 'otu' ),
				'singular_name' => esc_html_x( 'Service Category', 'taxonomy singular name', 'otu' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'rewrite'           => array( 'slug' => 'service-category' ),
			'show_in_rest'      => true,
		) );
	}
}
// Register late so the plugin (default priority 10) wins when both are active.
add_action( 'init', 'otu_register_service_cpt', 20 );

/* =========================================================
 * TITLE TAG FALLBACK
 * ========================================================= */
function otu_wp_title( $title, $sep ) {
	if ( is_feed() ) {
		return $title;
	}
	$title .= get_bloginfo( 'name' );
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " $sep " . $site_description;
	}
	return $title;
}
add_filter( 'wp_title', 'otu_wp_title', 10, 2 );

/* =========================================================
 * PAGINATION HELPER
 * ========================================================= */
function otu_pagination() {
	$args = array(
		'prev_text' => esc_html__( '&laquo; Previous', 'otu' ),
		'next_text' => esc_html__( 'Next &raquo;', 'otu' ),
		'type'      => 'plain',
	);
	$links = paginate_links( $args );
	if ( $links ) {
		echo '<nav class="pagination" aria-label="' . esc_attr__( 'Posts pagination', 'otu' ) . '">';
		echo $links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() already escapes output.
		echo '</nav>';
	}
}
