<?php
/**
 * 404 error page template.
 *
 * @package one-ten-united
 */

get_header();
?>

<main id="main-content" class="site-main error-404-template">
	<div class="container">
		<div class="error-404">

			<div class="error-404__visual" aria-hidden="true">
				<span class="error-404__code">404</span>
				<div class="error-404__icon">
					<svg viewBox="0 0 24 24" width="80" height="80" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
				</div>
			</div>

			<div class="error-404__content">
				<h1 class="error-404__title">
					<?php esc_html_e( 'Page Not Found', 'otu' ); ?>
				</h1>
				<p class="error-404__message">
					<?php esc_html_e( 'Sorry, the page you are looking for doesn\'t exist, has been moved, or the URL was typed incorrectly.', 'otu' ); ?>
				</p>

				<!-- Search Form -->
				<div class="error-404__search">
					<p class="error-404__search-label"><?php esc_html_e( 'Try searching for what you need:', 'otu' ); ?></p>
					<?php get_search_form(); ?>
				</div>

				<!-- Quick Links -->
				<div class="error-404__links">
					<p><?php esc_html_e( 'Or visit one of these helpful pages:', 'otu' ); ?></p>
					<div class="error-404__btn-group">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
							<?php esc_html_e( 'Home', 'otu' ); ?>
						</a>
						<a href="<?php echo esc_url( home_url( '/services' ) ); ?>" class="btn btn-secondary">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
							<?php esc_html_e( 'Services', 'otu' ); ?>
						</a>
						<a href="<?php echo esc_url( home_url( '/courses' ) ); ?>" class="btn btn-secondary">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
							<?php esc_html_e( 'Courses', 'otu' ); ?>
						</a>
						<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-gold">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
							<?php esc_html_e( 'Contact Us', 'otu' ); ?>
						</a>
					</div>
				</div>
			</div>

		</div><!-- .error-404 -->
	</div><!-- .container -->
</main>

<?php get_footer(); ?>
