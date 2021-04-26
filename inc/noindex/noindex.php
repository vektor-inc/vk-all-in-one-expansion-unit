<?php
/**
 * VkExUnit noindex.php
 * insert noindex tag for head.
 *
 * @package  VkExUnit
 * @author   Hidekazu IShikawa <ishikawa@vektor-inc.co.jp>
 * @since    13/May/2019
 */

/*
VEU_Metabox 内の get_post_type が実行タイミングによっては
カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
admin_menu のタイミングで読み込んでいる
 */
add_action(
	'admin_menu', function() {
		require_once( dirname( __FILE__ ) . '/class-veu-metabox-noindex.php' );

	}
);

/*
  noindex出力処理
/*-------------------------------------------*/
add_action( 'wp_head', 'veu_noindex_print_head' );
function veu_noindex_print_head() {

	$noindex = veu_is_noindex_print_head();

	apply_filters( 'veu_noindex_print_head', $noindex );

	if ( $noindex ){
		echo '<meta name="robots" content="noindex,follow" />';
	}
}

/**
 * noindex を出力するかどうか
 */
function veu_is_noindex_print_head() {
	global $post;
	$noindex = false;
	if ( is_singular() ) {
		$vk_print_noindex = get_post_meta( $post->ID, '_vk_print_noindex', true );
		if ( $vk_print_noindex ) {
			$noindex = true;
		}
	} else {
		$options = veu_noindex_options();
		$veu_noindex_targets = veu_noindex_targets();

		$post_type_info = vk_get_post_type();

		foreach ( $veu_noindex_targets as $key => $value ) {

			// $keyを条件分岐の関数名にセット
			$function = $key;
			// 条件分岐の引値をnullに
			$args = null;

			// 配列のキー名と条件分岐タグが違う場合
			if ( ! empty( $value['function'] ) ) {
				$function = $value['function'];
			}
			if ( ! empty( $value['args'] ) ) {
				$args = $value['args'];
			}
			
			if ( function_exists( $function ) ){
				if ( call_user_func( $function, $args ) ){
					// 保存値でtrueになっている場合のみ出力可否を上書き
					if ( ! empty( $options[$key] ) ) {
						$noindex = true;
					} else {
						$noindex = false;
					}
				}
			}
		}
	}
	// print '<pre style="text-align:left">';print_r( $noindex );print '</pre>';
	return $noindex;
}


/**
 * ExUnitの機能管理パッケージに登録
 * @return [type] [description]
 */
function veu_noindex_admin_register() {
	$tab_label         = __( 'Noindex ', 'vk-all-in-one-expansion-unit' );
	$option_name       = 'vkExUnit_noindex';
	$sanitize_callback = 'veu_noindex_sanitize';
	$render_page       = 'veu_noindex_admin';
	vkExUnit_register_setting( $tab_label, $option_name, $sanitize_callback, $render_page );
}
add_action( 'veu_package_init', 'veu_noindex_admin_register' );

/**
 * ExUnit メイン設定画面
 */
function veu_noindex_admin() {
?>
<div class="sectionBox">
<h3><?php _e( 'Noindex', 'vk-all-in-one-expansion-unit' ); ?></h3>
<table class="form-table">
<tr>
<th><?php _e( 'Add noindex page', 'vk-all-in-one-expansion-unit' ); ?> </th>
<td>
<ul>
<?php
$options = veu_noindex_options();
$veu_noindex_targets = veu_noindex_targets();

// 新しく作る階層を持った配列
$new_array = array();
// 元の配列
$raw_array = $veu_noindex_targets;

// 先祖階層は parent が 空になって下層とは処理が違うのでまずは先祖階層だけの新しい配列を作成
// 元配列をループ {
foreach ( $raw_array as $key => $value ) {
	// 親指定がない場合 {
	if ( empty( $value['parent'] ) ){
		// 新しい配列に格納
		$new_array[$key] = array(); 
	}
}

$new_array = veu_create_nest_array( $new_array, $raw_array );

veu_display_nest_checkbox( $new_array, $raw_array, $options );
?>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>
<?php
}

/**
 * デフォルト値
 */
function veu_noindex_default() {
	$default_options = array(
		'is_archive' => false,
		// 'is_date' => false,
		// 'is_author' => false,
		// 'is_post_type_archive' => false,
		// 'is_category_and_tax' => false,
	);
	return apply_filters( 'veu_noindex_default', $default_options );
}

/**
 * オプション保存値取得
 */
function veu_noindex_options() {
	$options = get_option( 'vkExUnit_noindex', array() );
	$options = wp_parse_args( $options, veu_noindex_default() );
	return $options;
}

