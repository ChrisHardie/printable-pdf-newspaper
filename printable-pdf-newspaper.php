<?php
/**
 * Plugin Name:       Printable PDF Newspaper
 * Plugin URI:        https://github.com/ChrisHardie/printable-pdf-newspaper
 * Description:       Generate a Printable PDF Newspaper from Posts
 * Version:           1.1.2
 * Author:            Chris Hardie
 * Author URI:        https://chrishardie.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       printable-pdf-newspaper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'PrintablePdfNewspaper', false ) ) {
	class PrintablePdfNewspaper {

		/**
		 * Constructor method.
		 * Only does anything if the current user is an admin.
		 * Load Dependencies, instantiate the Admin class, set the text domain
		 */
		public function __construct() {
			if ( is_admin() ) {
				$this->load_dependencies();
				new PrintablePdfNewspaper\Admin();
			}
		}

		/**
		 * Load plugin dependencies
		 */
		private function load_dependencies() {
			if ( is_admin() ) {
				require_once plugin_dir_path( __FILE__ ) . 'lib/tcpdf/tcpdf.php';
				require_once plugin_dir_path( __FILE__ ) . 'classes/class-admin-init.php';
				require_once plugin_dir_path( __FILE__ ) . 'classes/class-admin-ajax.php';
				require_once plugin_dir_path( __FILE__ ) . 'classes/class-admin-pdf-handler.php';
				require_once plugin_dir_path( __FILE__ ) . 'classes/class-admin-post-handler.php';
				require_once plugin_dir_path( __FILE__ ) . 'classes/class-admin-newspaper-tcpdf.php';
			}
		}
	}
}

if ( ! function_exists( 'printable_pdf_newspaper_init' ) ) {
	/**
	 * Initialize the Printable PDF Newspaper plugin.
	 */
	function printable_pdf_newspaper_init() {
		new PrintablePdfNewspaper();
	}

	add_action( 'init', 'printable_pdf_newspaper_init', 9 );
}
