<?php
/**
 * Core methods, there will be used
 *
 * @since   07/24/2013
 */

add_action( 'init', array( 'Multisite_Core', 'init' ) );

class Multisite_Core {

	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Returns an array of arrays containing information about each public blog
	 * hosted on this WPMU install.
	 *
	 * Only blogs marked as public and flagged as safe (mature flag off) are returned.
	 *
	 * @param   Integer  The first blog to return in the array.
	 * @param   Integer  The number of blogs to return in the array (thus the size of the array).
	 *                   Setting this to string 'all' returns all blogs from $start
	 * @param   Boolean  Get also Postcount for each blog, default is False for a better performance
	 * @param   Integer  Time until expiration in seconds, default 86400s (1day)
	 *
	 * @return  Array    Returns an array of arrays each representing a blog.
	 *                   Details are represented in the following format:
	 *                       blog_id   (integer) ID of blog detailed.
	 *                       domain    (string)  Domain used to access this blog.
	 *                       path      (string)  Path used to access this blog.
	 *                       postcount (integer) The number of posts in this blog.
	 */
	public static function get_blog_list( $start = 0, $num = 10, $details = FALSE, $expires = 86400 ) {

		// get blog list from cache
		$blogs = get_site_transient( 'multisite_blog_list' );

		// For debugging purpose
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$blogs = FALSE;
		}

		if ( FALSE === $blogs ) {

			global $wpdb;

			// add limit for select
			if ( 'all' === $num ) {
				$limit = '';
			} else {
				$limit = "LIMIT $start, $num";
			}

			$blogs = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT blog_id, domain, path 
					FROM $wpdb->blogs
					WHERE site_id = %d 
					AND public = '1' 
					AND archived = '0' 
					AND mature = '0' 
					AND spam = '0' 
					AND deleted = '0' 
					ORDER BY registered ASC
					$limit
				", $wpdb->siteid
				),
				ARRAY_A
			);

			// Set the Transient cache
			set_site_transient( 'multisite_blog_list', $blogs, $expires );
		}

		// only if usable, set via var
		if ( TRUE === $details ) {

			$blog_list = get_site_transient( 'multisite_blog_list_details' );

			// For debugging purpose
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$blog_list = FALSE;
			}

			if ( FALSE === $blog_list ) {

				global $wpdb;
				foreach ( (array) $blogs as $details ) {
					$blog_list[ $details[ 'blog_id' ] ]                = $details;
					$blog_list[ $details[ 'blog_id' ] ][ 'postcount' ] = $wpdb->get_var(
						"SELECT COUNT(ID)
						FROM " . $wpdb->get_blog_prefix( $details[ 'blog_id' ] ) . "posts
						WHERE post_status='publish' 
						AND post_type='post'"
					);
				}

				// Set the Transient cache
				set_site_transient( 'multisite_blog_list_details', $blog_list, $expires );
			}
			unset( $blogs );
			$blogs = $blog_list;
		}

		if ( FALSE === is_array( $blogs ) ) {
			return array();
		}

		return $blogs;
	}

} // end class
