<?php

/*-------------------------------------------*/
/*  Side Profile widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_profile extends WP_Widget {

	function __construct() {
		$widget_name = vkExUnit_get_short_name(). '_' . __( 'Profile', 'vkExUnit' );

		parent::__construct(
			'WP_Widget_vkExUnit_profile',
			$widget_name,
			array( 'description' => __( 'Displays a your profile', 'biz-vektor' ) )
		);
	}

	function form( $instance ) {
		$defaults = array(
			'label' => __( 'Profile', 'vkExUnit' ),
			'mediaFile' => '',
			'mediaAlt' => '',
			'mediaAlign_left' => '',
			'mediaRound' => '',
			'mediaSize' => '',
			'mediaFloat' => '',
			'profile' => __( 'Profile Text', 'vkExUnit' ),
			'facebook' => '',
			'twitter' => '',
			'mail' => '',
			'youtube' => '',
			'rss' => '',
			'instagram' => '',
			'linkedin' => '',
			'iconFont_bgType' => '',
			'icon_color' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

	<?php //title ?>
<p><label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Title:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo esc_attr( $instance['label'] ); ?>" />
</p>

		<?php //media uploader ?>
<p><label for="<?php echo $this->get_field_id( 'profile' );  ?>"><?php _e( 'Select Profile image:', 'vkExUnit' ); ?></label><br/>
<input type="hidden" class="media_url" id="<?php echo $this->get_field_id( 'mediaFile' ); ?>" name="<?php echo $this->get_field_name( 'mediaFile' ); ?>" value="<?php echo esc_attr( $instance['mediaFile'] ); ?>" />
<input type="hidden" class="media_alt" id="<?php echo $this->get_field_id( 'mediaAlt' ); ?>" name="<?php echo $this->get_field_name( 'mediaAlt' ); ?>" value="<?php echo esc_attr( $instance['mediaAlt'] ); ?>" />
<input type="button" class="media_select" value="<?php _e( 'Select image', 'vkExUnit' ); ?>" onclick="clickSelect(event.target);" />
<input type="button" class="media_clear" value="<?php _e( 'Clear image', 'vkExUnit' ); ?>" onclick="clickClear(event.target);" />
</p>
<div class="media">
	<?php if ( ! empty( $instance['mediaFile'] ) ) :  ?>
	<img class="media_image" src="<?php echo esc_url( $instance['mediaFile'] ); ?>" alt="<?php echo esc_attr( $instance['mediaAlt'] ); ?>" />
	<?php endif; ?>
</div>

		<?php //image round setting ?>
<p><input type="checkbox" id="<?php echo $this->get_field_id( 'mediaRound' ); ?>" name="<?php echo $this->get_field_name( 'mediaRound' ); ?>" value="true" <?php echo ($instance['mediaRound'])? 'checked': '' ; ?> ><label for="<?php echo $this->get_field_id( 'mediaRound' );  ?>"><?php _e( 'Cut out round the image.', 'vkExUnit' ); ?></label>
</p>

		<?php //image size setting ?>
<p><label for="<?php echo $this->get_field_id( 'mediaSize' );  ?>"><?php _e( 'Media size (Optional)', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'mediaSize' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'mediaSize' ); ?>" style="width:50px;" value="<?php echo esc_attr( $instance['mediaSize'] ); ?>" /> px
</p>

		<?php //image mediaAlign_left setting ?>
<p><input type="checkbox" id="<?php echo $this->get_field_id( 'mediaAlign_left' ); ?>" name="<?php echo $this->get_field_name( 'mediaAlign_left' ); ?>" value="true" <?php echo ($instance['mediaAlign_left'])? 'checked': '' ; ?> ><label for="<?php echo $this->get_field_id( 'mediaAlign_left' ); ?>"><?php _e( 'Image align left', 'vkExUnit' ); ?></label>
</p>

		<?php //image float setting ?>
<p><input type="checkbox" id="<?php echo $this->get_field_id( 'mediaFloat' ); ?>" name="<?php echo $this->get_field_name( 'mediaFloat' ); ?>" value="true" <?php echo ($instance['mediaFloat'])? 'checked': '' ; ?> ><label for="<?php echo $this->get_field_id( 'mediaFloat' );  ?>"><?php _e( 'Text float to image.', 'vkExUnit' ); ?></label><br/>
</p>

		<?php //profile text ?>
<p><label for="<?php echo $this->get_field_id( 'profile' );  ?>"><?php _e( 'Profile Text:', 'vkExUnit' ); ?></label></p>
<textarea rows="4" cols="40" id="<?php echo $this->get_field_id( 'profile' ); ?>" class="prof_input textarea" name="<?php echo $this->get_field_name( 'profile' ); ?>"><?php echo esc_textarea( $instance['profile'] ); ?></textarea>

		<?php //facebook_URL ?>
<p><label for="<?php echo $this->get_field_id( 'facebook' );  ?>"><?php _e( 'Facebook URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'facebook' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'facebook' ); ?>" value="<?php echo esc_attr( $instance['facebook'] ); ?>" />
</p>

		<?php //twitter_URL ?>
<p><label for="<?php echo $this->get_field_id( 'twitter' );  ?>"><?php _e( 'Twitter URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'twitter' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'twitter' ); ?>" value="<?php echo esc_attr( $instance['twitter'] ); ?>" />
</p>

		<?php //mail_URL ?>
<p><label for="<?php echo $this->get_field_id( 'mail' ); ?>"><?php _e( 'Email Address:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'mail' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'mail' ); ?>" value="<?php echo esc_attr( $instance['mail'] ); ?>" />
</p>

		<?php //youtube_URL ?>
<p><label for="<?php echo $this->get_field_id( 'youtube' );  ?>"><?php _e( 'Youtube URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'youtube' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'youtube' ); ?>" value="<?php echo esc_attr( $instance['youtube'] ); ?>" />
</p>

		<?php //rss_URL ?>
<p><label for="<?php echo $this->get_field_id( 'rss' ); ?>"><?php _e( 'RSS URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'rss' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'rss' ); ?>" value="<?php echo esc_attr( $instance['rss'] ); ?>" />
</p>

		<?php //instagram_URL ?>
<p><label for="<?php echo $this->get_field_id( 'instagram' );  ?>"><?php _e( 'instagram URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'instagram' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'instagram' ); ?>" value="<?php echo esc_attr( $instance['instagram'] ); ?>" /></p>

		<?php //linkedin_URL ?>
<p><label for="<?php echo $this->get_field_id( 'linkedin' );  ?>"><?php _e( 'linkedin URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'linkedin' ); ?>" class="prof_input" name="<?php echo $this->get_field_name( 'linkedin' ); ?>" value="<?php echo esc_attr( $instance['linkedin'] ); ?>" /></p>

<?php // icon font type ?>

<p><?php _e( 'Icon Background:', 'vkExUnit' ); ?><br>

<?php
$checked = ( !isset( $instance[ 'iconFont_bgType' ] ) || !$instance[ 'iconFont_bgType' ] ) ? ' checked' : '';
?>
<input type="radio" id="<?php echo $this->get_field_id( 'iconFont_bgType' ).'_solid'; ?>" name="<?php echo $this->get_field_name( 'iconFont_bgType' ); ?>" value=""<?php echo $checked; ?> />
<label for="<?php echo $this->get_field_id( 'iconFont_bgType' ).'_solid'; ?>"> <?php _e( 'Solid color', 'vkExUnit' ); ?></label>
<?php $checked = ( isset( $instance[ 'iconFont_bgType' ] ) && $instance[ 'iconFont_bgType' ] === 'no_paint' ) ? ' checked' : ''; ?>
<input type="radio" id="<?php echo $this->get_field_id( 'iconFont_bgType' ).'_no_paint'; ?>" name="<?php echo $this->get_field_name( 'iconFont_bgType' ); ?>" value="no_paint"<?php echo $checked; ?> />
<label for="<?php echo $this->get_field_id( 'iconFont_bgType' ).'_no_paint'; ?>"><?php _e( 'No background', 'vkExUnit' ); ?></label>
</p>
<p>※ アイコンの背景を指定しない場合は各ブランドカラーが設定されます。</p>

<?php // icon font color ?>
<p class="color_picker_wrap">
<label for="<?php echo $this->get_field_id( 'icon_color' ); ?>"><?php _e( 'Icon color:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'icon_color' ); ?>" class="color_picker" name="<?php echo $this->get_field_name( 'icon_color' ); ?>" value="<?php echo esc_attr( $instance[ 'icon_color' ] ); ?>" /></p>

	<?php  }


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['label'] = $new_instance['label'];
		$instance['mediaFile'] = $new_instance['mediaFile'];
		$instance['mediaAlt'] = $new_instance['mediaAlt'];
		$instance['profile'] = $new_instance['profile'];
		$instance['mediaAlign_left'] = $new_instance['mediaAlign_left'];
		$instance['mediaRound'] = $new_instance['mediaRound'];
		$instance['mediaSize'] = $new_instance['mediaSize'];
		$instance['mediaFloat'] = $new_instance['mediaFloat'];
		$instance['facebook'] = $new_instance['facebook'];
		$instance['twitter'] = $new_instance['twitter'];
		$instance['mail'] = $new_instance['mail'];
		$instance['youtube'] = $new_instance['youtube'];
		$instance['rss'] = $new_instance['rss'];
		$instance['instagram'] = $new_instance['instagram'];
		$instance['linkedin'] = $new_instance['linkedin'];
		$instance['iconFont_bgType'] = $new_instance['iconFont_bgType'];
		$instance['icon_color'] = $new_instance['icon_color'];
		return $instance;
	}

	/*
	SNSアイコンに出力するCSSを出力する関数
	*/
	static public function outer_css( $iconFont_bgType, $icon_color ){
		if ( !$iconFont_bgType && !$icon_color ){
			$outer_css = '';
		} else if ( $iconFont_bgType == 'no_paint' ){
			if ( ! $icon_color ) {
				$icon_color = '#ccc';
			}
			$outer_css = ' style="border:1px solid '.$icon_color.';background:none;"';
		} else {
			$outer_css = ' style="border:1px solid '.$icon_color.';background-color:'.$icon_color.';"';
		}
		return $outer_css;
	}
	static public function icon_css( $iconFont_bgType, $icon_color ){
		if ( !$iconFont_bgType && !$icon_color ){
			$icon_css = '';
		} else if ( $iconFont_bgType == 'no_paint' ){
			// 線のとき
			if ( ! $icon_color ) {
				$icon_color = '#ccc';
			}
			$icon_css = ' style="color:'.$icon_color.';"';
		} else {
			// 塗りのとき
			$icon_css = ' style="color:#fff;"';
		}
		return $icon_css;
	}

	function widget( $args, $instance ) {
		// From here Display a widget
		echo $args['before_widget'];
		echo PHP_EOL.'<div class="veu_profile">'.PHP_EOL;

		if ( isset( $instance['label'] ) && $instance['label'] ) {
			echo $args['before_title'];
			echo $instance['label'];
			echo $args['after_title'];
		} ?>
<div class="profile" >
<?php // Display a profile image

$mediaRound = isset( $instance['mediaRound'] ) ? ' media_round' : '' ;
$mediaSize = isset( $instance['mediaSize'] ) ? mb_convert_kana( $instance['mediaSize'] ) : 'auto' ;
$mediaClass = '';

if ( ! empty( $instance['mediaFloat'] ) ) {
	$mediaClass .= ' media_float';
}
if ( ! empty( $instance['mediaAlign_left'] ) ) {
	$mediaClass .= ' media_left';
}
if( ! empty( $instance['mediaFile'] ) ){
	// 配置がフロート設定の場合 $mediaClass で該当のクラス付与
	echo '<div class="media_class'.$mediaClass.'">';
	//  画像が角丸設定の場合 $mediaRound でクラス付与
	echo '<img class="profile_media'.$mediaRound.'" src="'.esc_url( $instance['mediaFile'] ).'" width="'.$mediaSize.'" alt="'.esc_attr( $instance['mediaAlt'] ).'" />';
	echo '</div>';
}

// Display a profile text
if ( ! empty( $instance['profile'] ) ) {
	echo '<p class="profile_text">'.nl2br( wp_kses_post( $instance['profile'] )  ).'</p>'.PHP_EOL;
}

// Display a sns botton
if (
	isset( $instance['facebook'] ) && $instance['facebook'] ||
	isset( $instance['twitter'] ) && $instance['twitter'] ||
	isset( $instance['mail'] ) && $instance['mail'] ||
	isset( $instance['youtube'] ) && $instance['youtube'] ||
	isset( $instance['rss'] ) && $instance['rss'] ||
	isset( $instance['instagram'] ) && $instance['instagram'] ||
	isset( $instance['linkedin'] ) && $instance['linkedin'] ) :  ?>

<?php
if ( isset( $instance[ 'iconFont_bgType' ] ) ) {
	$iconFont_bgType = esc_html( $instance[ 'iconFont_bgType' ] );
} else {
	$iconFont_bgType = '';
}
if ( isset( $instance[ 'icon_color' ] ) ) {
	$icon_color = esc_html( $instance[ 'icon_color' ] );
} else {
	$icon_color = '';
}
$outer_css = $this->outer_css( $iconFont_bgType, $icon_color );
$icon_css = $this->icon_css( $iconFont_bgType, $icon_color );
?>
<ul class="sns_btns">
<?php
$sns_names = array( 'facebook', 'twitter', 'mail', 'youtube', 'rss', 'instagram', 'linkedin' );
foreach ( $sns_names as $key => $sns_name ) {
	if ( ! empty( $instance[$sns_name] ) ) { // $instance[$sns_name] 入力されたURLが返ってくる
		if ( $sns_name == 'mail') {
			$sns_name_class = 'envelope';
		} else {
			$sns_name_class = $sns_name;
		}
		echo '<li class="'.$sns_name.'_btn"><a href="'.esc_url( $instance[$sns_name] ).'" target="_blank"'.$outer_css.'><i class="fa fa-'.$sns_name_class.'"'.$icon_css.'></i></a></li>';
	} // if ( ! empty( $instance[$sns_name] ) ) :
} // foreach ( $sns_names as $key => $sns_name ) {
 ?>
</ul>
<?php endif; ?>

</div>
<!-- / .site-profile -->
</div>
<?php echo $args['after_widget'];
}
}

