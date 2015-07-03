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
			'label-1' => __( '3PR area1 title', 'vkExUnit' ),
            'media-3pr-image-1' => '',
            'media-3pr-alt-1' => '',
            'media-3pr-image-sp-1' => '',
            'media-3pr-alt-sp-1' => '',
            'summary-1' => '',
            'linkurl-1' => '',
            
            'label-2' => __( '3PR area2 title', 'vkExUnit' ),
            'media-3pr-image-2' => '',
            'media-3pr-alt-2' => '',
            'media-3pr-image-sp-2' => '',
            'media-3pr-alt-sp-2' => '',
            'summary-2' => '',
            'linkurl-2' => '',
            
            'label-3' => __( '3PR area3 title', 'vkExUnit' ),
            'media-3pr-image-3' => '',
            'media-3pr-alt-3' => '',
            'media-3pr-image-sp-3' => '',
            'media-3pr-alt-sp-3' => '',
            'summary-3' => '',
            'linkurl-3' => ''
		);
        $instance = wp_parse_args((array) $instance, $defaults);
    ?>
        	
<?php // 3PR area 1 =========================================================== ?>
		<?php // 3PR area 1 タイトル ?>
		<h5 class="pr-sub-title"><?php _e( '3PR area1 setting', 'vkExUnit' ); ?></h5>
		<p>
			<label for="<?php echo $this->get_field_id('label-1');  ?>"><?php _e( 'Title:', 'vkExUnit' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id('label-1'); ?>-title" class="pr-input" name="<?php echo $this->get_field_name('label-1'); ?>" value="<?php echo $instance['label-1']; ?>" />
		</p>
		
		<?php // 3PR area 1 メディアアップローダー PC ?>
		<p>
			<label for="<?php echo $this->get_field_id('media-3pr-image-1');  ?>"><?php _e( 'Select image for PC:', 'vkExUnit' ); ?></label><br/>
	
			<input type="hidden" class="media-image-3pr-pc <?php echo $this->get_field_id('media-3pr-image-1');  ?>" id="<?php echo $this->get_field_id('media-3pr-image-1'); ?>-image" name="<?php echo $this->get_field_name('media-3pr-image-1'); ?>" value="<?php echo esc_attr($instance['media-3pr-image-1']); ?>" />
		
			<input type="hidden" class="media-alt-3pr-pc" id="<?php echo $this->get_field_id('media-3pr-alt-1'); ?>-alt" name="<?php echo $this->get_field_name('media-3pr-alt-1'); ?>" value="<?php echo esc_attr($instance['media-3pr-alt-1']); ?>" />
		        
			<input type="button" class="media-select select-3pr" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3pr(event.target);" />
			<input type="button" class="media-clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3pr(event.target);" />
        </p>
        <div class="media image-3pr">
	        <?php if(!empty($instance['media-3pr-image-1'])): ?>
	        <img class="media-image image-3pr" src="<?php echo esc_url($instance['media-3pr-image-1']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-1']); ?>" />
	        <?php endif; ?>
        </div>

        <?php // 3PR area 1 メディアアップローダー sp image ?>
        <p>
	        <label for="<?php echo $this->get_field_id('media-3pr-image-sp-1');  ?>"><?php _e( 'Select image for Mobile:', 'vkExUnit' ); ?></label><br/>
		
			<input type="hidden" class="media-image-3pr-sp" id="<?php echo $this->get_field_id('media-3pr-image-sp-1'); ?>-image" name="<?php echo $this->get_field_name('media-3pr-image-sp-1'); ?>" value="<?php echo esc_attr($instance['media-3pr-image-sp-1']); ?>" />
		
			<input type="hidden" class="media-alt-3pr-sp" id="<?php echo $this->get_field_id('media-3pr-alt-sp-1'); ?>-alt" name="<?php echo $this->get_field_name('media-3pr-alt-sp-1'); ?>" value="<?php echo esc_attr($instance['media-3pr-alt-sp-1']); ?>" />
		        
			<input type="button" class="media-select" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3prSP(event.target);" />
			<input type="button" class="media-clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3prSP(event.target);" />
        </p>
        <div class="media image-3pr-sp">
	        <?php if(!empty($instance['media-3pr-image-sp-1'])): ?>
	        <img class="media-image image-3pr-sp" src="<?php echo esc_url($instance['media-3pr-image-sp-1']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp-1']); ?>" />
	        <?php endif; ?>
        </div>
		
		<?php // 概要テキスト ?>
		<p><label for="<?php echo $this->get_field_id('summary-1');  ?>"><?php _e( 'Summary Text:', 'vkExUnit' ); ?></label><br/>
		</p>
		
		<textarea rows="4" cols="40" id="<?php echo $this->get_field_id('summary-1'); ?>-text" class="pr-input textarea" name="<?php echo $this->get_field_name('summary-1'); ?>"><?php echo $instance['summary-1']; ?></textarea>
		
		<?php // リンク先_URL ?>
		<p><label for="<?php echo $this->get_field_id('linkurl-1');  ?>"><?php _e( 'Link URL:', 'vkExUnit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('linkurl-1'); ?>-title" class="pr-input" name="<?php echo $this->get_field_name('linkurl-1'); ?>" value="<?php echo $instance['linkurl-1']; ?>" />
		</p>
		
<hr /><?php // 3PR area 2 =================================================?>
		<?php // 3PR area 2 タイトル ?>
		<h5 class="pr-sub-title"><?php _e( '3PR area2 setting', 'vkExUnit' ); ?></h5>
		<p>
			<label for="<?php echo $this->get_field_id('label-2');  ?>"><?php _e( 'Title:', 'vkExUnit' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id('label-2'); ?>-title" class="pr-input" name="<?php echo $this->get_field_name('label-2'); ?>" value="<?php echo $instance['label-2']; ?>" />
		</p>
		
		<?php // 3PR area 1 メディアアップローダー PC ?>
		<p>
			<label for="<?php echo $this->get_field_id('media-3pr-image-2');  ?>"><?php _e( 'Select image for PC:', 'vkExUnit' ); ?></label><br/>
	
			<input type="hidden" class="media-image-3pr-pc <?php echo $this->get_field_id('media-3pr-image-2');  ?>" id="<?php echo $this->get_field_id('media-3pr-image-2'); ?>-image" name="<?php echo $this->get_field_name('media-3pr-image-2'); ?>" value="<?php echo esc_attr($instance['media-3pr-image-2']); ?>" />
		
			<input type="hidden" class="media-alt-3pr-pc" id="<?php echo $this->get_field_id('media-3pr-alt-2'); ?>-alt" name="<?php echo $this->get_field_name('media-3pr-alt-2'); ?>" value="<?php echo esc_attr($instance['media-3pr-alt-2']); ?>" />
		        
			<input type="button" class="media-select select-3pr" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3pr(event.target);" />
			<input type="button" class="media-clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3pr(event.target);" />
        </p>
        
        <div class="media image-3pr">
	        <?php if(!empty($instance['media-3pr-image-2'])): ?>
	        <img class="media-image image-3pr" src="<?php echo esc_url($instance['media-3pr-image-2']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-2']); ?>" />
	        <?php endif; ?>
        </div>
        
        <?php // 3PR area 2 メディアアップローダー sp  ?>
        <p>
	        <label for="<?php echo $this->get_field_id('media-3pr-image-sp-2');  ?>"><?php _e( 'Select image for Mobile:', 'vkExUnit' ); ?></label><br/>
		
			<input type="hidden" class="media-image-3pr-sp" id="<?php echo $this->get_field_id('media-3pr-image-sp-2'); ?>-image" name="<?php echo $this->get_field_name('media-3pr-image-sp-2'); ?>" value="<?php echo esc_attr($instance['media-3pr-image-sp-2']); ?>" />
		
			<input type="hidden" class="media-alt-3pr-sp" id="<?php echo $this->get_field_id('media-3pr-alt-sp-2'); ?>-alt" name="<?php echo $this->get_field_name('media-3pr-alt-sp-2'); ?>" value="<?php echo esc_attr($instance['media-3pr-alt-sp-2']); ?>" />
		        
			<input type="button" class="media-select" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3prSP(event.target);" />
			<input type="button" class="media-clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3prSP(event.target);" />
        </p>
        <div class="media image-3pr-sp">
	        <?php if(!empty($instance['media-3pr-image-sp-2'])): ?>
	        <img class="media-image image-3pr-sp" src="<?php echo esc_url($instance['media-3pr-image-sp-2']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp-2']); ?>" />
	        <?php endif; ?>
        </div>
		
		<?php //概要テキスト ?>
		<p><label for="<?php echo $this->get_field_id('summary-2');  ?>"><?php _e( 'Summary Text:', 'vkExUnit' ); ?></label></p>
		<textarea rows="4" cols="40" id="<?php echo $this->get_field_id('summary-2'); ?>-text" class="pr-input textarea" name="<?php echo $this->get_field_name('summary-2'); ?>"><?php echo $instance['summary-2']; ?></textarea>
		
		<?php //リンク先_URL ?>
		<p><label for="<?php echo $this->get_field_id('linkurl-2');  ?>"><?php _e( 'Link URL:', 'vkExUnit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('linkurl-2'); ?>-title" class="pr-input" name="<?php echo $this->get_field_name('linkurl-2'); ?>" value="<?php echo $instance['linkurl-2']; ?>" />
		</p>
	
<hr /><?php // 3PR area 3  =================================================?>
		<?php // 3PR area 3 タイトル ?>
		<h5 class="pr-sub-title"><?php _e( '3PR area3 setting', 'vkExUnit' ); ?></h5>
		<p>
			<label for="<?php echo $this->get_field_id('label-3');  ?>"><?php _e( 'Title:', 'vkExUnit' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id('label-3'); ?>-title" class="pr-input" name="<?php echo $this->get_field_name('label-3'); ?>" value="<?php echo $instance['label-3']; ?>" />
		</p>
		
		<?php // 3PR area 1 メディアアップローダー PC ?>
		<p>
			<label for="<?php echo $this->get_field_id('media-3pr-image-3');  ?>"><?php _e( 'Select image for PC:', 'vkExUnit' ); ?></label><br/>
	
			<input type="hidden" class="media-image-3pr-pc <?php echo $this->get_field_id('media-3pr-image-3');  ?>" id="<?php echo $this->get_field_id('media-3pr-image-3'); ?>-image" name="<?php echo $this->get_field_name('media-3pr-image-3'); ?>" value="<?php echo esc_attr($instance['media-3pr-image-3']); ?>" />
		
			<input type="hidden" class="media-alt-3pr-pc" id="<?php echo $this->get_field_id('media-3pr-alt-3'); ?>-alt" name="<?php echo $this->get_field_name('media-3pr-alt-3'); ?>" value="<?php echo esc_attr($instance['media-3pr-alt-3']); ?>" />
		        
			<input type="button" class="media-select select-3pr" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3pr(event.target);" />
			<input type="button" class="media-clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3pr(event.target);" />
        </p>
        <div class="media image-3pr">
	        <?php if(!empty($instance['media-3pr-image-3'])): ?>
	        <img class="media-image image-3pr" src="<?php echo esc_url($instance['media-3pr-image-3']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-3']); ?>" />
	        <?php endif; ?>
        </div>
        
        <?php // 3PR area 2 メディアアップローダー sp  ?>
        <p>
	        <label for="<?php echo $this->get_field_id('media-3pr-image-sp-3');  ?>"><?php _e( 'Select image for Mobile:', 'vkExUnit' ); ?></label><br/>
		
			<input type="hidden" class="media-image-3pr-sp" id="<?php echo $this->get_field_id('media-3pr-image-sp-3'); ?>-image" name="<?php echo $this->get_field_name('media-3pr-image-sp-3'); ?>" value="<?php echo esc_attr($instance['media-3pr-image-sp-3']); ?>" />
		
			<input type="hidden" class="media-alt-3pr-sp" id="<?php echo $this->get_field_id('media-3pr-alt-sp-3'); ?>-alt" name="<?php echo $this->get_field_name('media-3pr-alt-sp-3'); ?>" value="<?php echo esc_attr($instance['media-3pr-alt-sp-3']); ?>" />
		        
			<input type="button" class="media-select" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect3prSP(event.target);" />
			<input type="button" class="media-clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear3prSP(event.target);" />
        </p>
        
        <div class="media image-3pr-sp">
	        <?php if(!empty($instance['media-3pr-image-sp-3'])): ?>
	        <img class="media-image image-3pr-sp" src="<?php echo esc_url($instance['media-3pr-image-sp-3']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp-3']); ?>" />
	        <?php endif; ?>
        </div>
		
		<?php //概要テキスト ?>
		<p><label for="<?php echo $this->get_field_id('summary-3');  ?>"><?php _e( 'Summary Text:', 'vkExUnit' ); ?></label>
		</p>
		<textarea rows="4" cols="40" id="<?php echo $this->get_field_id('summary-3'); ?>-text" class="pr-input textarea" name="<?php echo $this->get_field_name('summary-3'); ?>"><?php echo $instance['summary-3']; ?></textarea>
		
		<?php //リンク先_URL ?>
		<p><label for="<?php echo $this->get_field_id('linkurl-3');  ?>"><?php _e( 'Link URL:', 'vkExUnit' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('linkurl-3'); ?>-title" class="pr-input" name="<?php echo $this->get_field_name('linkurl-3'); ?>" value="<?php echo $instance['linkurl-3']; ?>" />
		</p>

 <?php  } 
    
    function update ($new_instance, $old_instance) {

        $instance = $old_instance;
        
        $instance['label-1'] = $new_instance['label-1'];
        $instance['media-3pr-image-1'] = $new_instance['media-3pr-image-1'];
        $instance['media-3pr-alt-1'] = $new_instance['media-3pr-alt-1'];
        $instance['media-3pr-image-sp-1'] = $new_instance['media-3pr-image-sp-1'];
        $instance['media-3pr-alt-sp-1'] = $new_instance['media-3pr-alt-sp-1'];
        $instance['summary-1'] = $new_instance['summary-1'];
        $instance['linkurl-1'] = $new_instance['linkurl-1'];
        
        $instance['label-2'] = $new_instance['label-2'];
        $instance['media-3pr-image-2'] = $new_instance['media-3pr-image-2'];
        $instance['media-3pr-alt-2'] = $new_instance['media-3pr-alt-2'];
        $instance['media-3pr-image-sp-2'] = $new_instance['media-3pr-image-sp-2'];
        $instance['media-3pr-alt-sp-2'] = $new_instance['media-3pr-alt-sp-2'];
        $instance['summary-2'] = $new_instance['summary-2'];
        $instance['linkurl-2'] = $new_instance['linkurl-2'];
        
        $instance['label-3'] = $new_instance['label-3'];
        $instance['media-3pr-image-3'] = $new_instance['media-3pr-image-3'];
        $instance['media-3pr-alt-3'] = $new_instance['media-3pr-alt-3'];
        $instance['media-3pr-image-sp-3'] = $new_instance['media-3pr-image-sp-3'];
        $instance['media-3pr-alt-sp-3'] = $new_instance['media-3pr-alt-sp-3'];
        $instance['summary-3'] = $new_instance['summary-3'];
        $instance['linkurl-3'] = $new_instance['linkurl-3'];
        
        return $instance;
    } 
    
    function widget($args, $instance) {
        echo '<aside class="widget pr-box">';
		?>
		
		<?php if( isset($instance['label-1']) && $instance['label-1'] ): ?>
		<div class="pr-area col-md-4">
		<?php
		echo '<h1 class="subSection-title">';
		if ( isset($instance['label-1']) && $instance['label-1'] ) {
			echo $instance['label-1'];
		} else {
			_e("3PR area", 'vkExUnit' );
		}
		echo '</h1>'; ?>
		
		<?php 
			if( isset($instance['media-3pr-image-1'], $instance['media-3pr-image-sp-1']) ): ?>
			<div class="media-pr">
			<?php if( !empty($instance['linkurl-1']) ): ?> 
				<a href="<?php echo esc_url($instance['linkurl-1']); ?>" >
					<?php if( !empty($instance['media-3pr-image-1']) ): ?>
						<img <?php if( !empty($instance['media-3pr-image-sp-1']) ){	echo 'class="media-pc"'; } ?> src="<?php echo esc_url($instance['media-3pr-image-1']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-1']); ?>" />
					<?php endif; ?>
					
					<?php if( !empty($instance['media-3pr-image-sp-1']) ): ?>
						<img class="media-sp" src="<?php  echo esc_url($instance['media-3pr-image-sp-1']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp-1']); ?>" />
					<?php endif; ?>
				</a>
			<?php else: ?> 
				<?php if( !empty($instance['media-3pr-image-1']) ): ?>
					<img <?php if( !empty($instance['media-3pr-image-sp-1']) ){	echo 'class="media-pc"'; } ?> src="<?php echo esc_url($instance['media-3pr-image-1']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-1']); ?>" />
				<?php endif; ?>
						
				<?php if( !empty($instance['media-3pr-image-sp-1']) ): ?>
					<img class="media-sp" src="<?php  echo esc_url($instance['media-3pr-image-sp-1']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp-1']); ?>" />
				<?php endif; ?>
					
			<?php endif; ?> 
			</div>
			<!--//.media-pr -->			
			<?php endif; ?>	           			 
            <?php if( !empty($instance['summary-1']) ){
                echo '<p class="summary">'.nl2br(esc_attr($instance['summary-1'])).'</p>';
            } 
            if( !empty($instance['linkurl-1']) ){
                echo '<p class="linkurl btn btn-default"><a href="'.esc_attr($instance['linkurl-1']).'">'.__('Read more', 'vkExUnit' ).'</a></p>';
            } 
		?>    
		</div>
		<!-- // div.pr-area -->
		<?php endif; ?>
				
		<?php if( isset($instance['label-2']) && $instance['label-2'] ): ?>
		<div class="pr-area col-md-4">
		<?php
		echo '<h1 class="subSection-title">';
		if ( isset($instance['label-2']) && $instance['label-2'] ) {
			echo $instance['label-2'];
		} else {
			_e("3PR area", 'vkExUnit' );
		}
		echo '</h1>'; ?>
		
		<?php if( isset($instance['media-3pr-image-2'], $instance['media-3pr-image-sp-2']) ): ?>
			<div class="media-pr">
			<?php if( !empty($instance['linkurl-2']) ): ?> 
				<a href="<?php echo esc_url($instance['linkurl-2']); ?>" >
					<?php if( !empty($instance['media-3pr-image-2']) ): ?>
					<img <?php if( !empty($instance['media-3pr-image-sp-2']) ){	echo 'class="media-pc"'; } ?> src="<?php echo esc_url($instance['media-3pr-image-2']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-2']); ?>" />
					<?php endif; ?>
						
					<?php if( !empty($instance['media-3pr-image-sp-2']) ): ?>
					<img class="media-sp" src="<?php  echo esc_url($instance['media-3pr-image-sp-2']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp-2']); ?>" />
					<?php endif; ?>
				</a>
			<?php else: ?> 
					<?php if( !empty($instance['media-3pr-image-2']) ): ?>
					<img <?php if( !empty($instance['media-3pr-image-sp-2']) ){	echo 'class="media-pc"'; } ?> src="<?php echo esc_url($instance['media-3pr-image-2']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-2']); ?>" />
					<?php endif; ?>
						
					<?php if( !empty($instance['media-3pr-image-sp-2']) ): ?>
					<img class="media-sp" src="<?php  echo esc_url($instance['media-3pr-image-sp-2']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp-2']); ?>" />
					<?php endif; ?>
					
			<?php endif; ?> 
			</div>
			<!--//.media-pr -->			
          <?php endif; ?>
          	           			 
            <?php 
	        if( !empty($instance['summary-2']) ){
                echo '<p class="summary">'.nl2br(esc_attr($instance['summary-2'])).'</p>';
            } 
        
            if( !empty($instance['linkurl-2']) ){
                echo '<p class="linkurl btn btn-default"><a href="'.esc_attr($instance['linkurl-2']).'">'.__('Read more', 'vkExUnit' ).'</a></p>';
            } 
		?>    
		</div>
		<!-- // div.pr-area -->
		<?php endif; ?>

		<?php if( isset($instance['label-3']) && $instance['label-3'] ):  ?>
		<div class="pr-area col-md-4">
		<?php
		echo '<h1 class="subSection-title">';
		if ( isset($instance['label-3']) && $instance['label-3'] ) {
			echo $instance['label-3'];
		} else {
			_e("3PR area", 'vkExUnit' );
		}
		echo '</h1>'; ?>
		
		<?php if( isset($instance['media-3pr-image-3'], $instance['media-3pr-image-sp-3']) ): ?>
			<div class="media-pr">
			<?php if( !empty($instance['linkurl-3']) ): ?> 
				<a href="<?php echo esc_url($instance['linkurl-3']); ?>" >
		
					<?php if( !empty($instance['media-3pr-image-3']) ): ?>
					<img <?php if( !empty($instance['media-3pr-image-sp-3']) ){	echo 'class="media-pc"'; } ?> src="<?php echo esc_url($instance['media-3pr-image-3']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-3']); ?>" />
					<?php endif; ?>
						
					<?php if( !empty($instance['media-3pr-image-sp-3']) ): ?>
					<img class="media-sp" src="<?php  echo esc_url($instance['media-3pr-image-sp-3']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp-3']); ?>" />
					<?php endif; ?>
				</a>
			<?php else: ?> 
				<?php if( !empty($instance['media-3pr-image-3']) ): ?>
					<img <?php if( !empty($instance['media-3pr-image-sp-3']) ){	echo 'class="media-pc"'; } ?> src="<?php echo esc_url($instance['media-3pr-image-3']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-3']); ?>" />
				<?php endif; ?>
						
				<?php if( !empty($instance['media-3pr-image-sp']) ): ?>
					<img class="media-sp" src="<?php  echo esc_url($instance['media-3pr-image-sp']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp']); ?>" />
				<?php endif; ?>
					
			<?php endif; ?> 
			</div>
			<!--//.media-pr -->			
          <?php endif; ?>
            
            <?php 
	        if( !empty($instance['summary-3']) ){
                echo '<p class="summary">'.nl2br(esc_attr($instance['summary-3'])).'</p>';
            } 
            if( !empty($instance['linkurl-3']) ){
                echo '<p class="linkurl btn btn-default"><a href="'.esc_attr($instance['linkurl-3']).'">'.__('Read more', 'vkExUnit' ).'</a></p>';
            } 
		?>    
		</div>
		<!-- // div.pr-area -->
		<?php endif; ?>		
        </aside>
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
		.pr-sub-title{
			box-sizing: border-box;
			padding: 0.8em;
			width: 100%;
			border: solid 1px #ddd;
			background: #EDEDED;
			font-size: 1em;
		}
		.pr-input{
			width: 100%;
		}
		.pr-input.textarea{
			margin-top: -1em;
		}
</style>'.PHP_EOL;
}
add_action("admin_print_styles-widgets.php", "style_3PR");