<?php
/**
 * Single post template.
 *
 * @package one-ten-united
 */

get_header();
?>

<main id="main-content" class="site-main single-post-template">
	<div class="container">
		<div class="content-area <?php echo is_active_sidebar( 'sidebar' ) ? 'has-sidebar' : 'full-width'; ?>">

			<div class="main-content">
				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>

						<!-- Post Header -->
						<header class="single-post__header">
							<?php
							$categories = get_the_category();
							if ( $categories ) :
							?>
								<div class="single-post__cats">
									<?php foreach ( $categories as $cat ) : ?>
										<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="post-cat-badge">
											<?php echo esc_html( $cat->name ); ?>
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<h1 class="single-post__title"><?php the_title(); ?></h1>

							<div class="single-post__meta">
								<span class="single-post__author">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
									<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
										<?php echo esc_html( get_the_author() ); ?>
									</a>
								</span>
								<span class="single-post__date">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
										<?php echo esc_html( get_the_date() ); ?>
									</time>
								</span>
								<?php if ( get_the_modified_date() !== get_the_date() ) : ?>
									<span class="single-post__updated">
										<?php
										echo esc_html__( 'Updated: ', 'otu' );
										echo esc_html( get_the_modified_date() );
										?>
									</span>
								<?php endif; ?>
								<span class="single-post__readtime">
									<?php
									$content    = get_the_content();
									$word_count = str_word_count( wp_strip_all_tags( $content ) );
									$read_time  = max( 1, (int) round( $word_count / 200 ) );
									/* translators: %d: estimated reading minutes */
									echo esc_html( sprintf( _n( '%d min read', '%d min read', $read_time, 'otu' ), $read_time ) );
									?>
								</span>
							</div>
						</header>

						<!-- Featured Image -->
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="single-post__thumb">
								<?php the_post_thumbnail( 'full', array( 'loading' => 'lazy', 'class' => 'single-post__img' ) ); ?>
							</div>
						<?php endif; ?>

						<!-- Post Content -->
						<div class="single-post__content entry-content">
							<?php
							the_content(
								sprintf(
									wp_kses(
										/* translators: %s: Name of current post. */
										__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'otu' ),
										array( 'span' => array( 'class' => array() ) )
									),
									wp_kses_post( get_the_title() )
								)
							);

							wp_link_pages( array(
								'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'otu' ),
								'after'       => '</div>',
								'link_before' => '<span class="page-link">',
								'link_after'  => '</span>',
							) );
							?>
						</div>

						<!-- Tags -->
						<?php
						$tags = get_the_tags();
						if ( $tags ) :
						?>
							<div class="single-post__tags">
								<span class="tags-label"><?php esc_html_e( 'Tags:', 'otu' ); ?></span>
								<?php foreach ( $tags as $tag ) : ?>
									<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="tag-pill">
										<?php echo esc_html( $tag->name ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<!-- Post Navigation -->
						<nav class="post-navigation" aria-label="<?php esc_attr_e( 'Post navigation', 'otu' ); ?>">
							<div class="post-nav-prev">
								<?php
								$prev_post = get_previous_post();
								if ( $prev_post ) :
								?>
									<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" class="post-nav__link">
										<span class="post-nav__label">
											<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
											<?php esc_html_e( 'Previous Post', 'otu' ); ?>
										</span>
										<span class="post-nav__title"><?php echo esc_html( $prev_post->post_title ); ?></span>
									</a>
								<?php endif; ?>
							</div>
							<div class="post-nav-next">
								<?php
								$next_post = get_next_post();
								if ( $next_post ) :
								?>
									<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="post-nav__link post-nav__link--next">
										<span class="post-nav__label">
											<?php esc_html_e( 'Next Post', 'otu' ); ?>
											<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
										</span>
										<span class="post-nav__title"><?php echo esc_html( $next_post->post_title ); ?></span>
									</a>
								<?php endif; ?>
							</div>
						</nav>

						<!-- Author Box -->
						<div class="author-box">
							<div class="author-box__avatar">
								<?php echo get_avatar( get_the_author_meta( 'email' ), 80, '', '', array( 'class' => 'author-box__img' ) ); ?>
							</div>
							<div class="author-box__info">
								<h3 class="author-box__name">
									<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
										<?php echo esc_html( get_the_author() ); ?>
									</a>
								</h3>
								<?php
								$author_bio = get_the_author_meta( 'description' );
								if ( $author_bio ) :
								?>
									<p class="author-box__bio"><?php echo esc_html( $author_bio ); ?></p>
								<?php endif; ?>
								<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="btn btn-secondary btn-sm">
									<?php esc_html_e( 'View All Posts', 'otu' ); ?>
								</a>
							</div>
						</div>

					</article>

					<!-- Related Posts -->
					<?php
					$categories = get_the_category( get_the_ID() );
					if ( $categories ) :
						$cat_ids = wp_list_pluck( $categories, 'term_id' );
						$related_query = new WP_Query( array(
							'category__in'        => $cat_ids,
							'post__not_in'        => array( get_the_ID() ),
							'posts_per_page'      => 3,
							'no_found_rows'       => true,
							'ignore_sticky_posts' => true,
						) );
						if ( $related_query->have_posts() ) :
					?>
						<section class="related-posts" aria-label="<?php esc_attr_e( 'Related posts', 'otu' ); ?>">
							<h2 class="related-posts__title"><?php esc_html_e( 'Related Posts', 'otu' ); ?></h2>
							<div class="related-posts__grid">
								<?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
									<article class="post-card post-card--small">
										<?php if ( has_post_thumbnail() ) : ?>
											<a href="<?php the_permalink(); ?>" class="post-card__thumb" tabindex="-1">
												<?php the_post_thumbnail( 'otu-card', array( 'loading' => 'lazy' ) ); ?>
											</a>
										<?php endif; ?>
										<div class="post-card__body">
											<h3 class="post-card__title">
												<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											</h3>
											<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
												<?php echo esc_html( get_the_date() ); ?>
											</time>
										</div>
									</article>
								<?php endwhile; wp_reset_postdata(); ?>
							</div>
						</section>
					<?php endif; endif; ?>

					<!-- Comments -->
					<?php
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>

				<?php endwhile; ?>
			</div><!-- .main-content -->

			<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>

		</div><!-- .content-area -->
	</div><!-- .container -->
</main>

<?php get_footer(); ?>
