<?php
/**
 * Theme header template.
 *
 * @package one-ten-united
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content">
	<?php esc_html_e( 'Skip to content', 'otu' ); ?>
</a>

<!-- ======================================================
     TOP BAR
     ====================================================== -->
<div class="top-bar" role="banner">
	<div class="container top-bar__inner">
		<ul class="top-bar__contact" aria-label="<?php esc_attr_e( 'Contact information', 'otu' ); ?>">
			<li>
				<a href="tel:+17185550100" class="top-bar__link">
					<svg aria-hidden="true" focusable="false" class="top-bar__icon" viewBox="0 0 24 24" width="14" height="14"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
					(718) 555-0100
				</a>
			</li>
			<li>
				<a href="mailto:info@onetenunited.com" class="top-bar__link">
					<svg aria-hidden="true" focusable="false" class="top-bar__icon" viewBox="0 0 24 24" width="14" height="14"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
					info@onetenunited.com
				</a>
			</li>
		</ul>

		<ul class="top-bar__social" aria-label="<?php esc_attr_e( 'Social media links', 'otu' ); ?>">
			<li>
				<a href="https://facebook.com/onetenunited" class="top-bar__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Facebook', 'otu' ); ?>">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
				</a>
			</li>
			<li>
				<a href="https://instagram.com/onetenunited" class="top-bar__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Instagram', 'otu' ); ?>">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" fill="none" stroke="currentColor" stroke-width="2"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="currentColor" stroke-width="2"/></svg>
				</a>
			</li>
			<li>
				<a href="https://twitter.com/onetenunited" class="top-bar__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Twitter / X', 'otu' ); ?>">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
				</a>
			</li>
		</ul>
	</div>
</div>

<!-- ======================================================
     MAIN HEADER
     ====================================================== -->
<header id="site-header" class="site-header" role="banner">
	<div class="container site-header__inner">

		<!-- Logo -->
		<div class="site-header__logo">
			<?php otu_get_logo(); ?>
		</div>

		<!-- Primary Navigation -->
		<nav id="primary-nav" class="primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'otu' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'primary-menu',
				'container'      => false,
				'fallback_cb'    => 'otu_fallback_menu',
			) );
			?>
		</nav>

		<!-- Hamburger Button -->
		<button
			id="nav-toggle"
			class="nav-toggle"
			aria-expanded="false"
			aria-controls="primary-nav"
			aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'otu' ); ?>"
		>
			<span class="hamburger-bar"></span>
			<span class="hamburger-bar"></span>
			<span class="hamburger-bar"></span>
		</button>

	</div>
</header>

<!-- ======================================================
     BREADCRUMBS
     ====================================================== -->
<?php if ( ! is_front_page() ) : ?>
<div class="breadcrumbs-wrap">
	<div class="container">
		<?php otu_breadcrumbs(); ?>
	</div>
</div>
<?php endif; ?>

<!-- ======================================================
     MAIN CONTENT STARTS
     ====================================================== -->
<?php
/**
 * Fallback menu when no menu is assigned.
 */
function otu_fallback_menu() {
	echo '<ul class="primary-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'otu' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/services' ) ) . '">' . esc_html__( 'Services', 'otu' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/courses' ) ) . '">' . esc_html__( 'Courses', 'otu' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/shop' ) ) . '">' . esc_html__( 'Shop', 'otu' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/contact' ) ) . '">' . esc_html__( 'Contact', 'otu' ) . '</a></li>';
	echo '</ul>';
}
