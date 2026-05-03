<?php
/**
 * Single service post template.
 *
 * @package one-ten-united
 */

get_header();
?>

<main id="main-content" class="site-main single-service-template">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-service' ); ?>>

				<!-- Service Hero -->
				<div class="service-hero">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="service-hero__image">
							<?php the_post_thumbnail( 'otu-hero', array( 'loading' => 'lazy', 'class' => 'service-hero__img' ) ); ?>
							<div class="service-hero__overlay"></div>
						</div>
					<?php endif; ?>

					<div class="service-hero__content">
						<?php
						$terms = get_the_terms( get_the_ID(), 'service_category' );
						if ( $terms && ! is_wp_error( $terms ) ) :
						?>
							<div class="service-hero__cats">
								<?php foreach ( $terms as $term ) : ?>
									<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="service-cat-badge">
										<?php echo esc_html( $term->name ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<h1 class="service-hero__title"><?php the_title(); ?></h1>

						<?php if ( has_excerpt() ) : ?>
							<p class="service-hero__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>

						<!-- Primary Action Buttons -->
						<div class="service-hero__actions">
							<?php
							// Check if product is linked via post meta.
							$product_id = get_post_meta( get_the_ID(), '_linked_product_id', true );
							if ( $product_id && function_exists( 'wc_get_product' ) ) :
								$product = wc_get_product( $product_id );
								if ( $product ) :
									$price = $product->get_price_html();
							?>
								<a
									href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
									data-product-id="<?php echo esc_attr( $product_id ); ?>"
									class="btn btn-gold service-hero__btn ajax_add_to_cart add_to_cart_button"
									rel="nofollow"
								>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/></svg>
									<?php
									echo wp_kses_post(
										sprintf(
											/* translators: %s: product price HTML */
											__( 'Add to Cart %s', 'otu' ),
											$price
										)
									);
									?>
								</a>
							<?php
								endif;
							else :
							?>
								<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-gold service-hero__btn">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/></svg>
									<?php esc_html_e( 'Buy This Service', 'otu' ); ?>
								</a>
							<?php endif; ?>

							<a href="<?php echo esc_url( home_url( '/book-appointment' ) ); ?>" class="btn btn-primary service-hero__btn">
								<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
								<?php esc_html_e( 'Book Appointment', 'otu' ); ?>
							</a>
						</div>
					</div>
				</div>

				<!-- Service Content -->
				<div class="service-content-wrap">
					<div class="service-content">

						<div class="service-body entry-content">
							<?php the_content(); ?>
						</div>

						<!-- Service Meta -->
						<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
							<div class="service-meta">
								<h3 class="service-meta__label"><?php esc_html_e( 'Service Category', 'otu' ); ?></h3>
								<ul class="service-meta__terms">
									<?php foreach ( $terms as $term ) : ?>
										<li>
											<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
												<?php echo esc_html( $term->name ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<!-- Bottom CTA -->
						<div class="service-cta-box">
							<h3 class="service-cta-box__title">
								<?php
								/* translators: %s: service title */
								echo esc_html( sprintf( __( 'Ready to get %s?', 'otu' ), get_the_title() ) );
								?>
							</h3>
							<p><?php esc_html_e( 'Contact our team today or book an appointment to discuss your needs.', 'otu' ); ?></p>
							<div class="service-cta-box__actions">
								<a href="tel:+17185550100" class="btn btn-primary">
									<?php esc_html_e( 'Call (718) 555-0100', 'otu' ); ?>
								</a>
								<a href="<?php echo esc_url( home_url( '/book-appointment' ) ); ?>" class="btn btn-secondary">
									<?php esc_html_e( 'Book Appointment', 'otu' ); ?>
								</a>
							</div>
						</div>

					</div><!-- .service-content -->

					<!-- Service Sidebar -->
					<aside class="service-sidebar">
						<div class="service-sidebar__card">
							<h3><?php esc_html_e( 'Why Choose Us', 'otu' ); ?></h3>
							<ul class="service-sidebar__features">
								<li>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( 'Licensed Professionals', 'otu' ); ?>
								</li>
								<li>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( 'Fast 24–72 Hour Turnaround', 'otu' ); ?>
								</li>
								<li>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( '100% Confidential', 'otu' ); ?>
								</li>
								<li>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( 'NYC Based – All Boroughs', 'otu' ); ?>
								</li>
							</ul>

							<div class="service-sidebar__contact">
								<a href="tel:+17185550100" class="btn btn-primary btn--full">
									<?php esc_html_e( 'Call Now', 'otu' ); ?>
								</a>
								<a href="https://wa.me/17185550100" class="btn btn-gold btn--full" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'WhatsApp Us', 'otu' ); ?>
								</a>
							</div>
						</div>

						<!-- Other Services -->
						<div class="service-sidebar__card service-sidebar__card--other">
							<h3><?php esc_html_e( 'Other Services', 'otu' ); ?></h3>
							<?php
							$other_services = new WP_Query( array(
								'post_type'           => 'service',
								'posts_per_page'      => 5,
								'post__not_in'        => array( get_the_ID() ),
								'no_found_rows'       => true,
								'ignore_sticky_posts' => true,
								'orderby'             => 'rand',
							) );
							if ( $other_services->have_posts() ) :
							?>
								<ul class="service-sidebar__list">
									<?php while ( $other_services->have_posts() ) : $other_services->the_post(); ?>
										<li>
											<a href="<?php the_permalink(); ?>">
												<?php the_title(); ?>
												<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
											</a>
										</li>
									<?php endwhile; wp_reset_postdata(); ?>
								</ul>
							<?php endif; ?>
						</div>
					</aside>

				</div><!-- .service-content-wrap -->

			</article>

			<!-- Related Services -->
			<?php
			$related_terms = get_the_terms( get_the_ID(), 'service_category' );
			if ( $related_terms && ! is_wp_error( $related_terms ) ) :
				$term_ids = wp_list_pluck( $related_terms, 'term_id' );
				$related_services = new WP_Query( array(
					'post_type'           => 'service',
					'posts_per_page'      => 3,
					'post__not_in'        => array( get_the_ID() ),
					'no_found_rows'       => true,
					'ignore_sticky_posts' => true,
					'tax_query'           => array(
						array(
							'taxonomy' => 'service_category',
							'field'    => 'term_id',
							'terms'    => $term_ids,
						),
					),
				) );
				if ( $related_services->have_posts() ) :
			?>
				<section class="related-services" aria-label="<?php esc_attr_e( 'Related services', 'otu' ); ?>">
					<div class="container">
						<h2 class="related-services__title"><?php esc_html_e( 'Related Services', 'otu' ); ?></h2>
						<div class="services-grid services-grid--related">
							<?php while ( $related_services->have_posts() ) : $related_services->the_post(); ?>
								<a href="<?php the_permalink(); ?>" class="service-card service-card--compact">
									<?php if ( has_post_thumbnail() ) : ?>
										<div class="service-card__img">
											<?php the_post_thumbnail( 'otu-card', array( 'loading' => 'lazy' ) ); ?>
										</div>
									<?php endif; ?>
									<div class="service-card__body">
										<h3 class="service-card__title"><?php the_title(); ?></h3>
										<p class="service-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
										<span class="service-card__link"><?php esc_html_e( 'Learn More', 'otu' ); ?> &rarr;</span>
									</div>
								</a>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</div>
				</section>
			<?php endif; endif; ?>

		<?php endwhile; ?>
	</div><!-- .container -->
</main>

<?php get_footer(); ?>
