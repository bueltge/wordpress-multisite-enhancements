<?php
/**
 * Plugin configuration page
 * Based on: https://vedovini.net/2015/10/using-the-wordpress-settings-api-with-network-admin-pages/
 *
 * @package multisite-enhancements
 */

namespace Multisite_Enhancements;

/**
 * Class Settings
 */
class Settings {

	/**
	 * Database option name
	 * This is a network-wide option, saved in the `wp_sitemeta` table
	 */
	const OPTION_NAME = 'wpme_options';

	/**
	 * Default options settings
	 *
	 * @var int[]
	 */
	const DEFAULT_OPTIONS = array(
		'remove-logo'         => 1,
		'add-favicon'         => 1,
		'add-blog-id'         => 1,
		'add-css'             => 1,
		'add-plugin-list'     => 1,
		'add-theme-list'      => 1,
		'add-site-status'     => 1,
		'add-ssl-identifier'  => 1,
		'add-manage-comments' => 1,
		'add-new-plugin'      => 1,
		'add-user-last-login' => 1,
		'filtering-themes'    => 1,
		'change-footer'       => 1,
		'delete-settings'     => 1,
	);


	/**
	 * Initialize the class.
	 */
	public function init() {
		// Register settings.
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		// Add menu item in the network Settings menu.
		add_action( 'network_admin_menu', array( $this, 'add_settings_menu' ) );
		// Register our custom URL to save the options.
		add_action( 'network_admin_edit_wpme_update_settings', array( $this, 'update_settings' ) );
	}

	/**
	 * Check if a feature is enabled
	 *
	 * @param string $key Key of desired setting to check.
	 *
	 * @return boolean
	 */
	public static function is_feature_enabled( $key ) {
		return (bool) self::get_settings( $key );
	}

	/**
	 * Register settings
	 */
	public function settings_init() {
		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}

		register_setting(
			'wpme_options',
			self::OPTION_NAME
		);

		add_settings_section(
			'wpme_general',
			esc_html__( 'General configuration', 'multisite-enhancements' ),
			array( $this, 'settings_section_callback' ),
			'wpme_config'
		);

		add_settings_field(
			'enable_features',
			esc_html__( 'Plugin features', 'multisite-enhancements' ),
			array( $this, 'settings_fields_callback' ),
			'wpme_config',
			'wpme_general',
			array(
				'group' => 'features',
			)
		);

