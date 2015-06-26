<?php 

/*-------------------------------------------*/
/*	PR area widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_3PR_area extends WP_Widget {
    
    // コンストラクタを定義
    function WP_Widget_vkExUnit_3PR_area() {
		$widget_ops = array(
			'classname' => 'WP_Widget_vkExUnit_3PR_area',
			'description' => __( "Displays a 3PR area", 'vkExUnit' ),
		);
		$widget_name = vkExUnit_get_short_name(). '_' . __( "3PR area", 'vkExUnit' );
		$this->WP_Widget('WP_Widget_vkExUnit_3PR_area', $widget_name, $widget_ops);
	}
    
    // form メソッド で管理画面に入力フォームをだす
    function form($instance){
        $defaults = array(
			'label-1' => __("3PR area Title-1", 'vkExUnit' ),
            'media-3pr-image-1' => '',
            'media-3pr-alt' => '',
            'media-3pr-image-sp' => '',
            'media-3pr-alt-sp' => '',
            'summary' => '',
            'linkurl' => '',
            
            'label-2' => __("3PR area Title-2", 'vkExUnit' ),
            'media-3pr-image-2' => '',
            'media-3pr-alt-2' => '',
            'media-3pr-image-sp-2' => '',
            'media-3pr-alt-sp-2' => '',
            'summary-2' => '',
            'linkurl-2' => '',
            
            'label-3' => __("3PR area Title-3", 'vkExUnit' ),
            'media-3pr-image-3' => '',
            'media-3pr-alt-3' => '',
            'media-3pr-image-sp-3' => '',
            'media-3pr-alt-sp-3' => '',
            'summary-3' => '',
            'linkurl-3' => ''
		);
        $instance = wp_parse_args((array) $instance, $defaults);
    ?>
        	
		<?php // 3PR area 1 タイトル ?>
		<p><label for="<?php echo $this->get_field_id('label');  ?>"><?php _e('Title:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('label'); ?>-title" class="prof-input" name="<?php echo $this->get_field_name('label'); ?>" value="<?php echo $instance['label']; ?>" />
		</p>
		
		<?php // 3PR area 1 メディアアップローダー PC image ?>
		<p>
			<label for="<?php echo $this->get_field_id('media-3pr-image');  ?>"><?php _e('Select image for PC:'); ?></label><br/>
		
			<input type="hidden" class="media-image-3pr-pc <?php echo $this->get_field_id('media-3pr-image');  ?>" id="<?php echo $this->get_field_id('media-3pr-image'); ?>-image" name="<?php echo $this->get_field_name('media-3pr-image'); ?>" value="<?php echo esc_attr($instance['media-3pr-image']); ?>" />
		
			<?php //メディアアップローダー alt ?>
			<input type="hidden" class="media-alt-3pr-pc" id="<?php echo $this->get_field_id('media-3pr-alt'); ?>-alt" name="<?php echo $this->get_field_name('media-3pr-alt'); ?>" value="<?php echo esc_attr($instance['media-3pr-alt']); ?>" />
		        
			<input type="button" class="media-select select-3pr" value="画像を選択" onclick="clickSelect3pr(event.target);" />
			<input type="button" class="media-clear" value="画像をクリア" onclick="clickClear3pr(event.target);" />
        </p>
        
        <div class="media image-3pr">
	        <?php if(!empty($instance['media-3pr-image'])): ?>
	        <img class="media-image image-3pr" src="<?php echo esc_url($instance['media-3pr-image']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt']); ?>" />
	        <?php endif; ?>
        </div>
        
        <?php // sp ==================================================== ?>
        
        <p>
	        <?php //メディアアップローダー SP ?>
	        <label for="<?php echo $this->get_field_id('media-3pr-image-sp');  ?>"><?php _e('Select image for Mobile:'); ?></label><br/>
		
			<input type="hidden" class="media-image-3pr-sp" id="<?php echo $this->get_field_id('media-3pr-image-sp'); ?>-image" name="<?php echo $this->get_field_name('media-3pr-image-sp'); ?>" value="<?php echo esc_attr($instance['media-3pr-image-sp']); ?>" />
		
			<?php //メディアアップローダー SP alt ?>
			<input type="hidden" class="media-alt-3pr-sp" id="<?php echo $this->get_field_id('media-3pr-alt-sp'); ?>-alt" name="<?php echo $this->get_field_name('media-3pr-alt-sp'); ?>" value="<?php echo esc_attr($instance['media-3pr-alt-sp']); ?>" />
		        
			<input type="button" class="media-select" value="画像を選択" onclick="clickSelect3prSP(event.target);" />
			<input type="button" class="media-clear" value="画像をクリア" onclick="clickClear3prSP(event.target);" />
        </p>
        
        <div class="media image-3pr-sp">
	        <?php if(!empty($instance['media-3pr-image-sp'])): ?>
	        <img class="media-image image-3pr-sp" src="<?php echo esc_url($instance['media-3pr-image-sp']); ?>" alt="<?php echo esc_attr($instance['mediaalt']); ?>" />
	        <?php endif; ?>
        </div>
		
		<?php //概要テキスト ?>
		<p><label for="<?php echo $this->get_field_id('summary');  ?>"><?php _e('Summary Text:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('summary'); ?>-title" class="prof-input" name="<?php echo $this->get_field_name('summary'); ?>" value="<?php echo $instance['summary']; ?>" />
		</p>
		
		<?php //リンク先_URL ?>
		<p><label for="<?php echo $this->get_field_id('linkurl');  ?>"><?php _e('Link URL:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('linkurl'); ?>-title" class="prof-input" name="<?php echo $this->get_field_name('linkurl'); ?>" value="<?php echo $instance['linkurl']; ?>" />
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
    
    // サイトの外観ここから　===================================================
    function widget($args, $instance) {
		// ここからサイドバーに表示される部分
        echo '<aside class="widget pr-box col-md-4">';
		echo '<h1 class="widget-title subSection-title">';
		if ( isset($instance['label']) && $instance['label'] ) {
			echo $instance['label'];
		} else {
			_e("PR area", 'vkExUnit' );
		}
		echo '</h1>'; ?>
		
		<div class="pr-area">
		<?php 
			// pr-area pc sp 画像表示 
			
			// pc か sp 用の画像のいずれかがあった場合
			if( isset($instance['media-3pr-image'], $instance['media-3pr-image-sp']) ): ?>
			<div class="media-pr">
			<?php // リンクがあった場合の処理 
				if( !empty($instance['linkurl']) ): ?> 
				<a href="<?php echo esc_url($instance['linkurl']); ?>" >
		
					<?php // PC 用画像があった時 
					if( !empty($instance['media-3pr-image']) ): ?>
					<img class="media-pc" src="<?php echo esc_url($instance['media-3pr-image']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt']); ?>" />
					
					<?php endif; 
						
					// SP 要画像があった時
					if( !empty($instance['media-3pr-image-sp']) ): ?>
					<img class="media-sp" src="<?php  echo esc_url($instance['media-3pr-image-sp']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp']); ?>" />
					<?php endif; ?>
				</a>
			<?php else: ?> 
			
				<?php // PC 用画像があった時 
					if( !empty($instance['media-3pr-image']) ): ?>
					<img class="media-pc" src="<?php echo esc_url($instance['media-3pr-image']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt']); ?>" />
					
					<?php endif; 
						
					// SP 要画像があった時
					if( !empty($instance['media-3pr-image-sp']) ): ?>
					<img class="media-sp" src="<?php  echo esc_url($instance['media-3pr-image-sp']); ?>" alt="<?php echo esc_attr($instance['media-3pr-alt-sp']); ?>" />
					<?php endif; ?>
					
			<?php endif; ?> 
			</div>
			<!--//.media-pr -->			
          <?php endif; // pc か sp 用の画像のいずれかがあった場合の処理ここまで	           			 
            // 概要テキスト表示
            if( !empty($instance['summary']) ){
                echo '<p class="summary">'.esc_attr($instance['summary']).'</p>';
            } 
        
            // 詳しく見るリンクURL表示する
            if( !empty($instance['linkurl']) ){
                echo '<p class="linkurl"><a href="'.esc_attr($instance['linkurl']).'">詳しくはこちら</a></p>';
            } 
		?>    
		</div>
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