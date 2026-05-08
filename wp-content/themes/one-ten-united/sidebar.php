<?php
/**
 * Sidebar template.
 *
 * @package one-ten-united
 */

if ( ! is_active_sidebar( 'sidebar' ) ) {
	return;
}
?>

<aside id="sidebar" class="sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Blog Sidebar', 'otu' ); ?>">
	<?php dynamic_sidebar( 'sidebar' ); ?>
</aside>
