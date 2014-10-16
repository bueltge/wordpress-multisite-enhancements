<?php
/**
 * Adds several useful items to the multisite 'Network Admin' admin bar
 *
 * @since   07/19/2013
 * @version 01/03/2014
 */

add_action( 'init', array( 'Multisite_Admin_Bar_Tweaks', 'init' ) );

class Multisite_Admin_Bar_Tweaks {

	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks
	 *
	 * @since   0.0.1
	 * @return \Multisite_Admin_Bar_Tweaks
	 */
	public function __construct() {

		add_action( 'wp_before_admin_bar_render', array( $this, 'enhance_network_admin_bar' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'enhance_network_blog_admin_bar' ) );
	}

	/**
	 * Enhance network item
	 *
	 * @since   0.0.1
	 * @return  void
	 */
	public function enhance_network_admin_bar() {

		global $wp_admin_bar;

		// Show only when the user has at least one site, or they're a super admin.
		if ( count( $wp_admin_bar->user->blogs ) < 1 ) {
			return NULL;
		}

		// since WP version 3.7 is the plugin link in core.
		// return, if is active
		$wp_admin_bar_nodes = $wp_admin_bar->get_nodes();
		if ( isset( $wp_admin_bar_nodes[ 'network-admin-p' ] ) ) {
			return NULL;
		}

		// Add a link to the Network > Plugins page
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
	 * Enhance each blog menu in network admin bar
	 * Add new 'Manage Comment' Item with count of comments, there wait for moderate
	 *
	 * @since   0.0.1
	 * @return  void
	 */
	public function enhance_network_blog_admin_bar() {

		global $wp_admin_bar;

		foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {

			switch_to_blog( $blog->userblog_id );

			$menu_id = 'blog-' . $blog->userblog_id;

			if ( current_user_can( 'edit_posts' ) ) {

				$wp_admin_bar->remove_node( $menu_id . '-c' );

				$awaiting_mod = wp_count_comments();
				$awaiting_mod = $awaiting_mod->moderated;

				$title = __( 'Manage Comments' )
					. '<span id="ab-awaiting-mod" class="ab-label awaiting-mod pending-count count-'
					. intval( $awaiting_mod ) . '" style="margin-left:.2em">' . number_format_i18n( $awaiting_mod ) . '</span>';

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
