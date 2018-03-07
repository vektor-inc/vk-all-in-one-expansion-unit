<?php
/*-------------------------------------------*/
/*  Options Init
/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/
/*  set global $vkExUnit_sns_options
/*-------------------------------------------*/
/*  Add facebook aprication id
/*-------------------------------------------*/
/*  SNSアイコンに出力するCSSを出力する関数
/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/

// シェアボタンを表示する設定の読み込み
require_once(dirname(__FILE__)."/hide_controller.php");

function veu_sns_options_init() {
	if ( false === veu_get_sns_options() ) {
		add_option( 'vkExUnit_sns_options', veu_get_sns_options_default() );
	}
	vkExUnit_register_setting(
		__( 'SNS', 'vkExUnit' ), 	// tab label.
		'vkExUnit_sns_options',			// name attr
		'vkExUnit_sns_options_validate', // sanitaise function name
		'vkExUnit_add_sns_options_page'  // setting_page function name
	);
}
add_action( 'vkExUnit_package_init', 'veu_sns_options_init' );

function veu_get_sns_options() {
	$options			= get_option( 'vkExUnit_sns_options', veu_get_sns_options_default() );
	$options_dafault	= veu_get_sns_options_default();
	foreach ( $options_dafault as $key => $value ) {
		$options[ $key ] = (isset( $options[ $key ] )) ? $options[ $key ] : $options_dafault[ $key ];
	}
	return apply_filters( 'vkExUnit_sns_options', $options );
}

function veu_get_sns_options_default() {
	$default_options = array(
		'fbAppId' 									=> '',
		'fbPageUrl' 								=> '',
		'ogImage' 									=> '',
		'twitterId' 								=> '',
		'enableOGTags' 							=> true,
		'enableTwitterCardTags' 		=> true,
		'enableSnsBtns' 						=> true,
		'snsBtn_exclude_post_types' => array( 'post' => '', 'page' => '' ),
		'snsBtn_ignorePosts'				=> '',
		'snsBtn_bg_fill_not'				=> false,
		'snsBtn_color'							=> false,
		'enableFollowMe' 						=> true,
		'followMe_title'						=> 'Follow me!',
		'useFacebook'								=> true,
		'useTwitter'								=> true,
		'useHatena'									=> true,
		'usePocket'									=> true,
		'useLine'										=> true,
	);
	return apply_filters( 'vkExUnit_sns_options_default', $default_options );
}

/*-------------------------------------------*/
/*  validate
/*-------------------------------------------*/

function vkExUnit_sns_options_validate( $input ) {
	$output = $defaults = veu_get_sns_options_default();

	$output['fbAppId']										= esc_attr( $input['fbAppId'] );
	$output['fbPageUrl']									= esc_url( $input['fbPageUrl'] );
	$output['ogImage']										= esc_url( $input['ogImage'] );
	$output['twitterId']									= esc_attr( $input['twitterId'] );
	$output['snsBtn_ignorePosts']					= preg_replace('/[^0-9,]/', '', $input['snsBtn_ignorePosts']);
	$output['enableOGTags']  							= ( isset( $input['enableOGTags'] ) && $input['enableOGTags'] ) ? true: false;
	$output['enableTwitterCardTags']  		= ( isset( $input['enableTwitterCardTags'] ) && $input['enableTwitterCardTags'] ) ? true: false;
	$output['enableSnsBtns']   						= ( isset( $input['enableSnsBtns'] ) && $input['enableSnsBtns'] ) ? true: false;
	$output['snsBtn_exclude_post_types']	= ( isset( $input['snsBtn_exclude_post_types'] ) ) ? $input['snsBtn_exclude_post_types'] : '';
	$output['enableFollowMe']  						= ( isset( $input['enableFollowMe'] ) && $input['enableFollowMe'] )? true: false;
	$output['followMe_title']							= $input['followMe_title'];
	$output['useFacebook']								= ( isset( $input['useFacebook'] ) && $input['useFacebook'] == 'true' );
	$output['useTwitter']									= ( isset( $input['useTwitter'] ) && $input['useTwitter'] == 'true' );
	$output['useHatena']									= ( isset( $input['useHatena'] ) && $input['useHatena'] == 'true' );
	$output['usePocket']									= ( isset( $input['usePocket'] ) && $input['usePocket'] == 'true' );
	$output['useLine']										= ( isset( $input['useLine'] ) && $input['useLine'] == 'true' );

	/*
	SNSボタンの塗りつぶし関連は管理画面に値がないので、カスタマイザーで保存された値を入れる必要がある
	既に保存されている値をアップデート用にそのまま返すだけなのでサニタイズしていない
	 */
	$options_old = get_option( 'vkExUnit_sns_options');
	$output['snsBtn_bg_fill_not']	=	$options_old['snsBtn_bg_fill_not'];
	$output['snsBtn_color']	=	$options_old['snsBtn_color'];

	return apply_filters( 'vkExUnit_sns_options_validate', $output, $input, $defaults );
}

