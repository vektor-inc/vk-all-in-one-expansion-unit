<?php

// namespace Vektor\ExUnit\Package\Cta;
if ( ! class_exists( 'Vk_Call_To_Action' ) ) {

	class Vk_Call_To_Action {

		const POST_TYPE = 'cta';

		const CONTENT_NUMBER = 100;

		public static function init() {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
			add_action( 'veu_package_init', array( __CLASS__, 'option_init' ) );
			add_action( 'init', array( __CLASS__, 'set_posttype' ) );
			add_action( 'admin_menu', array( __CLASS__, 'add_metabox_cta_register' ) );
			add_action( 'save_post', array( __CLASS__, 'save_custom_field' ) );
			add_action( 'widgets_init', array( __CLASS__, 'widget_init' ) );

			$options = get_option( 'vkExUnit_cta_settings' );
			if ( ! empty( $options['hook_point'] ) ) {
				add_action( $options['hook_point'], array( __CLASS__, 'display_cta_to_hook' ), self::CONTENT_NUMBER, 1 );
			} else {
				add_filter( 'the_content', array( __CLASS__, 'content_filter' ), self::CONTENT_NUMBER, 1 );
			}

			require_once dirname( __FILE__ ) . '/widget-call-to-action.php';
			require_once dirname( __FILE__ ) . '/block/index.php';

			/*
			VEU_Metabox 内の get_post_type が実行タイミングによっては
			カスタム投稿タイプマネージャーで作成した投稿タイプが取得できないために
			admin_menu のタイミングで読み込んでいる
			 */
			add_action(
				'admin_menu',
				function() {
					require_once dirname( __FILE__ ) . '/class-veu-metabox-cta.php';
				}
			);
		}

		/**
		 * option_init
		 * set_posttype
		 * add_metabox_cta_register
		 * save_custom_field
		 * widget_init
		 * setting_page_url
		 * render_meta_box_cta
		 * get_cta_post
		 * render_cta_content
		 * cta_id_random
		 * is_cta_id
		 * content_filter
		 * display_cta_to_hook
		 * is_pagewidget
		 * is_contentsarea_posts_widget
		 * sanitize_config
		 * get_default_option
		 * get_option
		 * get_ctas
		 * render_configPage
		 */

		public static function option_init() {
			vkExUnit_register_setting(
				'Call To Action',                       // tab label.
				'vkExUnit_cta_settings',                // name attr
				array( __CLASS__, 'sanitize_config' ),      // sanitaise function name
				array( __CLASS__, 'render_configPage' )     // setting_page function name
			);
		}

		public static function enqueue_scripts() {
			wp_enqueue_style(
				'veu-cta',
				plugin_dir_url( __FILE__ ) . 'assets/css/style.css',
				array(),
				VEU_VERSION
			);
		}

		/**
		 * Set CTA Post Type
		 *
		 * @return void
		 */
		public static function set_posttype() {

			$labels = array(
				'name'          => 'CTA',
				'singular_name' => 'CTA',
				'edit_item'     => __( 'Edit CTA', 'vk-all-in-one-expansion-unit' ),
				'add_new_item'  => __( 'Add new CTA', 'vk-all-in-one-expansion-unit' ),
				'new_item'      => __( 'New CTA', 'vk-all-in-one-expansion-unit' ),
			);

			$args = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'has_archive'        => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'menu_position'      => 5,
				'query_var'          => true,
				'rewrite'            => true,
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'taxonomies'         => array(),
				'supports'           => array( 'title', 'editor' ),
				'show_in_rest'       => true,
			);
			register_post_type( self::POST_TYPE, $args );
		}

		/**
		 * Add CTA Metabox
		 *
		 * @return void
		 */
		public static function add_metabox_cta_register() {

			// Meta box of CTA edit and register page.
			add_meta_box( 'vkExUnit_cta_url', __( 'CTA Contents', 'vk-all-in-one-expansion-unit' ), array( __CLASS__, 'render_meta_box_cta' ), self::POST_TYPE, 'normal', 'high' );
		}


		/**
		 * [save_custom_field description]
		 *
		 * @param  [type] $post_id [description]
		 * @return [type]          [description]
		 */
		public static function save_custom_field( $post_id ) {
			if ( ! isset( $_POST['_vkExUnit_cta_switch'] ) ) {
				return $post_id; }
			$noonce = isset( $_POST['_nonce_vkExUnit_custom_cta'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit_custom_cta'] ) : null;

			// if autosave is to deny.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id; }

			if ( ! wp_verify_nonce( $noonce, plugin_basename( __FILE__ ) ) ) {
				return $post_id;
			}

			if ( 'cta_number' === $_POST['_vkExUnit_cta_switch'] ) {
				$data = $_POST['vkexunit_cta_each_option'];

				if ( get_post_meta( $post_id, 'vkexunit_cta_each_option' ) == '' ) {
					add_post_meta( $post_id, 'vkexunit_cta_each_option', $data, true );
				} elseif ( get_post_meta( $post_id, 'vkexunit_cta_each_option', true ) !== $data ) {
					update_post_meta( $post_id, 'vkexunit_cta_each_option', $data );
				} elseif ( ! $data ) {
					delete_post_meta( $post_id, 'vkexunit_cta_each_option', get_post_meta( $post_id, 'vkexunit_cta_each_option', true ) );
				}
				return $post_id;
			} elseif ( 'cta_content' === $_POST['_vkExUnit_cta_switch'] ) {

				// カスタムフィールドの設定.
				$custom_fields = array(
					'vkExUnit_cta_use_type'           => array(
						'escape_type' => '',
					),
					'vkExUnit_cta_img'                => array(
						'escape_type' => 'esc_url',
					),
					'vkExUnit_cta_img_position'       => array(
						'escape_type' => '',
					),
					'vkExUnit_cta_button_text'        => array(
						'escape_type' => array( 'stripslashes', 'wp_kses_post' ),
					),
					'vkExUnit_cta_button_icon'        => array(
						'escape_type' => 'wp_kses_post',
					),
					'vkExUnit_cta_button_icon_before' => array(
						'escape_type' => 'wp_kses_post',
					),
					'vkExUnit_cta_button_icon_after'  => array(
						'escape_type' => 'wp_kses_post',
					),
					'vkExUnit_cta_url'                => array(
						'escape_type' => 'esc_url',
					),
					'vkExUnit_cta_url_blank'          => array(
						'escape_type' => '',
					),
					'vkExUnit_cta_text'               => array(
						'escape_type' => array( 'stripslashes', 'wp_kses_post' ),
					),
				);

				// カスタムフィールドの保存.
				foreach ( $custom_fields as $custom_field_name => $custom_field_options ) {

					if ( isset( $_POST[ $custom_field_name ] ) ) {
						if ( ! empty( $custom_field_name['escape_type'] ) ) {
							if ( is_array( $custom_field_name['escape_type'] ) ) {
								// エスケープ処理が複数ある場合
								$data =  $_POST[ $custom_field_name ];
								foreach ( $custom_field_name['escape_type'] as $escape ) {
									$data = call_user_func( $escape, $data );
								}
							} else {
								// エスケープ処理が一つの場合
								$data = call_user_func( $custom_field_name['escape_type'], $_POST[ $custom_field_name ] );
							}
						} else {
							$data = $_POST[ $custom_field_name ];
						}
					}

					if ( get_post_meta( $post_id, $custom_field_name ) == '' ) {
						// データが今までなかったらカスタムフィールドに新規保存
						add_post_meta( $post_id, $custom_field_name, $data, true );
					} elseif ( $data != get_post_meta( $post_id, $custom_field_name, true ) ) {
						// 保存されてたデータと送信されてきたデータが違ったら更新
						update_post_meta( $post_id, $custom_field_name, $data );
					} elseif ( ! $data ) {
						// データが送信されてこなかった（空のデータが送られてきた）らフィールドの値を削除
						delete_post_meta( $post_id, $custom_field_name, get_post_meta( $post_id, $custom_field_name, true ) );
					}
				} // foreach ( $custom_fields as $key => $custom_field_name ) {

				return $post_id;
			}
		}

		/**
		 * [widget_init description]
		 *
		 * @return [type] [description]
		 */
		public static function widget_init() {
			return register_widget( 'Widget_CTA' );
		}

		/**
		 * CTAメイン設定画面のurl
		 * ExUnitと単体プラグインなどによって変動する
		 *
		 * @return [type] [description]
		 */
		public static function setting_page_url() {
			if ( veu_is_cta_active() ) {
				$setting_page_url = admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_cta_settings' );
			} else {
				$setting_page_url = admin_url( 'options-general.php?page=vk_cta_options' );
			}
			return $setting_page_url;
		}

		public static function render_meta_box_cta() {

			echo '<input type="hidden" name="_nonce_vkExUnit_custom_cta" id="_nonce_vkExUnit__custom_field_metaKeyword" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
			$imgid          = get_post_meta( get_the_id(), 'vkExUnit_cta_img', true );
			$cta_image      = wp_get_attachment_image_src( $imgid, 'large' );
			$image_position = get_post_meta( get_the_id(), 'vkExUnit_cta_img_position', true );
			?>
	<style>
	#message.updated a {display:none;}
	#thumbnail_box { max-width:300px; max-height:300px; }
	#cta-thumbnail_image { max-width:300px; max-height:300px; }
	#cta-thumbnail_image.noimage { display:none; }
	#cta-thumbnail_control.add #media_thumb_url_add { display:inline; }
	#cta-thumbnail_control.add #media_thumb_url_change,
	#cta-thumbnail_control.add #media_thumb_url_remove { display:none; }
	#cta-thumbnail_control.change #media_thumb_url_add { display:none; }
	#cta-thumbnail_control.change #media_thumb_url_change,
	#cta-thumbnail_control.change #media_thumb_url_remove { display:inline; }
	.form-table input[type=text],
	.form-table input[type=url],
	.form-table textarea { width:80%; }
	</style>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		var custom_uploader;
		jQuery('.cta-media_btn').click(function(e) {
			e.preventDefault();

			if (custom_uploader) {
			custom_uploader.open();
			return;
			}
			custom_uploader = wp.media({
			title: 'Choose Image',
			library: {type: 'image'},
			button: {text: 'Choose Image'},
			multiple: false,
			});

			custom_uploader.on('select', function() {
			var images = custom_uploader.state().get('selection');
			images.each(function(file){
				jQuery('#cta-thumbnail_image').attr('src', file.toJSON().url).removeClass("noimage");
				jQuery('.vkExUnit_cta_img').val(file.toJSON().id);
				jQuery('#cta-thumbnail_control').removeClass("add").addClass("change");
			});
			});
			custom_uploader.open();
		});
		jQuery('#cta-thumbnail_control #media_thumb_url_remove').on('click', function(){
			jQuery('#cta-thumbnail_image').attr('src', '').addClass("noimage");
			jQuery('.vkExUnit_cta_img').val('');
			jQuery('#cta-thumbnail_control').removeClass("change").addClass("add");
			return false;
		});
	});
	</script>
	<input type="hidden" name="_vkExUnit_cta_switch" value="cta_content" />
	<p><?php _e( 'You can create it with a free layout in the content field using, such as Outer block and PR Content block in VK Blocks.', 'vk-all-in-one-expansion-unit' ); ?><br>
			<?php _e( 'If the contents field is entered, the contents of the body will be displayed with priority, so the following contents will be ignored.', 'vk-all-in-one-expansion-unit' ); ?><br>
	* <?php _e( 'The entered contents are displayed directly. You can not use Dynamic blocks, reuse blocks, etc.', 'vk-all-in-one-expansion-unit' ); ?>
	</p>
	<table class="form-table">

	<tr>
	<th>
	<label for="vkExUnit_cta_use_type"><?php _e( 'Use Classic layout', 'vk-all-in-one-expansion-unit' ); ?></label>
	</th>
	<td>
			<?php
			$target_blank = get_post_meta( get_the_id(), 'vkExUnit_cta_use_type', true );
			if ( 'veu_cta_normal' === $target_blank ) {
				$checked = ' checked';
			} else {
				$checked = '';
			}
			?>
	<input type="checkbox" id="vkExUnit_cta_use_type" name="vkExUnit_cta_use_type" value="veu_cta_normal"<?php echo esc_attr( $checked ); ?> />
	<label for="vkExUnit_cta_use_type"><?php _e( 'Use following data (Do not use content data)', 'vk-all-in-one-expansion-unit' ); ?></label>
	</td>
	</tr>

	<tr>
	<th><?php esc_html_e( 'CTA image', 'vk-all-in-one-expansion-unit' ); ?></th>
	<td>
		<div id="cta-thumbnail_box" >
		<img id="cta-thumbnail_image" src="<?php echo ( $cta_image ) ? $cta_image[0] : ''; ?>" class="<?php echo ( $cta_image ) ? '' : 'noimage'; ?>" />
		</div>
		<div id="cta-thumbnail_control" class="<?php echo ( $cta_image ) ? 'change' : 'add'; ?>">
		<button id="media_thumb_url_add" class="cta-media_btn button button-default"><?php _e( 'Add image', 'vk-all-in-one-expansion-unit' ); ?></button>
		<button id="media_thumb_url_change" class="cta-media_btn button button-default"><?php _e( 'Change image', 'vk-all-in-one-expansion-unit' ); ?></button>
		<button id="media_thumb_url_remove" class="button button-default"><?php _e( 'Remove image', 'vk-all-in-one-expansion-unit' ); ?></button>
		</div>
		<input type="hidden" name="vkExUnit_cta_img" class="vkExUnit_cta_img" value="<?php echo esc_attr( $imgid ); ?>" />
	</td>
	</tr>
	<tr><th><label for="vkExUnit_cta_img_position"><?php _e( 'CTA image position', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
		<select name="vkExUnit_cta_img_position" id="vkExUnit_cta_img_position">
		<option value="right" <?php echo ( $image_position == 'right' ) ? 'selected' : ''; ?> ><?php _e( 'right', 'vk-all-in-one-expansion-unit' ); ?></option>
		<option value="center" <?php echo ( $image_position == 'center' ) ? 'selected' : ''; ?> ><?php _e( 'center', 'vk-all-in-one-expansion-unit' ); ?></option>
		<option value="left" <?php echo ( $image_position == 'left' ) ? 'selected' : ''; ?> ><?php _e( 'left', 'vk-all-in-one-expansion-unit' ); ?></option>
		</select>
	</td></tr>
	<tr><th>
	<label for="vkExUnit_cta_button_text"><?php _e( 'Button text', 'vk-all-in-one-expansion-unit' ); ?></label></th><td>
	<input type="text" name="vkExUnit_cta_button_text" id="vkExUnit_cta_button_text" value="<?php echo esc_html( get_post_meta( get_the_id(), 'vkExUnit_cta_button_text', true ) ); ?>" />
	</td></tr>
	<tr><th>
	<label for="vkExUnit_cta_button_icon"><?php _e( 'Button icon', 'vk-all-in-one-expansion-unit' ); ?></label></th>
	<td>
	<p><?php _e( 'To choose your favorite icon, and enter the class.', 'vk-all-in-one-expansion-unit' ); ?></p>
	<div class="vkExUnit_cta_button_icon_inputset">
		<dl>
		<dt><label for="icon_before"><?php _e( 'Before :', 'vk-all-in-one-expansion-unit' ); ?></label></dt>
		<dd><input type="text" name="vkExUnit_cta_button_icon_before"  id="vkExUnit_cta_button_icon_before" value="<?php echo esc_attr( get_post_meta( get_the_id(), 'vkExUnit_cta_button_icon_before', true ) ); ?>" /></dd>
		</dl>
		<dl>
		<dt><label for="icon_after"><?php _e( 'After :', 'vk-all-in-one-expansion-unit' ); ?></label></dt>
		<dd><input type="text" name="vkExUnit_cta_button_icon_after"  id="vkExUnit_cta_button_icon_after" value="<?php echo esc_attr( get_post_meta( get_the_id(), 'vkExUnit_cta_button_icon_after', true ) ); ?>" /></dd>
		</dl>
	</div>

			<?php
			if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
				echo Vk_Font_Awesome_Versions::ex_and_link();
			}
			?>

	</p>
	</td></tr>
	<tr><th>
	<label for="vkExUnit_cta_url"><?php _e( 'Button link url', 'vk-all-in-one-expansion-unit' ); ?></label></th><td>
	<input type="url" name="vkExUnit_cta_url" id="vkExUnit_cta_url" placeholder="https://" value="<?php echo esc_url( get_post_meta( get_the_id(), 'vkExUnit_cta_url', true ) ); ?>" />
	</td></tr>
	<tr><th>

			<?php
			$target_blank = get_post_meta( get_the_id(), 'vkExUnit_cta_url_blank', true );
			if ( $target_blank == 'window_self' ) {
				$checked = ' checked';
			} else {
				$checked = '';
			}
			?>
	<label for="vkExUnit_cta_url_blank"><?php _e( 'Target window', 'vk-all-in-one-expansion-unit' ); ?></label></th><td>
<input type="checkbox" id="vkExUnit_cta_url_blank" name="vkExUnit_cta_url_blank" value="window_self"<?php echo $checked; ?> />
<label for="vkExUnit_cta_url_blank"><?php _e( 'Open in a self window', 'vk-all-in-one-expansion-unit' ); ?></label>
</td></tr>
<tr><th><label for="vkExUnit_cta_text"><?php _e( 'Text message', 'vk-all-in-one-expansion-unit' ); ?>
</th>
<td>
<textarea name="vkExUnit_cta_text" id="vkExUnit_cta_text" rows="10em" cols="50em"><?php echo wp_kses_post( get_post_meta( get_the_id(), 'vkExUnit_cta_text', true ) ); ?></textarea>
</td></tr>
</table>
<a href="<?php echo admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_cta_settings' ); ?>" class="button button-default" target="_blank"><?php _e( 'CTA setting', 'vk-all-in-one-expansion-unit' ); ?></a>
			<?php
		}

		/**
		 * Get CTA Post
		 *
		 * @param int $id post_id of CTA.
		 * @return object CTA $post object.
		 */
		public static function get_cta_post( $id ) {
			$args  = array(
				'post_type'   => self::POST_TYPE,
				'p'           => $id,
				'post_count'  => 1,
				'post_status' => array( 'publish' ),
			);
			$query = new \WP_Query( $args );
			if ( ! $query->post_count ) {
				return null; }

			$target = $query->posts[0];
			wp_reset_postdata();
			return $target;
		}

		/**
		 * CTAとして返す内容の処理
		 *
		 * @param  [type] $id CTA Post ID
		 * @return [type]     [description]
		 */
		public static function render_cta_content( $id ) {

			global $post;

			// 各記事で非表示指定されてたら表示しない.
			if ( is_singular() ) {
				$post_config = get_post_meta( $post->ID, 'vkexunit_cta_each_option', true );
				if ( 'disable' === $post_config ) {
					return;
				}
			}

			if ( ! $id ) {
				return '';
			}
			$cta_post = self::get_cta_post( $id );

			// たぶん何か必ず $cta_post にはデータが返ってくるので事実上不要.
			if ( ! $cta_post ) {
				return ''; }

			// 本文に入力がある場合は本文を表示.
			$cta_content = $cta_post->post_content;
			if ( $cta_content && 'veu_cta_normal' !== $cta_post->vkExUnit_cta_use_type ) {
				$content = $cta_content;
			} else {
				// 旧 CTA レイアウト.
				include dirname( __FILE__ ) . '/view-actionbox.php';
			}

			// Display Edit Button.
			$url = get_edit_post_link( $cta_post->ID );
			if ( $url ) {
				$content .= '<div class="veu_adminEdit"><a href="' . $url . '" class="btn btn-default" target="_blank">' . __( 'Edit CTA', 'vk-all-in-one-expansion-unit' ) . '</a></div>';
			}

			// リセットしないと$postが改変されたままでコメント欄が表示されなくなるなどの弊害が発生する.
			wp_reset_postdata();

			// wp_kses_post でエスケープすると outerブロックが出力するstyle属性を無効化されるので使わないように.
			// 出力時にエスケープしたいが、wp_kses_post だと style属性が無効化される / wp_kses でも allow_html で opacity を許可しても無視・削除される。
			// 結局本文欄はどのみち HTML ブロックで <script>alert(0)</script> 入れられ標準でXSSは実行可能なので、ここでは処理していない。
			return do_blocks( do_shortcode(  $content ) );
		}

		/**
		 * CTAの投稿IDをランダムに取得する
		 *
		 * @return int|bool cta_id or false
		 */
		public static function cta_id_random() {
			$return = null;
			// ランダムに抽出したCTAの投稿IDを返す
			// CTAの投稿をランダムで１件取得
			$args     = array(
				'post_type'      => self::POST_TYPE, // 投稿タイプを指定
				'posts_per_page' => 1, // １ページでの表示件数を指定
				'orderby'        => 'rand', // 表示順をランダムで取得
			);
			$cta_post = get_posts( $args );
			if ( $cta_post && isset( $cta_post[0] ) ) {
				$return = $cta_post[0]->ID;
			}
			return $return;
		}

		public static function is_cta_id( $id = null ) {

			// 表示する投稿のIDを取得
			if ( ! $id ) {
				$id = get_the_id(); }
			// ?
			if ( ! $id ) {
				return null; }

			// 各投稿編集画面で プルダウンで指定されている 表示するCTAの投稿ID（もしくは共通設定や非表示）
			$post_config = get_post_meta( $id, 'vkexunit_cta_each_option', true );

			// 「共通設定を使用」じゃなかった場合
			if ( $post_config ) {

				// 「表示しない」が選択されていたら $id には nullを返す（　CTAは表示されない ）
				if ( 'disable' === $post_config ) {
					return null; }

				// 「表示しない」が選択されていたら $id には nullを返す（　CTAは表示されない ）
				if ( 'random' === $post_config ) {
					return self::cta_id_random();
				}
				return $post_config;
			}

			//
			// 共通設定を使用の場合
			//
			// 今表示している記事の投稿タイプを取得
			$post_type = get_post_type( $id );
			// 投稿タイプ別にどのCTAを共通設定として表示するかの情報を取得
			$option = self::get_option();

			// 今表示している記事の投稿タイプのとき どのCTAを表示するかの設定が
			// 定義されており なおかつ 数字で入っている場合
			if (
			isset( $option[ $post_type ] ) &&
			is_numeric( $option[ $post_type ] )
			) {
				// その数字（表示するCTAの投稿ID）を返す
				return $option[ $post_type ];
			} else {
				return self::cta_id_random();
			}
			return null;
		}

		public static function content_filter( $content ) {

			// 固定ページウィジェットの場合
			if ( self::is_pagewidget() ) {
				return $content; }
			// Ligthning Advanced Unit のウィジェットだと...思う...
			if ( self::is_contentsarea_posts_widget() ) {
				return $content; }
			// 抜粋の場合
			if ( vkExUnit_is_excerpt() ) {
				return $content;
			}

			// 上記以外の場合に出力
			$content .= self::render_cta_content( self::is_cta_id() );
			return $content;
		}

		public static function display_cta_to_hook() {
			echo self::render_cta_content( self::is_cta_id() );
		}


		public static function is_pagewidget() {
			global $is_pagewidget;
			return ( $is_pagewidget ) ? true : false;
		}


		public static function is_contentsarea_posts_widget() {
			global $is_contentsarea_posts_widget;
			return ( $is_contentsarea_posts_widget ) ? true : false;
		}


		public static function sanitize_config( $input ) {
			$posttypes = array_merge(
				array(
					'post' => 'post',
					'page' => 'page',
				),
				get_post_types(
					array(
						'public'   => true,
						'_builtin' => false,
					),
					'names'
				)
			);
			$option    = get_option( 'vkExUnit_cta_settings' );
			if ( ! $option ) {
				$current_option = self::get_default_option();
			}
			if ( is_array( $input ) ) {
				foreach ( $input as $key => $value ) {
					if ( $value == 'random' ) {
						$option[ $key ] = 'random';
					} else {
						if ( 'hook_point' === $key ) {
							$option[ $key ] = stripslashes( sanitize_text_field( $value ) );
						} else {
							$option[ $key ] = ( is_numeric( $value ) ) ? $value : 0;
						}
					}
				}
			}

			return $option;
		}


		public static function get_default_option() {
			$option    = array();
			$posttypes = array_merge(
				array(
					'post' => 'post',
					'page' => 'page',
				),
				get_post_types(
					array(
						'public'   => true,
						'_builtin' => false,
					),
					'names'
				)
			);
			foreach ( $posttypes  as $key => $posttype ) {
				$option[ $posttype ] = '0';
			}
			return $option;
		}


		public static function get_option( $show_label = false ) {
			$default = self::get_default_option();
			$option  = get_option( 'vkExUnit_cta_settings' );

			// ↓ これであかんの？
			// $output_option = wp_parse_args( $option, $default );
			if ( ! $option || ! is_array( $option ) ) {
				return $default; 
			}

			$posttypes = array_merge(
				array(
					'post' => 'post',
					'page' => 'page',
				),
				get_post_types(
					array(
						'public'   => true,
						'_builtin' => false,
					),
					'names'
				)
			);

			$output_option = array();
			foreach ( $posttypes  as $key => $posttype ) {
				$output_option[ $posttype ] = ( isset( $option[ $posttype ] ) ) ? $option[ $posttype ] : $default[ $posttype ];
			}

			return $output_option;
		}


		public static function get_ctas( $show_label = false, $head = '' ) {
			$args  = array(
				'post_type'  => self::POST_TYPE,
				'nopaging'   => true,
				'post_count' => -1,
			);
			$query = new \WP_Query( $args );
			$ctas  = array();
			foreach ( $query->posts  as $key => $post ) {
				if ( $show_label ) {
					$ctas[] = array(
						'key'   => $post->ID,
						'label' => $head . $post->post_title,
					);
				} else {
					$ctas[] = $post->ID;
				}
			}
			wp_reset_postdata();
			return $ctas;
		}


		public static function render_configPage() {

			$options = self::get_option();
			$ctas    = self::get_ctas( true, '  - ' );

			// ランダムを先頭に追加.
			array_unshift(
				$ctas,
				array(
					'key'   => 'random',
					'label' => __( 'Random', 'vk-all-in-one-expansion-unit' ),
				)
			);
			// 表示しないを先頭に追加.
			array_unshift(
				$ctas,
				array(
					'key'   => 0,
					'label' => __( 'Disable display', 'vk-all-in-one-expansion-unit' ),
				)
			);

			include dirname( __FILE__ ) . '/view-adminsetting.php';
		}
	}

	Vk_Call_To_Action::init();

} // if ( ! class_exists( 'Vk_Call_To_Action' ) )
