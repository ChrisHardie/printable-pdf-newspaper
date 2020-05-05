<?php

/**
 * A class that extends the TCPDF PDF object to define header and footer behavior.
 *
 * @package PrintablePdfNewspaper\Admin
 */

namespace PrintablePdfNewspaper\Admin;

class NewspaperPdf extends \TCPDF {

	/**
	 * The URL of a header image, if any.
	 * @var string
	 */
	private $image;

	/**
	 * Define the header image within the PDF object.
	 * @param string $image
	 */
	public function setHeaderImage( $image ) {
		$this->image = $image;
	}

	/**
	 * Set up PDF header behavior.
	 */
	public function Header() {

		// On the first page, add a header image or blog title, and a line.
		if ( 1 === $this->PageNo() ) {
			// Add an image if one is defined
			if ( ! empty( $this->image ) ) {
				$this->Image(
					$this->image, // Filename containing an image
					1, // Abscissa of the upper-left corner (LTR) or upper-right corner (RTL).
					3, // Ordinate of the upper-left corner (LTR) or upper-right corner (RTL).
					100, // Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
					25, // Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
					'JPG', // Image format. Possible values are (case insensitive): JPEG and PNG (whitout GD library)
					'', // URL or identifier returned by AddLink()
					'T', // Indicates the alignment of the pointer next to image insertion relative to image height. T = top-right
					false, // If true resize (reduce) the image to fit $w and $h (requires GD or ImageMagick library); if false do not resize
					300, // dot-per-inch resolution used on resize
					'C', // Allows to center or align the image on the current line. C = center
					false, // true if this image is a mask, false otherwise
					false, // image object returned by this function or false
					0, // Indicates if borders must be drawn around the cell
					'CM', // If not false scale image dimensions proportionally to fit within the ($w, $h) box. CM = center middle
					false, // If true do not display the image.
					false // If true the image is resized to not exceed page dimensions.
				);
			} else {
				// Otherwise, use the blog name in regular text.
				$this->SetY( 15 );
				$this->SetFont( 'lorab', '', 30 );
				$this->Cell(
					0, // Cell width. If 0, the cell extends up to the right margin.
					15, // Cell height. Default value: 0.
					esc_html( get_bloginfo( 'name' ) ), // String to print
					0, // Indicates if borders must be drawn around the cell.
					false, // Indicates where the current position should go after the call
					'C', // Allows to center or align the text.
					0, // Indicates if the cell background must be painted (true) or transparent (false).
					'', // URL or identifier returned by AddLink()
					1, // font stretch mode
					false, // if true ignore automatic minimum height value
					'C', // cell vertical alignment relative to the specified Y value,
					'C' // text vertical alignment inside the cell.
				);
			}

			// Add a line
			$this->Line(
				0, // Abscissa of first point.
				30, // Ordinate of first point.
				212, // Abscissa of second point.
				30, // Ordinate of second point.
				array( // Line style. Array like for SetLineStyle()
					'width' => 0.3,
					'cap'   => 'square', // Type of cap to put on the line. Possible values are butt, round, square. The difference between "square" and "butt" is that "square" projects a flat end past the end of the line.
					'join'  => 'miter', // Type of join. Possible values are: miter, round, bevel
					'dash'  => '0', // dash (mixed): Dash pattern. Is 0 (without dash) or string with series of length values, which are the lengths of the on and off dashes. For example: "2" represents 2 on, 2 off, 2 on, 2 off, ...; "2,1" is 2 on, 1 off, 2 on, 1 off, ...
					'color' => array( 102, 102, 102 ), // Draw color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K) or array(C,M,Y,K,SpotColorName)
				)
			);

			// Provide a sub-masthead with the date, site description and website address
			$mastsubhead_text  = '';
			$mastsubhead_text .= current_time( 'l, F j, Y' );
			$mastsubhead_text .= " \u{2022} " . get_bloginfo( 'description' );
			$mastsubhead_text .= " \u{2022} " . str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );
			$mastsubhead_text  = strtoupper( $mastsubhead_text );

			$this->SetY( 33 );
			$this->SetFont( 'helvetica', '', 8 );
			$this->Cell(
				0, // Cell width. If 0, the cell extends up to the right margin.
				5, // Cell height. Default value: 0.
				$mastsubhead_text, // String to print
				0, // Indicates if borders must be drawn around the cell.
				false, // Indicates where the current position should go after the call
				'C', // Allows to center or align the text.
				0, // Indicates if the cell background must be painted (true) or transparent (false).
				'', // URL or identifier returned by AddLink()
				0, // font stretch mode
				false, // if true ignore automatic minimum height value
				'C', // cell vertical alignment relative to the specified Y value,
				'C' // text vertical alignment inside the cell.
			);

			// Add another line
			$this->Line(
				0, // Abscissa of first point.
				36, // Ordinate of first point.
				212, // Abscissa of second point.
				37, // Ordinate of second point.
				array( // Line style. Array like for SetLineStyle()
					'width' => 0.3,
					'cap'   => 'square', // Type of cap to put on the line. Possible values are butt, round, square. The difference between "square" and "butt" is that "square" projects a flat end past the end of the line.
					'join'  => 'miter', // Type of join. Possible values are: miter, round, bevel
					'dash'  => '0', // dash (mixed): Dash pattern. Is 0 (without dash) or string with series of length values, which are the lengths of the on and off dashes. For example: "2" represents 2 on, 2 off, 2 on, 2 off, ...; "2,1" is 2 on, 1 off, 2 on, 1 off, ...
					'color' => array( 102, 102, 102 ), // Draw color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K) or array(C,M,Y,K,SpotColorName)
				)
			);


		}
	}

	/**
	 * Set up PDF footer behavior. Basically page numbers. Lot of code for some page numbers.
	 */
	public function Footer() {
		$this->SetY( - 8 );

		// Print page number
		$w_page = isset( $this->l['w_page'] ) ? $this->l['w_page'] . ' ' : '';

		$this->SetFont( 'roboto', '', 10 );

		if ( empty( $this->pagegroups ) ) {
			$pagenumtxt = $w_page . $this->getAliasNumPage() . '/' . $this->getAliasNbPages();
		} else {
			$pagenumtxt = $w_page . $this->getPageNumGroupAlias() . '/' . $this->getPageGroupAlias();
		}

		$html = $this->getAliasRightShift() . __( 'Page' ) . ' ' . $pagenumtxt;

		if ( $this->getRTL() ) {
         $this->SetX( $this->original_rMargin ); // phpcs:ignore -- Parent code style
		} else {
         $this->SetX( $this->original_lMargin ); // phpcs:ignore -- Parent code style
		}

		$this->Line(
			PDF_MARGIN_LEFT,
			$this->getPageHeight() - PDF_MARGIN_BOTTOM / 2,
			$this->getPageWidth() - absint( $this->GetStringWidth( $pagenumtxt ) ) - PDF_MARGIN_RIGHT,
			$this->getPageHeight() - PDF_MARGIN_BOTTOM / 2,
			array(
				'width' => 0.4,
				'cap'   => 'square',
				'join'  => 'miter',
				'dash'  => '0',
				'color' => array( 233, 233, 233 ),
			)
		);

		$this->writeHTMLCell(
			$this->getPageWidth() - PDF_MARGIN_RIGHT - 6,
			0,
			PDF_MARGIN_LEFT,
			$this->getPageHeight() - 10,
			$html,
			0,
			0,
			'',
			0,
			'R'
		);
	}
}