/*-------------------------------------------*/
/*  set global $vkExUnit_sns_options
/*-------------------------------------------*/
add_action( 'wp_head', 'vkExUnit_set_sns_options',1 );
function vkExUnit_set_sns_options() {
	global $vkExUnit_sns_options;
	$vkExUnit_sns_options = veu_get_sns_options();
}

/*-------------------------------------------*/
/*  Add facebook aprication id
/*-------------------------------------------*/
add_action( 'wp_footer', 'exUnit_print_fbId_script' );
function exUnit_print_fbId_script() {
?>
<div id="fb-root"></div>
<?php
$options = veu_get_sns_options();
$fbAppId = (isset( $options['fbAppId'] )) ? $options['fbAppId'] : '';
?>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/<?php echo esc_attr(_x('en_US', 'facebook language code', 'vkExUnit'));?>/sdk.js#xfbml=1&version=v2.9&appId=<?php echo esc_html( $fbAppId );?>";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<?php //endif;
}

$vkExUnit_sns_options = veu_get_sns_options();

require vkExUnit_get_directory() . '/plugins/sns/function_fbPagePlugin.php';

if ( $vkExUnit_sns_options['enableOGTags'] == true ) {
	require vkExUnit_get_directory() . '/plugins/sns/function_og.php'; }
if ( $vkExUnit_sns_options['enableSnsBtns'] == true ) {
	require vkExUnit_get_directory() . '/plugins/sns/function_snsBtns.php'; }
if ( $vkExUnit_sns_options['enableTwitterCardTags'] == true ) {
	require vkExUnit_get_directory() . '/plugins/sns/function_twitterCard.php'; }
if ( $vkExUnit_sns_options['enableFollowMe'] == true ) {
	require vkExUnit_get_directory() . '/plugins/sns/function_follow.php'; }

require vkExUnit_get_directory() . '/plugins/sns/function_meta_box.php';

/*-------------------------------------------*/
/*  Add setting page
/*-------------------------------------------*/

function vkExUnit_add_sns_options_page() {
	require dirname( __FILE__ ) . '/sns_admin.php';
	?>
	<?php
}

/*-------------------------------------------*/
/*  Add Customize Panel
/*-------------------------------------------*/
add_filter( 'veu_customize_panel_activation', 'veu_customize_panel_activation_sns' );
function veu_customize_panel_activation_sns(){
	return true;
}

if ( apply_filters('veu_customize_panel_activation', false ) ){
	add_action( 'customize_register', 'veu_customize_register_sns' );
}

