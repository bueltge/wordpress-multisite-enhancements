<?php
/**
 * Plugin Name: Multisite Enhancements
 * Description: Enhance Multisite for Network Admins with different topics
 * Plugin URI:  https://github.com/bueltge/WordPress-Multisite-Enhancements
 * Version:     1.6.1
 * Author:      Frank Bültge
 * Author URI:  https://bueltge.de
 * License:     GPLv2+
 * License URI: LICENSE
 * Text Domain: multisite-enhancements
 * Domain Path: /languages
 * Network:     true
 *
 * @package multisite-enhancement
 */

! defined( 'ABSPATH' ) && exit;
// phpcs:disable
add_filter( 'plugins_loaded', array( 'Multisite_Enhancements', 'get_object' ) );

/**
 * Class Multisite_Enhancements.
 * Plugin wrapper to list as plugin in WordPress environment and load all necessary files.
 * Use the filter hook 'multisite_enhancements_autoload' to unset classes, there is not necessary for you.
 */
class Multisite_Enhancements {
// phpcs:enable
	/**
	 * Define folder, there have inside the autoload files.
	 *
	 * @since  0.0.1
	 * @var    String
	 */
	protected static $file_base = '';

	/**
	 * The class object.
	 *
	 * @since  0.0.1
	 * @var    String
	 */
	protected static $class_object;

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
		self::$file_base = __DIR__ . '/src';
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
			false,
			basename( __DIR__ ) . '/languages/'
		);
	}

	/**
	 * Load all files in folder src.
	 * Use the filter hook 'multisite_enhancements_autoload' to unset classes, there is not necessary for you.
	 *
	 * @since   0.0.1
	 */
	public static function load() {
		$file_base = self::$file_base;
		define( 'MULTISITE_ENHANCEMENT_BASE', $file_base );

		// Load configuration settings.
		require_once $file_base . '/settings.php';

		$autoload_paths = glob( "$file_base/*.php" );
		$autoload_files = array();

		foreach ( $autoload_paths as $classnames => $path ) {
			$path_split               = explode( DIRECTORY_SEPARATOR, $path );
			$class                    = end( $path_split );
			$autoload_files[ $class ] = $path;
		}

		$autoload_files = (array) apply_filters( 'multisite_enhancements_autoload', $autoload_files );

		// Remove from autoload classes for disabled features.
		$feature_modules = array(
			'class-add-admin-favicon.php'        => array( 'add-favicon', 'remove-logo' ),
			'class-add-blog-id.php'              => 'add-blog-id',
			'class-add-css.php'                  => 'add-css',
			'class-add-plugin-list.php'          => 'add-plugin-list',
			'class-add-site-status-labels.php'   => 'add-site-status',
			'class-add-ssl-identifier.php'       => 'add-ssl-identifier',
			'class-add-theme-list.php'           => 'add-theme-list',
			'class-admin-bar-tweaks.php'         => 'add-manage-comments',
			'class-change-footer-text.php'       => 'change-footer',
			'class-filtering-themes.php'         => 'filtering-themes',
			'class-multisite-add-new-plugin.php' => 'add-new-plugin',
		);

		foreach ( $feature_modules as $file => $settings ) {
			if ( is_array( $settings ) ) {
				$enabled = array_reduce(
					$settings,
					function ( $carry, $item ) {
						return $carry || Multisite_Enhancements_Settings::is_feature_enabled( $item );
					},
					false
				);
			} else {
				$enabled = Multisite_Enhancements_Settings::is_feature_enabled( $settings );
			}

			if ( ! $enabled ) {
				unset( $autoload_files[ $file ] );
			}
		}

		// Load files.
		foreach ( $autoload_files as $path ) {
			/**
			 * Path of each file, that we load.
			 *
			 * @var string $path
			 */
			// phpcs:disable
			require_once $path;
			// phpcs: enable
		}
	}

	/**
	 * Load the object and get the current state.
	 *
	 * @return String $class_object
	 * @since   0.0.1
	 */
	public static function get_object() {
		if ( null === self::$class_object ) {
			self::$class_object = new self();
		}

		return self::$class_object;
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
				<?php
				esc_html_e(
					'The plugin only works in a multisite installation. See how to install a multisite network:',
					'multisite-enhancements'
				);
				?>
				<a href="http://codex.wordpress.org/Create_A_Network" title="
				<?php
				esc_html_e(
					'WordPress Codex: Create a network',
					'multisite-enhancements'
				);
				?>
				">
					<?php esc_html_e( 'WordPress Codex: Create a network', 'multisite-enhancements' ); ?>
				</a>
			</p>
		</div>

		<div class="updated notice">
			<p>
				<?php
				echo wp_kses(
					__( 'Plugin <strong>deactivated</strong>.', 'multisite-enhancements' ),
					array(
						'strong' => array(),
					)
				);
				?>
			</p>
		</div>
		<?php
	}

} // end class
