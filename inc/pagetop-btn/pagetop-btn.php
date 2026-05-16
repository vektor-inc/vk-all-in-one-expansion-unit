<?php
/**
 * Page top button (ページトップへ戻るボタン)
 *
 * フッターに「ページトップへ戻る」ボタンを出力する機能。
 * ユーザーが管理画面 / カスタマイザーから画像をアップロードした場合は
 * `<a>` の style 属性で `--veu_page_top_button_url` を上書きし、
 * 既存ユーザーがテーマで上書きしている `--ver_` には触らない。
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/*
	footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'veu_add_pagetop' );

/**
 * Output the page top button HTML in the footer.
 *
 * フッターに `<a>` 要素を出力する。
 * モバイル非表示オプションが有効でモバイル端末の場合は何も出力しない。
 *
 * @return void
 */
function veu_add_pagetop() {
	$options = veu_pagetop_options();

	// Bail when the user has chosen to hide the button on touch screen devices.
	// モバイル非表示が有効かつモバイル判定の場合は表示しない。
	if ( wp_is_mobile() && ! empty( $options['hide_mobile'] ) ) {
		return;
	}

	echo veu_pagetop_render( $options ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- veu_pagetop_render() で適切にエスケープ済み。
}

/**
 * Generate the page top button HTML.
 *
 * `image_url` が設定されている場合は style 属性で
 * `--veu_page_top_button_url` を上書きする。
 * テスト容易性のためマークアップ生成を関数化している。
 *
 * @param array $options vkExUnit_pagetop オプション配列。
 * @return string The page top button HTML.
 */
function veu_pagetop_render( $options = array() ) {
	// Backfill defaults so callers can pass partial arrays / option output as-is.
	// 不足キーをデフォルト値で補完。
	$options = wp_parse_args( $options, veu_pagetop_default() );

	// Sanitize the URL again on the output side to guard against any
	// tampering of the option value that bypassed the input sanitizer.
	// 出力時にもサニタイズし、入力サニタイザーを通っていない値で
	// 万一汚染されていた場合でも CSS injection を防ぐ。
	$image_url = veu_pagetop_sanitize_image_url( $options['image_url'] );

	$style_attr = '';
	$class      = 'page_top_btn';
	if ( '' !== $image_url ) {
		// Use double quotes inside url() as required by CSS specs. The
		// sanitizer already rejects values containing quotes / parens /
		// whitespace, so this stays a single token.
		// url() 内はダブルクォート固定。サニタイザーで
		// クォート・括弧・空白を含む値は除外済みのため安全。
		$style_value = '--veu_page_top_button_url:url("' . $image_url . '");';
		$style_attr  = ' style="' . esc_attr( $style_value ) . '"';
		// `has-image` クラスを付けることで SCSS 側で画像サイズを
		// `contain` に切り替えるなどの調整が可能。
		$class .= ' has-image';
	}

	return '<a href="#top" id="page_top" class="' . esc_attr( $class ) . '"' . $style_attr . '>PAGE TOP</a>';
}

/**
 * Sanitize the image URL stored in vkExUnit_pagetop[image_url].
 *
 * `esc_url_raw()` で正規化したうえで、CSS injection を引き起こしうる
 * 値（クォート・括弧・制御文字・空白）を含むものや、画像として
 * 妥当な拡張子を持たない値は空文字を返す。
 *
 * @param mixed $value Raw value from option / customizer / POST.
 * @return string Sanitized URL, or empty string when the input is unsafe.
 */
function veu_pagetop_sanitize_image_url( $value ) {
	// Non-string inputs are rejected outright.
	// 文字列以外は問答無用で空文字。
	if ( ! is_string( $value ) ) {
		return '';
	}

	// Trim leading/trailing whitespace before normalization.
	// 前後の空白を除去してから正規化。
	$value = trim( $value );
	if ( '' === $value ) {
		return '';
	}

	// Reject values that contain characters which would let an attacker
	// break out of the `url("...")` context (quotes, parens, backslash,
	// control chars, internal whitespace).
	// `url("...")` のコンテキストから脱出可能な文字（クォート・括弧・
	// バックスラッシュ・制御文字・内部空白）を含む場合は拒否する。
	if ( preg_match( '/[\s"\'\\\\()]|[\x00-\x1F\x7F]/', $value ) ) {
		return '';
	}

	// Run through WordPress core URL sanitization.
	// WordPress 標準の URL サニタイズを通す。
	$value = esc_url_raw( $value );
	if ( '' === $value ) {
		return '';
	}

	// Whitelist common image extensions only (svg は管理画面で
	// アップロード不可だが、ユーザーが既存の svg URL を貼るケース
	// を想定して許可する。SVG 自体のアップロード許可は別 issue).
	$allowed_extensions = array( 'svg', 'png', 'jpg', 'jpeg', 'gif', 'webp' );

	// Strip the query string / fragment when extracting the extension so
	// that `image.png?ver=1` is recognized correctly.
	// 拡張子判定は ?ver=1 等を除いた path 部分から行う。
	$path = wp_parse_url( $value, PHP_URL_PATH );
	if ( ! is_string( $path ) || '' === $path ) {
		return '';
	}

	$ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
	if ( ! in_array( $ext, $allowed_extensions, true ) ) {
		return '';
	}

	return $value;
}

add_action( 'customize_register', 'veu_customize_register_pagetop' );

/**
 * Register Customizer settings & controls for the page top button.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function veu_customize_register_pagetop( $wp_customize ) {

	/*
		Page Top setting
	/*-------------------------------------------*/
	$wp_customize->add_section(
		'veu_pagetop_setting',
		array(
			'title'    => __( 'Page Top Button', 'vk-all-in-one-expansion-unit' ),
			'priority' => 10000,
			'panel'    => 'veu_setting',
		)
	);

	// Hide on mobile.
	// モバイル端末で非表示にするチェックボックス。
	$wp_customize->add_setting(
		'vkExUnit_pagetop[hide_mobile]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'vkExUnit_pagetop[hide_mobile]',
		array(
			'label'    => __( 'Do not display on touch screen devices', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_pagetop_setting',
			'settings' => 'vkExUnit_pagetop[hide_mobile]',
			'type'     => 'checkbox',
		)
	);

	// Image URL (file uploader).
	// 画像 URL のアップロード設定。
	$wp_customize->add_setting(
		'vkExUnit_pagetop[image_url]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_pagetop_sanitize_image_url',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'vkExUnit_pagetop_image_url',
			array(
				'label'       => __( 'Page top button image', 'vk-all-in-one-expansion-unit' ),
				'section'     => 'veu_pagetop_setting',
				'settings'    => 'vkExUnit_pagetop[image_url]',
				'description' => __( 'Upload an image to replace the default page top button icon. Recommended formats: SVG, PNG, JPG, GIF, WebP.', 'vk-all-in-one-expansion-unit' ),
			)
		)
	);

	// Selective refresh: re-render the `<a>` element when settings change.
	// 設定変更時に `<a>` を差し替える selective refresh パーシャル。
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_pagetop[hide_mobile]',
		array(
			'selector'        => '.page_top_btn',
			'render_callback' => '',
			'supports'        => array(),
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_pagetop[image_url]',
		array(
			'selector'            => '.page_top_btn',
			'container_inclusive' => true,
			'render_callback'     => 'veu_pagetop_partial_render',
		)
	);
}

