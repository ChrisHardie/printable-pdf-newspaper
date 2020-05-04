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

<style>
	h3{
		font-family: 'Lora-Bold', serif;
		font-size: 16pt;
		text-align: left;
	}

	p {
		font-size: 10pt;
		font-family: 'Roboto', sans-serif;
	}

	.author{
		color: rgb(102, 102, 102);
		font-family: 'Roboto', sans-serif;
	}

	.author strong{
		font-family: 'Roboto-Bold', sans-serif;
	}

	.date{
		color: #666;
		line-height: 100%;
	}

	.content, .excerpt {
		color: rgb(102, 102, 102);
		font-size: 10pt;
		font-family: 'Roboto', sans-serif;
	}

	.permalink-text {
		font-weight: bold;
	}

	.permalink-qr-code-image {
		text-align: center;
	}

</style>

<?php foreach ( $posts_to_include as $item ) : ?>
	<?php if ( $item['image'] ) : ?>
		<p><img src="<?php echo esc_url( $item['image'] ); ?>" alt=""></p>
	<?php endif; ?>
	<h3><?php echo esc_html( $item['title'] ); ?></h3>
	<?php if ( ! empty( $item['author'] ) || ! empty( $item['date'] ) ) : ?>
		<p class="meta">
			<?php if ( ! empty( $item['author'] ) ) : ?>
				<span class="author"><?php _e( 'By', 'printable-pdf-newspaper' ); ?> <strong><?php echo esc_html( $item['author'] ); ?></strong></span><br />
			<?php endif; ?>
			<?php if ( ! empty( $item['date'] ) ) : ?>
				<span class="date"><?php echo esc_html( $item['date'] ); ?></span>
			<?php endif; ?>
		</p>
	<?php endif; ?>
	<div class="<?php echo $item['has_excerpt'] ? 'excerpt' : 'content'; ?>">
		<?php echo wp_kses_post( $item['content'] ); ?>
		<?php if ( $item['permalink'] ) : ?>
			<br /><span class="permalink-text">&nbsp;&nbsp;&nbsp;<?php esc_attr_e( 'Continue&nbsp;Reading', 'printable-pdf-newspaper' ); ?>:</span><br/>
			<img src="https://chart.googleapis.com/chart?chs=50x50&chld=M|1&cht=qr&chl=<?php echo esc_url( $item['permalink'] ); ?>"
				 class="permalink-qr-code-image" width="50" height="50" />
		<?php endif; ?>
	</div>
	<p style="border-top: 1px dashed #d7d7d7;"></p>
<?php endforeach; ?>
