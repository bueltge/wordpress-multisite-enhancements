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

! defined( 'ABSPATH' ) && exit;
// phpcs:disable
add_filter( 'plugins_loaded', array( 'Multisite_Enhancements', 'get_object' ) );

/**
 * Class Multisite_Enhancements.
 * Plugin wrapper to list as plugin in WordPress environment and load all necessary files.
 */
class Multisite_Enhancements {
// phpcs:enable
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
	 * Autoload and init used functions.
	 *
	 * @since   0.0.1
	 */
	public static function load() {
		define( 'MULTISITE_ENHANCEMENT_BASE', __DIR__ . '/src' );

		require_once __DIR__ . '/vendor/autoload.php';

		add_action( 'init', array( 'Multisite_Enhancements_Settings', 'init' ) );
		add_action( 'init', array( 'Multisite_Core', 'init' ) );

		$modules = array(
			'add-favicon'         => array( 'init' => array( 'Multisite_Add_Admin_Favicon', 'init' ) ),
			'remove-logo'         => array( 'init' => array( 'Multisite_Add_Admin_Favicon', 'init' ) ),
			'add-blog-id'         => array( 'init' => array( 'Multisite_Add_Blog_Id', 'init' ) ),
			'add-css'             => array( 'init' => array( 'Add_Css', 'init' ) ),
			'add-plugin-list'     => array( 'init' => array( 'Multisite_Add_Plugin_List', 'init' ) ),
			'add-site-status'     => array( 'init' => array( 'Multisite_Add_Site_Status_labels', 'init' ) ),
			'add-ssl-identifier'  => array(
				'admin_init' => function () {
						$multisite_add_ssh_identifier = new Bueltge\Multisite_Add_Ssh_Identifier\Multisite_Add_Ssh_Identifier();
						$multisite_add_ssh_identifier->init();
				},
			),
			'add-theme-list'      => array( 'init' => array( 'Multisite_Add_Theme_List', 'init' ) ),
			'add-manage-comments' => array(
				'init' => function () {
								$multisite_admin_bar_tweaks = new Bueltge\Admin_Bar_Tweaks\Multisite_Admin_Bar_Tweaks();
								$multisite_admin_bar_tweaks->init();
				},
			),
			'change-footer'       => array( 'init' => array( 'Multisite_Change_Footer_Text', 'init' ) ),
			'filtering-themes'    => array( 'admin_init' => array( 'Filtering_Themes', 'init' ) ),
			'add-new-plugin'      => array( 'init' => array( 'Multisite_Add_New_Plugin', 'init' ) ),
		);

		foreach ( $modules as $id => $hooks ) {
			if ( Multisite_Enhancements_Settings::is_feature_enabled( $id ) ) {
				foreach ( $hooks as $hook_name => $callback ) {
					if ( ! has_action( $hook_name, $callback ) ) {
						add_action( $hook_name, $callback );
					}
				}
			}
		}
	}

	/**
	 * Load the object and get the current state.
	 *
	 * @return Multisite_Enhancements $class_object
	 * @since  0.0.1
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
