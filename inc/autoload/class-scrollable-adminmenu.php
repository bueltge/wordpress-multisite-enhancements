<?php
namespace Bueltge\Multisite_Scrollable_AdminMenu;

add_action( 'admin_init', __NAMESPACE__ . '\\bootstrap' );

/**
 * Create the instance of this class.
 */
function bootstrap() {
	$multisite_scollable_adminmenu = new Multisite_Scrollable_AdminMenu();
	$multisite_scollable_adminmenu->init();
}

class Multisite_Scrollable_AdminMenu {

	/**
	 * @var string
	 */
	private $suffix;

	/**
	 * Init function to register all used hooks.
	 *
	 */
	public function init() {
		$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_style' ) );
	}

	/**
	 * Enqueue admin styles
	 */
	public function admin_enqueue_style() {
		$handle = 'enhance-adminmenu';
		
		wp_register_style(
			$handle,
			plugins_url( MULTISITE_ENHANCEMENT_ASSETS . 'css/enhance-adminmenu' . $this->suffix . '.css', MULTISITE_ENHANCEMENT_BASE ),
			false );

		wp_enqueue_style( $handle );
	}

}
