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
			array( 'description' => __( 'Displays a your profile', 'vkExUnit' ) )
		);
	}


	/*-------------------------------------------*/
	/*  form
	/*-------------------------------------------*/
	function form( $instance )
	{
		$defaults = array(
			'label' => __( 'Profile', 'vkExUnit' ),
			'mediaFile' => '',
			'mediaAlt' => '',
			'mediaAlign_left' => '', // 'mediaAlign' に移行したので事実上廃止
			'mediaAlign' => 'left',
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
<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo esc_attr( $instance['label'] ); ?>" />
</p>

		<?php //media uploader ?>
<p><label for="<?php echo $this->get_field_id( 'profile' );  ?>"><?php _e( 'Select Profile image:', 'vkExUnit' ); ?></label><br/>

<div class="media_image_section">
	<div class="_display admin_widget_thumb_outer" style="height:auto">
	    <?php
			if ( ! empty( $instance[ 'mediaFile' ] ) ): ?>
	        <img src="<?php echo esc_url( $instance['mediaFile'] ); ?>" class="admin_widget_thumb" />
	    <?php endif; ?>
	</div>
	<button class="button button-default widget_media_btn_select" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_add(this);return false;"><?php _e('Select image', 'vkExUnit' ); ?></button>
	<button class="button button-default widget_media_btn_reset" style="text-align: center; margin:4px 0;" onclick="javascript:vk_widget_image_del(this);return false;"><?php _e('Clear image', 'vkExUnit' ); ?></button>
	<div class="_form" style="line-height: 2em">
	    <input type="hidden" class="_url" name="<?php echo $this->get_field_name( 'mediaFile' ); ?>" value="<?php echo esc_attr( $instance['mediaFile'] ); ?>" />
			<input type="hidden" class="_alt" name="<?php echo $this->get_field_name( 'mediaAlt' ); ?>" value="<?php echo esc_attr( $instance['mediaAlt'] ); ?>" />
	</div>
</div><!-- [ /.media_image_section ] -->

		<?php //image round setting ?>
<p><input type="checkbox" id="<?php echo $this->get_field_id( 'mediaRound' ); ?>" name="<?php echo $this->get_field_name( 'mediaRound' ); ?>" value="true" <?php echo ($instance['mediaRound'])? 'checked': '' ; ?> ><label for="<?php echo $this->get_field_id( 'mediaRound' );  ?>"><?php _e( 'Cut out round the image.', 'vkExUnit' ); ?></label>
</p>

		<?php //image size setting ?>
<p><label for="<?php echo $this->get_field_id( 'mediaSize' );  ?>"><?php _e( 'Media size (Optional)', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'mediaSize' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'mediaSize' ); ?>" style="width:50px;" value="<?php echo esc_attr( $instance['mediaSize'] ); ?>" /> px
</p>

		<?php //image mediaAlign_left setting ?>

<p>
	<?php $image_align = self::image_align( $instance ); ?>
	<?php $checked = ( $image_align === 'left' ) ? ' checked' : ''; ?>
	<input type="radio" id="<?php echo $this->get_field_id( 'mediaAlign' ); ?>_left" name="<?php echo $this->get_field_name( 'mediaAlign' ); ?>" value="left"<?php echo $checked; ?> />
	<label for="<?php echo $this->get_field_id( 'mediaAlign' ).'_left'; ?>"> <?php _e( 'Align left', 'vkExUnit' ); ?></label>
	<?php $checked = ( $image_align === 'center' ) ? ' checked' : ''; ?>
	<input type="radio" id="<?php echo $this->get_field_id( 'mediaAlign' ); ?>_center" name="<?php echo $this->get_field_name( 'mediaAlign' ); ?>" value="center"<?php echo $checked; ?> />
	<label for="<?php echo $this->get_field_id( 'mediaAlign' ).'_center'; ?>"> <?php _e( 'Align center', 'vkExUnit' ); ?></label>
</p>

		<?php //image float setting ?>
<p><input type="checkbox" id="<?php echo $this->get_field_id( 'mediaFloat' ); ?>" name="<?php echo $this->get_field_name( 'mediaFloat' ); ?>" value="true" <?php echo ($instance['mediaFloat'])? 'checked': '' ; ?> ><label for="<?php echo $this->get_field_id( 'mediaFloat' );  ?>"><?php _e( 'Text float to image.', 'vkExUnit' ); ?></label><br/>
</p>

		<?php //profile text ?>
<p><label for="<?php echo $this->get_field_id( 'profile' );  ?>"><?php _e( 'Profile Text:', 'vkExUnit' ); ?></label></p>
<textarea rows="4" cols="40" id="<?php echo $this->get_field_id( 'profile' ); ?>" class="admin_widget_input textarea" name="<?php echo $this->get_field_name( 'profile' ); ?>"><?php echo esc_textarea( $instance['profile'] ); ?></textarea>

		<?php //facebook_URL ?>
<p><label for="<?php echo $this->get_field_id( 'facebook' );  ?>"><?php _e( 'Facebook URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'facebook' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'facebook' ); ?>" value="<?php echo esc_attr( $instance['facebook'] ); ?>" />
</p>

		<?php //twitter_URL ?>
<p><label for="<?php echo $this->get_field_id( 'twitter' );  ?>"><?php _e( 'Twitter URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'twitter' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'twitter' ); ?>" value="<?php echo esc_attr( $instance['twitter'] ); ?>" />
</p>

		<?php //mail_URL ?>
<p><label for="<?php echo $this->get_field_id( 'mail' ); ?>"><?php _e( 'Email Address:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'mail' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'mail' ); ?>" value="<?php echo esc_attr( $instance['mail'] ); ?>" />
</p>

		<?php //youtube_URL ?>
<p><label for="<?php echo $this->get_field_id( 'youtube' );  ?>"><?php _e( 'Youtube URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'youtube' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'youtube' ); ?>" value="<?php echo esc_attr( $instance['youtube'] ); ?>" />
</p>

		<?php //rss_URL ?>
<p><label for="<?php echo $this->get_field_id( 'rss' ); ?>"><?php _e( 'RSS URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'rss' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'rss' ); ?>" value="<?php echo esc_attr( $instance['rss'] ); ?>" />
</p>

		<?php //instagram_URL ?>
<p><label for="<?php echo $this->get_field_id( 'instagram' );  ?>"><?php _e( 'instagram URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'instagram' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'instagram' ); ?>" value="<?php echo esc_attr( $instance['instagram'] ); ?>" /></p>

		<?php //linkedin_URL ?>
<p><label for="<?php echo $this->get_field_id( 'linkedin' );  ?>"><?php _e( 'linkedin URL:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'linkedin' ); ?>" class="admin_widget_input" name="<?php echo $this->get_field_name( 'linkedin' ); ?>" value="<?php echo esc_attr( $instance['linkedin'] ); ?>" /></p>

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
<p><?php _e( '* When "Icon Background: Fill" is selected and "Icon color" is not specified, each brand color will be painted.', 'vkExUnit' ); ?></p>

<?php // icon font color ?>
<p class="color_picker_wrap">
<label for="<?php echo $this->get_field_id( 'icon_color' ); ?>"><?php _e( 'Icon color:', 'vkExUnit' ); ?></label><br/>
<input type="text" id="<?php echo $this->get_field_id( 'icon_color' ); ?>" class="color_picker" name="<?php echo $this->get_field_name( 'icon_color' ); ?>" value="<?php echo esc_attr( $instance[ 'icon_color' ] ); ?>" /></p>

	<?php  }

	/*-------------------------------------------*/
	/*  update
	/*-------------------------------------------*/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['label'] = $new_instance['label'];
		$instance['mediaFile'] = $new_instance['mediaFile'];
		$instance['mediaAlt'] = $new_instance['mediaAlt'];
		$instance['profile'] = $new_instance['profile'];
		$instance['mediaAlign_left'] = $new_instance['mediaAlign_left'];
		$instance['mediaAlign'] = $new_instance['mediaAlign'];
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
	/*-------------------------------------------*/
	/*  SNSアイコンに出力するCSSを出力する関数
	/*-------------------------------------------*/
	static public function outer_css( $instance )
	{
		// iconFont_bgType が定義されている場合
		if ( isset( $instance[ 'iconFont_bgType' ] ) ) {
			$iconFont_bgType = esc_html( $instance[ 'iconFont_bgType' ] ); // 中身が ''の場合もありえる
		} else {
			$iconFont_bgType = '';
		}

		// icon_color が定義されている場合
		if ( isset( $instance[ 'icon_color' ] ) ) {
			$icon_color = esc_html( $instance[ 'icon_color' ] );
		} else {
			$icon_color = '';
		}

		// 背景塗り && 色指定がない場合
		if ( !$iconFont_bgType && !$icon_color ){
			// （ ExUnitのCSSファイルに書かれている色が適用されているので個別には出力しなくてよい ）
			$outer_css = '';

		// 背景なし枠線の場合
		} else if ( $iconFont_bgType == 'no_paint' ){
			// 色指定がない場合
			if ( ! $icon_color ) {
				$icon_color = '#ccc';
			}
			$outer_css = ' style="border:1px solid '.$icon_color.';background:none;"';

		// それ以外（ 背景塗りの時 ）
		} else {
			$outer_css = ' style="border:1px solid '.$icon_color.';background-color:'.$icon_color.';"';
		}
		return $outer_css;
	}


	static public function icon_css( $instance )
	{
		// iconFont_bgType が定義されている場合
		if ( isset( $instance[ 'iconFont_bgType' ] ) ) {
			$iconFont_bgType = esc_html( $instance[ 'iconFont_bgType' ] ); // 中身が ''の場合もありえる
		} else {
			$iconFont_bgType = '';
		}

		// icon_color が定義されている場合
		if ( isset( $instance[ 'icon_color' ] ) ) {
			$icon_color = esc_html( $instance[ 'icon_color' ] );
		} else {
			$icon_color = '';
		}

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

	/*
		@seince 6.0.0
	 */
	static public function image_align( $instance )
	{
		$image_align = 'left';
		// 新フィールド（media_align）未保存の場合
		if ( ! isset( $instance['mediaAlign'] ) ) {
			$image_align = 'center';
			if ( isset( $instance['mediaAlign_left'] ) && $instance['mediaAlign_left'] ){
				$image_align = 'left';
			}
		}

		if ( isset( $instance['mediaAlign'] ) ) {
			if ( $instance['mediaAlign'] == 'left' ){
				$image_align = 'left';
			} elseif ( $instance['mediaAlign'] == 'center' ) {
				$image_align = 'center';
			}
		}
		return $image_align;
	} // static public function image_align( $instance )

	static public function image_outer_size_css( $instance )
	{

		if( empty( $instance['mediaRound'] ) ){
			/* ピン角の場合 */

			if( empty( $instance['mediaSize'] ) ){
				// 画像サイズ指定がない場合
				$media_outer_size_css = '';

			} elseif ( $instance['mediaSize'] ){
				// 画像サイズ指定がある場合
				$media_outer_size_css = 'width:'.esc_attr( mb_convert_kana( $instance['mediaSize'] ) ).'px;';
			}

		} elseif ( $instance['mediaRound'] ) {
			// 丸抜き指定の場合
			if ( isset( $instance['mediaSize'] ) && $instance['mediaSize'] ){
				// サイズ指定がある場合
				$media_outer_size_css = 'width:'.esc_attr( $instance['mediaSize'] ).'px;';
				$media_outer_size_css .= 'height:'.esc_attr( $instance['mediaSize'] ).'px;';
			} else {
				$media_outer_size_css = '';
			}

		}

		return $media_outer_size_css;
	}

	/*-------------------------------------------*/
	/*  widget
	/*-------------------------------------------*/
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

	if( ! empty( $instance['mediaFile'] ) ){

		// $outer_css
		/*-------------------------------------------*/
		$outer_class = '';

		if ( ! empty( $instance['mediaFloat'] ) ) {
			$outer_class .= ' media_float';
		}

		if ( ! empty( $instance['mediaRound'] ) ) {
			$outer_class .= ' media_round';
		}

		// image align
		// 抜きなし / 中央 / サイズ指定なし の場合、画像で中央揃え
		// 抜きあり / 中央 / サイズ指定なし の場合、外枠で中央揃え のため、外枠の class 基準でcssをあてる
		$media_align = self::image_align( $instance );
		if ( $media_align == 'center' ){
			$outer_class .= ' media_center';
		} elseif ( $media_align == 'left' ){
			$outer_class .= ' media_left';
		}

		// $outer_css
		/*-------------------------------------------*/
		$outer_css = '';

		// image size
		$outer_css .= self::image_outer_size_css( $instance );

		if ( ! empty( $instance['mediaRound'] ) ) {
			$outer_css .= 'background:url('.esc_url( $instance[ 'mediaFile' ] ).') no-repeat 50% center;background-size: cover;';
		}

		echo '<div class="media_outer'.$outer_class.'" style="'.$outer_css.'">';
		//  画像が角丸設定の場合 $mediaRound でクラス付与
		echo '<img class="profile_media" src="'.esc_url( $instance['mediaFile'] ).'" alt="'.esc_attr( $instance['mediaAlt'] ).'" />';
		echo '</div>';

} // if( ! empty( $instance['mediaFile'] ) ){

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
$outer_css = $this->outer_css( $instance );
$icon_css = $this->icon_css( $instance );
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
	wp_register_script( 'vk-admin-widget', plugin_dir_url( __FILE__ ) . 'js/admin-widget.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'vk-admin-widget' );
}
add_action( 'admin_print_scripts', 'vkExUnit_profile_admin_scripts' );

add_action('widgets_init', 'vkExUnit_widget_register_profile');
function vkExUnit_widget_register_profile(){
	return register_widget("WP_Widget_vkExUnit_profile");
}
