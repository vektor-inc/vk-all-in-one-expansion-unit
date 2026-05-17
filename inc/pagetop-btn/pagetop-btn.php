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
 * @param mixed $options vkExUnit_pagetop オプション配列。配列以外が渡された場合は空配列として扱う。
 * @return string The page top button HTML.
 */
function veu_pagetop_render( $options = array() ) {
	// Defensive guard: callers may pass non-array values (null, string, etc.)
	// either by mistake or via legacy code paths. Type-hint cannot be added
	// without breaking backward compatibility, so normalize to an empty array
	// here and let wp_parse_args() backfill defaults below.
	// 後方互換のため引数の型宣言は付けず、配列以外が渡された場合は空配列として
	// 扱うガードを入れる。後段の wp_parse_args() でデフォルト値を補完する。
	if ( ! is_array( $options ) ) {
		$options = array();
	}

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
	// control chars including C0 / DEL / C1, internal whitespace).
	// The `u` modifier enables UTF-8 mode so that multi-byte C1 controls
	// (U+0080-U+009F) are matched as a single code point rather than
	// individual bytes.
	// `url("...")` のコンテキストから脱出可能な文字（クォート・括弧・
	// バックスラッシュ・制御文字・内部空白）を含む場合は拒否する。
	// `u` 修飾子で UTF-8 モードを有効化し、C1 制御文字 (U+0080-U+009F) も
	// マルチバイト文字として一括りに検出する。
	if ( preg_match( '/[\s"\'\\\\()]|[\x00-\x1F\x7F]|[\x{0080}-\x{009F}]/u', $value ) ) {
		return '';
	}

	// Reject URL-encoded variants of the dangerous characters above. Browsers
	// may decode `%22` / `%27` / `%28` / `%29` / `%5C` inside `url("...")`,
	// allowing an attacker to break out even though the raw value passed the
	// first regex. Detection is case-insensitive so that `%5C` and `%5c`
	// (both valid encodings of the backslash) are caught equally.
	// 上記の危険文字を URL エンコードした表現も拒否する。ブラウザは
	// `url("...")` 内の `%22` などをデコードしうるため、生クォート等を
	// 弾いただけでは脱出を防げない。URL の percent-encoding は大文字小文字
	// が等価（例: `%5C` と `%5c` はどちらもバックスラッシュ）なので、
	// 取りこぼさないよう case-insensitive で判定する。
	if ( preg_match( '/%(22|27|28|29|5C)/i', $value ) ) {
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
	// Build the description for the customizer image control.
	// Translation rule: split into one sentence per translation call. Each
	// sentence is concatenated with a newline; an empty line is used to
	// visually separate groups.
	// 翻訳ルールに従い 1 文ずつ翻訳関数で区切り、改行で連結する。
	// グループ間は空行で視覚的に分ける。。
	$customizer_description  = __( 'Upload an image to replace the default page top button icon.', 'vk-all-in-one-expansion-unit' ) . "\n";
	$customizer_description .= __( 'Recommended formats: SVG, PNG, JPG, GIF, WebP.', 'vk-all-in-one-expansion-unit' ) . "\n\n";
	$customizer_description .= __( 'A square (1:1) image is recommended.', 'vk-all-in-one-expansion-unit' ) . "\n";
	$customizer_description .= __( 'Images with a very different aspect ratio may show extra empty space.', 'vk-all-in-one-expansion-unit' ) . "\n\n";
	$customizer_description .= __( 'Clearing the selection only removes this setting and does not delete the image from the Media Library.', 'vk-all-in-one-expansion-unit' ) . "\n\n";
	$customizer_description .= __( 'If your theme or custom CSS overrides --veu_page_top_button_url, the theme value takes precedence and the image may not appear.', 'vk-all-in-one-expansion-unit' );

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'vkExUnit_pagetop_image_url',
			array(
				'label'         => __( 'Page top button image', 'vk-all-in-one-expansion-unit' ),
				'section'       => 'veu_pagetop_setting',
				'settings'      => 'vkExUnit_pagetop[image_url]',
				'description'   => $customizer_description,
				// Customize the built-in image control button labels so that
				// "Remove" is interpreted as "clear this setting", not
				// "delete from the media library".
				// ビルトインの button_labels をカスタマイズして、
				// 「削除」がメディアライブラリからの削除ではなく
				// 「設定値を解除」である事をユーザーに明示する。
				'button_labels' => array(
					'select'       => __( 'Select image', 'vk-all-in-one-expansion-unit' ),
					'change'       => __( 'Change image', 'vk-all-in-one-expansion-unit' ),
					'remove'       => __( 'Clear image selection', 'vk-all-in-one-expansion-unit' ),
					'default'      => __( 'Default', 'vk-all-in-one-expansion-unit' ),
					'placeholder'  => __( 'No image selected', 'vk-all-in-one-expansion-unit' ),
					'frame_title'  => __( 'Select image', 'vk-all-in-one-expansion-unit' ),
					'frame_button' => __( 'Choose image', 'vk-all-in-one-expansion-unit' ),
				),
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
 * `veu_add_pagetop()` と同じく、モバイル非表示オプションが有効で
 * モバイル端末の場合は空文字を返してプレビュー表示も非表示にする。
 * これを行わないと、フロント側では非表示なのにカスタマイザのプレビュー
 * だけ表示されてしまい挙動が一致しなくなる。
 *
 * @return string The page top button HTML, or empty string when hidden on mobile.
 */
function veu_pagetop_partial_render() {
	$options = veu_pagetop_options();
	// Match veu_add_pagetop() behavior: return empty on mobile when hide_mobile is enabled.
	// veu_add_pagetop() と挙動を合わせ、モバイル非表示時は空文字を返す。
	if ( wp_is_mobile() && ! empty( $options['hide_mobile'] ) ) {
		return '';
	}
	return veu_pagetop_render( $options );
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
		<button type="button" id="veu_pagetop_image_clear" class="button button-default"><?php esc_html_e( 'Clear image selection', 'vk-all-in-one-expansion-unit' ); ?></button>
	</p>
	<p class="description">
		<?php esc_html_e( 'Upload an image to replace the default page top button icon.', 'vk-all-in-one-expansion-unit' ); ?><br />
		<?php esc_html_e( 'Recommended formats: SVG, PNG, JPG, GIF, WebP.', 'vk-all-in-one-expansion-unit' ); ?><br />
		<?php esc_html_e( 'A square (1:1) image is recommended.', 'vk-all-in-one-expansion-unit' ); ?><br />
		<?php esc_html_e( 'Images with a very different aspect ratio may show extra empty space.', 'vk-all-in-one-expansion-unit' ); ?><br />
		<?php esc_html_e( 'Clearing the selection only removes this setting and does not delete the image from the Media Library.', 'vk-all-in-one-expansion-unit' ); ?><br />
		<?php esc_html_e( 'If your theme or custom CSS overrides --veu_page_top_button_url, the theme value takes precedence and the image may not appear.', 'vk-all-in-one-expansion-unit' ); ?><br />
		<a href="<?php echo esc_url( $customizer_url ); ?>"><?php esc_html_e( 'Configure with live preview in the Customizer', 'vk-all-in-one-expansion-unit' ); ?> &rarr;</a>
	</p>
</td>
</tr>
</table>
	<?php submit_button(); ?>
</div>
<script>
	// Toggle the thumbnail preview in sync with the shared vk_admin.js
	// .media_btn handler, which updates `#thumb_pagetop_image_url` (img src)
	// and `#pagetop_image_url` (text input value) when a user picks an image.
	// We observe the img src via MutationObserver instead of polling, so the
	// preview reflects changes the moment the shared handler writes to it.
	// 共通ヘルパー (vk_admin.js .media_btn) が `#thumb_pagetop_image_url` の
	// src と `#pagetop_image_url` の value を更新するので、MutationObserver
	// で src の変化を検知してプレビューの表示/非表示を切り替える。
	// (以前は setInterval で 250ms × 60 回のポーリングをしていたが、
	// 共通ヘルパーと二重に src を更新する形になっていたため廃止。)
	(function(){
		var urlInput = document.getElementById('pagetop_image_url');
		var thumb    = document.getElementById('thumb_pagetop_image_url');
		var preview  = thumb ? thumb.parentNode : null;
		var clearBtn = document.getElementById('veu_pagetop_image_clear');

		// Toggle the preview wrapper visibility based on the current img src.
		// 現在の thumb.src を見てプレビュー枠の表示/非表示を切り替える。
		var togglePreview = function() {
			if ( ! thumb || ! preview ) {
				return;
			}
			var src = thumb.getAttribute( 'src' ) || '';
			preview.style.display = ( '' !== src ) ? '' : 'none';
		};

		// Sync the preview from the URL text input.
		// When the user pastes / types a URL directly into the text field,
		// the shared media handler does not fire so we have to mirror the
		// value into thumb.src ourselves and re-evaluate the preview state.
		// URL テキスト欄にユーザーが直接 URL を貼り付け / 入力した場合は
		// 共通ヘルパーが発火しないため、ここで thumb.src に反映して
		// プレビューの表示状態を再評価する。
		var syncPreviewFromInput = function() {
			if ( ! urlInput || ! thumb ) {
				return;
			}
			thumb.setAttribute( 'src', urlInput.value );
			togglePreview();
		};

		// Watch for src changes coming from the shared .media_btn handler.
		// MutationObserver なら共通ハンドラが src を書き換えた瞬間に検知できる。
		if ( thumb && 'MutationObserver' in window ) {
			var observer = new MutationObserver( togglePreview );
			observer.observe( thumb, { attributes: true, attributeFilter: [ 'src' ] } );
		}

		// URL input: react to manual edits (paste, typing, etc.) so the
		// thumbnail preview stays in sync without requiring a save.
		// URL 入力欄: 手入力や貼り付けに反応してプレビューを同期させる。
		// `change` は確定時、`blur` はフォーカスアウト時のフォールバック。
		if ( urlInput ) {
			urlInput.addEventListener( 'change', syncPreviewFromInput );
			urlInput.addEventListener( 'blur', syncPreviewFromInput );
		}

		// Clear button: empty the URL input, clear the thumb src and hide the preview.
		// 「画像の指定を解除」ボタン: URL を空にし thumb.src を空に、プレビューを非表示にする。
		if ( clearBtn ) {
			clearBtn.addEventListener('click', function(){
				if ( urlInput ) {
					urlInput.value = '';
				}
				if ( thumb ) {
					// Setting src triggers the observer which hides the preview.
					// src を空にすると observer 経由でプレビューも非表示になる。
					thumb.setAttribute( 'src', '' );
				}
				// Fallback for environments without MutationObserver.
				// MutationObserver 非対応環境向けのフォールバック。
				togglePreview();
			});
		}
	})();
</script>
	<?php
}


/**
 * Get vkExUnit_pagetop options merged with defaults.
 *
 * 取得時点で `image_url` を必ず `veu_pagetop_sanitize_image_url()` に
 * 通すことで、option 直書き等で配列・null・破損文字列が保存されていた場合でも
 * 後段の `esc_url()` / `esc_attr()` で TypeError を起こさず安全に空文字へ正規化する。
 *
 * @return array
 */
function veu_pagetop_options() {
	$options = get_option( 'vkExUnit_pagetop', array() );
	if ( ! is_array( $options ) ) {
		$options = array();
	}
	$options = wp_parse_args( $options, veu_pagetop_default() );

	// Defensive normalization: option storage can hold non-string values
	// (e.g. arrays / null) when written directly by other code. The sanitizer
	// itself rejects non-string inputs and returns an empty string, but we
	// keep the explicit guard here to make the contract obvious to readers.
	// 取得時の防御的サニタイズ。サニタイザー側で非文字列は空文字にされるが、
	// 意図を明示するため `is_string` ガードも残す。
	$image_url            = isset( $options['image_url'] ) ? $options['image_url'] : '';
	$options['image_url'] = is_string( $image_url ) ? veu_pagetop_sanitize_image_url( $image_url ) : '';

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
		// Use the shared boolean sanitizer to match the Customizer setting's
		// sanitize_callback (`veu_sanitize_boolean`) and keep the stored
		// representation consistent across both entry points.
		// カスタマイザ側の sanitize_callback と揃えるため共通の
		// veu_sanitize_boolean() を使用し、保存形式（bool）を統一する。
		$output['hide_mobile'] = veu_sanitize_boolean( $input['hide_mobile'] );
	}
	if ( isset( $input['image_url'] ) ) {
		$output['image_url'] = veu_pagetop_sanitize_image_url( $input['image_url'] );
	} else {
		$output['image_url'] = '';
	}
	return $output;
}
