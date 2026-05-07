<?php
/**
 * Plugin Name: OTU Memory Limit
 * Description: Raises PHP memory_limit early (before regular plugins load) so
 *              memory-hungry modules like Jetpack's plugin-search do not
 *              exhaust the default 128M limit and crash with HTTP 500.
 * Version:     1.0.0
 * Author:      One Ten United Services
 * License:     GPL-2.0-or-later
 *
 * Drop-in must-use plugin. WordPress loads files in wp-content/mu-plugins/
 * before any regular plugin, so this runs before Jetpack and friends.
 *
 * The host's php.ini may forbid raising the limit, in which case this is a
 * no-op and the site owner needs to ask the host to bump PHP `memory_limit`
 * (or set `WP_MEMORY_LIMIT` / `WP_MAX_MEMORY_LIMIT` in wp-config.php).
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
 * Raise the runtime memory_limit to at least 256M when possible.
 *
 * Skipped if the current limit is already >= 256M or unlimited (-1).
 */
function otu_memory_raise_limit() {
	$current = otu_memory_shorthand_to_bytes( ini_get( 'memory_limit' ) );
	$target  = 256 * 1024 * 1024;

	if ( -1 === $current || $current >= $target ) {
		return;
	}

	// Suppress warnings on hosts where ini_set is disabled.
	@ini_set( 'memory_limit', '256M' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.IniSet.Risky
}
otu_memory_raise_limit();
