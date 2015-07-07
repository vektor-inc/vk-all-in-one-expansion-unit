<?php 

/*-------------------------------------------*/
/*	PR area widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_3PR_area extends WP_Widget {
    
    function WP_Widget_vkExUnit_3PR_area() {
		$widget_ops = array(
			'classname' => 'WP_Widget_vkExUnit_3PR_area',
			'description' => __( "Displays a 3PR area", 'vkExUnit' ),
		);
		$widget_name = vkExUnit_get_short_name(). '_' . __( "3PR area", 'vkExUnit' );
		$this->WP_Widget('WP_Widget_vkExUnit_3PR_area', $widget_name, $widget_ops);
	}
    function form($instance){
        $defaults = array(
			'label_1' => __( '3PR area1 title', 'vkExUnit' ),
            'media_3pr_image_1' => '',
            'media_3pr_alt_1' => '',
            'media_3pr_image_sp_1' => '',
            'media_3pr_alt_sp_1' => '',
            'summary_1' => '',
            'linkurl_1' => '',
            
            'label_2' => __( '3PR area2 title', 'vkExUnit' ),
            'media_3pr_image_2' => '',
            'media_3pr_alt_2' => '',
            'media_3pr_image_sp_2' => '',
            'media_3pr_alt_sp_2' => '',
            'summary_2' => '',
            'linkurl_2' => '',
            
            'label_3' => __( '3PR area3 title', 'vkExUnit' ),
            'media_3pr_image_3' => '',
            'media_3pr_alt_3' => '',
            'media_3pr_image_sp_3' => '',
            'media_3pr_alt_sp_3' => '',
            'summary_3' => '',
            'linkurl_3' => ''
		);
        $instance = wp_parse_args((array) $instance, $defaults);
    ?>
        	
<?php // 3PR area 1 =========================================================== ?>
		<?php // 3PR area 1 タイトル ?>
		<h5 class="pr_subTitle"><?php _e( '3PR area1 setting', 'vkExUnit' ); ?></h5>
		<p>
			<label for="<?php echo $this->get_field_id('label_1');  ?>"><?php _e( 'Title:', 'vkExUnit' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id('label_1'); ?>-title" class="pr-input" name="<?php echo $this->get_field_name('label_1'); ?>" value="<?php echo $instance['label_1']; ?>" />
		</p>
		
		<?php // 3PR area 1 メディアアップローダー PC ?>
		<p>
			<label for="<?php echo $this->get_field_id('media_3pr_image_1');  ?>"><?php _e( 'Select image for PC:', 'vkExUnit' ); ?></label><br/>
	
			<input type="hidden" class="media_image_3pr_pc <?php echo $this->get_field_id('media_3pr_image_1');  ?>" id="<?php echo $this->get_field_id('media_3pr_image_1'); ?>-image" name="<?php echo $this->get_field_name('media_3pr_image_1'); ?>" value="<?php echo esc_attr($instance['media_3pr_image_1']); ?>" />
		
			<input type="hidden" class="media_alt_3pr_pc" id="<?php echo $this->get_field_id('media_3pr_alt_1'); ?>-alt" name="<?php echo $this->get_field_name('media_3pr_alt_1'); ?>" value="<?php echo esc_attr($instance['media_3pr_alt_1']); ?>" />
		        
			<input type="button" class="media_select select_3pr" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3pr(event.target);" />
			<input type="button" class="media_clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3pr(event.target);" />
        </p>
        <div class="media image_3pr">
	        <?php if(!empty($instance['media_3pr_image_1'])): ?>
	        <img class="media_image image_3pr" src="<?php echo esc_url($instance['media_3pr_image_1']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_1']); ?>" />
	        <?php endif; ?>
        </div>

        <?php // 3PR area 1 メディアアップローダー sp image ?>
        <p>
	        <label for="<?php echo $this->get_field_id('media_3pr_image_sp_1');  ?>"><?php _e( 'Select image for Mobile:', 'vkExUnit' ); ?></label><br/>
		
			<input type="hidden" class="media_image_3pr_sp" id="<?php echo $this->get_field_id('media_3pr_image_sp_1'); ?>_image" name="<?php echo $this->get_field_name('media_3pr_image_sp_1'); ?>" value="<?php echo esc_attr($instance['media_3pr_image_sp_1']); ?>" />
		
			<input type="hidden" class="media_alt_3pr_sp" id="<?php echo $this->get_field_id('media_3pr_alt_sp_1'); ?>-alt" name="<?php echo $this->get_field_name('media_3pr_alt_sp_1'); ?>" value="<?php echo esc_attr($instance['media_3pr_alt_sp_1']); ?>" />
		        
			<input type="button" class="media_select" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3prSP(event.target);" />
			<input type="button" class="media_clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3prSP(event.target);" />
        </p>
        <div class="media image_3pr_sp">
	        <?php if(!empty($instance['media_3pr_image_sp_1'])): ?>
	        <img class="media_image image_3pr_sp" src="<?php echo esc_url($instance['media_3pr_image_sp_1']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_1']); ?>" />
	        <?php endif; ?>
        </div>
		
		<?php // 概要テキスト ?>
		<p><label for="<?php echo $this->get_field_id('summary_1');  ?>"><?php _e( 'Summary Text:', 'vkExUnit' ); ?></label><br/>
		</p>
		
		<textarea rows="4" cols="40" id="<?php echo $this->get_field_id('summary_1'); ?>_text" class="pr_input textarea" name="<?php echo $this->get_field_name('summary_1'); ?>"><?php echo $instance['summary_1']; ?></textarea>
		
		<?php // リンク先_URL ?>
		<p><label for="<?php echo $this->get_field_id('linkurl_1');  ?>"><?php _e( 'Link URL:', 'vkExUnit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('linkurl_1'); ?>_title" class="pr_input" name="<?php echo $this->get_field_name('linkurl_1'); ?>" value="<?php echo $instance['linkurl_1']; ?>" />
		</p>
		
<hr /><?php // 3PR area 2 =================================================?>
		<?php // 3PR area 2 タイトル ?>
		<h5 class="pr_subTitle"><?php _e( '3PR area2 setting', 'vkExUnit' ); ?></h5>
		<p>
			<label for="<?php echo $this->get_field_id('label_2');  ?>"><?php _e( 'Title:', 'vkExUnit' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id('label_2'); ?>_title" class="pr_input" name="<?php echo $this->get_field_name('label_2'); ?>" value="<?php echo $instance['label_2']; ?>" />
		</p>
		
		<?php // 3PR area 1 メディアアップローダー PC ?>
		<p>
			<label for="<?php echo $this->get_field_id('media_3pr_image_2');  ?>"><?php _e( 'Select image for PC:', 'vkExUnit' ); ?></label><br/>
	
			<input type="hidden" class="media_image_3pr_pc <?php echo $this->get_field_id('media_3pr_image_2');  ?>" id="<?php echo $this->get_field_id('media_3pr_image_2'); ?>_image" name="<?php echo $this->get_field_name('media_3pr_image_2'); ?>" value="<?php echo esc_attr($instance['media_3pr_image_2']); ?>" />
		
			<input type="hidden" class="media_alt_3pr_pc" id="<?php echo $this->get_field_id('media_3pr_alt_2'); ?>_alt" name="<?php echo $this->get_field_name('media_3pr_alt_2'); ?>" value="<?php echo esc_attr($instance['media_3pr_alt_2']); ?>" />
		        
			<input type="button" class="media_select select_3pr" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3pr(event.target);" />
			<input type="button" class="media_clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3pr(event.target);" />
        </p>
        
        <div class="media image_3pr">
	        <?php if(!empty($instance['media_3pr_image_2'])): ?>
	        <img class="media_image image_3pr" src="<?php echo esc_url($instance['media_3pr_image_2']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_2']); ?>" />
	        <?php endif; ?>
        </div>
        
        <?php // 3PR area 2 メディアアップローダー sp  ?>
        <p>
	        <label for="<?php echo $this->get_field_id('media_3pr_image_sp_2');  ?>"><?php _e( 'Select image for Mobile:', 'vkExUnit' ); ?></label><br/>
		
			<input type="hidden" class="media_image_3pr_sp" id="<?php echo $this->get_field_id('media_3pr_image_sp_2'); ?>_image" name="<?php echo $this->get_field_name('media_3pr_image_sp_2'); ?>" value="<?php echo esc_attr($instance['media_3pr_image_sp_2']); ?>" />
		
			<input type="hidden" class="media_alt_3pr_sp" id="<?php echo $this->get_field_id('media_3pr_alt_sp_2'); ?>_alt" name="<?php echo $this->get_field_name('media_3pr_alt_sp_2'); ?>" value="<?php echo esc_attr($instance['media_3pr_alt_sp_2']); ?>" />
		        
			<input type="button" class="media_select" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3prSP(event.target);" />
			<input type="button" class="media_clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3prSP(event.target);" />
        </p>
        <div class="media image_3pr_sp">
	        <?php if(!empty($instance['media_3pr_image_sp_2'])): ?>
	        <img class="media_image image_3pr_sp" src="<?php echo esc_url($instance['media_3pr_image_sp_2']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_2']); ?>" />
	        <?php endif; ?>
        </div>
		
		<?php //概要テキスト ?>
		<p><label for="<?php echo $this->get_field_id('summary_2');  ?>"><?php _e( 'Summary Text:', 'vkExUnit' ); ?></label></p>
		<textarea rows="4" cols="40" id="<?php echo $this->get_field_id('summary_2'); ?>_text" class="pr_input textarea" name="<?php echo $this->get_field_name('summary_2'); ?>"><?php echo $instance['summary_2']; ?></textarea>
		
		<?php //リンク先_URL ?>
		<p><label for="<?php echo $this->get_field_id('linkurl_2');  ?>"><?php _e( 'Link URL:', 'vkExUnit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('linkurl_2'); ?>_title" class="pr_input" name="<?php echo $this->get_field_name('linkurl_2'); ?>" value="<?php echo $instance['linkurl_2']; ?>" />
		</p>
	
<hr /><?php // 3PR area 3  =================================================?>
		<?php // 3PR area 3 タイトル ?>
		<h5 class="pr_subTitle"><?php _e( '3PR area3 setting', 'vkExUnit' ); ?></h5>
		<p>
			<label for="<?php echo $this->get_field_id('label_3');  ?>"><?php _e( 'Title:', 'vkExUnit' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id('label_3'); ?>_title" class="pr_input" name="<?php echo $this->get_field_name('label_3'); ?>" value="<?php echo $instance['label_3']; ?>" />
		</p>
		
		<?php // 3PR area 1 メディアアップローダー PC ?>
		<p>
			<label for="<?php echo $this->get_field_id('media_3pr_image_3');  ?>"><?php _e( 'Select image for PC:', 'vkExUnit' ); ?></label><br/>
	
			<input type="hidden" class="media_image_3pr_pc <?php echo $this->get_field_id('media_3pr_image_3');  ?>" id="<?php echo $this->get_field_id('media_3pr_image_3'); ?>_image" name="<?php echo $this->get_field_name('media_3pr_image_3'); ?>" value="<?php echo esc_attr($instance['media_3pr_image_3']); ?>" />
		
			<input type="hidden" class="media_alt_3pr_pc" id="<?php echo $this->get_field_id('media_3pr_alt_3'); ?>-alt" name="<?php echo $this->get_field_name('media_3pr_alt_3'); ?>" value="<?php echo esc_attr($instance['media_3pr_alt_3']); ?>" />
		        
			<input type="button" class="media_select select_3pr" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3pr(event.target);" />
			<input type="button" class="media_clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3pr(event.target);" />
        </p>
        <div class="media image_3pr">
	        <?php if(!empty($instance['media_3pr_image_3'])): ?>
	        <img class="media_image image_3pr" src="<?php echo esc_url($instance['media_3pr_image_3']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_3']); ?>" />
	        <?php endif; ?>
        </div>
        
        <?php // 3PR area 2 メディアアップローダー sp  ?>
        <p>
	        <label for="<?php echo $this->get_field_id('media_3pr_image_sp_3');  ?>"><?php _e( 'Select image for Mobile:', 'vkExUnit' ); ?></label><br/>
		
			<input type="hidden" class="media_image_3pr_sp" id="<?php echo $this->get_field_id('media_3pr_image_sp_3'); ?>_image" name="<?php echo $this->get_field_name('media_3pr_image_sp_3'); ?>" value="<?php echo esc_attr($instance['media_3pr_image_sp_3']); ?>" />
		
			<input type="hidden" class="media_alt_3pr_sp" id="<?php echo $this->get_field_id('media_3pr_alt_sp_3'); ?>_alt" name="<?php echo $this->get_field_name('media_3pr_alt_sp_3'); ?>" value="<?php echo esc_attr($instance['media_3pr_alt_sp_3']); ?>" />
		        
			<input type="button" class="media_select" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3prSP(event.target);" />
			<input type="button" class="media_clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3prSP(event.target);" />
        </p>
        
        <div class="media image_3pr_sp">
	        <?php if(!empty($instance['media_3pr_image_sp_3'])): ?>
	        <img class="media_image image_3pr_sp" src="<?php echo esc_url($instance['media_3pr_image_sp_3']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_3']); ?>" />
	        <?php endif; ?>
        </div>
		
		<?php //概要テキスト ?>
		<p><label for="<?php echo $this->get_field_id('summary_3');  ?>"><?php _e( 'Summary Text:', 'vkExUnit' ); ?></label>
		</p>
		<textarea rows="4" cols="40" id="<?php echo $this->get_field_id('summary_3'); ?>_text" class="pr_input textarea" name="<?php echo $this->get_field_name('summary_3'); ?>"><?php echo $instance['summary_3']; ?></textarea>
		
		<?php //リンク先_URL ?>
		<p><label for="<?php echo $this->get_field_id('linkurl_3');  ?>"><?php _e( 'Link URL:', 'vkExUnit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('linkurl_3'); ?>_title" class="pr_input" name="<?php echo $this->get_field_name('linkurl_3'); ?>" value="<?php echo $instance['linkurl_3']; ?>" />
		</p>

 <?php  } 
    
    function update ($new_instance, $old_instance) {

        $instance = $old_instance;
        
        $instance['label_1'] = $new_instance['label_1'];
        $instance['media_3pr_image_1'] = $new_instance['media_3pr_image_1'];
        $instance['media_3pr_alt_1'] = $new_instance['media_3pr_alt_1'];
        $instance['media_3pr_image_sp_1'] = $new_instance['media_3pr_image_sp_1'];
        $instance['media_3pr_alt_sp_1'] = $new_instance['media_3pr_alt_sp_1'];
        $instance['summary_1'] = $new_instance['summary_1'];
        $instance['linkurl_1'] = $new_instance['linkurl_1'];
        
        $instance['label_2'] = $new_instance['label_2'];
        $instance['media_3pr_image_2'] = $new_instance['media_3pr_image_2'];
        $instance['media_3pr_alt_2'] = $new_instance['media_3pr_alt_2'];
        $instance['media_3pr_image_sp_2'] = $new_instance['media_3pr_image_sp_2'];
        $instance['media_3pr_alt_sp_2'] = $new_instance['media_3pr_alt_sp_2'];
        $instance['summary_2'] = $new_instance['summary_2'];
        $instance['linkurl_2'] = $new_instance['linkurl_2'];
        
        $instance['label_3'] = $new_instance['label_3'];
        $instance['media_3pr_image_3'] = $new_instance['media_3pr_image_3'];
        $instance['media_3pr_alt_3'] = $new_instance['media_3pr_alt_3'];
        $instance['media_3pr_image_sp_3'] = $new_instance['media_3pr_image_sp_3'];
        $instance['media_3pr_alt_sp_3'] = $new_instance['media_3pr_alt_sp_3'];
        $instance['summary_3'] = $new_instance['summary_3'];
        $instance['linkurl_3'] = $new_instance['linkurl_3'];
        
        return $instance;
    } 
    
function widget($args, $instance) {
	echo PHP_EOL.'<div class="widget prBox row">'.PHP_EOL; 
if( isset($instance['label_1']) && $instance['label_1'] ): ?>
<div class="prArea col-md-4">
<?php
echo '<h1 class="subSection-title">';
if ( isset($instance['label_1']) && $instance['label_1'] ) {
	echo $instance['label_1'];
} else {
	_e("3PR area", 'vkExUnit' );
}
echo '</h1>'.PHP_EOL; ?>
<?php if( isset($instance['media_3pr_image_1'], $instance['media_3pr_image_sp_1']) ): ?>
<div class="media_pr">
<?php if( !empty($instance['linkurl_1']) ): ?> 
	<a href="<?php echo esc_url($instance['linkurl_1']); ?>" >
	<?php if( !empty($instance['media_3pr_image_1']) ): ?>
		<img <?php if( !empty($instance['media_3pr_image_sp_1']) ){	echo 'class="media_pc"'; } ?> src="<?php echo esc_url($instance['media_3pr_image_1']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_1']); ?>" />
	<?php endif; 
	if( !empty($instance['media_3pr_image_sp_1']) ): ?>
		<img class="media_sp" src="<?php  echo esc_url($instance['media_3pr_image_sp_1']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_1']); ?>" />
	<?php endif; ?>
</a>
<?php else: 
	if( !empty($instance['media_3pr_image_1']) ): ?>
	<img <?php if( !empty($instance['media_3pr_image_sp_1']) ){	echo 'class="media_pc"'; } ?> src="<?php echo esc_url($instance['media_3pr_image_1']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_1']); ?>" />
	<?php endif; ?>					
	<?php if( !empty($instance['media_3pr_image_sp_1']) ): ?>
	<img class="media_sp" src="<?php  echo esc_url($instance['media_3pr_image_sp_1']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_1']); ?>" />
	<?php endif;
endif; ?>
</div>
<!--//.media_pr -->			
<?php endif; 
if( !empty($instance['summary_1']) ){
	echo '<p class="summary">'.nl2br(esc_attr($instance['summary_1'])).'</p>'.PHP_EOL;
} 
if( !empty($instance['linkurl_1']) ){
    echo '<p class="linkurl"><a href="'.esc_attr($instance['linkurl_1']).'" class="btn btn-default btn-sm">'.__('Read more', 'vkExUnit' ).'</a></p>';
} ?>    
</div>
<!-- // div.prArea1 -->
<?php endif; ?>
				
<?php if( isset($instance['label_2']) && $instance['label_2'] ): ?>
<div class="prArea col-md-4">
<?php
	echo '<h1 class="subSection-title">';
	if ( isset($instance['label_2']) && $instance['label_2'] ) {
		echo $instance['label_2'];
	} else {
		_e("3PR area", 'vkExUnit' );
	}
	echo '</h1>'.PHP_EOL; 
if( isset($instance['media_3pr_image_2'], $instance['media_3pr_image_sp_2']) ): ?>
<div class="media_pr">
	<?php if( !empty($instance['linkurl_2']) ): ?> 
	<a href="<?php echo esc_url($instance['linkurl_2']); ?>" >
	<?php if( !empty($instance['media_3pr_image_2']) ): ?>
		<img <?php if( !empty($instance['media_3pr_image_sp_2']) ){	echo 'class="media_pc"'; } ?> src="<?php echo esc_url($instance['media_3pr_image_2']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_2']); ?>" />
	<?php endif; 
	if( !empty($instance['media_3pr_image_sp_2']) ): ?>
		<img class="media_sp" src="<?php  echo esc_url($instance['media_3pr_image_sp_2']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_2']); ?>" />
	<?php endif; ?>
</a>
<?php else: 
	if( !empty($instance['media_3pr_image_2']) ): ?>
	<img <?php if( !empty($instance['media_3pr_image_sp_2']) ){	echo 'class="media_pc"'; } ?> src="<?php echo esc_url($instance['media_3pr_image_2']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_2']); ?>" />
	<?php endif; 
	if( !empty($instance['media_3pr_image_sp_2']) ): ?>
	<img class="media_sp" src="<?php  echo esc_url($instance['media_3pr_image_sp_2']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_2']); ?>" />
	<?php endif; 
endif; ?>
</div>
<!--//.media_pr -->			
<?php endif;  
	if( !empty($instance['summary_2']) ){
        echo '<p class="summary">'.nl2br(esc_attr($instance['summary_2'])).'</p>'.PHP_EOL;
    } 
    if( !empty($instance['linkurl_2']) ){
        echo '<p class="linkurl"><a href="'.esc_attr($instance['linkurl_2']).'" class="btn btn-default btn-sm">'.__('Read more', 'vkExUnit' ).'</a></p>';
    } 
?>    
</div>
<!-- // div.prArea2 -->
<?php endif; ?>

<?php if( isset($instance['label_3']) && $instance['label_3'] ):  ?>
<div class="prArea col-md-4">
<?php
echo '<h1 class="subSection-title">';
	if ( isset($instance['label_3']) && $instance['label_3'] ) {
		echo $instance['label_3'];
	} else {
		_e("3PR area", 'vkExUnit' );
	}
echo '</h1>'.PHP_EOL; 
if( isset($instance['media_3pr_image_3'], $instance['media_3pr_image_sp_3']) ): ?>
<div class="media_pr">
<?php if( !empty($instance['linkurl_3']) ): // has link ?> 
	<a href="<?php echo esc_url($instance['linkurl_3']); ?>" >
	<?php if( !empty($instance['media_3pr_image_3']) ): ?>
		<img <?php if( !empty($instance['media_3pr_image_sp_3']) ){	echo 'class="media_pc"'; } ?> src="<?php echo esc_url($instance['media_3pr_image_3']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_3']); ?>" />
	<?php endif; 
	if( !empty($instance['media_3pr_image_sp_3']) ): ?>
		<img class="media_sp" src="<?php  echo esc_url($instance['media_3pr_image_sp_3']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_3']); ?>" />
	<?php endif; ?>
	</a>
<?php else: // no link
	if( !empty($instance['media_3pr_image_3']) ): ?>
		<img <?php if( !empty($instance['media_3pr_image_sp_3']) ){	echo 'class="media_pc"'; } ?> src="<?php echo esc_url($instance['media_3pr_image_3']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_3']); ?>" />
	<?php endif;
	if( !empty($instance['media_3pr_image_sp_3']) ): ?>
	<img class="media_sp" src="<?php  echo esc_url($instance['media_3pr_image_sp_3']); ?>" alt="<?php echo esc_attr($instance['media_3pr_alt_sp_3']); ?>" />
	<?php endif; 
endif; ?>
</div>
<!--//.media_pr -->			
<?php endif;
if( !empty($instance['summary_3']) ){
	echo '<p class="summary">'.nl2br(esc_attr($instance['summary_3'])).'</p>'.PHP_EOL;
} 
if( !empty($instance['linkurl_3']) ){
    echo '<p class="linkurl"><a href="'.esc_attr($instance['linkurl_3']).'" class="btn btn-default btn-sm">'.__('Read more', 'vkExUnit' ).'</a></p>';
} ?>
</div>
<!-- // div.prArea3 -->
<?php endif; ?>
</div>
<?php }
} 
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_vkExUnit_3PR_area");'));

// メディアアップローダーjs
function my_admin_scripts_3pr() {
	
    wp_enqueue_media();
    wp_register_script( 'mediauploader-3pr', plugin_dir_url( __FILE__ ) . 'js/widget-3pr-uploader.js', array( 'jquery' ), false, true );
 
    wp_enqueue_script( 'mediauploader-3pr' );
}
add_action( 'admin_print_scripts', 'my_admin_scripts_3pr' );

// 3PR widget CSS
function style_3PR() {
echo '<style>
		.pr_subTitle{
			box-sizing: border-box;
			padding: 0.8em;
			width: 100%;
			border: solid 1px #ddd;
			background: #EDEDED;
			font-size: 1em;
		}
		.pr_input{
			width: 100%;
		}
		.pr_input.textarea{
			margin-top: -1em;
		}
</style>'.PHP_EOL;
}
add_action("admin_print_styles-widgets.php", "style_3PR");