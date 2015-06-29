<div class="wrap">
	<h2><?php echo vkExUnit_get_name(); ?> <?php _e( 'CSS Customize', 'biz-vektor' ) ?></h2>
	<div class="fileedit-sub"></div>
	<?php echo $data['mess']; ?>
	<p><?php _e( 'You can add custom CSS here.', 'biz-vektor' );?></p>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="template">
		<textarea name="bv-css-css" cols="70" rows="10" id="newcontent"><?php echo esc_attr($data['customCss']); ?></textarea>
		<?php wp_nonce_field( 'biz-vektor-css-submit', 'biz-vektor-css-nonce'); ?>
		<p class="submit">
			<input type="submit" name="bv-css-submit" class="button button-primary" value="<?php _e( 'Save CSS', 'biz-vektor' ); ?>" />
		</p>
	</form>

<?php /*
<div id="tipsList">
<h3><?php _e( 'Examples of design customization', 'biz-vektor' ); ?></h3>
<ul>
</ul>
</div>

<div id="tipsBody">

<dl id="mainContentWidth">
<dt><?php _e( 'Increase the main area width on desktop screens', 'biz-vektor' ); ?></dt>
<dd>
<code>
@media (min-width: 970px) {<br>
#main #container #content { width:680px; }<br>
}
</code>
</dd>
</dl>

<dl id="sideBarWidth">
<dt><?php _e( 'Increase the sidebar width on desktop screens', 'biz-vektor' ); ?></dt>
<dd>
<code>
@media (min-width: 970px) {<br>
#main #container #sideTower { width:260px; }<br>
}
</code>
</dd>
</dl>

<dl id="postThumbnailLeft">
<dt><?php _e( 'Set the thumbnail images on the left side', 'biz-vektor' ); ?></dt>
<dd>
<code>#content .infoList .infoListBox div.thumbImage { float:left; }<br>
#content .infoList .infoListBox div.entryTxtBox.haveThumbnail { float:right }
</code>
</dd>
</dl>

<dl id="postThumbnailLeft">
<dt><?php _e( 'Make the thumbnail images bigger', 'biz-vektor' ); ?></dt>
<dd>
<code>
#content .infoList .infoListBox div.thumbImage div.thumbImageInner img { width:120px; }
</code>
</dd>
</dl>

<dl id="postThumbnailLeft">
<dt><?php _e( 'Make the content paragraphs text bigger', 'biz-vektor' ); ?></dt>
<dd>
<code>
#content p { font-size:16px; }
</code>
</dd>
</dl>

</div><!-- [ /#tipsBody ] -->
*/ ?>
</div>