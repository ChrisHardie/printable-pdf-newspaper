<?php defined( 'WPINC' ) || die; ?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_attr_e( 'Printable PDF Newspaper', 'printable-pdf-newspaper' ); ?></h1>
	<?php
	if ( ! empty( $_GET['ppn_is_error'] )
		&& isset( $_GET['nonce'] )
		&& wp_verify_nonce( $_GET['nonce'], 'ppn_error_nonce' ) ) {
		?>
		<div class="notice notice-error"><p>
			<?php
			esc_attr_e( 'There was a problem generating the PDF.', 'printable-pdf-newspaper' );
			if ( ! empty( $_GET['ppn_error_message'] ) ) {
				echo ' ';
				echo esc_html( $_GET['ppn_error_message'] );
			}
			?>
		</p></div>
		<?php
	}
	?>

	<div class="instructions">
<?php esc_attr_e( 'Configure how you want your printable PDF newspaper to look, and what content it should include.', 'printable-pdf-newspaper' ); ?>
	</div>
	<div class="ppn-container">
		<form id="ppn-pdf-form" action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=ppn-download-pdf' ) ); ?>" method="post" enctype="multipart/form-data">
			<div class="ppn-item">
				<label>
					<b><?php esc_attr_e( 'Which content type do you want to print', 'printable-pdf-newspaper' ); ?>?</b><br/>
				</label>
				<select name="configure[post_type]" id="ppn-post-type">
					<option></option>
					<?php
					foreach ( get_post_types( array( 'public' => true ), 'object' ) as $_type ) {
						if ( 'attachment' === $_type->name ) {
							continue;
						}
						?>
						<option <?php if ( 'post' === $_type->name ) { echo 'selected'; } ?> value="<?php echo esc_attr( $_type->name ); ?>"><?php echo esc_attr( $_type->label ); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="ppn-item ppn-item-helper">
				<div data-type="tag" class="ppn-item ppn-item-helper-tag">
					<label>
						<b><?php esc_attr_e( 'Only include content with a certain tag', 'printable-pdf-newspaper' ); ?>?</b><br/>
					</label>
					<select name="configure[tag_id]" id="ppn-tag-id">
						<option></option>
					</select>
				</div>
			</div>
			<div class="ppn-item ppn-item-helper">
				<div data-type="category" class="ppn-item ppn-item-helper-category">
					<label>
						<b><?php esc_attr_e( 'Only include content from a certain category', 'printable-pdf-newspaper' ); ?>?</b><br/>
					</label>
					<select name="configure[category_id]" id="ppn-category-id">
						<option></option>
					</select>
				</div>
			</div>
			<div class="ppn-item ppn-item-post">
				<label for="ppn-post-number">
					<b><?php esc_attr_e( 'Enter the number of posts to include', 'printable-pdf-newspaper' ); ?>:</b>
				</label>
				<input id="ppn-post-number" name="configure[number]" style="width: 55px" value="10" min="1" type="number">
			</div>
			<div class="ppn-item ppn-item-after-selection">
				<label for="ppn-content-length">
					<b><?php esc_attr_e( 'Truncate post content at', 'printable-pdf-newspaper' ); ?>:</b>
				</label>
				<input id="ppn-content-length" name="configure[length]" style="width: 80px;" value="500" min="1" type="number">
				<?php esc_attr_e( 'characters', 'printable-pdf-newspaper' ); ?>
				(<?php esc_attr_e( 'blank for full content', 'printable-pdf-newspaper' ); ?>)
			</div>
			<div class="ppn-item">
				<label>
					<b><?php esc_attr_e( 'Use excerpts instead of full post content', 'printable-pdf-newspaper' ); ?>?</b>
				</label>
				<input name="configure[items][excerpt]" type="checkbox">
			</div>
			<div class="ppn-item ppn-item-after-selection">
				<label for="ppn-content-columns">
					<b><?php esc_attr_e( 'Number of columns to use', 'printable-pdf-newspaper' ); ?> (1-3):</b>
				</label>
				<input id="ppn-content-columns" name="configure[columns]" style="width: 55px" value="3" min="1" max="3" type="number">
			</div>
			<div class="ppn-item ppn-item-after-selection">
				<label for="ppn-header-image">
					<b><?php esc_attr_e( 'Select a masthead image', 'printable-pdf-newspaper' ); ?>:</b>
				</label>
				<button id="upload_image_button" class="button" type="button">
					<?php esc_attr_e( 'Choose Image', 'printable-pdf-newspaper' ); ?>
				</button>
				&nbsp;&nbsp;<span id="image-url" style="display: none; margin-top: 10px">image</span>
				<input type="hidden" id="image_attachment_id" name="configure[image]">
			</div>
			<div class="ppn-item ppn-item-after-selection">
				<label for="ppn-custom-css">
					<b><?php esc_attr_e( 'Custom CSS', 'printable-pdf-newspaper' ); ?>:</b><br />
					<a href="https://wordpress.org/plugins/printable-pdf-newspaper/#faq-header" target="_blank"><?php esc_attr_e( 'Help' ); ?></a>
				</label>
				<textarea id="ppn-custom-css" name="configure[custom_css]" rows="10" cols="55"></textarea>
			</div>
			<div class="ppn-item ppn-item-after-selection ppn-include-items">
				<label for="ppn-items"><b><?php esc_attr_e( 'Which items should be included', 'printable-pdf-newspaper' ); ?>?</b></label>
				<div class="ppn-item-list">
				<ul>
					<li>
						<input checked name="configure[items][title]" type="checkbox">
						<?php esc_attr_e( 'Title', 'printable-pdf-newspaper' ); ?>
					</li>
				<li>
						<input checked name="configure[items][author]" type="checkbox">
						<?php esc_attr_e( 'Author', 'printable-pdf-newspaper' ); ?>
				</li>
				<li>
						<input checked name="configure[items][date]" type="checkbox">
						<?php esc_attr_e( 'Date', 'printable-pdf-newspaper' ); ?>
				</li>
				<li>
						<input checked name="configure[items][image]" type="checkbox">
						<?php esc_attr_e( 'Featured image', 'printable-pdf-newspaper' ); ?>
				</li>
				<li>
						<input checked name="configure[items][permalink]" type="checkbox">
						<?php esc_attr_e( 'Permalink QR Code', 'printable-pdf-newspaper' ); ?>
				</li>
				</ul>
				</div>
			</div>


			<div class="ppn-item">
				<button
						id="ppn-button-pdf-save"
						type="button"
						class="button button-primary"><?php esc_attr_e( 'Save PDF to Media Library', 'printable-pdf-newspaper' ); ?></button>
				OR
				<button
						id="ppn-button-pdf-download"
						class="button button-primary"><?php esc_attr_e( 'Download PDF', 'printable-pdf-newspaper' ); ?></button>
				<button id="ppn-button-pdf-view" type="button" style="display:none;" class="button">View Media</button>
				<span style="margin-top: 5px" class="dashicons dashicons-yes ppn-item-hidden ppn-icon-success"></span>
			</div>
			<?php wp_nonce_field( 'ppn-generate', 'ppn-nonce' ); ?>
		</form>
	</div>
</div>