/**
 * Render callback for the selective refresh partial.
 *
 * @return string The page top button HTML.
 */
function veu_pagetop_partial_render() {
	return veu_pagetop_render( veu_pagetop_options() );
}


/**
 * Register the page-top settings tab on ExUnit main setting page.
 *
 * @return void
 */
function veu_pagetop_admin_register() {
	$tab_label         = __( 'Page Top Button', 'vk-all-in-one-expansion-unit' );
	$option_name       = 'vkExUnit_pagetop';
	$sanitize_callback = 'veu_pagetop_sanitize';
	$render_page       = 'veu_pagetop_admin';
	vkExUnit_register_setting( $tab_label, $option_name, $sanitize_callback, $render_page );
}
add_action( 'veu_package_init', 'veu_pagetop_admin_register' );

/**
 * Render the page-top admin setting section.
 *
 * @return void
 */
function veu_pagetop_admin() {
	$options        = veu_pagetop_options();
	$image_url      = $options['image_url'];
	$preview_style  = '' !== $image_url ? '' : ' style="display:none;"';
	$customizer_url = admin_url( 'customize.php?autofocus[section]=veu_pagetop_setting' );
	?>
<div id="pagetopSetting" class="sectionBox">
<h3><?php esc_html_e( 'Page Top Button', 'vk-all-in-one-expansion-unit' ); ?></h3>
<table class="form-table">
<tr>
<th><?php esc_html_e( 'Page Top Button', 'vk-all-in-one-expansion-unit' ); ?> </th>
<td><label>
<input type="checkbox" name="vkExUnit_pagetop[hide_mobile]" value="true"
	<?php
	if ( ! empty( $options['hide_mobile'] ) ) {
		echo ' checked';}
	?>
	/> <?php esc_html_e( 'Do not display on touch screen devices', 'vk-all-in-one-expansion-unit' ); ?> </label>
</td>
</tr>
<tr>
<th><?php esc_html_e( 'Page top button image', 'vk-all-in-one-expansion-unit' ); ?></th>
<td>
	<div class="veu_pagetop_image_preview"<?php echo $preview_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定文字列のみ。 ?>>
		<img id="thumb_pagetop_image_url" src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:120px;height:auto;background:rgba(0,0,0,0.8);padding:6px;border-radius:4px;" />
	</div>
	<p>
		<input type="text" name="vkExUnit_pagetop[image_url]" id="pagetop_image_url" value="<?php echo esc_attr( $image_url ); ?>" style="width:60%;" />
		<button type="button" id="media_src_pagetop_image_url" class="media_btn button button-default"><?php esc_html_e( 'Select an image', 'vk-all-in-one-expansion-unit' ); ?></button>
		<button type="button" id="veu_pagetop_image_clear" class="button button-default"><?php esc_html_e( 'Clear image', 'vk-all-in-one-expansion-unit' ); ?></button>
	</p>
	<p class="description">
		<?php esc_html_e( 'Upload an image to replace the default page top button icon. Recommended formats: SVG, PNG, JPG, GIF, WebP.', 'vk-all-in-one-expansion-unit' ); ?><br />
		<a href="<?php echo esc_url( $customizer_url ); ?>"><?php esc_html_e( 'Configure with live preview in the Customizer', 'vk-all-in-one-expansion-unit' ); ?> &rarr;</a>
	</p>
</td>
</tr>
</table>
	<?php submit_button(); ?>
</div>
<script>
	// Clear the page top button image input and hide the preview.
	// 画像 URL 入力欄を空にし、プレビューを非表示にする。
	(function(){
		var clearBtn = document.getElementById('veu_pagetop_image_clear');
		if ( ! clearBtn ) {
			return;
		}
		clearBtn.addEventListener('click', function(){
			var urlInput = document.getElementById('pagetop_image_url');
			var thumb    = document.getElementById('thumb_pagetop_image_url');
			var preview  = thumb ? thumb.parentNode : null;
			if ( urlInput ) {
				urlInput.value = '';
			}
			if ( thumb ) {
				thumb.setAttribute( 'src', '' );
			}
			if ( preview ) {
				preview.style.display = 'none';
			}
		});

		// Show the preview when the shared media_btn handler injects a URL.
		// 共通ハンドラ (.media_btn) が URL を入れたらプレビューを表示する。
		var selectBtn = document.getElementById('media_src_pagetop_image_url');
		if ( selectBtn ) {
			selectBtn.addEventListener('click', function(){
				// Defer until the media frame's `select` handler has populated
				// the input. The frame stays open across clicks so we poll briefly.
				// メディアフレームの select 反映を待ってからプレビュー表示。
				var attempts = 0;
				var timer = setInterval(function(){
					attempts++;
					var urlInput = document.getElementById('pagetop_image_url');
					var thumb    = document.getElementById('thumb_pagetop_image_url');
					var preview  = thumb ? thumb.parentNode : null;
					if ( urlInput && urlInput.value && thumb && preview ) {
						thumb.setAttribute( 'src', urlInput.value );
						preview.style.display = '';
						clearInterval( timer );
					}
					if ( attempts > 60 ) {
						clearInterval( timer );
					}
				}, 250);
			});
		}
	})();
</script>
	<?php
}


