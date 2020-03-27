<?php
/**
 * VkExUnit meta_keyword.php
 * Set meta tag of keyword for single page each
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    26/Jun/2015
 */

 /*
 VEU_Metabox 内の get_post_type が実行タイミングによっては
 カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
 admin_menu のタイミングで読み込んでいる
  */
add_action(
	'admin_menu', function() {
		require_once( dirname( __FILE__ ) . '/class-veu-metabox-meta-keyword.php' );
	}
);

class vExUnit_meta_keywords {

	private static $instance;

	public static function instance() {
		if ( isset( self::$instance ) ) {
			return self::$instance; }

		self::$instance = new vExUnit_meta_keywords;
		self::$instance->run_init();
		return self::$instance;
	}


	private function __construct() {
		/***    do noting    ***/
	}


	protected function run_init() {
		add_action( 'veu_package_init', array( $this, 'option_init' ) );
		add_action( 'wp_head', array( $this, 'set_HeadKeywords' ), 1 );
	}


	public function option_init() {
		vkExUnit_register_setting(
			__( 'Meta Keywords', 'vk-all-in-one-expansion-unit' ),      // tab label.
			'vkExUnit_common_keywords',             // name attr
			array( $this, 'sanitize_config' ),      // sanitaise function name
			array( $this, 'render_configPage' )     // setting_page function name
		);
	}


	public function get_default_option() {
		$option = '';
		return $option;
	}


	public function sanitize_config( $option ) {
		$option = preg_replace( '/^,*(.+)$/', '$1', $option );
		$option = preg_replace( '/,*$/', '', $option );
		return $option;
	}


	public static function get_option() {
		return get_option( 'vkExUnit_common_keywords', '' );
	}


	public function render_configPage() {
	?>
 <h3><?php _e( 'Meta Keyword', 'vk-all-in-one-expansion-unit' ); ?></h3>
<div id="meta_keyword" class="sectionBox">
<table class="form-table">
<tr><th><?php _e( 'Common Keywords', 'vk-all-in-one-expansion-unit' ); ?></th>
<td><?php _e( 'Keywords for meta tag. This words will set Meta Keyword with post keywords. if you want multiple keywords, enter with separator of ",".', 'vk-all-in-one-expansion-unit' ); ?><br />
<input type="text" name="vkExUnit_common_keywords" id="commonKeyWords" value="<?php echo self::get_option(); ?>" style="width:90%;" /><br />

* <?php _e( 'This is not seriously, Because the SearchEngine does not care this.', 'vk-all-in-one-expansion-unit' ); ?><br/>
* <?php _e( 'For each page individual keyword is enter at the edit screen of each article. 10 keywords maximum, together with a each article keywords is desirable.', 'vk-all-in-one-expansion-unit' ); ?><br/>
* <?php _e( '"," separator at end of the last keyword is do not need.', 'vk-all-in-one-expansion-unit' ); ?><br/>
<?php _e( 'Example: WordPress,template,theme,free,GPL', 'vk-all-in-one-expansion-unit' ); ?></td></tr>
</table>
<?php submit_button(); ?>
</div>
<?php
	}

	public static function get_postKeyword() {
		$post_id = get_the_id();

		if ( empty( $post_id ) ) {
			return null;
		}

		$keyword = get_post_meta( $post_id, 'vkExUnit_metaKeyword', true );
		if ( !empty($keyword) ) {
			return $keyword;
		}

		$keyword = get_post_meta( $post_id, 'vkExUnit_common_keywords', true );
		if ( !empty($keyword) ) {
			return $keyword;
		}

	}

	public function set_HeadKeywords() {
		$commonKeyWords = self::get_option();
		// get custom field
		$entryKeyWords = self::get_postKeyword();

		$keywords      = array();
		if ( $commonKeyWords ) {
			$keywords[] = $commonKeyWords; }
		if ( $entryKeyWords ) {
			$keywords[] = $entryKeyWords;  }
		$key = implode( ',', $keywords );

		// print individual keywords
		if ( ! $key ) {
			return; }
		echo '<meta name="keywords" content="' . $key . '" />' . "\n";
	}
}

vExUnit_meta_keywords::instance();
