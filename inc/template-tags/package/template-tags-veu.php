<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

/**
 * ExUnit固有の関数だが、ExUnitの機能を複製している他のプラグインにも使用されるものもある
 */

function veu_get_common_options() {
	$dafault = veu_get_common_options_default();
	$options = get_option( 'vkExUnit_common_options' );
	$options = wp_parse_args( $options, $dafault );
	return apply_filters( 'vkExUnit_common_options', $options );
}

function veu_get_common_options_default( $is_block_theme = null ) {

	// hook veu_package_is_enable()
	// パッケージの情報を取得してデフォルトの配列を作成
	$defaults = array();
	$packages = veu_get_packages( $is_block_theme );
	foreach ( $packages as $key => $value ) {
		if ( empty( $value['section_title'] ) ) {
			$name                                 = $value['name'];
			$default_options[ 'active_' . $name ] = $value['default'];
		}
	}
	$default_options['post_metabox_individual']      = false;
	$default_options['delete_options_at_deactivate'] = false;
	$default_options['content_filter_state']         = 'content';
	return apply_filters( 'vkExUnit_common_options_default', $default_options );
}

 /*
 -------------------------------------------*/
 /*
   validate
 /*-------------------------------------------*/

function veu_common_options_validate( $input ) {
	/*
	 入力された値の無害化
	 ここでは機能の有効化有無に関する項目が殆どで、手動で項目を記載すると機能の増減の際に項目の編集漏れが出るため、
	 veu_get_common_options_default() の中で package に登録してある項目・デフォルト値を読み込み、それをループ処理する
	*/
	$defaults = veu_get_common_options_default();
	foreach ( $defaults as $key => $default_value ) {
		// 'content_filter_state'　以外は true か false しか返ってこない
		if ( $key != 'content_filter_state' ) {
				$output[ $key ] = ( isset( $input[ $key ] ) ) ? esc_html( $input[ $key ] ) : $default_value;
		} else {
				$output['content_filter_state'] = ( ! empty( $input['content_filter_state'] ) ) ? 'loop_end' : 'content';
		}
	}

	return apply_filters( 'vkExUnit_common_options_validate', $output, $input, $defaults );
}


if ( ! function_exists( 'veu_get_capability_required' ) ) {
	function veu_get_capability_required() {
		return add_filter( 'veu_get_capability_required', 'edit_theme_options' );
	}
}

if ( ! function_exists( 'veu_get_systemlogo_html' ) ) {
	function veu_get_systemlogo_html() {
		$logo  = '<div class="logo_exUnit">';
		$logo .= '<img src="' . apply_filters( 'vkExUnit_news_image_URL_small', VEU_DIRECTORY_URI . '/assets/images/head_logo_ExUnit.png' ) . '" alt="VK ExUnit" />';
		$logo .= '</div>';
		$logo  = apply_filters( 'veu_get_systemlogo_html', $logo );
		return $logo;
	}
}

if ( ! function_exists( 'veu_content_filter_state' ) ) {
	function veu_content_filter_state() {
		// $opt = veu_get_common_options();
		// return empty( $opt['content_filter_state'] )? 'content' : $opt['content_filter_state'];
		// コンテンツループ下部に出力すると誤動作が多いので、一旦コンテンツ下部出力に強制変更
		return 'content';
	}
}

if ( ! function_exists( 'veu_get_name' ) ) {
	function veu_get_name() {
		$system_name = apply_filters( 'veu_get_name', 'VK All in One Expansion Unit' );
		return $system_name;
	}
}

if ( ! function_exists( 'veu_get_little_short_name' ) ) {
	function veu_get_little_short_name() {
			$little_short_name = apply_filters( 'veu_get_little_short_name', 'ExUnit' );
			return $little_short_name;
	}
}

if ( ! function_exists( 'veu_get_short_name' ) ) {
	function veu_get_short_name() {
		$short_name = apply_filters( 'veu_get_short_name', 'VK' );
		return $short_name;
	}
}
if ( ! function_exists( 'veu_get_prefix' ) ) {
	function veu_get_prefix() {
		$prefix = apply_filters( 'veu_get_prefix', 'VK' );
		if ( $prefix ) {
			$prefix .= ' ';
		}
		return $prefix;
	}
}
if ( ! function_exists( 'veu_get_prefix_customize_panel' ) ) {
	function veu_get_prefix_customize_panel() {
		$prefix = apply_filters( 'veu_get_prefix_customize_panel', 'ExUnit' );
		if ( $prefix ) {
			$prefix .= ' ';
		}
		return $prefix;
	}
}

