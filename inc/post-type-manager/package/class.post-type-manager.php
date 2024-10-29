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
			echo '<script>
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
			echo esc_html__( 'However, it refers to the "category" itself, not to the “item” of the category.', 'vk-all-in-one-expansion-unit' ) . '<br />';
			echo esc_html__( 'For example, if you create a post type "construction result", Custom taxonomy will be "construction type", "construction area", etc.', 'vk-all-in-one-expansion-unit' );
			echo '</p>';

			echo '<table class="table table-border">';

			// カスタム分類の情報は カスタムフィールドの veu_taxonomy に連想配列で格納している.
			$taxonomy = get_post_meta( $post->ID, 'veu_taxonomy', true );

			for ( $i = 1; $i <= apply_filters( 'veu_post_type_taxonomies', 5 ); $i++ ) {

				$slug     = ( isset( $taxonomy[ $i ]['slug'] ) ) ? $taxonomy[ $i ]['slug'] : '';
				$label    = ( isset( $taxonomy[ $i ]['label'] ) ) ? $taxonomy[ $i ]['label'] : '';
				$tag      = ( isset( $taxonomy[ $i ]['tag'] ) ) ? $taxonomy[ $i ]['tag'] : '';
				$rest_api = ( isset( $taxonomy[ $i ]['rest_api'] ) ) ? $taxonomy[ $i ]['rest_api'] : '';

				echo '<tr>';

				echo '<th rowspan="4">' . esc_attr( $i ) . '</th>';

				// slug.
				echo '<td>' . esc_html__( 'Custon taxonomy name (slug)', 'vk-all-in-one-expansion-unit' ) . '</td>';
				echo '<td><input type="text" id="veu_taxonomy[' . esc_attr( $i ) . '][slug]" name="veu_taxonomy[' . esc_attr( $i ) . '][slug]" value="' . esc_attr( $slug ) . '" size="20">';
				echo '<div>' . esc_html__( '* Please enter a string consisting of half-width lowercase alphanumeric characters, half-width hyphens, and half-width underscores.', 'vk-all-in-one-expansion-unit' ) . '</div>';
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

				// RERT API.
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
				echo '<label><input type="radio" id="veu_taxonomy[' . esc_attr( $i ) . '][rest_api]" name="veu_taxonomy[' . esc_attr( $i ) . '][rest_api]" value="false"' . checked( $checked, 'false', false ) . '> ' . esc_html__( 'Does not correspond to the block editor', 'vk-all-in-one-expansion-unit' ) . '</label>';
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';
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
			
			$post_type_id            = ! empty( $_POST['veu_post_type_id'] )            ? esc_html( strip_tags( $_POST['veu_post_type_id'] ) )  : '';
			$post_type_items         = ! empty( $_POST['veu_post_type_items'] )         ? $_POST['veu_post_type_items']                         : '';
			$menu_posttion           = ! empty( $_POST['veu_menu_position'] )           ? esc_html( strip_tags( $_POST['veu_menu_position'] ) ) : '';
			$menu_icon               = ! empty( $_POST['veu_menu_icon'] )               ? esc_html( strip_tags( $_POST['veu_menu_icon'] ) )     : '';
			$post_type_export_to_api = ! empty( $_POST['veu_post_type_export_to_api'] ) ? esc_html( $_POST['veu_post_type_export_to_api'] )     : '';
			$post_type_rewrite       = ! empty( $_POST['veu_post_type_rewrite'] )       ? esc_html( $_POST['veu_post_type_rewrite'] )           : '';
			
			if ( ! empty ( $_POST['veu_taxonomy'] ) ) {
				$taxonomy      = $_POST['veu_taxonomy'];

				for ( $i = 1; $i <= apply_filters( 'veu_post_type_taxonomies', 5 ); $i++ ) {
					$taxonomy[$i]['slug']     = ! empty( $taxonomy[$i]['slug'] )     ? esc_html( strip_tags( $taxonomy[$i]['slug'] ) )  : '';
					$taxonomy[$i]['label']    = ! empty( $taxonomy[$i]['label'] )    ? esc_html( strip_tags( $taxonomy[$i]['label'] ) ) : '';
					$taxonomy[$i]['tag']      = ! empty( $taxonomy[$i]['tag'] )      ? esc_html( $taxonomy[$i]['tag'] )                 : '';
					$taxonomy[$i]['rest_api'] = ! empty( $taxonomy[$i]['rest_api'] ) ? esc_html( $taxonomy[$i]['rest_api'] )            : '';						
				}
			}

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
					delete_post_meta(  $post_id, $field_name );
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

						$menu_position = intval( mb_convert_kana( get_post_meta( $post->ID, 'veu_menu_position', true ), 'n' ) );
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

						// カスタム投稿タイプを発行.
						register_post_type( $post_type_id, $args );

						// パーマリンク設定を更新するかどうか.
						if ( empty( $flush_flag ) ) {
							flush_rewrite_rules();
							update_post_meta( $post->ID, 'veu_post_type_flush_rewrite_rules', 'true' );
						}

						/*******************************************
						 * カスタム分類を追加
						 */

						// カスタムフィールドに連想配列で格納しておいたカスタム分類の情報を取得.
						$veu_taxonomies = get_post_meta( $post->ID, 'veu_taxonomy', true );

						foreach ( $veu_taxonomies as $key => $taxonomy ) {

							if ( $taxonomy['slug'] && $taxonomy['label'] ) {

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
									'hierarchical'      => $hierarchical_true,
									'update_count_callback' => '_update_post_term_count',
									'labels'            => $labels,
									'public'            => true,
									'show_ui'           => true,
									'show_in_rest'      => $rest_api_true,
									'show_admin_column' => true,
									'rewrite'           => $rewrite,
								);

								if ( $rest_api_true ) {
									$args['rest_base'] = $taxonomy['slug'];
								}

								register_taxonomy(
									$taxonomy['slug'],
									$post_type_id,
									$args
								);
							} // if ( $taxonomy['slug'] && $taxonomy['label']){

						} // foreach ($veu_taxonomies as $key => $taxonomy) {

					} // if ( $post_type_id ) {

				} // foreach ($custom_post_types as $key => $post) {

			} // if ( $custom_post_types ) {
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
		}
	} // class VK_Post_Type_Manager

	$VK_Post_Type_Manager = new VK_Post_Type_Manager(); // phpcs:ignore

}
