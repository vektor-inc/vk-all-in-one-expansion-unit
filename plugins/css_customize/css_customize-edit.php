<div class="wrap">
	<h2><?php echo vkExUnit_get_name(); ?> <?php _e( 'CSS Customize', 'vkExUnit' ) ?></h2>
	<div class="fileedit-sub"></div>
	<?php echo $data['mess']; ?>
	<p><?php _e( 'You can add custom CSS here.', 'vkExUnit' );?></p>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="template">
		<textarea name="bv-css-css" cols="70" rows="10" id="newcontent"><?php echo esc_attr($data['customCss']); ?></textarea>
		<?php wp_nonce_field( 'biz-vektor-css-submit', 'biz-vektor-css-nonce'); ?>
		<p class="submit">
			<input type="submit" name="bv-css-submit" class="button button-primary" value="<?php _e( 'Save CSS', 'vkExUnit' ); ?>" />
		</p>
	</form>

</div>