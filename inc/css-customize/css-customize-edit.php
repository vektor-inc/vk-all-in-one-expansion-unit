<div class="wrap">
	<h2><?php echo esc_html( vkExUnit_get_name() ); ?> <?php esc_html_e( 'CSS Customize', 'vk-all-in-one-expansion-unit' ); ?></h2>
	<div class="fileedit-sub"></div>
	<?php echo wp_kses_post( $data['mess'] ); ?>
	<p><?php esc_html_e( 'You can add custom CSS here.', 'vk-all-in-one-expansion-unit' ); ?></p>
	<?php if ( get_locale() == 'ja' ) { ?>
		<p>CSSのカスタマイズについては、<a href="https://www.vektor-inc.co.jp/post/wordpress-css-customize-2020/" target="_blank">こちらのページ</a>を参照してください。</p>
	<?php } ?>
	<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>" method="post" id="template">
		<textarea name="bv-css-css" cols="70" rows="10" id="newcontent"><?php echo esc_attr( $data['customCss'] ); ?></textarea>
		<?php wp_nonce_field( 'biz-vektor-css-submit', 'biz-vektor-css-nonce' ); ?>
		<p class="submit">
			<input type="submit" name="bv-css-submit" class="button button-primary" value="<?php esc_attr_e( 'Save CSS', 'vk-all-in-one-expansion-unit' ); ?>" />
		</p>
	</form>

</div>
