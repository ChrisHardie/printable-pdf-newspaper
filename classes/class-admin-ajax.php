<?php // phpcs:ignore

namespace PrintablePdfNewspaper\Admin;

/**
 * A class to handle various admin-ajax calls that do the heavy lifting.
 *
 * @package PrintablePdfNewspaper\Admin
 */
class Ajax {

	/**
	 * Ajax constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_ppn-load-terms', array( $this, 'ajax_load_terms' ) );
		add_action( 'wp_ajax_ppn-download-pdf', array( $this, 'ajax_download_pdf' ) );
		add_action( 'wp_ajax_ppn-save-pdf', array( $this, 'ajax_save_pdf' ) );
	}

	/**
	 * Ajax handler for «Download PDF» button
	 */
	public function ajax_download_pdf() {
		$result = array();

		check_admin_referer( 'ppn-generate', 'ppn-nonce' );
		check_ajax_referer( 'ppn-generate', 'ppn-nonce' );

		$configure = apply_filters( 'ppn_pdf_configuration', $_POST['configure'] );

		if ( is_array( $configure ) && ! empty( $configure['number'] ) ) {
			$post_query = new PostHandler( $configure );
			$pdf        = new PdfHandler( $configure, $post_query->get_post_data_for_pdf() );
			$pdf->attach_header_image( ! empty( $configure['image'] ) ? (int) $configure['image'] : 0 );
			$pdf->get_link( true );
			exit;
		}

		wp_send_json( $result );
	}

	/**
	 * Ajax handler for «Save PDF» button
	 */
	public function ajax_save_pdf() {
		$result = array();

		check_admin_referer( 'ppn-generate', 'ppn-nonce' );
		check_ajax_referer( 'ppn-generate', 'ppn-nonce' );

		$configure = apply_filters( 'ppn_pdf_configuration', $_POST['configure'] );

		if ( current_user_can( 'upload_files' ) && is_array( $configure ) && ! empty( $configure['number'] ) ) {
			$post_query = new PostHandler( $configure );
			$pdf        = new PdfHandler( $configure, $post_query->get_post_data_for_pdf() );
			$pdf->attach_header_image( ! empty( $configure['image'] ) ? (int) $configure['image'] : 0 );
			$filename    = $pdf->get_link();
			$wp_filetype = wp_check_filetype( basename( $filename ) );

			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			$attach_id   = wp_insert_attachment( $attachment, $filename );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			$result['response'] = true;
			$result['link']     = wp_get_attachment_url( $attach_id );
		}
		wp_send_json( $result );
	}

	/**
	 * Ajax handler for tags and categories
	 */
	public function ajax_load_terms() {

		check_admin_referer( 'ppn-generate', 'ppn-nonce' );
		check_ajax_referer( 'ppn-generate', 'ppn-nonce' );

		$post_type = ! empty( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : 'post';

		if ( 'category' === sanitize_text_field( $_GET['tax_type'] ) ) {
			$taxonomy = 'category';
		} else {
			$taxonomy = $post_type . '_tag';
		}

		$term_args = array(
			'taxonomy' => $taxonomy,
			'orderby'  => 'name',
			'order'    => 'ASC',
			'number'   => 100,
		);
		if ( ! empty( $_GET['q'] ) ) {
			$term_args['name__like'] = sanitize_text_field( $_GET['q'] );
		}
		$term_query = new \WP_Term_Query( $term_args );
		$result     = $term_query->get_terms();

		wp_send_json( $result );
	}
}
