<?php
/**
 * Main index/blog loop template.
 *
 * @package one-ten-united
 */

get_header();
?>

<main id="main-content" class="site-main blog-archive">
	<div class="container">
		<div class="content-area <?php echo is_active_sidebar( 'sidebar' ) ? 'has-sidebar' : 'full-width'; ?>">

			<div class="posts-area">
				<?php if ( is_home() && ! is_front_page() ) : ?>
					<header class="archive-header">
						<h1 class="archive-title"><?php esc_html_e( 'Latest Posts', 'otu' ); ?></h1>
					</header>
				<?php endif; ?>

				<?php if ( have_posts() ) : ?>

					<div class="posts-grid">
						<?php while ( have_posts() ) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>

								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php the_permalink(); ?>" class="post-card__thumb" tabindex="-1" aria-hidden="true">
										<?php the_post_thumbnail( 'otu-card', array( 'loading' => 'lazy' ) ); ?>
									</a>
								<?php endif; ?>

								<div class="post-card__body">
									<div class="post-card__meta">
										<span class="post-card__cat">
											<?php
											$categories = get_the_category();
											if ( $categories ) {
												echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
											}
											?>
										</span>
										<span class="post-card__date">
											<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
												<?php echo esc_html( get_the_date() ); ?>
											</time>
										</span>
									</div>

									<h2 class="post-card__title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h2>

									<div class="post-card__excerpt">
										<?php the_excerpt(); ?>
									</div>

									<a href="<?php the_permalink(); ?>" class="btn btn-secondary post-card__more">
										<?php esc_html_e( 'Read More', 'otu' ); ?>
										<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
									</a>
								</div>

							</article>

						<?php endwhile; ?>
					</div>

					<?php otu_pagination(); ?>

				<?php else : ?>

					<div class="no-results">
						<h2><?php esc_html_e( 'Nothing Found', 'otu' ); ?></h2>
						<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'otu' ); ?></p>
						<?php get_search_form(); ?>
					</div>

				<?php endif; ?>
			</div><!-- .posts-area -->

			<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>

		</div><!-- .content-area -->
	</div><!-- .container -->
</main>

<?php get_footer(); ?>
