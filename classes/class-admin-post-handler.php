<?php

/**
 * Class to handle content querying and processing
 *
 * @package PrintablePdfNewspaper\Admin
 */

namespace PrintablePdfNewspaper\Admin;

use WP_Post;

class PostHandler {

	/**
	 * @var array
	 */
	private $configure;

	/**
	 * @var WP_Post[]
	 */
	private $posts_for_pdf;

	/**
	 * PdfHandler constructor.
	 *
	 * @param array $configure
	 */
	public function __construct( array $configure ) {
		$this->configure = $configure;
		$this->set_posts( $configure );
	}

	/**
	 * Generate posts array
	 *
	 * @param array $configure
	 */
	private function set_posts( array $configure ) {
		if ( ! empty( $configure['number'] ) && (int) $configure['number'] >= 1 ) {
			$post_query          = $this->create_post_query( $configure );
			$this->posts_for_pdf = $post_query->get_posts();
		}
	}

	/**
	 * @return \stdClass
	 */
	public function get_post_config() {
		$config                = new \stdClass();
		$config->has_title     = ! empty( $this->configure['items']['title'] );
		$config->has_author    = ! empty( $this->configure['items']['author'] );
		$config->has_date      = ! empty( $this->configure['items']['date'] );
		$config->has_permalink = ! empty( $this->configure['items']['permalink'] );
		$config->has_image     = ! empty( $this->configure['items']['image'] );
		$config->has_excerpt   = ! empty( $this->configure['items']['excerpt'] );
		$config->length        = (int) filter_var( $this->configure['length'], FILTER_SANITIZE_NUMBER_INT );

		return $config;
	}

	/**
	 * Populate a content array for PDF document creation loop
	 *
	 * @return array
	 */
	public function get_post_data_for_pdf() {
		$posts  = array();
		$config = $this->get_post_config();

		foreach ( $this->posts_for_pdf as $post ) {
			$data              = array();
			$data['title']     = $config->has_title ? $post->post_title : null;
			$data['author']    = $config->has_author ? get_the_author_meta( 'display_name', $post->post_author ) : null;
			$data['date']      = $config->has_date ? get_post_time( get_option( 'date_format' ), false, $post, true ) : null;
			$data['permalink'] = $config->has_permalink ? get_permalink( $post ) : null;
			$data['image']     = $config->has_image ? get_the_post_thumbnail_url( $post->ID, 'large' ) : null;

			$content             = $config->has_excerpt && ! empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
			$data['content']     = $this->process_text( $content, $config->length, has_blocks( $post ) );
			$data['has_excerpt'] = $config->has_excerpt;
			$posts[]             = $data;
		}

		return $posts;
	}

	/**
	 * Make some adjustments to the text so it's more like a newspaper
	 * @param string $text
	 * @param int $length
	 * @param bool $has_blocks
	 *
	 * @return string
	 */
	protected function process_text( $text, $length = 0, $has_blocks = false ) {

		// Remove shortcodes
		$text = preg_replace( '~\[[^\]]+\]~', '', $text );

		// If this post uses blocks, look for multiple consecutive line breaks and remove.
		if ( $has_blocks ) {
			$text = preg_replace( '~[\n]{2}~m', '', $text );
		}

		// Remove everything except bold, italics, strikethrough, spans and brs.
		$text = trim( strip_tags( $text, '<b><strong><strike><em><i><u><s><span><br>' ) );

		// If we're left with actual content, truncate it to the user-specified length.
		if ( $length > 0 ) {
			$text = $this->truncate( $text, $length );
		}

		// Replace two line breaks with a single line break (for source readability) and an HTML line break.
		// TODO: one can end up in a situation of having multiple <br /> tags next to each other.
		$text = (string) str_replace( "\n\n", "\n<br />", $text );

		return $text;
	}

	/**
	 * Truncate a string to approximately the desired length stopping at the nearest end of a sentence or line.
	 * @source https://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara
	 *
	 * @param $string
	 * @param $your_desired_length
	 *
	 * @return string
	 */
	protected function truncate( $string, $your_desired_length ) {
		$parts       = preg_split( '/([\!\.\?\n\r]+)/u', $string, null, PREG_SPLIT_DELIM_CAPTURE );
		$parts_count = count( $parts );

		$length    = 0;
		$last_part = 0;
		for ( ; $last_part < $parts_count; ++ $last_part ) {
			$length += strlen( $parts[ $last_part ] );
			if ( $length > (int) $your_desired_length ) {
				break;
			}
		}

		return trim( implode( array_slice( $parts, 0, $last_part ) ) );
	}

	/**
	 * Query WordPress for the desired posts and related criteria
	 * @param array $configure
	 *
	 * @return string
	 */
	private function create_post_query( array $configure ) {

		$post_type      = ! empty( $configure['post_type'] ) ? sanitize_text_field( $configure['post_type'] ) : 'post';
		$posts_per_page = $configure['number'] ? (int) $configure['number'] : 10;

		$query_args = array(
			'posts_per_page' => $posts_per_page,
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'order'          => 'DESC',
			'orderby'        => 'date',
		);

		if ( $configure['category_id'] ) {
			$query_args['cat'] = (int) $configure['category_id'];
		}
		if ( $configure['tag_id'] ) {
			$query_args['tax_query'] = array(
				array(
					'terms'    => (int) $configure['tag_id'],
					'taxonomy' => $post_type . '_tag',
				),
			);
		}

		$query_args = apply_filters( 'ppn_post_query_args', $query_args );

		return new \WP_Query( $query_args );

	}
}
