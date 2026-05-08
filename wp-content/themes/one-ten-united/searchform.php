<?php
/**
 * Custom search form template.
 *
 * @package one-ten-united
 */
$unique_id = esc_attr( uniqid( 'search-form-' ) );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo $unique_id; ?>">
		<?php esc_html_e( 'Search for:', 'otu' ); ?>
	</label>
	<div class="search-form__inner">
		<input
			type="search"
			id="<?php echo $unique_id; ?>"
			class="search-form__input"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'Search&hellip;', 'otu' ); ?>"
			autocomplete="off"
			aria-label="<?php esc_attr_e( 'Search query', 'otu' ); ?>"
		>
		<button type="submit" class="search-form__submit btn btn-primary" aria-label="<?php esc_attr_e( 'Submit search', 'otu' ); ?>">
			<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="18" height="18"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
			<span class="search-form__btn-text"><?php esc_html_e( 'Search', 'otu' ); ?></span>
		</button>
	</div>
</form>