// Profile widget uploader js
function vkExUnit_profile_admin_scripts() {
	wp_enqueue_media();
	wp_register_script( 'mediauploader', plugin_dir_url( __FILE__ ) . 'js/widget-prof-uploader.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'mediauploader' );
}
add_action( 'admin_print_scripts', 'vkExUnit_profile_admin_scripts' );

// Profile widget CSS
function vkExUnit_profile_admin_style() {
	echo '<style>.prof_input{ width: 100%;}
.media_select,.media_clear{ padding: 3px; border: none; border-radius: 3px; background: #00a0d2; color: #fff; font-size: 12px; cursor: pointer; outline: none;}
.media_select:hover,.media_clear:hover{ background: #0073aa; }
.media{ position: relative; z-index: 2; overflow: hidden; margin: 3px 0; min-height: 70px; max-height: 200px; width: 100%;
border: 1px dashed #ccc; border-radius: 5px; background-color: rgba(212, 212, 212, 0.1);}
.media:before{ position: absolute; top: 50%; left: 50%; z-index: 1; margin: -8px 0 0 -30px; color: #999; content: "No Image";}
.media_image{ position: relative; z-index: 3; display: block; width: 100%; height: auto;}
.prof_input.textarea{ margin-top: -1em; }</style>'.PHP_EOL;
}
add_action( 'admin_print_styles-widgets.php', 'vkExUnit_profile_admin_style' );


add_action('widgets_init', 'vkExUnit_widget_register_profile');
function vkExUnit_widget_register_profile(){
	return register_widget("WP_Widget_vkExUnit_profile");
}
