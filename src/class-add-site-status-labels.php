<?php
/**
 * Add status labels to blogs.
 *
 * @since   2015-07-14
 * @version 2019-11-14
 * @package WordPress
 */

/**
 * Add status labels to sites.
 *
 * Class Multisite_Add_Site_Status_labels
 */
class Multisite_Add_Site_Status_labels {

	/**
	 * Initialize the class.
	 */
	public function init() {
		if ( ! current_user_can( 'manage_network' ) ) {
			return;
		}
		$this->add_hooks();
	}

	/**
	 * Installs required hooks.
	 */
	public function add_hooks() {
		add_filter( 'multisite_enhancements_add_admin_bar_favicon_css', array( $this, 'status_label_css' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_status_label' ) );
	}

	/**
	 * Check string, if is a external url.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The search string.
	 *
	 * @return bool
	 */
	public function check_external_url( $haystack, $needle ) {

		// Remove last string for exactly check.
		$needle = rtrim( $needle, '/' );

		return $needle && false === strpos(
			$haystack,
			str_replace( array( 'http://', 'https://', '//' ), '', $needle )
		);
	}

	/**
	 * Check, if the status of the site public.
	 *
	 * @param integer $site_id ID of the site.
	 *
	 * @return bool
	 */
	public function is_site_live( $site_id ) {
		$site_id = (int) $site_id;
		return (bool) get_blog_option( $site_id, 'blog_public' );
	}

	/**
	 * Add status label from each blog to Multisite Menu of "My Sites".
	 *
	 * Use the filter hook 'multisite_enhancements_status_label' to change style, dashicon, markup.
	 *
	 * @param WP_Admin_Bar $admin_bar All necessary admin bar items.
	 *
	 * @return mixed
	 */
	public function add_status_label( WP_Admin_Bar $admin_bar ) {
		foreach ( $admin_bar->user->blogs as $key => $blog ) {
			$url_hint  = '';
			$live_hint = '';

			if ( $this->check_external_url( $blog->siteurl, $admin_bar->user->domain ) ) {
				$title    = esc_attr__( 'external domain', 'multisite-enhancements' );
				$class    = 'site-status-icon ab-icon dashicons-before dashicons-external';
				$url_hint = '<span title="' . $title . '" class="' . $class . '"></span>';
			}

			if ( ! $this->is_site_live( $blog->userblog_id ) ) {
				$title     = esc_attr__( 'noindex', 'multisite-enhancements' );
				$class     = 'site-status-icon ab-icon dashicons-before dashicons-dismiss';
				$live_hint = '<span title="' . $title . '" class="' . $class . '"></span>';
			}

			// Add span markup.
			$blogname = $url_hint . $live_hint . $blog->blogname;

			// Filter hook for custom style of the admin bar site string.
			$blogname = apply_filters( 'multisite_enhancements_status_label', $blogname, $blog );

			$admin_bar->user->blogs[ $key ]->blogname = $blogname;
		}

		return $admin_bar;
	}

	/**
	 * Add required CSS for site status labels.
	 *
	 * @param string $style css content for inline style.
	 *
	 * @return string
	 */
	public function status_label_css( $style = '' ) {
		$style .= '#wp-admin-bar-my-sites-list .site-status-icon { padding: 4px 0 0 0 !important; margin: 0 4px 0 -4px !important; }';
		return $style;
	}
} // end class
