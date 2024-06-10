<?php
/**
 * Adds several useful items to the multisite 'Network Admin' admin bar.
 *
 * @since   2013-07-19
 * @version 2016-10-28
 * @package multisite-enhancements
 */

namespace Multisite_Enhancements;

/**
 * Class Admin_Bar_Tweaks
 */
class Admin_Bar_Tweaks {

	/**
	 * Initialize this class.
	 */
	public function init() {
		add_action( 'admin_bar_menu', array( $this, 'enhance_network_blog_admin_bar' ), 500 );
	}

	/**
	 * Enhance each blog menu in network admin bar.
	 *
	 * Add new 'Manage Comment' Item with count of comments, there wait for moderate
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar The admin bar object.
	 *
	 * @since   0.0.1
	 */
	public function enhance_network_blog_admin_bar( \WP_Admin_Bar $wp_admin_bar ) {

		if ( ! isset( $wp_admin_bar->user->blogs ) || ! Settings::is_feature_enabled( 'add-manage-comments' ) ) {
			return;
		}

		foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
			switch_to_blog( $blog->userblog_id );

			$menu_id = 'blog-' . $blog->userblog_id;

			if ( current_user_can( 'edit_posts' ) ) {
				$wp_admin_bar->remove_node( $menu_id . '-c' );

				$awaiting_mod = wp_count_comments();
				$awaiting_mod = $awaiting_mod->moderated;

				// phpcs:ignore WordPress.WP.I18n.MissingArgDomain
				$title = esc_html__( 'Manage Comments' )
						. '<span class="ab-label awaiting-mod pending-count count-'
						. (int) $awaiting_mod . '" style="margin-left:.2em">' . number_format_i18n( $awaiting_mod ) . '</span>';

				$awaiting_title = esc_attr(
					sprintf(
						// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment, WordPress.WP.I18n.MissingArgDomain
						_n(
							'%s comment awaiting moderation',
							'%s comments awaiting moderation',
							$awaiting_mod
						),
						number_format_i18n( $awaiting_mod )
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
