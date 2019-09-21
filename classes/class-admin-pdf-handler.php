<?php

/**
 * Class to handle PDF generation
 *
 * @package PrintablePdfNewspaper\Admin
 */

namespace PrintablePdfNewspaper\Admin;

use TCPDF_FONTS;

class PdfHandler {

	/**
	 * @var array
	 */
	private $configure;

	/**
	 * @var array
	 */
	private $posts_to_include;

	/**
	 * @var string
	 */
	private $header;

	/**
	 * PdfHandler constructor.
	 *
	 * @param array $configure
	 * @param array $posts_to_include
	 */
	public function __construct( array $configure, array $posts_to_include ) {
		$this->configure        = $configure;
		$this->posts_to_include = $posts_to_include;
	}

	/**
	 * Called in the Ajax class to add a header image to the PDF object.
	 * @param int $image_id
	 */
	public function attach_header_image( $image_id ) {
		if ( ! empty( $image_id ) ) {
			$url = get_attached_file( $image_id );
			if ( in_array(
				mime_content_type( $url ),
				[
					'image/jpeg',
					'image/png',
				],
				true
			)
			) {
				$this->header = $url;
			}
		}
	}

	/**
	 * Find out where WordPress puts uploads
	 * @return string
	 */
	public function get_uploads_dir() {
		return ( (object) wp_upload_dir() )->path;
	}

	/**
	 * Get the user-specified number of columns, default to 1 if something out of range came through.
	 * @return int
	 */
	public function get_columns_count() {
		$columns = (int) $this->configure['columns'];

		return $columns >= 1 && $columns <= 3 ? $columns : 1;
	}

	/**
	 * Return the PDF contents as a file path
	 * @param bool $download
	 *
	 * @return string
	 */
	public function get_link( $download = false ) {
		$filename = $this->get_uploads_dir() . '/' . date( 'YmdHis' ) . '-posts.pdf';
		$this->create_output( $filename, $download );

		return $filename;
	}

	/**
	 * Generate the actual PDF contents
	 * @param $filename
	 * @param bool $download
	 */
	private function create_output( $filename, $download = false ) {
		/**
		 * PDF_PAGE_ORIENTATION: P=portrait, L=landscape. Default P.
		 * PDF_UNIT: pt=point, mm=millimeter, cm=centimeter, in=inch. Default mm.
		 * PDF_PAGE_FORMAT: A4, A5, etc. Default A4.
		 */
		$pdf = new NewspaperPdf( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

		// If a header url has been attached to the PDF object, set an image property used in the Header() method
		if ( $this->header ) {
			$pdf->setHeaderImage( $this->header );
		}

		// Include some fonts to be used
		TCPDF_FONTS::addTTFfont( plugin_dir_path( __DIR__ ) . 'assets/fonts/ttf/robotoregular.ttf' );
		TCPDF_FONTS::addTTFfont( plugin_dir_path( __DIR__ ) . 'assets/fonts/ttf/RobotoBold.ttf' );
		TCPDF_FONTS::addTTFfont( plugin_dir_path( __DIR__ ) . 'assets/fonts/ttf/RobotoRegularItalic.ttf' );
		TCPDF_FONTS::addTTFfont( plugin_dir_path( __DIR__ ) . 'assets/fonts/ttf/lorabold.ttf' );

		// Adjust font settings for each.
		// $family, $style, $size
		$pdf->SetFont( 'helvetica', '', 10 );
		$pdf->SetFont( 'roboto', '', 10 );
		$pdf->SetFont( 'robotob', 'bold', 10 );
		$pdf->SetFont( 'robotoi', 'italic', 10 );
		$pdf->SetFont( 'lorab', '', 10 );

		// Set up some footer properties
		$pdf->setFooterMargin( 25 );
		$pdf->setFooterFont( [ 'roboto', 'regular', 10 ] );

		// Margins defined in units defined above
		// 15 for top margin on pages 2+
		$pdf->SetMargins( 10, 15, 10 );
		$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );

		$pdf->AddPage();
		$align = '';

		// 40 margin on page 1
		$pdf->setPage( 1, true );
		$pdf->SetY( 40 );

		$col_count = $this->get_columns_count();
		$col_gap   = 5;

		$margins = $pdf->getMargins();

		if ( $col_count > 1 ) {
			$pdf->resetColumns();
			$width  = $pdf->getPageWidth() - $margins['left'] - $margins['right'];
			$width -= $col_gap * ( $col_count - 1 );

			$width /= $col_count;
			$pdf->setEqualColumns( $col_count, $width );
			$align = 'J';
		}

		$pdf->writeHTML(
			$this->render_template( $this->posts_to_include ),
			true,
			false,
			true,
			false,
			$align
		);

		// I - show in browser
		// F - save
		// D - download
		$mode = $download ? 'D' : 'F';

		$pdf->Output( $download ? date( 'YmdHis' ) . '-posts.pdf' : $filename, $mode );

	}

	/**
	 * Using our PHP template, do a loop and generate output that TCPDF can use in the writeHTML operation
	 * @param array $posts_to_include
	 *
	 * @return string
	 */
	private function render_template( $posts_to_include = null ) {

		ob_start();
		include plugin_dir_path( __DIR__ ) . 'views/admin/pdf/pdf-template.php';
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}
