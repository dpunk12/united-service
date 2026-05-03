<?php
/**
 * Theme footer template.
 *
 * @package one-ten-united
 */
?>

<!-- ======================================================
     SITE FOOTER
     ====================================================== -->
<footer id="site-footer" class="site-footer" role="contentinfo">

	<!-- Widget Columns -->
	<div class="footer-widgets">
		<div class="container footer-widgets__inner">

			<!-- Footer Column 1 -->
			<div class="footer-col footer-col--1">
				<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
					<?php dynamic_sidebar( 'footer-1' ); ?>
				<?php else : ?>
					<div class="footer-brand">
						<?php otu_get_logo(); ?>
						<p class="footer-brand__tagline">
							<?php esc_html_e( 'Your Trusted NYC Partner for Business, Immigration & More.', 'otu' ); ?>
						</p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Footer Column 2 -->
			<div class="footer-col footer-col--2">
				<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
					<?php dynamic_sidebar( 'footer-2' ); ?>
				<?php else : ?>
					<h3 class="footer-col__title"><?php esc_html_e( 'Quick Links', 'otu' ); ?></h3>
					<ul class="footer-links">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'otu' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/services' ) ); ?>"><?php esc_html_e( 'Services', 'otu' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/courses' ) ); ?>"><?php esc_html_e( 'Courses', 'otu' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/shop' ) ); ?>"><?php esc_html_e( 'Shop', 'otu' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>"><?php esc_html_e( 'About Us', 'otu' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact', 'otu' ); ?></a></li>
					</ul>
				<?php endif; ?>
			</div>

			<!-- Footer Column 3 -->
			<div class="footer-col footer-col--3">
				<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
					<?php dynamic_sidebar( 'footer-3' ); ?>
				<?php else : ?>
					<h3 class="footer-col__title"><?php esc_html_e( 'Contact Us', 'otu' ); ?></h3>
					<address class="footer-contact">
						<p>
							<strong><?php esc_html_e( 'Phone:', 'otu' ); ?></strong>
							<a href="tel:+17185550100">(718) 555-0100</a>
						</p>
						<p>
							<strong><?php esc_html_e( 'Email:', 'otu' ); ?></strong>
							<a href="mailto:info@onetenunited.com">info@onetenunited.com</a>
						</p>
						<p>
							<strong><?php esc_html_e( 'Hours:', 'otu' ); ?></strong><br>
							<?php esc_html_e( 'Mon – Fri: 9am – 6pm', 'otu' ); ?><br>
							<?php esc_html_e( 'Sat: 10am – 4pm', 'otu' ); ?>
						</p>
					</address>

					<!-- Social links -->
					<ul class="footer-social" aria-label="<?php esc_attr_e( 'Social media', 'otu' ); ?>">
						<li>
							<a href="https://facebook.com/onetenunited" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Facebook', 'otu' ); ?>">
								<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="20" height="20"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
							</a>
						</li>
						<li>
							<a href="https://instagram.com/onetenunited" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Instagram', 'otu' ); ?>">
								<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="20" height="20"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" fill="none" stroke="currentColor" stroke-width="2"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="currentColor" stroke-width="2"/></svg>
							</a>
						</li>
						<li>
							<a href="https://twitter.com/onetenunited" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Twitter / X', 'otu' ); ?>">
								<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="20" height="20"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
							</a>
						</li>
						<li>
							<a href="https://wa.me/17185550100" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'WhatsApp', 'otu' ); ?>">
								<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
							</a>
						</li>
					</ul>
				<?php endif; ?>
			</div>

		</div>
	</div>

	<!-- Bottom Bar -->
	<div class="footer-bottom">
		<div class="container footer-bottom__inner">
			<p class="footer-bottom__copy">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<?php esc_html_e( 'One Ten United Services. All rights reserved.', 'otu' ); ?>
			</p>

			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'menu_id'        => 'footer-menu',
				'menu_class'     => 'footer-menu',
				'container'      => 'nav',
				'container_attr' => array( 'aria-label' => esc_attr__( 'Footer menu', 'otu' ) ),
				'depth'          => 1,
				'fallback_cb'    => false,
			) );
			?>

			<a href="#" class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'otu' ); ?>" id="back-to-top">
				<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><path d="M18 15l-6-6-6 6"/></svg>
				<?php esc_html_e( 'Top', 'otu' ); ?>
			</a>
		</div>
	</div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
