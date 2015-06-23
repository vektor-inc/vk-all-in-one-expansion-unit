<?php 

/*-------------------------------------------*/
/*	Side Profile widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_profile extends WP_Widget {
    
    // コンストラクタを定義
    function WP_Widget_vkExUnit_profile() {
		$widget_ops = array(
			'classname' => 'WP_Widget_vkExUnit_profile',
			'description' => __( "Displays a your site's profile", 'vkExUnit' ),
		);
		$widget_name = vkExUnit_get_short_name(). '_' . __( "Site's Profile", 'vkExUnit' );
		$this->WP_Widget('WP_Widget_vkExUnit_profile', $widget_name, $widget_ops);
	}
    
    // form メソッド で管理画面に入力フォームをだす
    function form($instance){
        $defaults = array(
			'label' => __("Site's Profile", 'vkExUnit' ),
            'mediafile' => '',
            'mediaalt' => '',
            'profile' => __("Site's Profile Text", 'vkExUnit' ),
            'facebook' => '',
            'twitter' => '',
            'mail' => '',
            'youtube' => '',
            'rss' => '',
            'instagram' => '',
            'linkedin' => ''
		);
        $instance = wp_parse_args((array) $instance, $defaults);
    ?>
        	
		<?php //タイトル ?>
		<label for="<?php echo $this->get_field_id('label');  ?>"><?php _e('Title:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('label'); ?>-title" name="<?php echo $this->get_field_name('label'); ?>" value="<?php echo $instance['label']; ?>" />
		<br/>
		
		<?php //メディアアップローダー ?>
        <label for="<?php echo $this->get_field_id('profile');  ?>"><?php _e('Select Image:'); ?></label><br/>
		<input type="hidden" class="mediaurl" id="<?php echo $this->get_field_id('mediafile'); ?>-title" name="<?php echo $this->get_field_name('mediafile'); ?>" value="<?php echo esc_attr($instance['mediafile']) ?>" />
		
		<input type="hidden" class="mediaalt" id="<?php echo $this->get_field_id('mediaalt'); ?>-title" name="<?php echo $this->get_field_name('mediaalt'); ?>" value="<?php echo esc_attr($instance['mediaalt']) ?>" />
        
        <input type="button" class="media-select" name="media" value="画像を選択" />
        <input type="button" class="media-clear" name="media-clear" value="画像をクリア" /><br/>
        <div class="media">
	        <?php if(!empty($instance['mediafile'])): ?>
	        <img class="media-image" src="<?php echo esc_url($instance['mediafile']); ?>" alt="<?php echo esc_attr($instance['mediaalt']); ?>" />
	        <?php endif; ?>
        </div>
		
		<?php //profile ?>
		<label for="<?php echo $this->get_field_id('profile');  ?>"><?php _e('Profile Text:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('profile'); ?>-title" name="<?php echo $this->get_field_name('profile'); ?>" value="<?php echo $instance['profile']; ?>" />
		<br/>
		
		<?php //facebook_URL ?>
		<label for="<?php echo $this->get_field_id('facebook');  ?>"><?php _e('Facebook URL:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('facebook'); ?>-title" name="<?php echo $this->get_field_name('facebook'); ?>" value="<?php echo $instance['facebook']; ?>" />
		<br/>
		
		<?php //twitter_URL ?>
		<label for="<?php echo $this->get_field_id('twitter');  ?>"><?php _e('Twitter URL:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('twitter'); ?>-title" name="<?php echo $this->get_field_name('twitter'); ?>" value="<?php echo $instance['twitter']; ?>" />
		<br/>
		
		<?php //mail_URL ?>
		<label for="<?php echo $this->get_field_id('mail');  ?>"><?php _e('Email Address:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('mail'); ?>-title" name="<?php echo $this->get_field_name('mail'); ?>" value="<?php echo $instance['mail']; ?>" />
		<br/>
		
		<?php //youtube_URL ?>
		<label for="<?php echo $this->get_field_id('youtube');  ?>"><?php _e('Youtube URL:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('youtube'); ?>-title" name="<?php echo $this->get_field_name('youtube'); ?>" value="<?php echo $instance['youtube']; ?>" />
		<br/>
		
		<?php //rss_URL ?>
		<label for="<?php echo $this->get_field_id('rss'); ?>"><?php _e('RSS URL:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('rss'); ?>-title" name="<?php echo $this->get_field_name('rss'); ?>" value="<?php echo $instance['rss']; ?>" />
		<br/>
		
		<?php //instagram_URL ?>
		<label for="<?php echo $this->get_field_id('instagram');  ?>"><?php _e('instagram URL:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('instagram'); ?>-title" name="<?php echo $this->get_field_name('instagram'); ?>" value="<?php echo $instance['instagram']; ?>" />
		<br/>
		
		<?php //linkedin_URL ?>
		<label for="<?php echo $this->get_field_id('linkedin');  ?>"><?php _e('linkedin URL:'); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id('linkedin'); ?>-title" name="<?php echo $this->get_field_name('linkedin'); ?>" value="<?php echo $instance['linkedin']; ?>" />
		<br/>
   
    <?php  } // form メソッド ここまで
    
    // update メソッドで管理画面からの入力を保存する
    function update ($new_instance, $old_instance) {
        
        $instance = $old_instance;
        $instance['label'] = $new_instance['label'];
        $instance['mediafile'] = $new_instance['mediafile'];
        $instance['mediaalt'] = $new_instance['mediaalt'];
        $instance['profile'] = $new_instance['profile'];
        $instance['facebook'] = $new_instance['facebook'];
        $instance['twitter'] = $new_instance['twitter'];
        $instance['mail'] = $new_instance['mail'];
        $instance['youtube'] = $new_instance['youtube'];
        $instance['rss'] = $new_instance['rss'];
        $instance['instagram'] = $new_instance['instagram'];
        $instance['linkedin'] = $new_instance['linkedin'];
        return $instance;
        
    } // update メソッドここまで
    
    // widget メソッドでサイトへ出力する
    function widget($args, $instance) {
		// ここからサイドバーに表示される部分
        echo '<aside class="widget">';
		echo '<h1 class="widget-title subSection-title">';
		if ( isset($instance['label']) && $instance['label'] ) {
			echo $instance['label'];
		} else {
			_e("Site's Profile", 'vkExUnit' );
		}
		echo '</h1>'; ?>
		
		<div class="site-profile">
			<?php 
			// プロフィール画像表示
			if( !empty($instance['mediafile']) ){
                echo '<img class="profile-media" src="'.esc_url($instance['mediafile']).'" alt="'.esc_attr($instance['mediaalt']).'" />';
            } 
			 
            // プロフィールテキスト表示
            if( !empty($instance['profile']) ){
                echo '<p class="profile-text">'.esc_attr($instance['profile']).'</p>';
            } 
        
            // sns リンクボタン表示する
            if( isset($instance['facebook'], $instance['twitter'], $instance['mail'], $instance['youtube'], $instance['rss'], $instance['instagram'], $instance['linkedin'] ) ): ?>
            <ul class="sns-btns">
		        
		        <?php // facebook ボタン表示
                if( !empty($instance['facebook']) ): ?>
				<li class="facebook-btn">
                    <a href="<?php echo esc_url($instance['facebook']); ?>" target="_blank">
                    <i class="fa fa-facebook-official"></i>
                    </a>
				</li>
				<?php endif; ?>
				
				<?php // twitter ボタン表示
                if( !empty($instance['twitter']) ): ?>
				<li class="twitter-btn">
                    <a href="<?php echo esc_url($instance['twitter']); ?>" target="_blank">
                    <i class="fa fa-twitter"></i>
                    </a>
				</li>
				<?php endif; ?>
				
				<?php // mail ボタン表示
                if( !empty($instance['mail']) ): ?>
				<li class="mail-btn">
                    <a href="<?php echo esc_url($instance['mail']); ?>" target="_blank">
                    <i class="fa fa-envelope-o"></i>
                    </a>
				</li>
				<?php endif; ?>
				
				<?php // youtube ボタン表示
                if( !empty($instance['youtube']) ): ?>
				<li class="youtube-btn">
                    <a href="<?php echo esc_url($instance['youtube']); ?>" target="_blank">
                    <i class="fa fa-youtube"></i>
                    </a>
				</li>
				<?php endif; ?>
				
				<?php // RSS ボタン表示
                if( !empty($instance['rss']) ): ?>
				<li class="rss-btn">
                    <a href="<?php echo esc_url($instance['rss']); ?>" target="_blank">
                    <i class="fa fa-rss"></i>
                    </a>
				</li>
				<?php endif; ?>
				
				<?php // instagram ボタン表示
                if( !empty($instance['instagram']) ): ?>
				<li class="instagram-btn">
                    <a href="<?php echo esc_url($instance['instagram']); ?>" target="_blank">
                    <i class="fa fa-instagram"></i>
                    </a>
				</li>
				<?php endif; ?>
				
				<?php // linkedin ボタン表示
                if( !empty($instance['linkedin']) ): ?>
				<li class="linkedin-btn">
                    <a href="<?php echo esc_url($instance['linkedin']); ?>" target="_blank">
                    <i class="fa fa-linkedin-square"></i>
                    </a>
				</li>
				<?php endif; ?>
            
            </ul> 
            <?php endif; ?> 
		</div>
        </aside>
<?php }

} // class WP_Widget_vkExUnit_profile
add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_vkExUnit_profile");'));

// メディアアップローダーjs
function my_admin_scripts() {
 
    wp_register_script(
        'mediauploader',
        plugin_dir_url( __FILE__ ) . 'widget-prof-uploader.js',
        array( 'jquery' ),
        false,
        true
    );
 
    /* メディアアップローダの javascript API */
    wp_enqueue_media();
 
    /* 作成した javascript */
    wp_enqueue_script( 'mediauploader' );
 
}
add_action( 'admin_print_scripts', 'my_admin_scripts' );

// メディアアップローダーCSS
function my_admin_style() {
echo '<style>
    .media-select,.media-clear{
	    padding: 3px;
	    border: none;
	    border-radius: 3px;
	    background: #00a0d2;
	    color: #fff;
	    font-size: 12px;
	    cursor: pointer;
	    outline: none;
    }
    .media-select:hover,.media-clear:hover{
	    background: #0073aa;
    }
    .media{
		position: relative;
		z-index: 2;
		overflow: hidden;
		margin: 3px 0;
		min-height: 70px;
		max-height: 200px;
		width: 85%;
		border: 1px dashed #ccc;
		border-radius: 5px;
		background-color: rgba(212, 212, 212, 0.1);
	}
	.media:before{
		position: absolute;
		top: 50%;
		left: 50%;
		z-index: 1;
		margin: -8px 0 0 -30px;
		color: #999;
		content: "No Image";
	}
	.media-image{
		position: relative;
		z-index: 3;
		display: block;
		width: 100%!important;
		height: auto!important;
	}	
</style>'.PHP_EOL;
}
add_action("admin_print_styles-widgets.php", "my_admin_style");
