<?php
/**
 * Services archive template.
 *
 * @package one-ten-united
 */

get_header();
?>

<main id="main-content" class="site-main services-archive-template">

	<!-- Archive Hero -->
	<div class="archive-hero archive-hero--services">
		<div class="container">
			<span class="section-eyebrow"><?php esc_html_e( 'What We Do', 'otu' ); ?></span>
			<h1 class="archive-hero__title"><?php esc_html_e( 'Our Services', 'otu' ); ?></h1>
			<p class="archive-hero__subtitle">
				<?php esc_html_e( 'Professional services for individuals and businesses across New York City.', 'otu' ); ?>
			</p>
		</div>
	</div>

	<div class="container">

		<!-- Filter by Category -->
		<?php
		$service_cats = get_terms( array(
			'taxonomy'   => 'service_category',
			'hide_empty' => true,
		) );

		if ( $service_cats && ! is_wp_error( $service_cats ) ) :
		?>
			<div class="archive-filter" role="navigation" aria-label="<?php esc_attr_e( 'Filter by category', 'otu' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>"
				   class="filter-btn <?php echo ( ! is_tax() ) ? 'filter-btn--active' : ''; ?>">
					<?php esc_html_e( 'All Services', 'otu' ); ?>
				</a>
				<?php foreach ( $service_cats as $cat ) : ?>
					<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
					   class="filter-btn <?php echo ( is_tax( 'service_category', $cat->term_id ) ) ? 'filter-btn--active' : ''; ?>">
						<?php echo esc_html( $cat->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<!-- Services Grid -->
		<?php if ( have_posts() ) : ?>

			<div class="services-archive-grid">
				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'service-archive-card' ); ?>>

						<a href="<?php the_permalink(); ?>" class="service-archive-card__link" tabindex="-1" aria-hidden="true">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="service-archive-card__thumb">
									<?php the_post_thumbnail( 'otu-card', array( 'loading' => 'lazy' ) ); ?>
									<div class="service-archive-card__overlay"></div>
								</div>
							<?php else : ?>
								<div class="service-archive-card__thumb service-archive-card__thumb--placeholder">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="60" height="60"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
								</div>
							<?php endif; ?>
						</a>

						<div class="service-archive-card__body">
							<?php
							$terms = get_the_terms( get_the_ID(), 'service_category' );
							if ( $terms && ! is_wp_error( $terms ) ) :
							?>
								<span class="service-archive-card__cat"><?php echo esc_html( $terms[0]->name ); ?></span>
							<?php endif; ?>

							<h2 class="service-archive-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<div class="service-archive-card__excerpt">
								<?php the_excerpt(); ?>
							</div>

							<div class="service-archive-card__actions">
								<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
									<?php esc_html_e( 'Learn More', 'otu' ); ?>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
								</a>
								<a href="<?php echo esc_url( home_url( '/book-appointment' ) ); ?>" class="btn btn-secondary btn-sm">
									<?php esc_html_e( 'Book Now', 'otu' ); ?>
								</a>
							</div>
						</div>

					</article>

				<?php endwhile; ?>
			</div><!-- .services-archive-grid -->

			<?php otu_pagination(); ?>

		<?php else : ?>

			<div class="no-results">
				<h2><?php esc_html_e( 'No Services Found', 'otu' ); ?></h2>
				<p>
					<?php esc_html_e( "We couldn't find any services matching your criteria. Please check back soon or contact us directly.", 'otu' ); ?>
				</p>
				<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-primary">
					<?php esc_html_e( 'Contact Us', 'otu' ); ?>
				</a>
			</div>

		<?php endif; ?>

	</div><!-- .container -->

	<!-- CTA Bar -->
	<div class="archive-cta-bar">
		<div class="container archive-cta-bar__inner">
			<p><?php esc_html_e( "Don't see what you're looking for? We offer custom solutions.", 'otu' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-gold">
				<?php esc_html_e( 'Contact Us', 'otu' ); ?>
			</a>
		</div>
	</div>

</main>

<?php get_footer(); ?>
