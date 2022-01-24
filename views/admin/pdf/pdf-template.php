<?php
/**
 * Printable PDF Page template
 *
 * Template for the plugin-generated printable PDF page.
 * Used by the render_template method in the PDF Handler class.
 * Note that TCPDF only supports a limited subset of CSS.
 *
 */

use PrintablePdfNewspaper\Admin\NewspaperPdf;

defined( 'WPINC' ) || die;
?>
<?php foreach ( $posts_to_include as $item ) : ?>
	<div class="ppn-article-wrapper">
		<?php if ( $item['image'] ) : ?>
			<p><img class="ppn-article-image" src="<?php echo esc_url( $item['image'] ); ?>" alt=""></p>
		<?php endif; ?>
		<h3 class="ppn-article-title"><?php echo esc_html( $item['title'] ); ?></h3>
		<?php if ( ! empty( $item['author'] ) || ! empty( $item['date'] ) ) : ?>
			<p class="meta ppn-meta">
				<?php if ( ! empty( $item['author'] ) ) : ?>
					<span class="author ppn-author"><?php esc_attr_e( 'By', 'printable-pdf-newspaper' ); ?> <strong><?php echo esc_html( $item['author'] ); ?></strong></span><br />
				<?php endif; ?>
				<?php if ( ! empty( $item['date'] ) ) : ?>
					<span class="date ppn-date"><?php echo esc_html( $item['date'] ); ?></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
		<div class="
		<?php
			echo $item['has_excerpt'] ? 'excerpt ppn-excerpt' : 'content ppn-content';
			echo is_rtl() ? ' ppn-content-rtl' : ' ppn-content-ltr';
		?>
		">
			<?php echo wp_kses_post( $item['content'] ); ?>
			<?php if ( $item['permalink'] ) : ?>
				<br /><span class="ppn-permalink-text"><?php esc_attr_e( 'Continue Reading', 'printable-pdf-newspaper' ); ?>:</span><br/>
				<img src="https://chart.googleapis.com/chart?chs=50x50&chld=M|1&cht=qr&chl=<?php echo esc_url( $item['permalink'] ); ?>"
					 class="ppn-permalink-qr-code-image" width="50" height="50" />
			<?php endif; ?>
		</div>
		<p class="ppn-article-bottom-border"></p>
	</div>
<?php endforeach; ?>
