<?php
/**
 * Core methods, there will be used.
 *
 * @since   2013-07-24
 * @version 2016-10-28
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Core', 'init' ) );

/**
 * Class Multisite_Core
 */
class Multisite_Core {

	/**
	 * Init the class.
	 */
	public static function init() {
		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			// phpcs:disable
			$GLOBALS[ $class ] = new $class();
			// phpcs:enable
		}
	}

	/**
	 * Returns an array of arrays containing information about each public blog hosted on this WPMU install.
	 *
	 * Only blogs marked as public and flagged as safe (mature flag off) are returned.
	 *
	 * @param  Integer $start        The first blog to return in the array.
	 * @param  Integer $num          The number of blogs to return in the array (thus the size of the array).
	 *                          Setting this to string 'all' returns all blogs from $start.
	 * @param  Boolean $details Get also Postcount for each blog, default is False for a better performance.
	 * @param  Integer $expires      Time until expiration in seconds, default 86400s (1day).
	 *
	 * @return array   Returns an array of arrays each representing a blog.
	 *                  Details are represented in the following format:
	 *                      blog_id   (integer) ID of blog detailed.
	 *                      domain    (string)  Domain used to access this blog.
	 *                      path      (string)  Path used to access this blog.
	 *                      postcount (integer) The number of posts in this blog.
	 */
	public static function get_blog_list( $start = 0, $num = 10, $details = false, $expires = 86400 ) {

		// Since WP version 4.6.0 is a new function inside the core to get this value.
		if ( function_exists( 'get_sites' ) ) {
			return get_sites(
				array(
					'number' => $num,
				)
			);
		}

		// For WordPress smaller version 4.6.0, available since WordPress 3.7.
		// phpcs:disable WordPress.WP.DeprecatedFunctions.wp_get_sitesFound
		if ( function_exists( 'wp_get_sites' ) ) {
			return wp_get_sites(
				array(
					'limit' => $num,
				)
			);
		}
		// phpcs:enable

		// Get blog list from cache.
		$blogs = get_site_transient( 'multisite_blog_list' );

		// For debugging purpose.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$blogs = false;
		}

		if ( false === $blogs ) {
			global $wpdb;

			// Add limit for select.
			$limit = "LIMIT $start, $num";
			if ( 'all' === $num ) {
				$limit = '';
			}

			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
			/** @noinspection SqlDialectInspection */
			/** @noinspection SqlNoDataSourceInspection */
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
				",
					$wpdb->siteid
				),
				ARRAY_A
			);
			// phpcs:enable

			// Set the Transient cache.
			set_site_transient( 'multisite_blog_list', $blogs, $expires );
		}

		// Only if usable, set via var.
		if ( true === $details ) {
			/**
			 * Get data to each site in the network.
			 *
			 * @var array $blog_list
			 */
			$blog_list = get_site_transient( 'multisite_blog_list_details' );

			// For debugging purpose.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$blog_list = false;
			}

			if ( false === $blog_list ) {
				global $wpdb;
				/**
				 * The data details of each site of the network.
				 *
				 * @var array $blog_details
				 */
				foreach ( (array) $blogs as $blog_details ) {
					// phpcs:disable
					$blog_list[ $blog_details['blog_id'] ]              = $blog_details;
					$blog_list[ $blog_details['blog_id'] ]['postcount'] = $wpdb->get_var(
						'SELECT COUNT(ID)
						FROM ' . $wpdb->get_blog_prefix( $blog_details['blog_id'] ) . "posts
						WHERE post_status='publish'
						AND post_type='post'"
					);
					// phpcs:enable
				}

				// Set the Transient cache.
				set_site_transient( 'multisite_blog_list_details', $blog_list, $expires );
			}
			unset( $blogs );
			$blogs = $blog_list;
		}

		if ( false === is_array( $blogs ) ) {
			return array();
		}

		return $blogs;
	}

} // end class
