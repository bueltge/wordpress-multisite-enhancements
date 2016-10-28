<?php
/**
 * Core functions, there was missed in WP Core for use with Multisite.
 *
 * Hint: Since WordPress version 3.7.0 use the function 'wp_get_sites()',
 * Hint again: Since WordPress version 4.6.0 is the function 'get_sites()' the right alternative,
 * a alternative inside the core of WP
 *
 * @since    2013-07-24
 * @version  2016-10-05
 * @package WordPress
 */

if ( ! function_exists( 'get_blog_list' ) ) {

	/**
	 * Returns an array of arrays containing information about each public blog hosted on this WPMU install.
	 *
	 * Only sites marked as public and flagged as safe (mature flag off) are returned.
	 *
	 * @param Integer $start   The first blog to return in the array.
	 * @param Integer $num     The number of sites to return in the array (thus the size of the array).
	 *                         Setting this to string 'all' returns all sites from $start.
	 * @param Integer $expires Time until expiration in seconds, default 86400s (1day).
	 *
	 * @return array|bool
	 *                   Details are represented in the following format:
	 *                       blog_id   (integer) ID of blog detailed.
	 *                       domain    (string)  Domain used to access this blog.
	 *                       path      (string)  Path used to access this blog.
	 *                       postcount (integer) The number of posts in this blog.
	 */
	function get_blog_list( $start = 0, $num = 10, $expires = 86400 ) {

		if ( ! is_multisite() ) {
			return FALSE;
		}

		if ( ! class_exists( 'Multisite_Core' ) ) {
			require_once __DIR__ . '/class-core.php';
			new Multisite_Core();
		}

		return Multisite_Core::get_blog_list( $start, $num, $expires );
	}
} // end if fct exist