if ( ! function_exists( 'veu_is_cta_active' ) ) {
	function veu_is_cta_active() {
		if ( vk_is_plugin_active( 'vk-all-in-one-expansion-unit/vkExUnit.php' ) ) {
			$veu_common_options = get_option( 'vkExUnit_common_options' );
			if ( isset( $veu_common_options['active_call_to_action'] ) && $veu_common_options['active_call_to_action'] ) {
				return true;
			}
		}
	}
}

require_once 'template-tags-veu-old.php';


function veu_is_parent_metabox_display() {
	$flag = apply_filters( 'veu_parent_metabox_activation', false );
	if ( ! $flag ) {
		$flag = veu_is_parent_metabox_display_maual();
	}
	return $flag;
}

function veu_is_insert_item_metabox_display() {
	$options         = veu_get_common_options();
	$admin_post_type = vk_get_post_type();

	/*
	  childPageIndex
	/*-------------------------------------------*/
	if ( ! empty( $options['active_childPageIndex'] ) && $admin_post_type['slug'] == 'page' ) {
		return true;
	}
	/*
	  pageList_ancestor
	/*-------------------------------------------*/
	if ( ! empty( $options['active_pageList_ancestor'] ) && $admin_post_type['slug'] == 'page' ) {
		return true;
	}
	/*
	  contact_section
	/*-------------------------------------------*/
	if ( ! empty( $options['active_contact_section'] ) && $admin_post_type['slug'] == 'page' ) {
		return true;
	}
	/*
	  HTML Sitemap
	/*-------------------------------------------*/
	if ( ! empty( $options['active_sitemap_page'] ) && $admin_post_type['slug'] == 'page' ) {
		return true;
	}
}

function veu_is_parent_metabox_display_maual() {
	$flag            = false;
	$options         = veu_get_common_options();
	$admin_post_type = vk_get_post_type();

	$insert_item_metabox_display = veu_is_insert_item_metabox_display();
	if ( $insert_item_metabox_display ) {
		return true;
	}

	/*
	  Meta KeyWords
	/*-------------------------------------------*/
	if ( ! empty( $options['active_metaKeyword'] ) ) {
		return true;
	}

	/*
	  CSS Customize
	/*-------------------------------------------*/
	if ( ! empty( $options['active_css_customize'] ) ) {
		return true;
	}

	/*
	  CTA
	/*-------------------------------------------*/
	if ( ! empty( $options['active_call_to_action'] ) ) {
		return true;
	}

	/*
	  NoIndex
	/*-------------------------------------------*/
	if ( ! empty( $options['active_noindex'] ) ) {
		return true;
	}

	/*
	  Auto Eye Catch
	/*-------------------------------------------*/
	if ( ! empty( $options['active_auto_eyecatch'] ) ) {
		return true;
	}

	/*
	  SNS
	/*-------------------------------------------*/

	if ( ! empty( $options['active_sns'] ) ) {
		$sns_options = veu_get_sns_options();

		// OGタグ出力機能がONの時は タイトルタグ 書き換え欄の出力が必要
		if ( ! empty( $sns_options['enableOGTags'] ) || ! empty( $sns_options['enableTwitterCardTagss'] ) ) {
			return true;
		}
		// シェアボタンの表示
		if ( ! empty( $sns_options['enableSnsBtns'] ) ) {
			// 表示除外投稿タイプの配列指定がある場合
			if ( is_array( $sns_options['snsBtn_exclude_post_types'] ) ) {
					// 表示除外投稿タイプをループ
				foreach ( $sns_options['snsBtn_exclude_post_types'] as $loop_post_type => $value ) {
					// 除外対象じゃない投稿タイプの時
					if ( ! $value && $admin_post_type['slug'] == $loop_post_type ) {
						return true;
					}
				}
			} else {
				// SNSを表示しない投稿タイプの指定がない場合
				return true;
			}
		}
	}

	return $flag;
}