/**
 * Get vkExUnit_pagetop options merged with defaults.
 *
 * @return array
 */
function veu_pagetop_options() {
	$options = get_option( 'vkExUnit_pagetop', array() );
	if ( ! is_array( $options ) ) {
		$options = array();
	}
	$options = wp_parse_args( $options, veu_pagetop_default() );
	return $options;
}

/**
 * Default values for vkExUnit_pagetop option.
 *
 * @return array
 */
function veu_pagetop_default() {
	$default_options = array(
		'hide_mobile' => false,
		'image_url'   => '',
	);
	return apply_filters( 'veu_pagetop_default', $default_options );
}

/**
 * Sanitize the vkExUnit_pagetop option on save (main setting page).
 *
 * @param array $input Raw POSTed values.
 * @return array
 */
function veu_pagetop_sanitize( $input ) {
	$output = array();
	if ( ! is_array( $input ) ) {
		$input = array();
	}
	if ( isset( $input['hide_mobile'] ) ) {
		$output['hide_mobile'] = esc_attr( $input['hide_mobile'] );
	}
	if ( isset( $input['image_url'] ) ) {
		$output['image_url'] = veu_pagetop_sanitize_image_url( $input['image_url'] );
	} else {
		$output['image_url'] = '';
	}
	return $output;
}
