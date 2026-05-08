<?php
/**
 * Digital Products archive template.
 *
 * @package one-ten-united
 */

get_header();
?>

<main id="main-content" class="site-main digital-products-archive-template">

	<!-- Archive Hero -->
	<div class="archive-hero archive-hero--digital">
		<div class="container">
			<span class="section-eyebrow"><?php esc_html_e( 'Digital Store', 'otu' ); ?></span>
			<h1 class="archive-hero__title"><?php esc_html_e( 'Digital Products', 'otu' ); ?></h1>
			<p class="archive-hero__subtitle">
				<?php esc_html_e( 'Downloadable templates, guides, and tools to help you manage your business and personal documents.', 'otu' ); ?>
			</p>
		</div>
	</div>

	<div class="container">

		<?php if ( have_posts() ) : ?>

			<div class="digital-products-grid">
				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'digital-product-card' ); ?>>

						<a href="<?php the_permalink(); ?>" class="digital-product-card__thumb-link" tabindex="-1" aria-hidden="true">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="digital-product-card__thumb">
									<?php the_post_thumbnail( 'otu-card', array( 'loading' => 'lazy' ) ); ?>
								</div>
							<?php else : ?>
								<div class="digital-product-card__thumb digital-product-card__thumb--placeholder">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="60" height="60">
										<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
										<polyline points="14 2 14 8 20 8"/>
										<line x1="16" y1="13" x2="8" y2="13"/>
										<line x1="16" y1="17" x2="8" y2="17"/>
										<polyline points="10 9 9 9 8 9"/>
									</svg>
								</div>
							<?php endif; ?>
						</a>

						<div class="digital-product-card__body">

							<h2 class="digital-product-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<div class="digital-product-card__excerpt">
								<?php the_excerpt(); ?>
							</div>

							<?php
							// Show WooCommerce price if this is a WooCommerce product.
							if ( function_exists( 'wc_get_product' ) ) {
								$product = wc_get_product( get_the_ID() );
								if ( $product ) {
									echo '<div class="digital-product-card__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
								}
							}
							?>

							<div class="digital-product-card__actions">
								<?php if ( function_exists( 'wc_get_product' ) && wc_get_product( get_the_ID() ) ) : ?>
									<a href="<?php echo esc_url( add_query_arg( array( 'add-to-cart' => get_the_ID() ), wc_get_cart_url() ) ); ?>"
									   class="btn btn-primary btn-sm">
										<?php esc_html_e( 'Add to Cart', 'otu' ); ?>
									</a>
								<?php endif; ?>
								<a href="<?php the_permalink(); ?>" class="btn btn-secondary btn-sm">
									<?php esc_html_e( 'View Details', 'otu' ); ?>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
								</a>
							</div>

						</div>

					</article>

				<?php endwhile; ?>
			</div><!-- .digital-products-grid -->

			<?php otu_pagination(); ?>

		<?php else : ?>

			<div class="no-results">
				<h2><?php esc_html_e( 'No Digital Products Available Yet', 'otu' ); ?></h2>
				<p>
					<?php esc_html_e( 'We are working on adding digital tools, templates, and guides. Check back soon!', 'otu' ); ?>
				</p>
				<a href="<?php echo esc_url( home_url( '/services' ) ); ?>" class="btn btn-primary">
					<?php esc_html_e( 'Browse Our Services', 'otu' ); ?>
				</a>
			</div>

		<?php endif; ?>

	</div><!-- .container -->

	<!-- CTA Bar -->
	<div class="archive-cta-bar">
		<div class="container archive-cta-bar__inner">
			<p><?php esc_html_e( 'Looking for a specific template or tool? Let us know!', 'otu' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/contact-us' ) ); ?>" class="btn btn-gold">
				<?php esc_html_e( 'Contact Us', 'otu' ); ?>
			</a>
		</div>
	</div>

</main>

<?php get_footer(); ?>
