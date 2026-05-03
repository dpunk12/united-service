<?php
/**
 * LMS Integration for OTU Services.
 *
 * @package OTU_Services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OTU_LMS
 *
 * Handles Tutor LMS integration including course scaffolding and certificate triggers.
 */
class OTU_LMS {

	/**
	 * Courses to scaffold on activation.
	 *
	 * @var array
	 */
	private $scaffold_courses = array(
		array(
			'title'       => 'Notary Exam Course',
			'description' => 'This comprehensive course prepares you for your state notary public examination. Learn the laws, responsibilities, and best practices required to become a certified notary. Upon completion, you will receive an OTU Certificate of Completion.',
			'lessons'     => array(
				'Introduction to Notary Law and Responsibilities',
				'Notarization Procedures and Document Types',
				'State-Specific Requirements and Exam Preparation',
			),
		),
		array(
			'title'       => 'CAA Forensic Document Training',
			'description' => 'This professional training course covers the principles of forensic document examination as they apply to Certified Acceptance Agents (CAA). Learn to identify document fraud, understand authentication processes, and meet CAA compliance requirements. OTU Certificate of Completion awarded upon passing.',
			'lessons'     => array(
				'Fundamentals of Forensic Document Examination',
				'Identity Document Authentication Techniques',
				'CAA Compliance, Ethics, and Reporting Standards',
			),
		),
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! function_exists( 'tutor' ) ) {
			add_action( 'admin_notices', array( $this, 'tutor_missing_notice' ) );
		}

		add_action( 'tutor_course_complete_after', array( $this, 'on_course_complete' ), 10, 1 );
		add_filter( 'tutor_lesson_content_after', array( $this, 'add_progress_indicator' ), 10, 1 );
	}

	/**
	 * Show an admin notice if Tutor LMS is not active.
	 */
	public function tutor_missing_notice() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: Tutor LMS plugin link */
						__( '<strong>OTU Services LMS features</strong> require %s to be installed and active.', 'otu' ),
						'<a href="' . esc_url( admin_url( 'plugin-install.php?s=tutor+lms&tab=search&type=term' ) ) . '">Tutor LMS</a>'
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Scaffold default courses on plugin activation.
	 * Called from OTU_Setup::activate().
	 */
	public static function scaffold_courses() {
		if ( ! function_exists( 'tutor' ) ) {
			return;
		}

		$instance = new self();
		foreach ( $instance->scaffold_courses as $course_data ) {
			$existing = get_page_by_title( $course_data['title'], OBJECT, 'courses' );
			if ( $existing ) {
				continue;
			}

			$course_id = wp_insert_post(
				array(
					'post_type'    => 'courses',
					'post_title'   => $course_data['title'],
					'post_content' => $course_data['description'],
					'post_status'  => 'publish',
				)
			);

			if ( is_wp_error( $course_id ) || ! $course_id ) {
				continue;
			}

			$topic_id = wp_insert_post(
				array(
					'post_type'   => 'topics',
					'post_title'  => __( 'Course Content', 'otu' ),
					'post_status' => 'publish',
					'post_parent' => $course_id,
				)
			);

			if ( is_wp_error( $topic_id ) || ! $topic_id ) {
				continue;
			}

			$order = 0;
			foreach ( $course_data['lessons'] as $lesson_title ) {
				wp_insert_post(
					array(
						'post_type'    => 'lesson',
						'post_title'   => $lesson_title,
						'post_content' => sprintf(
							/* translators: %s: lesson title */
							__( 'Content for lesson: %s. Please update this lesson with detailed course content.', 'otu' ),
							$lesson_title
						),
						'post_status'  => 'publish',
						'post_parent'  => $topic_id,
						'menu_order'   => $order++,
					)
				);
			}
		}
	}

	/**
	 * Fired after a Tutor LMS course is completed.
	 *
	 * @param int $course_id Course ID.
	 */
	public function on_course_complete( $course_id ) {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		if ( class_exists( 'OTU_Certificate' ) ) {
			OTU_Certificate::generate( $user_id, $course_id );
		}
	}

	/**
	 * Add a progress indicator after lesson content.
	 *
	 * @param string $content Lesson content.
	 * @return string
	 */
	public function add_progress_indicator( $content ) {
		if ( ! function_exists( 'tutor' ) ) {
			return $content;
		}

		$user_id   = get_current_user_id();
		$course_id = tutor_utils()->get_course_id_by_lesson();

		if ( ! $user_id || ! $course_id ) {
			return $content;
		}

		$progress    = tutor_utils()->get_course_completed_percent( $course_id, $user_id );
		$progress    = absint( $progress );
		$progress_ui = '<div class="otu-lesson-progress">';
		$progress_ui .= '<p class="otu-progress-label">' . esc_html__( 'Course Progress', 'otu' ) . '</p>';
		$progress_ui .= '<div class="otu-progress-bar-wrap">';
		$progress_ui .= '<div class="otu-progress-bar" style="width:' . esc_attr( $progress ) . '%;" data-progress="' . esc_attr( $progress ) . '"></div>';
		$progress_ui .= '</div>';
		$progress_ui .= '<p class="otu-progress-percent">' . esc_html( $progress ) . '% ' . esc_html__( 'Complete', 'otu' ) . '</p>';
		$progress_ui .= '</div>';

		return $content . $progress_ui;
	}

	/**
	 * Get enrolled courses for a user with progress.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array Array of ['course_id' => int, 'progress' => int]
	 */
	public static function otu_get_user_courses( $user_id ) {
		$user_id = absint( $user_id );
		$courses = array();

		if ( ! function_exists( 'tutor_utils' ) ) {
			return $courses;
		}

		$enrolled = tutor_utils()->get_enrolled_courses_by_user( $user_id );

		if ( ! $enrolled || ! $enrolled->have_posts() ) {
			return $courses;
		}

		while ( $enrolled->have_posts() ) {
			$enrolled->the_post();
			$course_id = get_the_ID();
			$progress  = tutor_utils()->get_course_completed_percent( $course_id, $user_id );
			$courses[] = array(
				'course_id'   => $course_id,
				'title'       => get_the_title(),
				'progress'    => absint( $progress ),
				'permalink'   => get_permalink( $course_id ),
				'thumbnail'   => get_the_post_thumbnail_url( $course_id, 'thumbnail' ),
			);
		}
		wp_reset_postdata();

		return $courses;
	}
}
