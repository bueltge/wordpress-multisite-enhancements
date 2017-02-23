<?php # -*- coding: utf-8 -*-
/**
 * Plugin Name: Multisite Enhancements
 * Description: Enhance Multisite for Network Admins with different topics
 * Plugin URI:  https://github.com/bueltge/WordPress-Multisite-Enhancements
 * Version:     1.3.8
 * Author:      Frank Bültge
 * Author URI:  https://bueltge.de
 * License:     GPLv2+
 * License URI: ./assets/LICENSE
 * Text Domain: multisite-enhancements
 * Domain Path: /languages
 * Network:     true
 */

! defined( 'ABSPATH' ) and exit;

add_filter( 'plugins_loaded', array( 'Multisite_Enhancements', 'get_object' ) );

/**
 * Class Multisite_Enhancements.
 * Plugin wrapper to list as plugin in WordPress environment and load all necessary files.
 * Use the filter hook 'multisite_enhancements_autoload' to unset classes, there is not necessary for you.
 */
class Multisite_Enhancements {

	/**
	 * Define folder, there have inside the autoload files.
	 *
	 * @since  0.0.1
	 * @var    String
	 */
	static protected $file_base = '';

	/**
	 * The class object.
	 *
	 * @since  0.0.1
	 * @var    String
	 */
	static protected $class_object;

	/**
	 * Load the object and get the current state.
	 *
	 * @since   0.0.1
	 * @return String $class_object
	 */
	public static function get_object() {

		if ( NULL === self::$class_object ) {
			self::$class_object = new self;
		}

		return self::$class_object;
	}

	/**
	 * Init function to register all used hooks.
	 *
	 * @since   0.0.1
	 */
	public function __construct() {

		// This check prevents using this plugin not in a multisite.
		if ( function_exists( 'is_multisite' ) && ! is_multisite() ) {
			add_filter( 'admin_notices', array( $this, 'error_msg_no_multisite' ) );

			return;
		}

		$this->load_translation();

		// Since 2015-08-18 only PHP 5.3, use now __DIR__ as equivalent to dirname(__FILE__).
		self::$file_base = __DIR__ . '/inc';
		self::load();
	}

	/**
	 * Load translation file.
	 *
	 * @since 2016-10-23
	 */
	public function load_translation() {

		load_plugin_textdomain(
			'multisite-enhancements',
			FALSE,
			basename( __DIR__ ) . '/languages/'
		);
	}

	/**
	 * Display an Admin Notice if multisite is not active.
	 *
	 * @since   0.0.1
	 */
	public function error_msg_no_multisite() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
		?>
		<div class="error">
			<p>
				<?php esc_html_e(
					'The plugin only works in a multisite installation. See how to install a multisite network:',
					'multisite-enhancements'
				); ?>
				<a href="http://codex.wordpress.org/Create_A_Network" title="<?php esc_html_e(
					'WordPress Codex: Create a network', 'multisite-enhancements'
				); ?>">
					<?php esc_html_e( 'WordPress Codex: Create a network', 'multisite-enhancements' ); ?>
				</a>
			</p>
		</div>

		<div class="updated notice">
			<p>
				<?php echo wp_kses(
					__( 'Plugin <strong>deactivated</strong>.', 'multisite-enhancements' ),
					array(
						'strong' => array(),
					)
				); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Load all files in folder inc.
	 * Use the filter hook 'multisite_enhancements_autoload' to unset classes, there is not necessary for you.
	 *
	 * @since   0.0.1
	 */
	public static function load() {

		$file_base = self::$file_base;
		define( 'MULTISITE_ENHANCEMENT_BASE', $file_base );

		$autoload_files = glob( "$file_base/autoload/*.php" );
		$autoload_files = (array) apply_filters( 'multisite_enhancements_autoload', $autoload_files );

		// Load files.
		foreach ( $autoload_files as $path ) {
			/**
			 * Path of each file, that we load.
			 *
			 * @var string $path
			 */
			require_once $path;
		}
	}

} // end class
