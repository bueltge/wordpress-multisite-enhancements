<?php
/**
 * Add status labels to blogs
 *
 * @since    14/07/2015
 * @version  dev
 */

add_action( 'init', array( 'Multisite_Add_Blog_Status_labels', 'init' ) );

class Multisite_Add_Blog_Status_labels {
	
	private $admin_color_scheme = false;
	
	public static function init() {

		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Init function to register all used hooks
	 *
	 * @since   dev
	 * @return \Multisite_Add_Blog_Status_labels
	 */
	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'print_admin_bar_blog_status_labels' ) );
	}
	
	
	/**
	 * Add status label from each blog to Multisite Menu of "My Sites"
	 *
	 * Use the filter hook to change style
	 *     Hook: multisite_enhancements_add_admin_bar_favicon
	 *
	 * @since   dev
	 * @return  none
	 */
	public function print_admin_bar_blog_status_labels( $admin_bar ) {

		if (current_user_can('manage_network')) {
				
			global $_wp_admin_css_colors;
			$admin_color_schemes = $_wp_admin_css_colors;
			$this->admin_color_scheme = $admin_color_schemes[get_user_option('admin_color')];
			
			foreach ($admin_bar->user->blogs as $key => $blog) {
				
				$prefix = '';
				
					$label = 'ext-domain';
					$color = 'inherit';
					if ($this->admin_color_scheme->colors[3]) {
						$color = $this->admin_color_scheme->colors[3];
					}
					if (strpos($blog->siteurl, str_replace(array('http://', 'https://', '//'), '', $admin_bar->user->domain)) === false) {
						$prefix .= '<span style="font-style: italic; font-weight: bold; line-height: 1; color: '.$color.';">['.$label.']</span> ';
					}
				
					$label = 'noindex';
					$color = 'inherit';
					if ($this->admin_color_scheme->colors[2]) {
						$color = $this->admin_color_scheme->colors[2];
					}
					$is_live = get_blog_option($blog->userblog_id, 'blog_public', '0'); // $blog->site_id (is altijd 1)
					if ($is_live == '0') {
						$prefix .= '<span style="font-style: italic; font-weight: bold; line-height: 1; color: '.$color.';">['.$label.']</span> ';
					}
				
				$blog->blogname = $prefix.$blog->blogname;
				$admin_bar->user->blogs[$key]->blogname = $blog->blogname;
			}
					
		}

	}
	
} // end class
