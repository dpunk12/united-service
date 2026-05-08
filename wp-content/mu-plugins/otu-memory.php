<?php
/**
 * Plugin Name: OTU Memory Limit
 * Description: Raises PHP memory_limit early (before regular plugins load) so
 *              memory-hungry modules like Jetpack's plugin-search and
 *              WooCommerce do not exhaust the default 128M limit and crash
 *              with HTTP 500.
 * Version:     1.1.0
 * Author:      One Ten United Services
 * License:     GPL-2.0-or-later
 *
 * Drop-in must-use plugin. WordPress loads files in wp-content/mu-plugins/
 * before any regular plugin, so this runs before Jetpack, WooCommerce, etc.
 *
 * IMPORTANT — host limitations:
 * `ini_set( 'memory_limit', ... )` only succeeds when the host has registered
 * `memory_limit` as PHP_INI_ALL (i.e. not locked via `php_admin_value` /
 * PHP_INI_SYSTEM) and has not disabled `ini_set` via `disable_functions` or
 * Suhosin. If the host has locked it, this file is a silent no-op and the
 * fatal `Allowed memory size of 134217728 bytes exhausted` will keep firing.
 *
 * In that case the only remaining fixes are host-side and outside this repo:
 *   - cPanel → "Select PHP Version" → Options → set `memory_limit = 512M`.
 *   - cPanel → "MultiPHP INI Editor" → set `memory_limit = 512M`.
 *   - Add `define( 'WP_MEMORY_LIMIT', '512M' );` and
 *     `define( 'WP_MAX_MEMORY_LIMIT', '512M' );` to `wp-config.php`
 *     (works only if the host honours those WP-level overrides, which
 *     internally still call `ini_set`).
 *   - Place a `.user.ini` with `memory_limit = 512M` next to `wp-config.php`
 *     (not inside `wp-content/`).
 *   - Open a ticket with the host to raise PHP `memory_limit` to 512M.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert a shorthand byte value (e.g. "128M", "1G") to bytes.
 *
 * @param string $value Shorthand byte value as returned by ini_get().
 * @return int Bytes.
 */
function otu_memory_shorthand_to_bytes( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value || '-1' === $value ) {
		return -1;
	}

	$unit   = strtolower( substr( $value, -1 ) );
	$number = (int) $value;

	switch ( $unit ) {
		case 'g':
			return $number * 1024 * 1024 * 1024;
		case 'm':
			return $number * 1024 * 1024;
		case 'k':
			return $number * 1024;
		default:
			return (int) $value;
	}
}

/**
 * Raise the runtime memory_limit to at least 512M when possible.
 *
 * Skipped if the current limit is already >= 512M or unlimited (-1).
 */
function otu_memory_raise_limit() {
	$current = otu_memory_shorthand_to_bytes( ini_get( 'memory_limit' ) );
	$target  = 512 * 1024 * 1024;

	if ( -1 === $current || $current >= $target ) {
		return;
	}

	// Suppress warnings on hosts where ini_set is disabled.
	@ini_set( 'memory_limit', '512M' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.IniSet.Risky
}
otu_memory_raise_limit();

/*
 * Backstop: define WP-level memory constants if the wp-config.php hasn't.
 *
 * NOTE: WordPress reads `WP_MEMORY_LIMIT` / `WP_MAX_MEMORY_LIMIT` in
 * `wp_initial_constants()` which runs from `wp-settings.php` BEFORE
 * mu-plugins load, so defining them here is *too late* for WordPress's own
 * `wp_raise_memory_limit()` to act on them on the current request. They are
 * still defined here so that any third-party code that reads these
 * constants later in the request (e.g. health-check / debug pages) sees a
 * sensible value rather than an undefined constant. The real, effective
 * raise is the `ini_set` call above.
 */
if ( ! defined( 'WP_MEMORY_LIMIT' ) ) {
	define( 'WP_MEMORY_LIMIT', '512M' );
}
if ( ! defined( 'WP_MAX_MEMORY_LIMIT' ) ) {
	define( 'WP_MAX_MEMORY_LIMIT', '512M' );
}