/**
 * 保存時サニタイズ
 */
function veu_noindex_sanitize( $input ) {
	$output               = $input;
	$output               = array();
	$veu_noindex_targets = veu_noindex_targets();
	foreach ( $veu_noindex_targets as $key => $value ) {
		if ( isset( $input[$key] ) ){
			$output[$key] = esc_attr( $input[$key] );
		}
	}
	return $output;
}

/**
 * noindex対象の配列
 */
function veu_noindex_targets(){

	/*
	この配列は後に書いてある情報で優先上書きされるので順番には注意する事
	*/

	$array = array( 
		'is_archive' => array(
			'label' => __( 'Archive', 'vk-all-in-one-expansion-unit' ),
			'parent' => '',
			'para' => null,
		),
		'is_date' => array(
			'label' => __( 'Date ( Yearly / Monthly / Daily /time ) archive', 'vk-all-in-one-expansion-unit' ),
			'parent' => 'is_archive',
		),
		// 'is_year' => array(
		// 	'label' => __( 'Yearly archive', 'vk-all-in-one-expansion-unit' ),
		// 	'parent' => 'is_date',
		// ),
		'is_author' => array(
			'label' => __( 'Author archive', 'vk-all-in-one-expansion-unit' ),
			'parent' => 'is_archive',
		),
		'is_post_type_archive' => array(
			'label' => __( 'Post type archive', 'vk-all-in-one-expansion-unit' ),
			'parent' => 'is_archive',
		),
		// 'is_post_type_archive_post' => array(
		// 	'label' => __( 'Post type archive', 'vk-all-in-one-expansion-unit' ) .' [ '. $post_types['post']->label . ' ]',
		// 	'parent' => 'is_post_type_archive',
		// 	'function' => 'is_home',
		// ),
		'is_category' => array(
			'label' => __( 'Category archive', 'vk-all-in-one-expansion-unit' ),
			'parent' => 'is_archive',
		),
		'is_tag' => array(
			'label' => __( 'Tag archive', 'vk-all-in-one-expansion-unit' ),
			'parent' => 'is_archive',
		),
		'is_tax' => array(
			'label' => __( 'Custom taxonomy archive', 'vk-all-in-one-expansion-unit' ),
			'parent' => 'is_archive',
		),
	);

	$args    = array(
		'post_types_args'    => array(
			'public' => true,
		),
		'name'               => '',
		'checked'            => array( 'post' => true ),
		'id'                 => '',
	);
	$post_types = get_post_types( $args['post_types_args'], 'object' );

	unset( $post_types['post'] );
	unset( $post_types['page'] );

	foreach ( $post_types as $key => $value ) {
		$array['is_post_type_archive_'.$key] = array(
			'label' => __( 'Post type archive', 'vk-all-in-one-expansion-unit' ) .' [ '. $value->label . ' ]',
			'parent' => 'is_post_type_archive',
			'function' => 'is_post_type_archive',
			'args' => $key,
		);
	}
	return $array;
}

/**
 * 配列の下階層を辿って新しい多次元配列を整形する関数
 */
function veu_create_nest_array( $array, $raw_array ){
	if ( is_array( $array ) && count( $array ) ){
		foreach ( $array as $key => $child ) {
			// 元の配列をループ {
			foreach ( $raw_array as $raw_key => $value) {
				// ループ中の $key を 親に項目に指定している項目（子）がある場合 {
				if ( ! empty( $value['parent'] ) && $value['parent'] === $key ){
					// 子項目をを新しい配列に格納
					$array[$key][$raw_key] = array();

					// 同様に下階層を処理 /////////////////////////
					$array[$key] = veu_create_nest_array( $array[$key], $raw_array );
				}
			}
		}
	}
	return $array;
}

/**
 * 多次元配列を階層を維持しながらチェックボックスを出力
 */
function veu_display_nest_checkbox( $array, $raw_array, $options ){
	// echo checked( $options, true );
	if ( is_array( $array ) && count( $array ) ){
		echo '<ul class="no-style">';
		foreach ( $array as $key => $child ) {
			echo '<li><label><input type="checkbox" name="vkExUnit_noindex[' . esc_attr( $key ) . ']" value="true" ';
			if ( isset( $options[$key] ) ){
				echo checked( $options[$key], "true" );
			}
			echo ' /> ';
			// print '<pre style="text-align:left">';print_r($options[$key]);print '</pre>';
			echo esc_html( $raw_array[$key]['label'] );
			echo '</label>';

			veu_display_nest_checkbox( $child, $raw_array, $options );

			echo '</li>';
		}
		echo '</ul>';
	}
}