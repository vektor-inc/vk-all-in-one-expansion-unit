<?php

class vExUnit_icons {
    // singleton instance
    private static $instance;
 
    public static function instance() {
        if ( isset( self::$instance ) )
            return self::$instance;
 
        self::$instance = new vExUnit_icons;
        self::$instance->run_init();
        return self::$instance;
    }
 
    private function __construct() {
    }
 
    protected function run_init() {
        add_action('admin_init', array($this, 'option_init' ));
		add_action('wp_head',    array($this, 'output_tag' ));
    }
 
	public function option_init() {
		vkExUnit_register_setting(
			__('icon setting', 'vkExUnit'), 	// tab label.
			'vkExUnit_icon_settings',			// name attr
			array( $this, 'sanitize_config' ), // sanitaise function name
			array( $this, 'render_configPage' )  // setting_page function name
		);
	}

	public static function get_default_option(){
		$option = array('favicon' => null, 'sp'=>null);
		return $option;
	}

	public function sanitize_config( $option ){

		$output = self::get_default_option();
		$output['favicon'] = $option['favicon'];
		$output['sp']      = $option['sp'];
		return $output;
	}


	public static function get_option(){
		return get_option( 'vkExUnit_icon_settings', self::get_default_option() );
	}


	public function render_configPage(){
		$options = self::get_option();
?>
<h3><?php _e('icon setting', 'vkExUnit'); ?></h3>
<div id="on_setting" class="sectionBox">
<table class="form-table">
	<!-- Favicon -->
	<tr>
	<th>Favicon設定</th>
		<td><input type="text" name="vkExUnit_icon_settings[favicon]" id="favicon" value="<?php echo $options['favicon'] ?>" style="width:60%;" /> 
	<button id="media_favicon" class="media_btn">画像を選択</button>
	<p>作成したicoファイルをアップロードしてください。</p>
	</td>
	</tr>
	<!-- Favicon -->
	<tr>
	<th>ウェブクリップアイコン設定</th>
		<td><input type="text" name="vkExUnit_icon_settings[sp]" id="sp" value="<?php echo $options['sp'] ?>" style="width:60%;" /> 
	<button id="media_sp" class="media_btn">画像を選択</button>
	<p>スマートフォンでウェブページのショートカット作成時にアイコンとして使われる画像を設定します。114x114以上の正方形画像を設定してください。</p>
	</td>
	</tr>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存"  /></p>
</div>
<?php
	}


	public function output_tag(){
		$options = self::get_option();
		if(isset($options['favicon']) && $options['favicon']){
			echo '<link rel="SHORTCUT ICON" HREF="'.$options['favicon'].'" />';
		}
		if(isset($options['sp']) && $options['sp']){
			echo '<link rel="apple-touch-icon-precomposed" HREF="'.$options['sp'].'" />';
		}
	}

}
 
vExUnit_icons::instance();