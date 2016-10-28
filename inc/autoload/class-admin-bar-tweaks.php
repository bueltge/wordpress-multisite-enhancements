<?php
/**
 * Adds several useful items to the multisite 'Network Admin' admin bar.
 *
 * @since   2013-07-19
 * @version 2016-10-28
 * @package WordPress
 */

add_action( 'init', array( 'Multisite_Admin_Bar_Tweaks', 'init' ) );

/**
 * Class Multisite_Admin_Bar_Tweaks
 */
class Multisite_Admin_Bar_Tweaks {

	/**
	 * Initialize this class.
	 */
	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks.
	 *
	 * @since   0.0.1
	 */
	public function __construct() {

		add_action( 'wp_before_admin_bar_render', array( $this, 'enhance_network_admin_bar' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'enhance_network_blog_admin_bar' ) );
	}

	/**
	 * Enhance network item.
	 *
	 * @since   0.0.1
	 */
	public function enhance_network_admin_bar() {

		global $wp_admin_bar;

		// Show only when the user has at least one site, or they're a super admin.
		if ( count( $wp_admin_bar->user->blogs ) < 1 ) {
			return;
		}

		// Since WP version 3.7 is the plugin link in core.
		// Return, if is active.
		/**
		 * Toolbar API class.
		 *
		 * @var WP_Admin_Bar $wp_admin_bar
		 */
		$wp_admin_bar_nodes = (array) $wp_admin_bar->get_nodes();

		if ( array_key_exists( 'network-admin-p', $wp_admin_bar_nodes ) ) {
			return;
		}

		// Add a link to the Network > Plugins page.
		$wp_admin_bar->add_node(
			array(
				'parent' => 'network-admin',
				'id'     => 'network-admin-plugins',
				'title'  => __( 'Plugins' ),
				'href'   => network_admin_url( 'plugins.php' ),
			)
		);
	}

	/**
	 * Enhance each blog menu in network admin bar.
	 *
	 * Add new 'Manage Comment' Item with count of comments, there wait for moderate
	 *
	 * @since   0.0.1
	 */
	public function enhance_network_blog_admin_bar() {

		/**
		 * The Toolbar API class.
		 *
		 * @var WP_Admin_Bar $wp_admin_bar
		 */
		global $wp_admin_bar;

		foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {

			switch_to_blog( $blog->userblog_id );

			$menu_id = 'blog-' . $blog->userblog_id;

			if ( current_user_can( 'edit_posts' ) ) {

				$wp_admin_bar->remove_node( $menu_id . '-c' );

				$awaiting_mod = wp_count_comments();
				$awaiting_mod = $awaiting_mod->moderated;

				$title = __( 'Manage Comments' )
					. '<span class="ab-label awaiting-mod pending-count count-'
					. (int) $awaiting_mod . '" style="margin-left:.2em">' . number_format_i18n( $awaiting_mod ) . '</span>';

				$awaiting_title = esc_attr(
					sprintf(
						_n(
							'%s comment awaiting moderation',
							'%s comments awaiting moderation',
							$awaiting_mod
						), number_format_i18n( $awaiting_mod )
					)
				);

				$wp_admin_bar->add_menu(
					array(
						'parent' => $menu_id,
						'id'     => $menu_id . '-comments',
						'title'  => $title,
						'href'   => admin_url( 'edit-comments.php' ),
						'meta'   => array( 'title' => $awaiting_title ),
					)
				);

			}

			restore_current_blog();

		}
	}

} // end class
