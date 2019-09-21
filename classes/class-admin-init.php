<?php

namespace PrintablePdfNewspaper;

/**
 * A class to initialize the Admin functions (which are really the only functions) of the plugin.
 *
 * @package PrintablePdfNewspaper\Admin
 */
class Admin {

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		$this->register_actions();
		new Admin\Ajax();
	}

	/**
	 * Register action handlers:
	 *   Top-level Menu Item
	 *   Javascript and CSS
	 *
	 * @return void
	 */
	private function register_actions() {
		add_action( 'admin_menu', array( $this, 'ppn_register_menu_item' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ppn_enqueue_assets' ) );
	}

	/**
	 * Create a new top-level admin menu item for the plugin
	 *
	 * @return void
	 */
	public function ppn_register_menu_item() {
		add_menu_page(
			__( 'Printable PDF Newspaper', 'printable-pdf-newspaper' ),
			__( 'Printable PDF', 'printable-pdf-newspaper' ),
			'edit_posts',
			'printable-pdf-newspaper',
			function() {
				include plugin_dir_path( __DIR__ ) . 'views/admin/config-form.php';
			},
			'dashicons-media-document',
			80
		);
	}

	/**
	 * Enqueue necessary CSS and JS assets for the page.
	 * Select2 resource files can be updated in the future from https://github.com/select2/select2
	 *
	 * @return void
	 */
	public function ppn_enqueue_assets() {
		if ( get_current_screen()->id === 'toplevel_page_printable-pdf-newspaper' ) {
			wp_enqueue_script( 'select2-js', plugin_dir_url( __DIR__ ) . 'assets/admin/js/select2.min.js', true, '4.0.8', [ 'jquery' ] );
			wp_enqueue_script(
				'pdf-generator-js',
				plugin_dir_url( __DIR__ ) . 'assets/admin/js/pdf-generator.js',
				true,
				time(),
				[
					'jquery',
					'select2-js',
				]
			);
			wp_enqueue_media();
			wp_localize_script( 'pdf-generator-js', '_ajax', [ 'url' => admin_url( 'admin-ajax.php' ) ] );
			wp_enqueue_style( 'select2-css', plugin_dir_url( __DIR__ ) . 'assets/admin/css/select2.min.css', [], time() );
			wp_enqueue_style( 'pdf-generator-css', plugin_dir_url( __DIR__ ) . 'assets/admin/css/pdf-generator.css', [], time() );
		}
	}
}
