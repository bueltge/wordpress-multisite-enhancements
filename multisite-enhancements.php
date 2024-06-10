<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name:  Multisite Enhancements
 * Description:  Enhance Multisite for Network Admins with different topics
 * Plugin URI:   https://github.com/bueltge/WordPress-Multisite-Enhancements
 * Version:      1.7.0
 * Author:       Frank Bültge
 * Author URI:   https://bueltge.de
 * License:      GPLv2+
 * License URI:  LICENSE
 * Requires PHP: 7.2
 * Text Domain:  multisite-enhancements
 * Domain Path:  /languages
 * Network:      true
 *
 * @package multisite-enhancement
 */

if ( function_exists( 'add_action' ) ) {
	add_action( 'plugins_loaded', array( Multisite_Enhancements::get_object(), 'load' ) );
}

/**
 * Class Multisite_Enhancements.
 * Plugin wrapper to list as plugin in WordPress environment and load all necessary files.
 */
class Multisite_Enhancements {

	/**
	 * The class object.
	 *
	 * @var array
	 */
	private static $class_objects = array();

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
	 * Autoload and init used functions.
	 *
	 * @since   0.0.1
	 */
	public function load() {
		define( 'MULTISITE_ENHANCEMENT_BASE', __DIR__ . '/src' );

		if ( function_exists( 'is_multisite' ) && ! is_multisite() ) {
			add_filter( 'admin_notices', array( $this, 'error_msg_no_multisite' ) );

			return;
		}

		$this->load_translation();

		require_once __DIR__ . '/vendor/autoload.php';

		add_action( 'init', array( self::set_object( new Multisite_Enhancements\Settings() ), 'init' ) );

		$modules = array(
			'add-favicon'         => array( 'init' => array( Multisite_Enhancements\Add_Admin_Favicon::class, 'init' ) ),
			'remove-logo'         => array( 'init' => array( Multisite_Enhancements\Add_Admin_Favicon::class, 'init' ) ),
			'add-blog-id'         => array( 'init' => array( Multisite_Enhancements\Add_Blog_Id::class, 'init' ) ),
			'add-css'             => array( 'init' => array( Multisite_Enhancements\Add_Css::class, 'init' ) ),
			'add-plugin-list'     => array( 'init' => array( Multisite_Enhancements\Add_Plugin_List::class, 'init' ) ),
			'add-site-status'     => array( 'init' => array( Multisite_Enhancements\Add_Site_Status_Labels::class, 'init' ) ),
			'add-ssl-identifier'  => array(
				'admin_init' => array( Multisite_Enhancements\Add_Ssh_Identifier::class, 'init' ),
			),
			'add-theme-list'      => array( 'init' => array( Multisite_Enhancements\Add_Theme_List::class, 'init' ) ),
			'add-manage-comments' => array(
				'init' => array( Multisite_Enhancements\Admin_Bar_Tweaks::class, 'init' ),
			),
			'change-footer'       => array( 'init' => array( Multisite_Enhancements\Change_Footer_Text::class, 'init' ) ),
			'filtering-themes'    => array( 'admin_init' => array( Multisite_Enhancements\Filtering_Themes::class, 'init' ) ),
			'add-new-plugin'      => array( 'init' => array( Multisite_Enhancements\Multisite_Add_New_Plugin::class, 'init' ) ),
			'add-user-last-login' => array(
				'init' => array( Multisite_Enhancements\Add_User_Last_Login::class, 'init' ),
			),
		);

		foreach ( $modules as $id => $hooks ) {
			if ( Multisite_Enhancements\Settings::is_feature_enabled( $id ) ) {
				foreach ( $hooks as $hook_name => $callback ) {
					if ( is_string( $callback[0] ) && class_exists( $callback[0] ) ) {
						$callback[0] = self::set_object( new $callback[0]() );
					}
					if ( ! has_action( $hook_name, $callback ) ) {
						add_action( $hook_name, $callback );
					}
				}
			}
		}
	}

	/**
	 * Load objects for all plugin classes, default for this class.
	 *
	 * @param string $class_name FQN of the class to get object from.
	 * @return object|null $class_object
	 * @since  0.0.1
	 */
	public static function get_object( string $class_name = '' ): ?object {
		if ( '' === $class_name ) {
			$class_name = __CLASS__;
		}

		if ( isset( self::$class_objects[ $class_name ] ) ) {
			return self::$class_objects[ $class_name ];
		}

		if ( __CLASS__ === $class_name ) {
			self::set_object( new self() );
			return self::get_object( __CLASS__ );
		}

		return null;
	}

	/**
	 * Store objects for all plugin classes, default for this class.
	 *
	 * @param object $class_object The object to store.
	 * @return object
	 */
	public static function set_object( object $class_object ): object {
		$class_name = get_class( $class_object );

		if ( isset( self::$class_objects[ $class_name ] ) ) {
			return self::$class_objects[ $class_name ];
		}
		self::$class_objects[ $class_name ] = $class_object;
		return self::get_object( $class_name );
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
				<a href="https://developer.wordpress.org/advanced-administration/multisite/create-network/" title="
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
