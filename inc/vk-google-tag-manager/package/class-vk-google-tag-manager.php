<?php
/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
 → まだない。Lightning Pro がマスター
*/

// add_action( 'after_setup_theme', 'vkmn_nav_add_customize_panel' );
//
// // カスタマイズパネルを出力するかどうかの判別
// function vkmn_nav_add_customize_panel() {
// 		// カスタマイザーが利用されるので、独自のコントロールクラスを追加
//
// }


if ( ! class_exists( 'Vk_Goole_Tag_Manager' ) ) {

	class Vk_Goole_Tag_Manager {


		public static $version = '0.0.0';

		/*-------------------------------------------*/
		/*	Customizer
		/*-------------------------------------------*/

		public function __construct() {
			/*
			このカスタマイザーで利用している ExUnit_Custom_Text_Control クラス（ ExUnitの /admin/customizer.php ）を
			登録する 関数（ veu_customize_register_add_control() ）を読み込む ための メソッド（ load_veu_customize_register_add_control() ）を読み込む。
			※直接 veu_customize_register_add_control() を読み込まないのは、もし ExUnit が有効化されてないときに
			veu_customize_register_add_control が存在しないので、ここで直接読み込んでいるとエラーになるため。
			※ ExUnitでも ExUnit_Custom_Text_Control は登録されているが after_setup_theme のタイミングのあとで登録されているので、
			もっと早い段階で読み込まれるように、plugins_loaded で読み込んでいる。
			*/
			add_action( 'plugins_loaded', array( $this, 'load_veu_customize_register_add_control' ) );

			add_action( 'customize_register', array( $this, 'vk_google_tag_manager_customize_register' ), 11 );
		}

		public function load_veu_customize_register_add_control() {
			// veu_customize_register_add_control() が（書いてあるファイルが）読み込まてていたら
			if ( function_exists( 'veu_customize_register_add_control' ) ) {
				/*
				customize_register のタイミングで veu_customize_register_add_control() を実行し、
				ExUnit_Custom_Text_Control を使えるようにしている
				*/
				add_action( 'customize_register', 'veu_customize_register_add_control', 10 );
			}
		}

		public function vk_google_tag_manager_customize_register( $wp_customize ) {

			global $vk_gtm_prefix;
			global $vk_gtm_priority;
			global $vk_gtm_panel;
			if ( ! $vk_gtm_panel ) {
				$vk_gtm_panel = '';
			}
			if ( ! $vk_gtm_priority ){
				$vk_gtm_priority = 900;
			}

			// セクション追加
			$wp_customize->add_section(
				'vk_google_tag_manager_related_setting', array(
					'title'    => $vk_gtm_prefix . __( 'Google Tag Manager', 'vk-all-in-one-expansion-unit' ),
					'priority' => $vk_gtm_priority,
					'panel'    => $vk_gtm_panel,
				)
			);

			// Google Tag Manager ID
			$wp_customize->add_setting(
				'vk_google_tag_manager_related_options[gtm_id]',
				array(
					'default'           => '',
					'type'              => 'option', // 保存先 option or theme_mod
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			if ( class_exists( 'ExUnit_Custom_Text_Control' ) ) {
				$wp_customize->add_control(
					new ExUnit_Custom_Text_Control(
						$wp_customize, 'gtm_id', array(
							'label'        => __( 'Google tag manager ID :', 'vk-all-in-one-expansion-unit' ),
							'section'      => 'vk_google_tag_manager_related_setting',
							'settings'     => 'vk_google_tag_manager_related_options[gtm_id]',
							'type'         => 'text',
							'description'  => __( 'Please enter the Google Tag Manager ID to use on this site.', 'vk-all-in-one-expansion-unit' ),
							'input_before' => 'GTM-',
						)
					)
				);
			} else {
				$wp_customize->add_control(
					'gtm_id', array(
						'label'       => __( 'Google tag manager ID :', 'vk-all-in-one-expansion-unit' ),
						'section'     => 'vk_google_tag_manager_related_setting',
						'settings'    => 'vk_google_tag_manager_related_options[gtm_id]',
						'type'        => 'text',
						'description' => __( 'Please enter the Google Tag Manager ID to use on this site.', 'vk-all-in-one-expansion-unit' ),
					)
				);
			}

			// gtm_id セッティング
			// $wp_customize->add_setting(
			// 		'vk_google_tag_manager_related_options[gtm_id]', array(
			// 		'default'           => '',
			// 		'type'              => 'option', // 保存先 option or theme_mod
			// 		'capability'        => 'edit_theme_options', // サイト編集者
			// 		'sanitize_callback' => 'sanitize_text_field',
			// 		)
			// );

			// gtm_id コントロール
			// $wp_customize->add_control(
			// 		'gtm_id', array(
			// 		'label'    => __( 'Google tag manager ID :', 'vk-all-in-one-expansion-unit' ),
			// 		'section'  => 'vk_google_tag_manager_related_setting',
			// 		'settings' => 'vk_google_tag_manager_related_options[gtm_id]',
			// 		'type'     => 'text',
			// 		)
			// );

			/*-------------------------------------------*/
			/*	Add Edit Customize Link Btn
			/*-------------------------------------------*/
			// $wp_customize->selective_refresh->add_partial(
			//   'vk_google_tag_manager_related_options[nav_bg_color]', array(
			//     'selector'        => '.mobil-fix-nav',
			//     'render_callback' => '',
			//   )
			// );

		} // function vk_google_tag_manager_customize_register( $wp_customize ) {
	} // class Vk_Goole_Tag_Manager {

	$vk_mobile_fix_nav = new Vk_Goole_Tag_Manager();

} // if ( ! class_exists('Vk_Goole_Tag_Manager') )  {

// head に読み込むコード
add_action( 'wp_head', 'vk_gtm_head', 0 );
function vk_gtm_head() {

	$options = get_option( 'vk_google_tag_manager_related_options' );
	if ( isset( $options['gtm_id'] ) && $options['gtm_id'] ) {
		$gtm_id = $options['gtm_id'];

		// GTM head コード
		$gtm_head =
		"<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-" . esc_html( $gtm_id ) . "');</script>
<!-- End Google Tag Manager -->";

		echo $gtm_head;
	} // if( isset( 'vk_google_tag_manager_related_options'['head_code'] ) ) {

} // function vk_gtm_head() {

// body に読み込むコード
add_action( 'wp_body_open', 'vk_gtm_body', 0 );
function vk_gtm_body() {
	$options = get_option( 'vk_google_tag_manager_related_options' );
	if ( isset( $options['gtm_id'] ) && $options['gtm_id'] ) {
		$gtm_id = $options['gtm_id'];

		// GTM body コード
		$gtm_body =
		'<!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-' . esc_html( $gtm_id ) . '" title="Google Tag Manager (noscript)" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->';

		echo $gtm_body;
	} // if( isset( 'vk_google_tag_manager_related_options'['body_code'] ) ) {
} // function vk_gtm_body() {
