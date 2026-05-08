<?php
/**
 * Single course template (Tutor LMS compatible).
 *
 * @package one-ten-united
 */

get_header();
?>

<main id="main-content" class="site-main single-course-template">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-course' ); ?>>

				<!-- Course Hero -->
				<div class="course-hero">
					<div class="course-hero__content">

						<!-- Course breadcrumb category -->
						<?php
						$course_cats = get_the_terms( get_the_ID(), 'course-category' );
						if ( ! $course_cats ) {
							$course_cats = get_the_terms( get_the_ID(), 'course_category' );
						}
						if ( $course_cats && ! is_wp_error( $course_cats ) ) :
						?>
							<div class="course-hero__cats">
								<?php foreach ( $course_cats as $cat ) : ?>
									<span class="course-cat-badge"><?php echo esc_html( $cat->name ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<h1 class="course-hero__title"><?php the_title(); ?></h1>

						<?php if ( has_excerpt() ) : ?>
							<p class="course-hero__desc"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>

						<!-- Course Meta -->
						<div class="course-meta">
							<?php
							$duration     = get_post_meta( get_the_ID(), '_tutor_course_duration', true );
							$total_lessons = get_post_meta( get_the_ID(), '_tutor_course_total_lesson', true );
							$difficulty   = get_post_meta( get_the_ID(), '_tutor_course_level', true );
							$language     = get_post_meta( get_the_ID(), '_tutor_course_language', true );
							?>
							<?php if ( $duration ) : ?>
								<span class="course-meta__item">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
									<?php echo esc_html( $duration ); ?>
								</span>
							<?php endif; ?>
							<?php if ( $total_lessons ) : ?>
								<span class="course-meta__item">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
									<?php
									/* translators: %s: number of lessons */
									echo esc_html( sprintf( __( '%s Lessons', 'otu' ), $total_lessons ) );
									?>
								</span>
							<?php endif; ?>
							<?php if ( $difficulty ) : ?>
								<span class="course-meta__item">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
									<?php echo esc_html( ucfirst( $difficulty ) ); ?>
								</span>
							<?php endif; ?>
							<?php if ( $language ) : ?>
								<span class="course-meta__item">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
									<?php echo esc_html( $language ); ?>
								</span>
							<?php endif; ?>
						</div>

						<!-- Instructor -->
						<div class="course-instructor">
							<?php
							$instructor_id = get_post_field( 'post_author', get_the_ID() );
							if ( $instructor_id ) :
								$instructor_avatar = get_avatar( $instructor_id, 40, '', '', array( 'class' => 'course-instructor__avatar' ) );
								$instructor_name   = get_the_author_meta( 'display_name', $instructor_id );
								$instructor_bio    = get_the_author_meta( 'description', $instructor_id );
							?>
								<?php echo $instructor_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() already escapes. ?>
								<div class="course-instructor__info">
									<span class="course-instructor__label"><?php esc_html_e( 'Instructor', 'otu' ); ?></span>
									<span class="course-instructor__name"><?php echo esc_html( $instructor_name ); ?></span>
								</div>
							<?php endif; ?>
						</div>

						<!-- Enroll Button -->
						<div class="course-hero__actions">
							<?php
							// Tutor LMS enroll button if available.
							if ( function_exists( 'tutor_course_enroll_btn' ) ) :
								tutor_course_enroll_btn();
							else :
							?>
								<a href="<?php the_permalink(); ?>#enroll" class="btn btn-gold course-enroll-btn">
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( 'Enroll in This Course', 'otu' ); ?>
								</a>
							<?php endif; ?>

							<?php
							$price = get_post_meta( get_the_ID(), '_tutor_course_price', true );
							if ( $price ) :
							?>
								<span class="course-price">
									<?php
									/* translators: %s: course price */
									echo esc_html( sprintf( __( 'Price: $%s', 'otu' ), $price ) );
									?>
								</span>
							<?php elseif ( function_exists( 'tutor_utils' ) ) : ?>
								<span class="course-price course-price--free"><?php esc_html_e( 'Free', 'otu' ); ?></span>
							<?php endif; ?>
						</div>

					</div><!-- .course-hero__content -->

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="course-hero__image">
							<?php the_post_thumbnail( 'otu-hero', array( 'loading' => 'eager', 'class' => 'course-hero__img' ) ); ?>
						</div>
					<?php endif; ?>
				</div><!-- .course-hero -->

				<!-- Course Body -->
				<div class="course-body-wrap">
					<div class="course-body">

						<!-- Description -->
						<div class="course-description entry-content">
							<h2><?php esc_html_e( 'About This Course', 'otu' ); ?></h2>
							<?php the_content(); ?>
						</div>

						<!-- What You'll Learn -->
						<?php
						$learn_items = get_post_meta( get_the_ID(), '_tutor_course_benefits', true );
						if ( $learn_items ) :
							$items = explode( "\n", wp_strip_all_tags( $learn_items ) );
						?>
							<div class="course-outcomes">
								<h2><?php esc_html_e( "What You'll Learn", 'otu' ); ?></h2>
								<ul class="course-outcomes__list">
									<?php foreach ( $items as $item ) : ?>
										<?php if ( trim( $item ) ) : ?>
											<li>
												<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
												<?php echo esc_html( trim( $item ) ); ?>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<!-- Requirements -->
						<?php
						$requirements = get_post_meta( get_the_ID(), '_tutor_course_requirements', true );
						if ( $requirements ) :
							$req_items = explode( "\n", wp_strip_all_tags( $requirements ) );
						?>
							<div class="course-requirements">
								<h2><?php esc_html_e( 'Requirements', 'otu' ); ?></h2>
								<ul class="course-requirements__list">
									<?php foreach ( $req_items as $req ) : ?>
										<?php if ( trim( $req ) ) : ?>
											<li><?php echo esc_html( trim( $req ) ); ?></li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<!-- Tutor LMS Course Curriculum if available -->
						<?php
						if ( function_exists( 'tutor_course_topics' ) ) {
							tutor_course_topics();
						}
						?>

					</div><!-- .course-body -->

					<!-- Instructor Detail Sidebar -->
					<aside class="course-sidebar">
						<?php
						$instructor_id = get_post_field( 'post_author', get_the_ID() );
						if ( $instructor_id ) :
							$instructor_name   = get_the_author_meta( 'display_name', $instructor_id );
							$instructor_bio    = get_the_author_meta( 'description', $instructor_id );
							$instructor_avatar = get_avatar( $instructor_id, 80, '', '', array( 'class' => 'instructor-card__avatar' ) );
						?>
							<div class="instructor-card">
								<h3 class="instructor-card__heading"><?php esc_html_e( 'Your Instructor', 'otu' ); ?></h3>
								<div class="instructor-card__profile">
									<?php echo $instructor_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<div>
										<strong class="instructor-card__name"><?php echo esc_html( $instructor_name ); ?></strong>
									</div>
								</div>
								<?php if ( $instructor_bio ) : ?>
									<p class="instructor-card__bio"><?php echo esc_html( $instructor_bio ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<!-- Enroll CTA -->
						<div class="course-sidebar__cta">
							<h3><?php esc_html_e( 'Start Learning Today', 'otu' ); ?></h3>
							<?php if ( function_exists( 'tutor_course_enroll_btn' ) ) : ?>
								<?php tutor_course_enroll_btn(); ?>
							<?php else : ?>
								<a href="<?php the_permalink(); ?>#enroll" class="btn btn-gold btn--full">
									<?php esc_html_e( 'Enroll Now', 'otu' ); ?>
								</a>
							<?php endif; ?>

							<ul class="course-sidebar__includes">
								<li>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( 'Lifetime access', 'otu' ); ?>
								</li>
								<li>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( 'Certificate of completion', 'otu' ); ?>
								</li>
								<li>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( 'Expert-led instruction', 'otu' ); ?>
								</li>
								<li>
									<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="16" height="16"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<?php esc_html_e( 'Access on all devices', 'otu' ); ?>
								</li>
							</ul>
						</div>
					</aside>

				</div><!-- .course-body-wrap -->

			</article>

		<?php endwhile; ?>
	</div><!-- .container -->
</main>

<?php get_footer(); ?>
