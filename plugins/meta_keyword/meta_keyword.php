<?php

class vExUnit_meta_description {
    // singleton instance
    private static $instance;
 
    public static function instance() {
        if ( isset( self::$instance ) )
            return self::$instance;
 
        self::$instance = new vExUnit_meta_description;
        self::$instance->run_init();
        return self::$instance;
    }
 
    private function __construct() {
    }
 
    protected function run_init() {
        add_action('admin_init', array($this, 'option_init' ));
		add_action('admin_menu', array($this, 'add_custom_field'));
		add_action('save_post' , array($this, 'save_custom_field'));
		add_action('wp_head',    array($this, 'set_HeadKeywords' ), 1);
    }
 
	public function option_init() {
		vkExUnit_register_setting(
			__('Meta Keywords', 'vkExUnit'), 	// tab label.
			'vkExUnit_common_keywords',			// name attr
			array( $this, 'sanitize_config' ), // sanitaise function name
			array( $this, 'render_configPage' )  // setting_page function name
		);
	}

	public function get_default_option(){
		$option = '';
		return $option;
	}

	public function sanitize_config( $option ){
		$option = preg_replace('/^,*(.+)$/', '$1', $option);
		$option = preg_replace('/,*$/', '', $option);
		return $option;
	}


	public static function get_option(){
		return get_option( 'vkExUnit_common_keywords', '' );
	}


	public function render_configPage(){
?>
<h3><?php _e('Meta Description', 'vkExUnit'); ?></h3>
<div id="meta_description" class="sectionBox">
<table class="form-table">
<tr><th>共通キーワード</th>
<td>metaタグのキーワードで、サイト全体で共通して入れるキーワードを , 区切りで入力して下さい。<br />
<input type="text" name="vkExUnit_common_keywords" id="commonKeyWords" value="<?php echo self::get_option(); ?>" style="width:90%;" /><br />
* 現在は検索エンジンからの評価に影響しませんのであまり真剣に考えなくてもかまいません。<br />
* 各ページ個別のキーワードについては、それぞれの記事の編集画面より入力して下さい。共通キーワードと合わせて最大10個程度が望ましいです。<br />
* 最後のキーワード欄の末尾には , は必要ありません。<br />
【記入例】 WordPress,テンプレート,テーマ,無料,GPL</td></tr>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存"  /></p>
</div>
<?php
	}


	public function add_custom_field(){
		if(function_exists('add_custom_field_metaKeyword') || true){
			add_meta_box('div1', __('Meta Keywords', 'vkExUnit'), array( $this, 'render_meta_box' ), 'page', 'normal', 'high');
			add_meta_box('div1', __('Meta Keywords', 'vkExUnit'), array( $this, 'render_meta_box' ), 'post', 'normal', 'high');
		}
	}

	public function render_meta_box(){
		global $post;
		echo '<input type="hidden" name="_nonce_vkExUnit__custom_field_metaKeyword" id="_nonce_vkExUnit__custom_field_metaKeyword" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
		echo '<label class="hidden" for="vkExUnit_metaKeyword">'.__('Meta Keywords', 'biz-vektor').'</label><input type="text" id="vkExUnit_metaKeyword" name="vkExUnit_metaKeyword" size="50" value="'.get_post_meta($post->ID, 'vkExUnit_metaKeyword', true).'" />';
		echo '<p>'.__('To distinguish between individual keywords, please enter a , delimiter (optional).', 'vkExUnit').'<br />';
		$theme_option_seo_link = '<a href="'.get_admin_url().'/themes.php?page=theme_options#seoSetting" target="_blank">'._x('','link to seo setting', 'vkExUnit').'</a>';
		sprintf(__('* keywords common to the entire site can be set from %s.', 'vkExUnit'),$theme_option_seo_link);
		echo '</p>';
	}

	public function save_custom_field($post_id){
		$metaKeyword = isset($_POST['_nonce_vkExUnit__custom_field_metaKeyword']) ? htmlspecialchars($_POST['_nonce_vkExUnit__custom_field_metaKeyword']) : null;

	    // ドラフトなら破棄
	    if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
	       return $post_id;

		if(!wp_verify_nonce($metaKeyword, plugin_basename(__FILE__))){
			return $post_id;
		}
		if('page' == $_POST['vkExUnit_metaKeyword']){
			if(!current_user_can('edit_page', $post_id)) return $post_id;
		}else{
			if(!current_user_can('edit_post', $post_id)) return $post_id;
		}

		$data = $_POST['vkExUnit_metaKeyword'];

		if(get_post_meta($post_id, 'vkExUnit_metaKeyword') == ""){
			add_post_meta($post_id, 'vkExUnit_metaKeyword', $data, true);
		}elseif($data != get_post_meta($post_id, 'vkExUnit_metaKeyword', true)){
			update_post_meta($post_id, 'vkExUnit_metaKeyword', $data);
		}elseif($data == ""){
			delete_post_meta($post_id, 'vkExUnit_metaKeyword', get_post_meta($post_id, 'vkExUnit_metaKeyword', true));
		}
	}

	public function get_postKeyword(){
		$post_id = get_the_id();
		if(empty($post_id))
			return null;

		$keyword = get_post_meta($post_id, 'vkExUnit_metaKeyword', true);
		return $keyword;
	}

	public function set_HeadKeywords(){
		$commonKeyWords = self::get_option();
		// get custom field
		$entryKeyWords = self::get_postKeyword();
		$keywords = array();
		if($commonKeyWords){ $keywords[] = $commonKeyWords; }
		if($entryKeyWords){  $keywords[] = $entryKeyWords;  }
		$key = implode( ',', $keywords);
		// print individual keywords
		if(!$key){ return; }
		echo '<meta name="keywords" content="' . $key. '" />'."\n";
	}

}
 
vExUnit_meta_description::instance();

