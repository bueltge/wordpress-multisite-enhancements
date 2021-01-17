<?php
/**
 * Change/Enhance the admin footer text with RAM, SQL Queries and RAM version in Footer.
 *
 * Only for Admins
 *
 * Use the follow filter hooks for different changes
 *     Use the filter hook to change capability to view the new text on admin footer
 *     - Hook: multisite_enhancements_admin_footer_text_capability
 *     - default is: manage_options
 *     Reset of text from wp default
 *     - Hook: multisite_enhancements_reset_admin_footer_text
 *     - default is: TRUE
 *     Change content if admin footer text
 *     - Hook: multisite_enhancements_admin_footer_text
 *     - default is: Blog-Name, RAM, SQL, RAM Version
 *
 * @since   2013-07-23
 * @version 2016-10-28
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Change_Footer_Text', 'init' ) );

/**
 * Class Multisite_Change_Footer_Text
 */
class Multisite_Change_Footer_Text {

	/**
	 * Define the capability to view the new admin footer text.
	 *
	 * @since  0.0.2
	 * @var    String
	 */
	protected static $capability = 'manage_options';

	/**
	 * Filter to reset admin footer message.
	 *
	 * @since  0.0.2
	 * @var    Boolean
	 */
	protected static $reset_footer_text = true;

	/**
	 * Initialize the class.
	 */
	public static function init() {
		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			// phpcs:disable
			$GLOBALS[ $class ] = new $class();
			// phpcs:enable
		}
	}

	/**
	 * Init function to register all used hooks.
	 *
	 * Use the filter hook to change capability to view the new text on admin footer
	 *     Hook: multisite_enhancements_admin_footer_text_capability
	 *
	 * @since   0.0.2
	 */
	public function __construct() {

		/**
		 * Use this filter to change capability to view the new text on admin footer.
		 *
		 * @type string
		 */
		$capability = apply_filters(
			'multisite_enhancements_admin_footer_text_capability',
			self::$capability
		);

		// Get default content for non admins.
		if ( current_user_can( $capability ) ) {
			add_action( 'admin_footer_text', array( $this, 'get_footer_text' ) );
		}
	}

	/**
	 * Change admin footer text.
	 *
	 * Use the two different filters for change
	 *     Reset of text from wp default - Hook: multisite_enhancements_reset_admin_footer_text
	 *     Change content - Hook: multisite_enhancements_admin_footer_text
	 *
	 * @since   0.0.2
	 *
	 * @param String $footer_text The string for the footer to inform the users.
	 *
	 * @return String
	 */
	public function get_footer_text( $footer_text ) {

		/**
		 * Filter to reset admin footer message.
		 *
		 * @type string
		 */
		if ( apply_filters(
			'multisite_enhancements_reset_admin_footer_text',
			self::$reset_footer_text
		)
		) {
			$footer_text = '';
		}

		// Set string of admin area.
		$blogname = get_bloginfo( 'name' );
		if ( is_network_admin() ) {
			$blogname = ' ' . esc_html( $GLOBALS['current_site']->site_name );
		}

		$footer_text .= wp_html_excerpt( $blogname, 40, __( '&hellip;', 'multisite-enhancements' ) );
		$footer_text .= ' &bull; <abbr title="' . esc_html__(
			'Random-access memory',
			'multisite-enhancements'
		) . '">' . esc_html__(
			'RAM',
			'multisite-enhancements'
		) . '</abbr> ' . number_format_i18n(
			memory_get_peak_usage( true ) / 1024 / 1024,
			1
		) . esc_html__( '/', 'multisite-enhancements' ) . WP_MEMORY_LIMIT;
		$footer_text .= ' &bull; <abbr title="' . esc_html__(
			'Structured Query Language',
			'multisite-enhancements'
		) . '">' . esc_html__(
			'SQL',
			'multisite-enhancements'
		) . '</abbr> ' . $GLOBALS['wpdb']->num_queries;
		$footer_text .= ' &bull; <abbr title="' . esc_html__(
			'Version of PHP (Hypertext Preprocessor)',
			'multisite-enhancements'
		) . '">' . esc_html__(
			'PHPv',
			'multisite-enhancements'
		) . '</abbr> ' . PHP_VERSION;

		/**
		 * Filter for change content form other source.
		 *
		 * @type string
		 */
		return apply_filters( 'multisite_enhancements_admin_footer_text', $footer_text );
	}

} // end class
