<?php // phpcs:ignore

if ( ! class_exists( 'VK_Post_Type_Manager' ) ) {

	/**
	 * Post Type Manager
	 */
	class VK_Post_Type_Manager {

		/**
		 * カスタム投稿タイプ制御用投稿タイプを追加
		 *
		 * @return void
		 */
		public static function add_post_type_post_type_manage() {
			register_post_type(
				'post_type_manage', // カスタム投稿タイプのスラッグ.
				array(
					'labels'          => array(
						'name'          => __( 'Custom Post Type Setting', 'vk-all-in-one-expansion-unit' ),
						'singular_name' => __( 'Custom Post Type Setting', 'vk-all-in-one-expansion-unit' ),
					),
					'public'          => false,
					'show_ui'         => true,
					'show_in_menu'    => true,
					'menu_position'   => 100,
					'capability_type' => array( 'post_type_manage', 'post_type_manages' ),
					'map_meta_cap'    => true,
					'has_archive'     => false,
					'menu_icon'       => 'dashicons-admin-generic',
					'supports'        => array( 'title' ),
					'is_embeddable'   => false,
				)
			);
		}

		/**
		 * 編集権限を追加
		 * post_type_manage の編集権限を追加
		 *
		 * @return void
		 */
		public static function add_cap_post_type_manage() {
			$role           = get_role( 'administrator' );
			$post_type_name = 'post_type_manage';
			$role->add_cap( 'add_' . $post_type_name );
			$role->add_cap( 'add_' . $post_type_name . 's' );
			$role->add_cap( 'edit_' . $post_type_name );
			$role->add_cap( 'edit_' . $post_type_name . 's' );
			$role->add_cap( 'edit_published_' . $post_type_name . 's' );
			$role->add_cap( 'edit_others_' . $post_type_name . 's' );
			$role->add_cap( 'delete_' . $post_type_name );
			$role->add_cap( 'delete_' . $post_type_name . 's' );
			$role->add_cap( 'delete_private_' . $post_type_name . 's' );
			$role->add_cap( 'delete_others_' . $post_type_name . 's' );
			$role->add_cap( 'delete_published_' . $post_type_name . 's' );
			$role->add_cap( 'publish_' . $post_type_name . 's' );
			$role->add_cap( 'publish_others_' . $post_type_name . 's' );
		}

		/**
		 * ヘルプ通知
		 *
		 * @return string
		 */
		public static function add_post_type_get_help_notice() {

			$dismiss_url = esc_url(
				wp_nonce_url(
					add_query_arg( 'vk-all-in-one-expansion-unit-dismiss', 'dismiss_admin_notice' ),
					'vk-all-in-one-expansion-unit-dismiss-' . get_current_user_id()
				)
			);

			// ヘルプ通知のHTMLを生成して返す
			return wp_kses_post(
				'<div class="notice notice-info is-dismissible">
					<p style="margin-top: 10px;"><strong>' . __( 'Help and Documentation', 'vk-all-in-one-expansion-unit' ) . ':</strong> ' . __( 'Learn more about custom post type settings by visiting the following resources:', 'vk-all-in-one-expansion-unit' ) . '</p>
					<ul style="list-style-type: disc; margin-top: 0; margin-left: 20px;">
						<li><a href="https://ex-unit.nagoya/ja/about/custom_post_type_manager" target="_blank">' . __( 'ExUnit Site: Custom Post Type Manager', 'vk-all-in-one-expansion-unit' ) . '</a></li>
						<li><a href="https://training.vektor-inc.co.jp/courses/x-t9-custom-post-type/" target="_blank">' . __( 'Vektor Training: X-T9 Setup Guide for Custom Post Types', 'vk-all-in-one-expansion-unit' ) . '</a></li>
						<li><a href="https://training.vektor-inc.co.jp/courses/lightning-basic-settings/lessons/lightning-custom-post-type-lightning/" target="_blank">' . __( 'Vektor Training: Lightning Basic Settings for Custom Post Types', 'vk-all-in-one-expansion-unit' ) . '</a></li>
					</ul>
					<p><a href="' . $dismiss_url . '" target="_parent">' . esc_html__( 'Dismiss this notice', 'vk-all-in-one-expansion-unit' ) . '</a></p>
				</div>'
			);
		}

		/**
		 * Check if the help notice should be displayed on the current page.
		 *
		 * @return bool
		 */
		public static function is_display_help_notice() {
			global $pagenow;

			if ( get_locale() !== 'ja' ) {
				return false;
			}

			// 特定のページのみ通知を表示する
			if ( $pagenow === 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'post_type_manage' ) {
				// ユーザーが通知を無視したフラグが保存されているかどうかを確認
				if ( ! get_user_meta( get_current_user_id(), 'vk-all-in-one-expansion-unit_dismissed_notice', true ) ) {
					return true;
				}
			}
		}

		/**
		 * Display help notice on specific page
		 *
		 * @return void
		 */
		public static function display_help_notice() {

			if ( self::is_display_help_notice() ) {
				echo self::add_post_type_get_help_notice();
			}

			if ( isset( $_GET['vk-all-in-one-expansion-unit-dismiss'] ) && $_GET['vk-all-in-one-expansion-unit-dismiss'] === 'dismiss_admin_notice' ) {
				check_admin_referer( 'vk-all-in-one-expansion-unit-dismiss-' . get_current_user_id() );
				update_user_meta( get_current_user_id(), 'vk-all-in-one-expansion-unit_dismissed_notice', true );
			}
		}

		/*******************************************
		 * カスタムフィールドの meta box を作成.
		 */
		public static function add_meta_box() {
			add_meta_box( 'meta_box_post_type_manage', __( 'Custom Post Type Setting', 'vk-all-in-one-expansion-unit' ), array( __CLASS__, 'add_meta_box_action' ), 'post_type_manage', 'normal', 'high' );
		}

		/**
		 * Add meta box action
		 *
		 * @return void
		 */
		public static function add_meta_box_action() {

			global $post;

			// CSRF対策の設定（フォームにhiddenフィールドとして追加するためのnonceを「'noncename__post_type_manager」として設定）.
			wp_nonce_field( wp_create_nonce( __FILE__ ), 'noncename__post_type_manager' );

			// 通知メッセージを取得して表示
			echo self::add_post_type_get_help_notice();

			?>
			<style type="text/css">
			table.table { border-collapse: collapse; border-spacing: 0;width:100%; }
			table.table th,
			table.table td{ padding:0.5em 0.8em; }
			table.table th { background-color: #f5f5f5; }
			table.table-border,
			table.table-border th,
			table.table-border td { border:1px solid #e5e5e5; }
			</style>
			<?php

			/*******************************************
			 * Post Type ID
			 */
			echo '<h4>' . esc_html__( 'Post Type ID(Required)', 'vk-all-in-one-expansion-unit' ) . '</h4>';
			echo '<p>' . esc_html__( 'Please enter a string of up to 20 characters consisting of half-width lowercase alphanumeric characters, half-width hyphens, and half-width underscores.', 'vk-all-in-one-expansion-unit' ) . '</p>';
			echo '<input class="form-control" type="text" id="veu_post_type_id" name="veu_post_type_id" value="' . esc_attr( mb_strimwidth( mb_convert_kana( mb_strtolower( $post->veu_post_type_id ), 'a' ), 0, 20, '', 'UTF-8' ) ) . '" size="30">';
			echo '<hr>';

			$post_type_items_array = array(
				'title'         => __( 'title', 'vk-all-in-one-expansion-unit' ),
				'editor'        => __( 'editor', 'vk-all-in-one-expansion-unit' ),
				'author'        => __( 'author', 'vk-all-in-one-expansion-unit' ),
				'thumbnail'     => __( 'thumbnail', 'vk-all-in-one-expansion-unit' ),
				'excerpt'       => __( 'excerpt', 'vk-all-in-one-expansion-unit' ),
				'comments'      => __( 'comments', 'vk-all-in-one-expansion-unit' ),
				'revisions'     => __( 'revisions', 'vk-all-in-one-expansion-unit' ),
				'custom-fields' => __( 'custom-fields', 'vk-all-in-one-expansion-unit' ),
			);

			/*******************************************
			 * Supports(Required)
			 */
			echo '<h4>' . esc_html__( 'Supports ( Required )', 'vk-all-in-one-expansion-unit' ) . '</h4>';
			$post_type_items_value = get_post_meta( $post->ID, 'veu_post_type_items', true );

			echo '<ul>';
			foreach ( $post_type_items_array as $key => $label ) {
				$checked = ( isset( $post_type_items_value[ $key ] ) && $post_type_items_value[ $key ] ) ? ' checked' : '';
				echo '<li><label><input type="checkbox" id="veu_post_type_items[' . esc_attr( $key ) . ']" name="veu_post_type_items[' . esc_attr( $key ) . ']" value="true"' . esc_attr( $checked ) . '> ' . esc_html( $label ) . '</label></li>';
			}
			echo '</ul>';

			echo '<hr>';

			/*******************************************
			 * Menu position
			 */
			echo '<h4>' . esc_html__( 'Menu position(optional)', 'vk-all-in-one-expansion-unit' ) . '</h4>';
			echo '<p>' . esc_html__( 'Please enter a number.', 'vk-all-in-one-expansion-unit' ) . '</p>';
			echo '<input class="form-control" type="text" id="veu_menu_position" name="veu_menu_position" value="' . esc_attr( $post->veu_menu_position ) . '" size="30">';

			echo '<hr>';

			/*******************************************
			 * Menu Icon
			 */
			echo '<h4>' . esc_html__( 'Menu Icon(Optional)', 'vk-all-in-one-expansion-unit' ) . '</h4>';
			echo '<p>' . esc_html__( 'Select an icon from the images below, or enter a custom Dashicon class.', 'vk-all-in-one-expansion-unit' ) . '</p>';

			echo '<div style="margin-bottom: 1rem;">';
			$icons = array(
				'dashicons-admin-post',
				'dashicons-admin-site',
				'dashicons-admin-users',
				'dashicons-admin-media',
				'dashicons-admin-comments',
				'dashicons-admin-appearance',
				'dashicons-welcome-write-blog',
				'dashicons-dashboard',
				'dashicons-admin-plugins',
				'dashicons-admin-settings',
				'dashicons-admin-network',
				'dashicons-admin-home',
				'dashicons-admin-generic',
				'dashicons-admin-collapse',
			);

			foreach ( $icons as $icon ) {
				echo '<button type="button" class="button" style="margin-right: 10px; margin-bottom: 10px; width: 40px; height: 40px; padding: 5px;" onclick="updateIconSelection(\'' . esc_attr( $icon ) . '\');">';
				echo '<span class="dashicons ' . esc_attr( $icon ) . '" style="font-size: 20px; vertical-align: sub;"></span>';
				echo '</button>';
			}

			echo '<div>';
			echo '<input type="text" id="veu_menu_icon" name="veu_menu_icon" value="' . esc_attr( $post->veu_menu_icon ) . '" style="margin-right: 10px;" size="30">';
			echo '<a href="https://developer.wordpress.org/resource/dashicons/" class="button" target="_blank">' . esc_html__( 'Dashicons Library', 'vk-all-in-one-expansion-unit' ) . '</a>';
			echo '</div>';

			echo '</div>';

			echo '<hr>';

			// JavaScript to update icon selection and validate input
			echo '
			<script>
				function updateIconSelection(icon) {
					document.getElementById("veu_menu_icon").value = icon;
				}

				document.addEventListener("DOMContentLoaded", function () {
					var inputField = document.getElementById("veu_menu_icon");
					
					// `change` イベントを使用して、フォーカスが外れたときにチェックする
					inputField.addEventListener("change", function() {
						// SVGデータURI、\'none\'、または\'dashicons-\'で始まる値を許可
						if (!this.value.startsWith("dashicons-") && !this.value.startsWith("data:image/svg+xml;base64,") && this.value !== \'none\') {
							alert("' . __( 'Please enter a valid input. You can enter a Dashicon class, a base64-encoded SVG, or \'none\' to leave it blank for CSS customization.', 'vk-all-in-one-expansion-unit' ) . '");
							this.value = ""; // 不正な入力をクリア
						}
					});
				});
			</script>';

			/*******************************************
			 * Export to Rest api
			 */
			echo '<h4>' . esc_html__( 'Corresponds to the block editor (optional)', 'vk-all-in-one-expansion-unit' ) . '</h4>';

			// 現在保存されているカスタムフィールドの値を取得.
			$export_to_api_value = get_post_meta( $post->ID, 'veu_post_type_export_to_api', true );
			if ( 'false' !== $export_to_api_value && 'true' !== $export_to_api_value ) {
				$export_to_api_value = 'true';
			}

			echo '<label><input type="radio" id="veu_post_type_export_to_api" name="veu_post_type_export_to_api" value="true"' . checked( $export_to_api_value, 'true', false ) . '> ' . esc_html__( 'Corresponds to the block editor ( Export to REST API / optional )', 'vk-all-in-one-expansion-unit' ) . '</label>';
			echo '<br />';
			echo '<label><input type="radio" id="veu_post_type_export_to_api" name="veu_post_type_export_to_api" value="false"' . checked( $export_to_api_value, 'false', false ) . '> ' . esc_html__( 'Does not correspond to the block editor', 'vk-all-in-one-expansion-unit' ) . '</label>';

			echo '<p>' . esc_html__( 'If you want to use the block editor that, you have to use the REST API.', 'vk-all-in-one-expansion-unit' ) . '</p>';
			echo '<hr>';

			/*******************************************
			 * Embed Settings
			 */
			// WordPress 6.8以上の場合のみ表示
			$wp_version = get_bloginfo( 'version' );
			if ( version_compare( $wp_version, '6.8', '>=' ) ) {
				echo '<h4>' . esc_html__( 'Embed Settings (Optional)', 'vk-all-in-one-expansion-unit' ) . '</h4>';

				$is_embeddable = get_post_meta( $post->ID, 'veu_is_embeddable', true );
				$checked       = ( 'false' === $is_embeddable ) ? ' checked' : '';

				echo '<label><input type="checkbox" id="veu_is_embeddable" name="veu_is_embeddable" value="true"' . esc_attr( $checked ) . '> ' . esc_html( __( 'Disable embedding from external sites (oEmbed)', 'vk-all-in-one-expansion-unit' ) ) . '</label>';
				echo '<p>' . esc_html__( 'When checked, this post type will not be embeddable from external sites. This prevents blog card-like embedding when the URL is shared on other sites. Useful for creating post types that you want to prevent from being visible externally.', 'vk-all-in-one-expansion-unit' ) . '</p>';

				echo '<hr>';
			}

			/**
			 * Enable / Disable Rewrite.
			 * パーマリンクのリライトを有効にするかどうか.
			 */
			echo '<h4>' . esc_html__( 'Rewrite permalink (optional)', 'vk-all-in-one-expansion-unit' ) . '</h4>';

			$post_type_rewrite_value = get_post_meta( $post->ID, 'veu_post_type_rewrite', true );
			// post_type_rewrite_value の値が with_front_false だった場合はチェックを入れる.
			$checked = ( 'with_front_false' === $post_type_rewrite_value ) ? ' checked' : '';

			echo '<label><input type="checkbox" id="veu_post_type_rewrite" name="veu_post_type_rewrite" value="with_front_false"' . esc_attr( $checked ) . '> ' . esc_html( __( 'Disable the permalink settings specified in Custom Structure.（ Set with_front to false ）', 'vk-all-in-one-expansion-unit' ) ) . '</label>';

			echo '<p>';
			echo wp_kses_post(
				sprintf(
					__( 'For example, if "news/%%postname%%/" is set in the Custom Structure of the <a href="%1$s" target="_blank">Permalink Settings</a>, the URL for the custom post type "event" will also include "news", resulting in a URL like https://xxxx.xxx/news/event/%%postname%%/.', 'vk-all-in-one-expansion-unit' ),
					admin_url( 'options-permalink.php' )
				)
			);
			echo '<br>';
			echo esc_html__( 'By setting with_front to false, you can ensure the URL is formatted as https://xxxx.xxx/event/%postname%/ without being affected by the Custom Structure settings.', 'vk-all-in-one-expansion-unit' );
			echo '<br>';
			echo esc_html__( 'It is not affected if you do not add strings like "news" to the Custom Structure.', 'vk-all-in-one-expansion-unit' );
			echo '</p>';
			echo '<hr>';

			/*******************************************
			 * Custom taxonomies
			 */
			echo '<h4>' . esc_html__( 'Custom taxonomies(optional)', 'vk-all-in-one-expansion-unit' ) . '</h4>';

			echo '<p>';
			echo esc_html__( 'Custom taxonomy is like a category in post.', 'vk-all-in-one-expansion-unit' ) . '<br />';
			echo esc_html__( 'However, it refers to the "category" itself, not to the "item" of the category.', 'vk-all-in-one-expansion-unit' ) . '<br />';
			echo esc_html__( 'For example, if you create a post type "construction result", Custom taxonomy will be "construction type", "construction area", etc.', 'vk-all-in-one-expansion-unit' ) . '<br />';
			echo esc_html__( 'You can use a taxonomy used by other post types. However, changing settings like hierarchy or REST API support will affect all.', 'vk-all-in-one-expansion-unit' );
			echo '</p>';

			echo '<table class="table table-border">';

			// カスタム分類の情報は カスタムフィールドの veu_taxonomy に連想配列で格納している.
			$taxonomy = get_post_meta( $post->ID, 'veu_taxonomy', true );

			for ( $i = 1; $i <= apply_filters( 'veu_post_type_taxonomies', 5 ); $i++ ) {
				$slug     = ( isset( $taxonomy[ $i ]['slug'] ) ) ? $taxonomy[ $i ]['slug'] : '';
				$label    = ( isset( $taxonomy[ $i ]['label'] ) ) ? $taxonomy[ $i ]['label'] : '';
				$tag      = ( isset( $taxonomy[ $i ]['tag'] ) ) ? $taxonomy[ $i ]['tag'] : '';
				$rest_api = ( isset( $taxonomy[ $i ]['rest_api'] ) ) ? $taxonomy[ $i ]['rest_api'] : '';

				// グローバル設定があるかチェック
				if ( ! empty( $slug ) ) {
					$global_settings = self::get_global_taxonomy_settings( $slug );
					if ( $global_settings ) {
						$tag      = $global_settings['tag'];
						$rest_api = $global_settings['rest_api'];
					}
				}

				echo '<tr>';

				echo '<th rowspan="4">' . esc_attr( $i ) . '</th>';

				// slug.
				echo '<td>' . esc_html__( 'Custon taxonomy name (slug)', 'vk-all-in-one-expansion-unit' ) . '</td>';
				echo '<td><input type="text" id="veu_taxonomy[' . esc_attr( $i ) . '][slug]" name="veu_taxonomy[' . esc_attr( $i ) . '][slug]" value="' . esc_attr( $slug ) . '" size="20">';
				echo '<div>' . esc_html__( '* Please enter a string consisting of half-width lowercase alphanumeric characters, half-width hyphens, and half-width underscores.', 'vk-all-in-one-expansion-unit' ) . '</div>';
				if ( ! empty( $slug ) && self::is_taxonomy_shared( $slug, $post->ID ) ) {
					$shared_message = self::get_taxonomy_shared_info( $slug, $post->ID, 'message' );
					echo '<div style="color: #0073aa; font-size: 12px; margin-top: 5px; line-height: 1.4;">' . wp_kses_post( $shared_message ) . '</div>';
				}
				echo '</td>';

				// 表示名.
				echo '<tr>';
				echo '<td>' . esc_html__( 'Custon taxonomy label', 'vk-all-in-one-expansion-unit' ) . '</td>';
				echo '<td><input type="text" id="veu_taxonomy[' . esc_attr( $i ) . '][label]" name="veu_taxonomy[' . esc_attr( $i ) . '][label]" value="' . esc_attr( $label ) . '" size="20"></td>';
				echo '</tr>';

				// tag.
				echo '<tr>';
				$checked = ( isset( $taxonomy[ $i ]['tag'] ) && $taxonomy[ $i ]['tag'] ) ? ' checked' : '';
				echo '<td>' . esc_html__( 'Hierarchy', 'vk-all-in-one-expansion-unit' ) . '</td>';
				echo '<td><label><input type="checkbox" id="veu_taxonomy[' . esc_attr( $i ) . '][tag]" name="veu_taxonomy[' . esc_attr( $i ) . '][tag]" value="true"' . esc_attr( $checked ) . '> ' . esc_html__( 'Make it a tag (do not hierarchize)', 'vk-all-in-one-expansion-unit' ) . '</label></td>';
				echo '</tr>';

				// REST API.
				echo '<tr>';

				// チェックが元々入ってるかどうか.
				// 過去の仕様ではデフォルトで REST API はチェック無しだった.
				// しかし、一般的にブロックエディタ対応にする方が需要が高いため、デフォルトで true になるように変更した。
				// そのため、そのため、設定画面においては true で保存されていない場合は true にして返す.
				if ( isset( $taxonomy[ $i ]['rest_api'] ) ) {
					$checked = $taxonomy[ $i ]['rest_api'];
				}
				if ( 'false' !== $checked && 'true' !== $checked ) {
					$checked = 'true';
				}

				echo '<td>' . esc_html__( 'Corresponds to the block editor', 'vk-all-in-one-expansion-unit' ) . '</td>';
				echo '<td>';
				echo '<label><input type="radio" id="veu_taxonomy[' . esc_attr( $i ) . '][rest_api]" name="veu_taxonomy[' . esc_attr( $i ) . '][rest_api]" value="true"' . checked( $checked, 'true', false ) . '> ' . esc_html__( 'Corresponds to the block editor ( Export to REST API / optional )', 'vk-all-in-one-expansion-unit' ) . '</label>';
				echo '<br />';
				echo '<br />';
				echo '<label><input type="radio" id="veu_taxonomy[' . esc_attr( $i ) . '][rest_api]" name="veu_taxonomy[' . esc_attr( $i ) . '][rest_api]" value="false"' . checked( $checked, 'false', false ) . '> ' . esc_html__( 'Does not correspond to the block editor', 'vk-all-in-one-expansion-unit' ) . '</label>';
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';

			// カスタム分類の共通設定管理用JavaScript
			echo '<script>
				// グローバルスコープで関数を定義
				window.importSettings = function(index, settingsAttr) {
					if (!settingsAttr) return;
					
					try {
						var settings = JSON.parse(decodeURIComponent(settingsAttr));
						
						// ラベルを設定
						var labelField = document.getElementById("veu_taxonomy[" + index + "][label]");
						if (labelField) {
							labelField.value = settings.label;
						}

						// タグ設定を更新
						var tagField = document.getElementById("veu_taxonomy[" + index + "][tag]");
						if (tagField) {
							tagField.checked = settings.tag === "true";
						}

						// REST API設定を更新
						var restApiInputs = document.querySelectorAll("input[name=\'veu_taxonomy[" + index + "][rest_api]\']");
						restApiInputs.forEach(function(input) {
							input.checked = input.value === settings.rest_api;
						});
					} catch (e) {
						console.error("設定のインポートに失敗しました:", e);
					}
				};

				document.addEventListener("DOMContentLoaded", function() {
					var globalSettings = ' . wp_json_encode( get_option( 'veu_global_taxonomy_settings', array() ) ) . ';
					var currentPostId = ' . intval( $post->ID ) . ';
					var ajaxUrl = "' . admin_url( 'admin-ajax.php' ) . '";
					var nonce = "' . wp_create_nonce( 'check_taxonomy_shared' ) . '";
					
					// スラッグフィールドの処理
					for (var i = 1; i <= ' . apply_filters( 'veu_post_type_taxonomies', 5 ) . '; i++) {
						(function(index) {
							var slugField = document.getElementById("veu_taxonomy[" + index + "][slug]");
							if (!slugField) return;
							
							slugField.addEventListener("blur", function() {
								var slug = this.value.trim();
								var container = this.parentNode;
								
								if (!slug) {
									toggleNotice(container, false);
									return;
								}
								
								// AJAX でチェック
								fetch(ajaxUrl, {
									method: "POST",
									headers: {"Content-Type": "application/x-www-form-urlencoded"},
									body: "action=check_taxonomy_shared&taxonomy_slug=" + encodeURIComponent(slug) + 
										  "&current_post_id=" + currentPostId + "&nonce=" + nonce
								})
								.then(response => response.json())
								.then(data => {
									if (data.success && data.data.is_shared) {
										var settings = data.data.existing_settings;
										var settingsAttr = encodeURIComponent(JSON.stringify(settings));
										var importMessage = data.data.message + 
											\'<div style="margin-top: 10px;">\' +
											\'<button type="button" class="button" data-settings="\' + settingsAttr + \'" data-index="\' + index + \'" onclick="importSettings(this.dataset.index, this.dataset.settings)">\' + 
											\'' . esc_js( __( 'Import existing settings', 'vk-all-in-one-expansion-unit' ) ) . '\' +
											\'</button>\' +
											\'</div>\';
										toggleNotice(container, true, importMessage);
									} else {
										toggleNotice(container, false);
									}
								});
							});
						})(i);
					}
				});

				// 通知を表示/削除する関数
				function toggleNotice(container, show, message) {
					var notice = container.querySelector(".taxonomy-shared-notice");
					if (notice) notice.remove();
					
					if (show && message) {
						var newNotice = document.createElement("div");
						newNotice.className = "taxonomy-shared-notice";
						newNotice.style.cssText = "color: #0073aa; font-size: 12px; margin-top: 5px; line-height: 1.4;";
						newNotice.innerHTML = message;
						container.appendChild(newNotice);
					}
				}
			</script>';
		}

		/***
		 * 入力されたカスタムフィールドの値の保存
		 *
		 * @param string $post_id : post id.
		 */
		public static function save_cf_value( $post_id ) {
			global $post;

			// 設定したnonce を取得（CSRF対策）.
			$noncename__post_type_manager = isset( $_POST['noncename__post_type_manager'] ) ? wp_unslash( $_POST['noncename__post_type_manager'] ) : null;

			// nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）.
			if ( ! wp_verify_nonce( $noncename__post_type_manager, wp_create_nonce( __FILE__ ) ) ) {
				return $post_id;
			}

			// 自動保存ルーチンかどうかチェック。そうだった場合は何もしない（記事の自動保存処理として呼び出された場合の対策）.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			$post_type_id            = ! empty( $_POST['veu_post_type_id'] ) ? esc_html( strip_tags( $_POST['veu_post_type_id'] ) ) : '';
			$post_type_items         = ! empty( $_POST['veu_post_type_items'] ) ? $_POST['veu_post_type_items'] : '';
			$menu_posttion           = ! empty( $_POST['veu_menu_position'] ) ? esc_html( strip_tags( $_POST['veu_menu_position'] ) ) : '';
			$menu_icon               = ! empty( $_POST['veu_menu_icon'] ) ? esc_html( strip_tags( $_POST['veu_menu_icon'] ) ) : '';
			$post_type_export_to_api = ! empty( $_POST['veu_post_type_export_to_api'] ) ? esc_html( $_POST['veu_post_type_export_to_api'] ) : '';
			$post_type_rewrite       = ! empty( $_POST['veu_post_type_rewrite'] ) ? esc_html( $_POST['veu_post_type_rewrite'] ) : '';

			if ( ! empty( $_POST['veu_taxonomy'] ) ) {
				$taxonomy = $_POST['veu_taxonomy'];

				for ( $i = 1; $i <= apply_filters( 'veu_post_type_taxonomies', 5 ); $i++ ) {
					$taxonomy[ $i ]['slug']     = ! empty( $taxonomy[ $i ]['slug'] ) ? esc_html( strip_tags( $taxonomy[ $i ]['slug'] ) ) : '';
					$taxonomy[ $i ]['label']    = ! empty( $taxonomy[ $i ]['label'] ) ? esc_html( strip_tags( $taxonomy[ $i ]['label'] ) ) : '';
					$taxonomy[ $i ]['tag']      = ! empty( $taxonomy[ $i ]['tag'] ) ? esc_html( $taxonomy[ $i ]['tag'] ) : '';
					$taxonomy[ $i ]['rest_api'] = ! empty( $taxonomy[ $i ]['rest_api'] ) ? esc_html( $taxonomy[ $i ]['rest_api'] ) : '';

					// カスタム分類の共通設定を更新
					if ( ! empty( $taxonomy[ $i ]['slug'] ) ) {
						$settings = array(
							'tag'      => $taxonomy[ $i ]['tag'],
							'rest_api' => $taxonomy[ $i ]['rest_api'],
							'label'    => $taxonomy[ $i ]['label'],
						);
						self::update_global_taxonomy_settings( $taxonomy[ $i ]['slug'], $settings );
					}
				}
			}

			// Save is_embeddable option
			$is_embeddable = isset( $_POST['veu_is_embeddable'] ) ? 'false' : 'true';
			update_post_meta( $post_id, 'veu_is_embeddable', $is_embeddable );

			// 保存しているカスタムフィールド.
			$fields = array(
				'veu_post_type_id'            => $post_type_id,
				'veu_post_type_items'         => $post_type_items,
				'veu_menu_position'           => $menu_posttion,
				'veu_menu_icon'               => $menu_icon,
				'veu_post_type_export_to_api' => $post_type_export_to_api,
				'veu_post_type_rewrite'       => $post_type_rewrite,
				'veu_taxonomy'                => $taxonomy,
			);

			foreach ( $fields as $field_name => $field_value ) {
				if ( ! empty( $field_value ) ) {
					update_post_meta( $post_id, $field_name, $field_value );
				} else {
					delete_post_meta( $post_id, $field_name );
				}
			}

			// リライトルールを更新するように.
			delete_post_meta( $post_id, 'veu_post_type_flush_rewrite_rules' );
		}

		/**
		 * 登録したカスタム投稿タイプを実際に作成
		 */
		public static function add_post_type() {
			$args = array(
				'posts_per_page'   => -1,
				'post_type'        => 'post_type_manage',
				'post_status'      => 'publish',
				'order'            => 'ASC',
				'orderby'          => 'menu_order',
				'suppress_filters' => true,

			);
			$custom_post_types = get_posts( $args );
			if ( $custom_post_types ) {
				$post_type_ids = array();
				foreach ( $custom_post_types as $key => $post ) {

					/*******************************************
					 * メニューアイコンを設定するためのコード
					 */
					$menu_icon = get_post_meta( $post->ID, 'veu_menu_icon', true );
					if ( empty( $menu_icon ) ) {
						$menu_icon = 'dashicons-admin-post';
					} elseif ( $menu_icon === 'none' ) {
						$menu_icon = ''; // CSSでスタイリング可能に
					} elseif ( ! strpos( $menu_icon, 'dashicons-' ) === 0 && ! strpos( $menu_icon, 'data:image/svg+xml;base64,' ) === 0 ) {
						$menu_icon = 'dashicons-admin-post';
					}

					/*******************************************
					 * 投稿タイプ追加.
					 */
					$labels = array(
						'name'          => esc_html( $post->post_title ),
						'singular_name' => esc_html( $post->post_title ),
						'menu_name'     => esc_html( $post->post_title ),
					);

					$post_type_items = get_post_meta( $post->ID, 'veu_post_type_items', true );
					if ( ! $post_type_items ) {
						$post_type_items = array( 'title' );
					}
					foreach ( $post_type_items as $key => $value ) {
						$supports[] = $key;
					}

					// 投稿タイプのアイコンを取得
					$menu_icon = get_post_meta( $post->ID, 'veu_menu_icon', true );
					$menu_icon = ! empty( $menu_icon ) ? $menu_icon : 'dashicons-admin-post';

					// カスタム投稿タイプのスラッグ.
					$post_type_id = mb_strimwidth( mb_convert_kana( mb_strtolower( esc_html( get_post_meta( $post->ID, 'veu_post_type_id', true ) ) ), 'a' ), 0, 20, '', 'UTF-8' );

					if ( $post_type_id ) {
						$post_type_ids[] = $post_type_id;
						$menu_position   = intval( mb_convert_kana( get_post_meta( $post->ID, 'veu_menu_position', true ), 'n' ) );
						if ( ! $menu_position ) {
							$menu_position = 5;
						}

						$veu_post_type_rewrite = get_post_meta( $post->ID, 'veu_post_type_rewrite', true );

						if ( 'with_front_false' === $veu_post_type_rewrite ) {
							$rewrite = array(
								'slug'       => $post_type_id,
								'with_front' => false,
								// 'rewrite_slug' => false,
							);
						} elseif ( 'false' === $veu_post_type_rewrite ) {
							// 'false' の設定は旧バージョンのもので、9.96 で廃止したが、
							// 設定しているユーザーがいるかもしれないので、一応残してある
							// この false による指定は 2024年9月以降に削除可
							$rewrite = 'false';
						} else {
							$rewrite = true;
						}

						$args = array(
							'labels'        => $labels,
							'public'        => true,
							'has_archive'   => true,
							'menu_position' => $menu_position,
							'menu_icon'     => $menu_icon,
							'supports'      => $supports,
							'rewrite'       => $rewrite,
						);

						// REST API に出力するかどうかをカスタムフィールドから取得.
						$rest_api   = get_post_meta( $post->ID, 'veu_post_type_export_to_api', true );
						$flush_flag = get_post_meta( $post->ID, 'veu_post_type_flush_rewrite_rules', true );
						// REST APIに出力する場合.
						if ( 'true' === $rest_api || '1' === $rest_api ) {
							$rest_args = array(
								'show_in_rest' => true,
								'rest_base'    => $post_type_id,
							);
							$args      = array_merge( $args, $rest_args );
						}

						// Add is_embeddable option
						$args['is_embeddable'] = self::is_post_type_embeddable( $post->ID );

						// カスタム投稿タイプを発行.
						register_post_type( $post_type_id, $args );

						// パーマリンク設定を更新するかどうか.
						if ( empty( $flush_flag ) ) {
							flush_rewrite_rules();
							update_post_meta( $post->ID, 'veu_post_type_flush_rewrite_rules', 'true' );
						}

						// Add filter for post embeddable control
						add_filter( 'is_post_embeddable', array( __CLASS__, 'control_post_embeddable' ), 10, 2 );

						/*******************************************
						 * カスタム分類を追加
						 */

						// カスタムフィールドに連想配列で格納しておいたカスタム分類の情報を取得.
						$veu_taxonomies = get_post_meta( $post->ID, 'veu_taxonomy', true );

						foreach ( $veu_taxonomies as $key => $taxonomy ) {
							if ( $taxonomy['slug'] && $taxonomy['label'] ) {

								// 既存のタクソノミーをチェック
								if ( ! taxonomy_exists( $taxonomy['slug'] ) ) {
									// カスタム分類を階層化するかどうか.
									$hierarchical_true = ( empty( $taxonomy['tag'] ) ) ? true : false;
									// REST API を使用するかどうか.
									if ( isset( $taxonomy['rest_api'] ) ) {
										$rest_api = $taxonomy['rest_api'];
									}

									if ( 'true' === $rest_api || '1' === $rest_api ) {
										$rest_api_true = true;
									} else {
										$rest_api_true = false;
									}

									$labels = array(
										'name' => $taxonomy['label'],
									);

									// リライトルールの設定 //////////////////////////////////////
									// 投稿タイプのリライトルールを反映させる
									if ( 'with_front_false' === $veu_post_type_rewrite ) {
										$rewrite = array(
											'slug'       => $taxonomy['slug'],
											'with_front' => false,
											// 'rewrite_slug' => false,
										);
									} elseif ( isset( $taxonomy['rewrite'] ) && 'false' === $taxonomy['rewrite'] ) {
										// 旧バージョンではカスタム分類毎でリライト設定があったのでその設定を参照
										// 'false' の設定は旧バージョンのもので、9.96 で廃止したが、
										// 設定しているユーザーがいるかもしれないので、一応残してある
										// この $taxonomy['rewrite'] による指定は 2024年9月以降に削除可
										$rewrite = 'false';
									} else {
										$rewrite = true;
									}

									$args = array(
										'hierarchical' => $hierarchical_true,
										'update_count_callback' => '_update_post_term_count',
										'labels'       => $labels,
										'public'       => true,
										'show_ui'      => true,
										'show_in_rest' => $rest_api_true,
										'show_admin_column' => true,
										'rewrite'      => $rewrite,
									);

									if ( $rest_api_true ) {
										$args['rest_base'] = $taxonomy['slug'];
									}

									// 特定の投稿タイプにのみタクソノミーを登録
									register_taxonomy(
										$taxonomy['slug'],
										array( $post_type_id ),
										$args
									);
								} else {
									// 既存のタクソノミーを再利用
									register_taxonomy_for_object_type( $taxonomy['slug'], $post_type_id );
								}
							} // if ( $taxonomy['slug'] && $taxonomy['label']){
						} // foreach ($veu_taxonomies as $key => $taxonomy) {
					} // if ( $post_type_id ) {
				} // foreach ($custom_post_types as $key => $post) {
			} // if ( $custom_post_types ) {
		}

		/**
		 * Check if the post type is embeddable based on saved settings.
		 *
		 * @param int $post_id The post ID to check.
		 * @return bool True if embeddable, false otherwise.
		 */
		public static function is_post_type_embeddable( $post_id ) {
			$is_embeddable = get_post_meta( $post_id, 'veu_is_embeddable', true );
			return ( 'false' !== $is_embeddable );
		}

		/**
		 * Control whether a post is embeddable
		 *
		 * @param bool   $is_embeddable Whether the post is embeddable.
		 * @param object $post          Post object.
		 * @return bool
		 */
		public static function control_post_embeddable( $is_embeddable, $post ) {
			// Get post type settings
			$post_type_settings = get_posts(
				array(
					'post_type'      => 'post_type_manage',
					'posts_per_page' => 1,
					'meta_key'       => 'veu_post_type_id',
					'meta_value'     => $post->post_type,
				)
			);

			if ( ! empty( $post_type_settings ) ) {
				$settings = $post_type_settings[0];
				return self::is_post_type_embeddable( $settings->ID );
			}

			return $is_embeddable;
		}

		/**
		 * カスタム分類の共通設定を更新
		 *
		 * @param string $taxonomy_slug タクソノミーのスラッグ
		 * @param array  $settings      設定配列（tag, rest_api, label）
		 */
		public static function update_global_taxonomy_settings( $taxonomy_slug, $settings ) {
			if ( empty( $taxonomy_slug ) ) {
				return;
			}

			// グローバル設定を更新
			$global_taxonomy_settings                   = get_option( 'veu_global_taxonomy_settings', array() );
			$global_taxonomy_settings[ $taxonomy_slug ] = $settings;
			update_option( 'veu_global_taxonomy_settings', $global_taxonomy_settings );

			// 同じスラッグを使用している全ての投稿タイプを更新
			$args = array(
				'post_type'      => 'post_type_manage',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			);

			foreach ( get_posts( $args ) as $post ) {
				$taxonomy_data = get_post_meta( $post->ID, 'veu_taxonomy', true );
				if ( ! is_array( $taxonomy_data ) ) {
					continue;
				}

				$updated = false;
				foreach ( $taxonomy_data as $key => $taxonomy ) {
					if ( isset( $taxonomy['slug'] ) && $taxonomy['slug'] === $taxonomy_slug ) {
						$taxonomy_data[ $key ]['tag']      = $settings['tag'];
						$taxonomy_data[ $key ]['rest_api'] = $settings['rest_api'];
						$taxonomy_data[ $key ]['label']    = $settings['label'];
						$updated                           = true;
					}
				}

				if ( $updated ) {
					update_post_meta( $post->ID, 'veu_taxonomy', $taxonomy_data );
				}
			}
		}

		/**
		 * グローバルなカスタム分類設定を取得
		 *
		 * @param string $taxonomy_slug タクソノミーのスラッグ
		 * @return array|null 設定配列またはnull
		 */
		public static function get_global_taxonomy_settings( $taxonomy_slug ) {
			$global_taxonomy_settings = get_option( 'veu_global_taxonomy_settings', array() );
			return isset( $global_taxonomy_settings[ $taxonomy_slug ] ) ? $global_taxonomy_settings[ $taxonomy_slug ] : null;
		}

		/**
		 * タクソノミーが他の投稿タイプでも使用されているかチェック
		 *
		 * @param string $taxonomy_slug タクソノミーのスラッグ
		 * @param int    $current_post_id 現在の投稿ID（除外用）
		 * @return bool 他の投稿タイプで使用されている場合true
		 */
		public static function is_taxonomy_shared( $taxonomy_slug, $current_post_id = 0 ) {
			return self::get_taxonomy_shared_info( $taxonomy_slug, $current_post_id, 'check' );
		}

		/**
		 * AJAX: タクソノミーが共有されているかチェック
		 */
		public static function ajax_check_taxonomy_shared() {
			// nonce チェック
			if ( ! wp_verify_nonce( $_POST['nonce'], 'check_taxonomy_shared' ) ) {
				wp_die( 'Security check failed' );
			}

			$taxonomy_slug   = sanitize_text_field( $_POST['taxonomy_slug'] );
			$current_post_id = intval( $_POST['current_post_id'] );

			$is_shared = self::get_taxonomy_shared_info( $taxonomy_slug, $current_post_id, 'check' );
			$message   = $is_shared ? self::get_taxonomy_shared_info( $taxonomy_slug, $current_post_id, 'message' ) : '';

			// 既存の設定を取得
			$existing_settings = array();
			if ( $is_shared ) {
				$args = array(
					'post_type'      => 'post_type_manage',
					'posts_per_page' => 1,
					'post_status'    => 'publish',
					'post__not_in'   => array( $current_post_id ),
				);

				$posts = get_posts( $args );
				foreach ( $posts as $post ) {
					$taxonomy_data = get_post_meta( $post->ID, 'veu_taxonomy', true );
					if ( is_array( $taxonomy_data ) ) {
						foreach ( $taxonomy_data as $taxonomy ) {
							if ( isset( $taxonomy['slug'] ) && $taxonomy['slug'] === $taxonomy_slug ) {
								$existing_settings = array(
									'label'    => $taxonomy['label'],
									'tag'      => $taxonomy['tag'],
									'rest_api' => $taxonomy['rest_api'],
								);
								break 2;
							}
						}
					}
				}
			}

			wp_send_json_success(
				array(
					'is_shared'         => $is_shared,
					'message'           => $message,
					'existing_settings' => $existing_settings,
				)
			);
		}

		/**
		 * タクソノミー共有情報を取得（統合メソッド）
		 *
		 * @param string $taxonomy_slug タクソノミーのスラッグ
		 * @param int    $exclude_post_id 除外する投稿ID
		 * @param string $return_type 戻り値の型 ('check'|'message'|'types')
		 * @return mixed
		 */
		public static function get_taxonomy_shared_info( $taxonomy_slug, $exclude_post_id = 0, $return_type = 'check' ) {
			if ( empty( $taxonomy_slug ) ) {
				return ( 'check' === $return_type ) ? false : '';
			}

			$args = array(
				'post_type'      => 'post_type_manage',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'post__not_in'   => $exclude_post_id ? array( $exclude_post_id ) : array(),
			);

			$post_types  = array();
			$post_titles = array();
			foreach ( get_posts( $args ) as $post ) {
				$taxonomy_data = get_post_meta( $post->ID, 'veu_taxonomy', true );
				if ( ! is_array( $taxonomy_data ) ) {
					continue;
				}

				foreach ( $taxonomy_data as $taxonomy ) {
					if ( isset( $taxonomy['slug'] ) && $taxonomy['slug'] === $taxonomy_slug ) {
						$post_type_id = get_post_meta( $post->ID, 'veu_post_type_id', true );
						if ( $post_type_id ) {
							$post_types[]  = $post_type_id;
							$post_titles[] = get_the_title( $post->ID );
						}
						break;
					}
				}
			}

			$post_types  = array_unique( $post_types );
			$post_titles = array_unique( $post_titles );

			if ( 'check' === $return_type ) {
				return ! empty( $post_types );
			}

			if ( 'types' === $return_type ) {
				return $post_types;
			}

			// message
			if ( empty( $post_titles ) ) {
				return '';
			}

			return sprintf(
				__( 'This taxonomy is already used by the following post types: %s', 'vk-all-in-one-expansion-unit' ),
				'<strong>' . esc_html( implode( ', ', $post_titles ) ) . '</strong>'
			);
		}

		/**
		 * Constructer
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'add_post_type_post_type_manage' ) );
			add_action( 'admin_init', array( $this, 'add_cap_post_type_manage' ) );
			add_action( 'save_post', array( $this, 'save_cf_value' ) );
			add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
			// after_setup_theme では 6.0 頃から翻訳があたらなくなる .
			// init でも 0 などなど早めのpriority 指定しないと投稿タイプに連動するウィジェットエリアが動作しない .
			add_action( 'init', array( $this, 'add_post_type' ), 0 );

			// AJAX ハンドラーを追加
			add_action( 'wp_ajax_check_taxonomy_shared', array( __CLASS__, 'ajax_check_taxonomy_shared' ) );
		}
	} // class VK_Post_Type_Manager

	$VK_Post_Type_Manager = new VK_Post_Type_Manager(); // phpcs:ignore

	add_action( 'admin_notices', array( 'VK_Post_Type_Manager', 'display_help_notice' ), 20 );
}
