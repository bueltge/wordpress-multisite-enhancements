<?php
/**
 * Plugin Name: Multisite Enhancements
 * Description: Enhance Multisite for Network Admins with different topics
 * Plugin URI:  https://github.com/bueltge/WordPress-Multisite-Enhancements
 * Version:     1.0.7
 * Author:      Frank Bültge
 * Author URI:  http://bueltge.de
 * License:     GPLv2+
 * License URI: ./assets/license.txt
 * Text Domain: multisite_enhancements
 * Domain Path: /languages
 * Network:     true
 */

! defined( 'ABSPATH' ) and exit;

add_filter( 'plugins_loaded', array( 'Multisite_Enhancements', 'get_object' ) );

class Multisite_Enhancements {

	/**
	 * Define folder, there have inside the autoload files
	 *
	 * @since  0.0.1
	 * @var    String
	 */
	static protected $file_base = '';

	/**
	 * The class object
	 *
	 * @since  0.0.1
	 * @var    String
	 */
	static protected $class_object = NULL;

	/**
	 * Load the object and get the current state
	 *
	 * @since   0.0.1
	 * @return String $class_object
	 */
	public static function get_object() {

		if ( NULL == self::$class_object ) {
			self::$class_object = new self;
		}

		return self::$class_object;
	}

	/**
	 * Init function to register all used hooks
	 *
	 * @since   0.0.1
	 * @return \Multisite_Enhancements
	 */
	public function __construct() {

		// This check prevents using this plugin not in a multisite
		if ( function_exists( 'is_multisite' ) && ! is_multisite() ) {
			add_filter( 'admin_notices', array( $this, 'error_msg_no_multisite' ) );

			return NULL;
		}

		self::$file_base = dirname( __FILE__ ) . '/inc';

		$this->load();
	}

	/**
	 * Display an Admin Notice if multisite is not active
	 *
	 * @since   0.0.1
	 * @return  void
	 */
	public function error_msg_no_multisite() {

		?>
		<div class="error">
			<p>
				<?php _e(
					'The plugin only works in a multisite installation. See how to install a multisite network:',
					'multisite_enhancements'
				); ?>
				<a href="http://codex.wordpress.org/Create_A_Network" title="<?php _e(
					'WordPress Codex: Create a network', 'multisite_enhancements'
				); ?>"><?php _e( 'WordPress Codex: Create a network', 'multisite_enhancements' ); ?></a>
			</p>
		</div>
	<?php
	}

	/**
	 * Load all files in folder inc
	 *
	 * @since   0.0.1
	 * @return  void
	 */
	public static function load() {

		$file_base = self::$file_base;

		$autoload_files = glob( "$file_base/autoload/*.php" );

		// load files
		foreach ( $autoload_files as $path ) {
			require_once $path;
		}
	}

} // end class
