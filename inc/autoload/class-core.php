<?php
/**
 * Core functions, there was missed in WP Core for use with Multisite
 * 
 * @since   07/24/2013
 */

if ( ! function_exists( 'get_blog_list' ) ) {
	
	/**
	 * Returns an array of arrays containing information about each public blog hosted on this WPMU install.
	 * Only blogs marked as public and flagged as safe (mature flag off) are returned.
	 * 
	 * @param   Integer  The first blog to return in the array.
	 * @param   Integer  The number of blogs to return in the array (thus the size of the array).
	 *                   Setting this to string 'all' returns all blogs from $start
	 * @return  Array    Returns an array of arrays each representing a blog hosted on this WPMU install. 
	 *                   Details are represented in the following format:
	 *                       blog_id   (integer)ID of blog detailed.
	 *                       domain    (string) Domain used to access this blog.
	 *                       path      (string) Path used to access this blog.
	 *                       postcount (integer) The number of posts in this blog.
	 */
	function get_blog_list( $start = 0, $num = 10 ) {
	
		global $wpdb;
		$blogs = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT blog_id, domain, path 
				FROM $wpdb->blogs WHERE site_id = %d 
				AND public = '1' 
				AND archived = '0' 
				AND mature = '0' 
				AND spam = '0' 
				AND deleted = '0' 
				ORDER BY registered DESC
			", $wpdb->siteid ), 
		ARRAY_A );
		
		foreach ( (array) $blogs as $details ) {
			$blog_list[ $details['blog_id'] ] = $details;
			$blog_list[ $details['blog_id'] ]['postcount'] = $wpdb->get_var( "
				SELECT COUNT(ID) 
				FROM " . $wpdb->get_blog_prefix( $details['blog_id'] ). "posts 
				WHERE post_status='publish' 
				AND post_type='post'" 
			);
		}
		unset( $blogs );
		$blogs = $blog_list;
		
		if ( false == is_array( $blogs ) )
			return array();
		
		if ( $num == 'all' )
			return array_slice( $blogs, $start, count( $blogs ) );
		else
			return array_slice( $blogs, $start, $num );
	}
	
} // end if fct exist