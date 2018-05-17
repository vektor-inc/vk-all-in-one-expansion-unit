<?php
	$options = vkExUnit_get_ga_options();
	// $options_default = veu_get_sns_options_default();
/*-------------------------------------------*/
/*  Google Analytics
/*-------------------------------------------*/
?>
<div id="seoSetting" class="sectionBox">
<h3><?php _e( 'Google Analytics Settings', 'vkExUnit' ); ?></h3>
<table class="form-table">
<!-- Google Analytics -->
<tr>
<th><?php _e( 'Google Analytics Settings', 'vkExUnit' ); ?></th>
<td><?php _e( 'Please fill in the Google Analytics ID from the Analytics embed code used in the site.', 'vkExUnit' ); ?><br />
<p>UA-<input type="text" name="vkExUnit_ga_options[gaId]" id="gaId" value="<?php echo esc_attr( $options['gaId'] ); ?>" style="width:90%;" /><br />
<?php _e( 'ex) ', 'vkExUnit' );?>XXXXXXXX-X</p>

    <dl>
	<dt><?php _e( 'Please select the type of Analytics code . (If you are unsure you can skip this.)', 'vkExUnit' ); ?></dt>
    <dd>
<?php
$vkExUnit_gaTypes = array(
	'gaType_gtag'      => __( 'Recommendation ( gtag )', 'vkExUnit' ),
	'gaType_universal' => __( 'Universal Analytics code ( analytics.js )', 'vkExUnit' ),
	'gaType_normal'    => __( 'Normal code ( analytics.js )', 'vkExUnit' ),
	);
foreach ( $vkExUnit_gaTypes as $vkExUnit_gaTypeValue => $vkExUnit_gaTypeLavel ) {
	if ( $vkExUnit_gaTypeValue == $options['gaType'] ) { ?>
		<label><input type="radio" name="vkExUnit_ga_options[gaType]" value="<?php echo $vkExUnit_gaTypeValue ?>" checked> <?php echo $vkExUnit_gaTypeLavel ?></label><br />
	<?php } else { ?>
		<label><input type="radio" name="vkExUnit_ga_options[gaType]" value="<?php echo $vkExUnit_gaTypeValue ?>"> <?php echo $vkExUnit_gaTypeLavel ?></label><br />
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
