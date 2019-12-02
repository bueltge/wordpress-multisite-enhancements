<?php
/**
 * Configuration page
 * Based on: https://vedovini.net/2015/10/using-the-wordpress-settings-api-with-network-admin-pages/
 */

add_action( 'init', array( 'Multisite_Enhancements_Settings', 'init' ) );

class Multisite_Enhancements_Settings {

	/**
	 * Default options settings
	 */
	static protected $default_options = array(
		'remove-logo'         => '1',
		'add-favicon'         => '1',
		'add-blog-id'         => '1',
		'add-css'             => '1',
		'add-plugin-list'     => '1',
		'add-theme-list'      => '1',
		'add-site-status'     => '1',
		'add-ssl-identifier'  => '1',
		'add-manage-comments' => '1',
		'add-network-plugins' => '1',
		'add-new-plugin'      => '1',
		'filtering-themes'    => '1',
		'change-footer'       => '1',
	);

	/**
	 * Init function to register all used hooks.
	 */
	public function __construct() {
		// register settings
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		// add menu item in the network Settings menu
		add_action( 'network_admin_menu', array( $this, 'add_settings_menu' ) );
		// register our custom URL to save the options
		add_action( 'network_admin_edit_wpme_update_settings', array( $this, 'update_settings' ) );
	}

	/**
	 * Initialize the class.
	 */
	public static function init() {
		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class();
		}
	}

	/**
	 * Register settings
	 */
	public function settings_init() {
		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}

		// register database option
		register_setting(
			'wpme_options',	// group name, used in settings_fields() call
			'wpme_options'  // database option name
		);

		// register configuration page section
		add_settings_section(
			'wpme_general',		// unique ID
			__( 'General configuration', 'multisite-enhancements' ),	// section title
			array( $this, 'settings_section_callback' ),				// callback to render the section's HTML
			'wpme_config'			// config page slug - used in do_settings_sections() call
		);

		// regista campos para o formulário
		add_settings_field(
			'enable_features',	 	// unique ID
			__( 'Enabled features', 'multisite-enhancements' ),	// field label
			array( $this, 'settings_fields_callback' ),		// callback para exibir o HTML do campo
			'wpme_config',		// config page slug
			'wpme_general',		// section ID where the field will be shown
			array(					// arguments passed to the callback function
				'label_for' => 'enable_features',
			)
		);
	}

	/**
	 * Add link to configuration page in the network Settings menu
	 */
	public function add_settings_menu() {
		add_submenu_page(
			'settings.php',					// parent menu slug
			'Multisite Enhancements',		// page title
			'Multisite Enhancements',		// menu item title
			'manage_network_options',		// capabilities
			'wpme_config',		// menu slug
			array( $this, 'settings_page_callback' ) 	// callback to render the page HTML
		);
	}

	/**
	 * Render the configuration page HTML
	 * Notice the form 'action' which points to our custom URL
	 */
	public function settings_page_callback() {

		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}

		// add error/update messages

		// check if the user have submitted the settings
		// wordpress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['updated'] ) ) {
?>
			<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Settings saved', 'multisite-enhancements' ); ?></p></div>
<?php
		}

?>
		<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="edit.php?action=wpme_update_settings" method="post">
<?php
		// output security fields for our registered setting
		settings_fields( 'wpme_options' );
		// output setting sections and their fields
		do_settings_sections( 'wpme_config' );
		// output save settings button
		submit_button( __( 'Save settings', 'multisite-enhancements' ) );
?>
		</form>
		</div>
<?php
	}

	/**
	 * Configuration sections callback
	 */
	public function settings_section_callback( $args ) {
		if ( $args['id'] == 'wpme_general' ) {
			echo '<p>' . __( 'Check or uncheck the options below to enable or disable specific plugin features:', 'multisite-enhancements' ) . '</p>';
		}
	}

	/**
	 * Configuration fields callback
	 */
	public function settings_fields_callback( $args ) {

		$feature_settings = array(
			'remove-logo'         => __( 'Remove "W" logo menu from the admin top bar', 'multisite-enhancements' ),
			'add-favicon'         => __( 'Add sites favicons to admin area', 'multisite-enhancements' ),
			'add-blog-id'         => __( 'Add blog and user IDs to admin lists', 'multisite-enhancements' ),
			'add-css'             => __( 'Add custom CSS to allow showing or hiding the list of sites that uses a theme or plugin', 'multisite-enhancements' ),
			'add-plugin-list'     => __( 'On the network Plugins page, show which blogs have the plugin active', 'multisite-enhancements' ),
			'add-theme-list'      => __( 'On the network Themes page, show which blogs have the theme active', 'multisite-enhancements' ),
			'add-site-status'     => __( 'Add status labels for no-index and external domain to blogs in "My Sites" menu', 'multisite-enhancements' ),
			'add-ssl-identifier'  => __( 'Add an icon to identify the SSL protocol on each site in the network Sites page', 'multisite-enhancements' ),
			'add-manage-comments' => __( 'Add new "Manage Comments" item with count of comments waiting for moderation in "My Sites" menu', 'multisite-enhancements' ),
			'add-network-plugins' => __( 'Add a link to the Plugins page under Network Admin in "My Sites" menu', 'multisite-enhancements' ),
			'add-new-plugin'      => __( 'Enables an "Add New" link under the Plugins menu of each blog for Network admins.', 'multisite-enhancements' ),
			'filtering-themes'    => __( 'Add simple javascript to filter the theme list on network and single site theme page of WordPress back end', 'multisite-enhancements' ),
			'change-footer'       => __( 'Enhance the admin footer text with RAM, SQL queries and PHP version information', 'multisite-enhancements' ),
		);

		$options = self::get_settings();

		foreach( $feature_settings as $key => $description ) {
?>
			<p>
				<label>
					<input type="checkbox" name="wpme_options[<?php echo esc_attr( $key ); ?>]" <?php checked( $options[ $key ], 1 ); ?> value="1">
					<?php echo $description; ?>
				</label>
			</p>
<?php
		}
	}

	/**
	 * Save / update settings on the database
	 */
	public function update_settings() {
		// check the referer to make sure the data comes from our options page.
		check_admin_referer( 'wpme_options-options' );

		$options = $_POST['wpme_options'];

		foreach( array_keys( self::$default_options ) as $key ) {
			if ( ! isset( $options[ $key ] ) )
				$options[ $key ] = '0';
		}

		// update option on database
		update_site_option( 'wpme_options', $options );

		// redirect back to our options page
		wp_redirect(
			add_query_arg( array(
				'page' => 'wpme_config',
				'updated' => 'true'
			),
			network_admin_url( 'settings.php' )
		));
		exit;
	}

	/**
	 * Load settings from database and merge them with default values
	 *
 	 * @param string [$key] Key of an specific setting desired (optional).
	 *
	 * @return string|array Value of setting selected by $key or full array of settings.
	 */
	public static function get_settings( $key = NULL ) {
		$options = get_site_option( 'wpme_options' );
		$options = wp_parse_args( $options, self::$default_options );

		if ( isset( $key ) )
			return $options[ $key ];
		else
			return $options;
	}

} // end class