		add_settings_field(
			'clean_database',
			esc_html__( 'Clean database on uninstall', 'multisite-enhancements' ),
			array( $this, 'settings_fields_callback' ),
			'wpme_config',
			'wpme_general',
			array(
				'group'     => 'uninstall',
				'label_for' => 'delete-settings',
			)
		);
	}

	/**
	 * Add link to configuration page in the network Settings menu
	 */
	public function add_settings_menu() {
		add_submenu_page(
			'settings.php',
			'Multisite Enhancements',
			'Multisite Enhancements',
			'manage_network_options',
			'wpme_config',
			array( $this, 'settings_page_callback' )
		);
	}

	/**
	 * Render the configuration page HTML
	 */
	public function settings_page_callback() {
		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['updated'] ) ) {
			?>
			<div id="message" class="updated notice is-dismissible">
				<p><?php esc_html_e( 'Settings saved', 'multisite-enhancements' ); ?></p></div>
			<?php
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="edit.php?action=wpme_update_settings" method="post">
				<?php
				settings_fields( 'wpme_options' );
				do_settings_sections( 'wpme_config' );
				submit_button( __( 'Save settings', 'multisite-enhancements' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Configuration sections callback
	 *
	 * @param array $args array with following keys: title, id, callback - defined by add_settings_section().
	 */
	public function settings_section_callback( $args ) {
		if ( 'wpme_general' === $args['id'] ) {
			echo '<p>' . esc_html__( 'Check or uncheck the options below to enable or disable specific plugin features:', 'multisite-enhancements' ) . '</p>';
		}
	}

	/**
	 * Configuration fields callback
	 *
	 * @param array $args arguments defined by add_settings_field().
	 */
	public function settings_fields_callback( $args ) {
		$settings = array(
			'features'  => array(
				'remove-logo'         => esc_html__( 'Remove the "W" logo menu from the admin top bar', 'multisite-enhancements' ),
				'add-favicon'         => esc_html__( 'Add sites favicons to admin area', 'multisite-enhancements' ),
				'add-blog-id'         => esc_html__( 'Add blog and user IDs to admin lists', 'multisite-enhancements' ),
				'add-css'             => esc_html__( 'Add custom CSS to allow showing or hiding the list of sites that use a theme or plugin', 'multisite-enhancements' ),
				'add-plugin-list'     => esc_html__( 'On the network Plugins page, show which blogs have the plugin active', 'multisite-enhancements' ),
				'add-theme-list'      => esc_html__( 'On the network Themes page, show which blogs have the theme active', 'multisite-enhancements' ),
				'add-site-status'     => esc_html__( 'Add status labels for no-index and external domain to blogs in "My Sites" menu', 'multisite-enhancements' ),
				'add-ssl-identifier'  => esc_html__( 'Add an icon to identify the SSL protocol on each site in the network Sites page', 'multisite-enhancements' ),
				'add-manage-comments' => esc_html__( 'Add new "Manage Comments" item with count of comments waiting for moderation in "My Sites" menu', 'multisite-enhancements' ),
				'add-new-plugin'      => esc_html__( 'Enables an "Add New" link under the Plugins menu of each blog, for network admins', 'multisite-enhancements' ),
				'add-user-last-login' => esc_html__( 'Recorde last logins and add displaying of last login to admin user list', 'multisite-enhancements' ),
				'filtering-themes'    => esc_html__( 'Add simple javascript to filter the theme list on network and single site theme page of WordPress backend', 'multisite-enhancements' ),
				'change-footer'       => esc_html__( 'Enhance the admin footer text with RAM, SQL queries and PHP version information', 'multisite-enhancements' ),
			),
			'uninstall' => array(
				'delete-settings' => esc_html__( 'Delete configuration options from the database when uninstalling Multisite Enhancements', 'multisite-enhancements' ),
			),
		);

		$options = self::get_settings();

		foreach ( $settings[ $args['group'] ] as $key => $description ) {
			?>
			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $key ); ?>"
							name="wpme_options[<?php echo esc_attr( $key ); ?>]" <?php checked( $options[ $key ], 1 ); ?>
							value="1">
					<?php echo esc_html( $description ); ?>
				</label>
			</p>
			<?php
		}
	}

	/**
	 * Load settings from database and merge them with default values
	 *
	 * @param string $key Key of an specific setting desired (optional).
	 *
	 * @return string|array Value of setting selected by $key or full array of settings.
	 */
	public static function get_settings( $key = null ) {
		$options = get_site_option( self::OPTION_NAME );
		$options = wp_parse_args( $options, self::DEFAULT_OPTIONS );

		if ( isset( $key ) ) {
			return $options[ $key ];
		}

		return $options;
	}

	/**
	 * Save / update settings on the database
	 */
	public function update_settings() {
		// check the referer to make sure the data comes from our options page.
		check_admin_referer( 'wpme_options-options' );

		if ( ! isset( $_POST['wpme_options'] ) && ! is_array( $_POST['wpme_options'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$options = wp_unslash( $_POST['wpme_options'] );

		foreach ( array_keys( self::DEFAULT_OPTIONS ) as $key ) {
			$options[ $key ] = (int) isset( $options[ $key ] );
		}

		update_site_option( self::OPTION_NAME, $options );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'wpme_config',
					'updated' => 'true',
				),
				network_admin_url( 'settings.php' )
			)
		);
		exit;
	}
} // end class
