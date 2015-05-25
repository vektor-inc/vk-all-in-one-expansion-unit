<form method="post" action="options.php">
<?php
	settings_fields( 'biz_vektor_ga_options_fields' );
	$options = biz_vektor_get_ga_options();
	// $options_default = biz_vektor_get_sns_options_default();
/*-------------------------------------------*/
/*	Google Analytics
/*-------------------------------------------*/
?>
<div id="seoSetting" class="sectionBox">
<h3><?php _e('Google Analytics Settings', 'biz-vektor'); ?></h3>
<table class="form-table">
<!-- Google Analytics -->
<tr>
<th><?php _e('Google Analytics Settings', 'biz-vektor'); ?></th>
<td><?php _e('Please fill in the Google Analytics ID from the Analytics embed code used in the site.', 'biz-vektor'); ?><br />
<p>UA-<input type="text" name="biz_vektor_ga_options[gaID]" id="gaID" value="<?php echo esc_attr( $options['gaID'] ); ?>" style="width:90%;" /><br />
<?php _e('ex) ', 'biz-vektor') ;?>XXXXXXXX-X</p>

	<dl>
	<dt><?php _e('Please select the type of Analytics code . (If you are unsure you can skip this.)', 'biz-vektor'); ?></dt>
	<dd>
<?php
$biz_vektor_gaTypes = array(
	'gaType_normal' => __('To output only normal code (default)', 'biz-vektor'),
	'gaType_universal' => __('To output the Universal Analytics code', 'biz-vektor'),
	'gaType_both' => __('To output both types', 'biz-vektor')
	);
foreach( $biz_vektor_gaTypes as $biz_vektor_gaTypeValue => $biz_vektor_gaTypeLavel) {
	if ( $biz_vektor_gaTypeValue == $options['gaType'] ) { ?>
	<label><input type="radio" name="biz_vektor_ga_options[gaType]" value="<?php echo $biz_vektor_gaTypeValue ?>" checked> <?php echo $biz_vektor_gaTypeLavel ?></label><br />
	<?php } else { ?>
	<label><input type="radio" name="biz_vektor_ga_options[gaType]" value="<?php echo $biz_vektor_gaTypeValue ?>"> <?php echo $biz_vektor_gaTypeLavel ?></label><br />
	<?php }
}
?>
	</dd>
	</dl>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>
<div class="optionNav bottomNav">
<ul><li><a href="#wpwrap"><?php _e('Page top', 'biz-vektor'); ?></a></li></ul>
</div>
</form>