function veu_customize_register_sns( $wp_customize ) {

	/*	Add text control description
	/*-------------------------------------------*/
	class ExUnit_Custom_Text_Control extends WP_Customize_Control {
		public $type        = 'customtext';
		public $description = ''; // we add this for the extra description
		public $input_before = '';
		public $input_after = '';
		public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php $style = ( $this->input_before || $this->input_after ) ? ' style="width:50%"' : '';?>
			<div>
			<?php echo wp_kses_post( $this->input_before ); ?>
			<input type="text" value="<?php echo esc_attr( $this->value() ); ?>"<?php echo $style; ?> <?php $this->link(); ?> />
			<?php echo wp_kses_post( $this->input_after ); ?>
			</div>
			<span><?php echo $this->description; ?></span>
		</label>
		<?php
		} // public function render_content() {
	} // class Custom_Text_Control extends WP_Customize_Control


 	/*-------------------------------------------*/
 	/*	SNS Settings
 	/*-------------------------------------------*/
  //1. テーマカスタマイザー上に新しいセクションを追加
 	$wp_customize->add_section( 'veu_sns_setting',
		array(
	 		'title'				=> __('SNS Settings', 'vkExUnit'),
	 		'priority'		=> 1000,
	 		'panel'				=> 'veu_setting',
		)
	);

	//2. WPデータベースに新しいテーマ設定を追加
  // Bin bg fill
 	$wp_customize->add_setting('vkExUnit_sns_options[snsBtn_bg_fill_not]',
		array(
	 		'default'			      => false,
	    'type'				      => 'option', // 保存先 option or theme_mod
	 		'capability'		    => 'edit_theme_options',
	 		'sanitize_callback' => 'veu_sanitize_boolean',
	 	)
	);

 	$wp_customize->add_control( 'snsBtn_bg_fill_not',
		array(
	 		'label'		  => __( 'No background', 'vkExUnit' ),
	 		'section'	  => 'veu_sns_setting',
	 		'settings'  => 'vkExUnit_sns_options[snsBtn_bg_fill_not]',
	 		'type'		  => 'checkbox',
	 		'priority'	=> 1,
	 	)
	);

  // Btn color
  $wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_color]',
		array(
	 		'default'			      => false,
	    'type'				      => 'option', // 保存先 option or theme_mod
	 		'capability'		    => 'edit_theme_options',
	 		'sanitize_callback' => 'sanitize_hex_color',
	 	)
	);

   $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'snsBtn_color',
	 	array(
			'label'    => __('Btn color', 'vkExUnit'),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[snsBtn_color]',
			'priority' => 1,
   	)
	 ));

   // $wp_customize->get_setting( 'vkExUnit_sns_options[snsBtn_bg_fill_not]' )->transport        = 'postMessage';

	// Facebook application ID
 	$wp_customize->add_setting( 'vkExUnit_sns_options[fbAppId]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
 	);

 	$wp_customize->add_control( 'fbAppId',
		array(
			'label'    => __( 'Facebook application ID', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[fbAppId]',
			'type'     => 'text',
			'priority' => 1,
		)
 	);

	// Facebook Page URL
 	$wp_customize->add_setting( 'vkExUnit_sns_options[fbPageUrl]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
 	);

 	$wp_customize->add_control( 'fbPageUrl',
		array(
			'label'    => __( 'Facebook Page URL', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[fbPageUrl]',
			'type'     => 'text',
			'priority' => 1,
		)
 	);

	// OG default image
	$wp_customize->add_setting( 'vkExUnit_sns_options[ogImage]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			'ogImage',
			array(
				'label'       => __( 'OG default image', 'vkExUnit' ),
				'section'     => 'veu_sns_setting',
				'settings'    => 'vkExUnit_sns_options[ogImage]',
				'priority'    => 1,
				'description' => __( 'If, for example someone pressed the Facebook [Like] button, this is the image that appears on the Facebook timeline.<br>If a featured image is specified for the page, it takes precedence.<br>ex) https://www.vektor-inc.co.jp/images/ogImage.png<br>* Picture sizes are 300x300 pixels or more and picture ratio 16:9 is recommended.', 'vkExUnit' ),
			)
		)
	);

	// Twitter ID
	$wp_customize->add_setting( 'vkExUnit_sns_options[twitterId]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		new ExUnit_Custom_Text_Control(
			$wp_customize, 'twitterId', array(
				'label'    => __( 'Twitter ID', 'vkExUnit' ),
				'section'  => 'veu_sns_setting',
				'settings' => 'vkExUnit_sns_options[twitterId]',
				'type'     => 'text',
				'priority' => 1,
				'description' => '',
				'input_before' => '@',
			)
		)
	);

	// Print the OG tags
	$wp_customize->add_setting('vkExUnit_sns_options[enableOGTags]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'enableOGTags',
		array(
			'label'		    => __( 'Print the OG tags', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableOGTags]',
			'type'		    => 'checkbox',
			'description' => __( 'If other plug-ins are used for the OG, do not output the OG using this plugin.', 'vkExUnit' ),
			'priority'	  => 1,
		)
	);

	// Twitter Card tags
	$wp_customize->add_setting('vkExUnit_sns_options[enableTwitterCardTags]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'enableTwitterCardTags',
		array(
			'label'		    => __( 'Twitter Card tags', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableTwitterCardTags]',
			'type'		    => 'checkbox',
			'description' => __( 'Print the Twitter Card tags', 'vkExUnit' ),
			'priority'	  => 1,
		)
	);

	// Social bookmark buttons
	$wp_customize->add_setting('vkExUnit_sns_options[enableSnsBtns]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'enableSnsBtns',
		array(
			'label'		    => __( 'Social bookmark buttons', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableSnsBtns]',
			'type'		    => 'checkbox',
			'description' => __( 'Print the social bookmark buttons', 'vkExUnit' ),
			'priority'	  => 1,
		)
	);

	$args = array(
		'public'   => true,
	);
	$post_types = get_post_types($args,'object');
	foreach ($post_types as $key => $value) {
		if ( $key != 'attachment' ) {
			// Exclude Post Types(post,page)
			$wp_customize->add_setting('vkExUnit_sns_options[snsBtn_exclude_post_types]['.$key.']',
				array(
					'default'			      => false,
					'type'				      => 'option', // 保存先 option or theme_mod
					'capability'		    => 'edit_theme_options',
					'sanitize_callback' => 'veu_sanitize_boolean',
				)
			);

			$wp_customize->add_control( 'snsBtn_exclude_post_types_'.$key,
				array(
					'label'		    => esc_html( $value->label ),
					'section'	    => 'veu_sns_setting',
					'settings'    => 'vkExUnit_sns_options[snsBtn_exclude_post_types]['.$key.']',
					'type'		    => 'checkbox',
					'priority'	  => 1,
				)
			);
		}
	}

	// // Exclude Post Types(post,page)
	// $wp_customize->add_setting('vkExUnit_sns_options[snsBtn_exclude_post_types][page]',
	// 	array(
	// 		'default'			      => false,
	// 		'type'				      => 'option', // 保存先 option or theme_mod
	// 		'capability'		    => 'edit_theme_options',
	// 		'sanitize_callback' => 'veu_sanitize_boolean',
	// 	)
	// );
	//
	// $wp_customize->add_control( 'snsBtn_exclude_post_types_page',
	// 	array(
	// 		'label'		    => __( 'Page', 'vkExUnit' ),
	// 		'section'	    => 'veu_sns_setting',
	// 		'settings'    => 'vkExUnit_sns_options[snsBtn_exclude_post_types][page]',
	// 		'type'		    => 'checkbox',
	// 		'priority'	  => 1,
	// 	)
	// );


	// // Exclude Post ID(いらない)
	// $wp_customize->add_setting( 'vkExUnit_sns_options[snsBtn_ignorePosts]',
	// 	array(
	// 		'default'           => '',
	// 		'type'              => 'option', // 保存先 option or theme_mod
	// 		'capability'        => 'edit_theme_options',
	// 		'sanitize_callback' => 'sanitize_text_field',
	// 	)
	// );
	//
	// $wp_customize->add_control( 'snsBtn_ignorePosts',
	// 	array(
	// 		'label'    => __( 'Exclude Post ID', 'vkExUnit' ),
	// 		'section'  => 'veu_sns_setting',
	// 		'settings' => 'vkExUnit_sns_options[snsBtn_ignorePosts]',
	// 		'type'     => 'text',
	// 		'description' => __( 'If you need filtering by post_ID, add the ignore post_ID separate by ",".<br>If empty this area, I will do not filtering.<br>example(12,31,553)', 'vkExUnit' ),
	// 		'priority' => 1,
	// 	)
	// );

	// Follow me box
	$wp_customize->add_setting('vkExUnit_sns_options[enableFollowMe]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'enableFollowMe',
		array(
			'label'		    => __( 'Print the Follow me box', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[enableFollowMe]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box title
	$wp_customize->add_setting( 'vkExUnit_sns_options[followMe_title]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control( 'followMe_title',
		array(
			'label'    => __( 'Follow me box title', 'vkExUnit' ),
			'section'  => 'veu_sns_setting',
			'settings' => 'vkExUnit_sns_options[followMe_title]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	// Follow me box(Facebook)
	$wp_customize->add_setting('vkExUnit_sns_options[useFacebook]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'useFacebook',
		array(
			'label'		    => __( 'Share button for display( Facebook )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[useFacebook]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box(Twitter)
	$wp_customize->add_setting('vkExUnit_sns_options[useTwitter]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'useTwitter',
		array(
			'label'		    => __( 'Share button for display( Twitter )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[useTwitter]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box(Hatena)
	$wp_customize->add_setting('vkExUnit_sns_options[useHatena]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'useHatena',
		array(
			'label'		    => __( 'Share button for display( Hatena )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[useHatena]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box(Pocket)
	$wp_customize->add_setting('vkExUnit_sns_options[usePocket]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'usePocket',
		array(
			'label'		    => __( 'Share button for display( Pocket )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[usePocket]',
			'type'		    => 'checkbox',
			'priority'	  => 1,
		)
	);

	// Follow me box(LINE)
	$wp_customize->add_setting('vkExUnit_sns_options[useLine]',
		array(
			'default'			      => false,
			'type'				      => 'option', // 保存先 option or theme_mod
			'capability'		    => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);

	$wp_customize->add_control( 'useLine',
		array(
			'label'		    => __( 'Share button for display( LINE )', 'vkExUnit' ),
			'section'	    => 'veu_sns_setting',
			'settings'    => 'vkExUnit_sns_options[useLine]',
			'type'		    => 'checkbox',
			'description' => __( '(mobile only)', 'vkExUnit' ),
			'priority'	  => 1,
		)
	);







   /*-------------------------------------------*/
 	/*	Add Edit Customize Link Btn
 	/*-------------------------------------------*/
   $wp_customize->selective_refresh->add_partial( 'vkExUnit_sns_options[snsBtn_bg_fill_not]', array(
     'selector' => '.veu_socialSet',
     'render_callback' => '',
   ) );
 }
