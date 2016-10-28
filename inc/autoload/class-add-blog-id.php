<?php
/**
 * View Blog and User ID in WordPress Multisite.
 *
 * @see     http://wpengineer.com/2188/view-blog-id-in-wordpress-multisite/
 * @since   2013-07-19
 * @version 2016-01-15
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Add_Blog_Id', 'init' ) );

/**
 * View Blog and User ID in WordPress Multisite.
 * Class Multisite_Add_Blog_Id
 */
class Multisite_Add_Blog_Id {

	/**
	 * Init the class.
	 */
	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks.
	 *
	 * @since   0.0.1
	 */
	public function __construct() {

		if ( ! is_network_admin() ) {
			return;
		}

		// Add blog id.
		add_filter( 'wpmu_blogs_columns', array( $this, 'get_id' ) );
		add_action( 'manage_sites_custom_column', array( $this, 'get_blog_id' ), 10, 2 );

		// Add user id.
		add_filter( 'manage_users-network_columns', array( $this, 'get_id' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'get_user_id' ), 10, 3 );

		add_action( 'admin_print_styles-sites.php', array( $this, 'add_style' ) );
		add_action( 'admin_print_styles-users.php', array( $this, 'add_style' ) );
	}

	/**
	 * Echo the site id of each site.
	 *
	 * @param string  $column_name The name of the column.
	 * @param integer $blog_id     The Id of the blog.
	 *
	 * @return mixed
	 */
	public function get_blog_id( $column_name, $blog_id ) {

		if ( 'object_id' === $column_name ) {
			echo (int) $blog_id;
		}

		return $column_name;
	}

	/**
	 * Echo the ID of each user.
	 *
	 * @param string $value       Custom column output.
	 * @param string $column_name The current column name.
	 * @param int    $user_id     ID of the currently-listed user.
	 *
	 * @return int|string
	 */
	public function get_user_id( $value, $column_name, $user_id ) {

		if ( 'object_id' === $column_name ) {
			return (int) $user_id;
		}

		return $value;
	}

	/**
	 * Add in a column header.
	 *
	 * @param array $columns An array of displayed site columns.
	 *
	 * @return mixed
	 */
	public function get_id( $columns ) {

		$columns[ 'object_id' ] = __( 'ID' );

		return $columns;
	}

	/**
	 * Add custom style.
	 */
	public function add_style() {

		echo '<style>#object_id { width:7%; }</style>';
	}

} // end class
