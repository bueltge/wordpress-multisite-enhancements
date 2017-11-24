<?php # -*- coding: utf-8 -*-
/**
 * Add a icon to identify the ssl protocol on each site.
 *
 * @since   2017-07-13
 * @version 2017-08-10
 * @package WordPress
 */

add_action( 'admin_init', array( 'Multisite_Add_Ssh_Identifier', 'init' ) );

/**
 * Class Multisite_Add_Ssh_Identifier
 */
class Multisite_Add_Ssh_Identifier {

	/**
	 * Set column name to identifier the column.
	 *
	 * @var string
	 */
	private $column = 'site_ssl';

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
	 */
	public function __construct() {

		add_filter( 'wpmu_blogs_columns', array( $this, 'add_column' ) );
		add_action( 'manage_sites_custom_column', array( $this, 'get_protocol' ), 10, 2 );

		add_action( 'admin_print_styles-sites.php', array( $this, 'add_style' ) );
	}

	/**
	 * Determines if SSL is used.
	 *
	 * @param  integer $blog_id
	 *
	 * @return bool
	 */
	private function is_ssl( $blog_id ) {

		return ( strstr( get_home_url( $blog_id ), 'https' ) ) ? true : false;
	}

	/**
	 * Add new column for the ssh identifier.
	 *
	 * @param  array $columns
	 *
	 * @return array
	 */
	public function add_column( array $columns ) {

		$first_column        = array_slice( $columns, 0, 1 );
		$after_first_columns = array_slice( $columns, 1 );
		$ssh_column          = array( $this->column => esc_html__( 'https', 'multisite-enhancements' ) );

		// Union of the arrays.
		$columns = $first_column + $ssh_column + $after_first_columns;

		return $columns;
	}

	/**
	 * Print icon, markup to identifier the protocol for each site.
	 *
	 * @param string  $column_name
	 * @param integer $blog_id
	 *
	 * @return string
	 */
	public function get_protocol( $column_name, $blog_id ) {

		if ( $this->column === $column_name ) {

			$status = 'unlock';
			if ( $this->is_ssl( $blog_id ) ) {
				$status = 'lock';
			}

			echo '<span class="dashicons dashicons-' . $status . '"></span>';
		}
		return $column_name;
	}

	/**
	 * Print custom style for the https column.
	 */
	public function add_style() {

		echo '<style>#' . esc_attr( $this->column ) . ' { width:5%; } .column-' . esc_attr( $this->column ) . ' { text-align: center; }</style>';
	}
}
