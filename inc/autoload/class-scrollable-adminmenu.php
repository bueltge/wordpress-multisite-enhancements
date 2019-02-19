<?php
add_action( 'init', array( 'Enqueue_scrollable_adminmenu_Style', 'init' ) );

class Enqueue_scrollable_adminmenu_Style {

	private $suffix;

	/**
	 * Init function to register all used hooks.
	 *
	 */
	public function __construct() {
		$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_style' ) );

	}

	/**
	 * Initialize the class.
	 */
	public static function init() {
		$class = __CLASS__;
		if ( empty( $GLOBALS[ $class ] ) ) {
			$GLOBALS[ $class ] = new $class;
		}
	}


	public function admin_enqueue_style() {
		$handle = 'enhance-adminmenu';
		
		wp_register_style(
			$handle . 'styles',
			plugins_url( MULTISITE_ENHANCEMENT_ASSETS . 'css/enhance-adminmenu' . $this->suffix . '.css', MULTISITE_ENHANCEMENT_BASE ),
			false );

		wp_enqueue_style( $handle . 'styles' );
	}

}
