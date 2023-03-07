<div class="wrap">
	<h2><?php echo vkExUnit_get_name(); ?> <?php _e( 'CSS Customize', 'vk-all-in-one-expansion-unit' ); ?></h2>
	<div class="fileedit-sub"></div>
	<?php echo $data['mess']; ?>
	<p><?php _e( 'You can add custom CSS here.', 'vk-all-in-one-expansion-unit' ); ?></p>
	<?php if ( get_locale() == 'ja' ) { ?>
		<p>CSSのカスタマイズについては、<a href="https://www.vektor-inc.co.jp/post/wordpress-css-customize-2020/" target="_blank">こちらのページ</a>を参照してください。</p>
	<?php } ?>
	<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>" method="post" id="template">
		<textarea name="bv-css-css" cols="70" rows="10" id="newcontent"><?php echo esc_attr( $data['customCss'] ); ?></textarea>
		<?php wp_nonce_field( 'biz-vektor-css-submit', 'biz-vektor-css-nonce' ); ?>
		<p class="submit">
			<input type="submit" name="bv-css-submit" class="button button-primary" value="<?php _e( 'Save CSS', 'vk-all-in-one-expansion-unit' ); ?>" />
		</p>
	</form>

</div>
