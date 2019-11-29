<?php
/**
 * Configuration page
 * Based on: https://vedovini.net/2015/10/using-the-wordpress-settings-api-with-network-admin-pages/
 */

add_action( 'init', array( 'Multisite_Enhancements_Settings', 'init' ) );

class Multisite_Enhancements_Settings {

	/**
	 * Feature settings
	 */
	static protected $feature_settings;

	/**
	 * Init function to register all used hooks.
	 */
	public function __construct() {
		// populate feature settings array
		self::$feature_settings = array(
			'remove-logo'   => __( 'Remove "W" logo menu from the admin top bar', 'multisite-enhancements' ),
			'add-favicon'   => __( 'Add sites favicons to admin area', 'multisite-enhancements' ),
			'change-footer' => __( 'Enhance the admin footer text with RAM, SQL queries and PHP version information', 'multisite-enhancements' ),
		);

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
			'wpme_options'	// database option name
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

		$options = get_site_option( 'wpme_options' );

		if ( $args['label_for'] == 'enable_features' ) {
			foreach( self::$feature_settings as $key => $description ) {
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
	}

	/**
	 * Save / update settings on the database
	 */
	public function update_settings() {
		// check the referer to make sure the data comes from our options page.
		check_admin_referer( 'wpme_options-options' );

		$options = $_POST['wpme_options'];

		foreach( array_keys( self::$feature_settings ) as $key ) {
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


} // end class
