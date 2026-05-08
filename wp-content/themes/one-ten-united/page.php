<?php
/**
 * Standard page template.
 *
 * @package one-ten-united
 */

get_header();
?>

<main id="main-content" class="site-main page-template">
	<div class="container">
		<div class="content-area <?php echo is_active_sidebar( 'sidebar' ) ? 'has-sidebar' : 'full-width'; ?>">

			<div class="main-content">
				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>

						<header class="page-header">
							<h1 class="page-title"><?php the_title(); ?></h1>
						</header>

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="page-featured-image">
								<?php the_post_thumbnail( 'full', array( 'loading' => 'lazy' ) ); ?>
							</div>
						<?php endif; ?>

						<div class="page-body entry-content">
							<?php
							the_content();

							wp_link_pages( array(
								'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'otu' ),
								'after'       => '</div>',
								'link_before' => '<span class="page-link">',
								'link_after'  => '</span>',
							) );
							?>
						</div>

					</article>

					<?php
					// If comments are open or there is at least one comment, load up the comment template.
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
