<?php

/*
  Archive list widget
/*-------------------------------------------*/
class WP_Widget_VK_archive_list extends WP_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'WP_Widget_VK_archive_list',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		return veu_get_prefix() . __( 'archive list', 'vk-all-in-one-expansion-unit' );
	}

	public static function veu_widget_description() {
		return __( 'Displays a list of archives. You can choose the post type and also to display archives by month or by year.', 'vk-all-in-one-expansion-unit' );
	}

	/**
	 * ウィジェットの表示画面
	 */
	public function widget( $args, $instance ) {

		$defaults = self::get_option_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );

		$arg = array(
			'echo' => 1,
		);

		// 投稿タイプ.
		$arg['post_type'] = ( isset( $instance['post_type'] ) ) ? $instance['post_type'] : 'post';

		// 表示タイプ
		if ( isset( $instance['display_type'] ) && $instance['display_type'] == 'y' ) {
			$arg['type']      = 'yearly';
			if ( strtoupper( get_locale() ) == 'JA' ) {
				$arg['after'] = '年';
			}
		} else {
			$arg['type']      = 'monthly';
		}

		// 表示デザイン
		if ( isset( $instance['display_design'] ) && 'select' === $instance['display_design'] ) {
			$arg['format'] = 'option';
		} else {
			$arg['format'] = 'html';
		}

		$get_posts = get_posts(
			array(
				'post_type' => $arg['post_type']
			)
		);
		if ( ! empty( $get_posts ) ) {
		?>
			<?php echo $args['before_widget']; ?>
			<div class="sideWidget widget_archive">
				<?php
				if ( ! empty( $instance['label'] ) ) {
					echo $args['before_title'];
					echo wp_kses_post( $instance['label'] );
					echo $args['after_title'];
				}
				?>
				<?php if ( 'html' === $arg['format'] ) : ?>
					<ul class="localNavi">
						<?php wp_get_archives( $arg ); ?>
					</ul>
				<?php else : ?>
					<select class="localNavi" name="archive-dropdown" onChange='document.location.href=this.options[this.selectedIndex].value;'>
						<option value="" <?php selected( $instance['display_type'], '', true ); ?>><?php _e( 'Please select', 'vk-all-in-one-expansion-unit' ); ?></option>
						<?php wp_get_archives( $arg ); ?>
					</select>
				<?php endif; ?>

			</div>
			<?php echo $args['after_widget']; ?>
			<?php
		}
	}

	/**
	 * 
	 */
	public function get_option_defaults() {
		$defaults = array(
			'post_type'      => 'post',
			'display_type'   => 'm',
			'label'          => __( 'Monthly archives', 'vk-all-in-one-expansion-unit' ),
			'display_design' => 'list',
		);
		return $defaults;
	}

	/**
	 * ウィジェットの設定画面
	 */
	public function form( $instance ) {
		// インスタンスを初期化＆調整.
		$defaults = self::get_option_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );

		// 投稿タイプをオブジェクトで取得.
		$the_post_types    = get_post_types(
			array(
				'public'   => true,
				'show_ui'  => true,
				'_builtin' => false,
			),
			'objects',
			'and'
		);

		// 選択肢用配列の定義＆「投稿」を挿入.
		$select_post_types = array(
			array(
				'label' => get_post_type_object( 'post' )->labels->singular_name,
				'value' => get_post_type_object( 'post' )->name,
			),
		);
		// 選択肢用配列に各カスタム投稿タイプを追加.
		foreach ( $the_post_types as $the_post_type ) {
			$get_posts = get_posts(
				array(
					'post_type' => $the_post_type->name,
				)
			);
			if ( ! empty( $get_posts ) ) {
				$select_post_types[] = array(
					'label' => $the_post_type->labels->singular_name,
					'value' => $the_post_type->name,
				);
			}
		}
		?>
		<div>

			<!-- タイトル -->
			<div style="margin-top:15px;">
				<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Label to display', 'vk-all-in-one-expansion-unit' ); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo esc_attr( $instance['label'] ); ?>"  class="admin-custom-input">
			</div>

			<!-- 投稿タイプ -->
			<div>
				<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'vk-all-in-one-expansion-unit' ); ?>:</label>
				<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" class="admin-custom-input">
					<?php foreach ( $select_post_types as $select_post_type ) : ?>
						<option value="<?php echo $select_post_type['value']; ?>" <?php selected( $select_post_type['value'], $instance['post_type'] , true  ); ?>>
							<?php echo $select_post_type['label']; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Archiveタイプ -->
			<div>
				<label for="<?php echo $this->get_field_id( 'display_type' ); ?>">
				<?php _e( 'Archive type', 'vk-all-in-one-expansion-unit' ); ?>
				</label>
				<select name="<?php echo $this->get_field_name( 'display_type' ); ?>" class="admin-custom-input">
					<option value="m" <?php selected( $instance['display_type'], 'm', true ); ?>><?php _e( 'Monthly', 'vk-all-in-one-expansion-unit' ); ?></option>
					<option value="y" <?php selected( $instance['display_type'], 'y', true ); ?>><?php _e( 'Yearly', 'vk-all-in-one-expansion-unit' ); ?></option>
				</select>
			</div>

			<!-- Displayデザイン -->
			<div style="margin-bottom:2em;">
				<label for="<?php echo $this->get_field_id( 'display_design' ); ?>">
				<?php _e( 'Display design', 'vk-all-in-one-expansion-unit' ); ?>
				</label>
				<select name="<?php echo $this->get_field_name( 'display_design' ); ?>" class="admin-custom-input">
					<option value="list" <?php selected( $instance['display_design'],'list',true ); ?>><?php _e( 'Lists', 'vk-all-in-one-expansion-unit' ); ?></option>
					<option value="select" <?php selected( $instance['display_design'],'select',true ); ?>><?php _e( 'Drop down', 'vk-all-in-one-expansion-unit' ); ?></option>
				</select>
			</div>

		</div>
		<?php
	}


	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['label'] = $new_instance['label'];
		$instance['post_type']    = $new_instance['post_type'];
		$instance['display_type'] = $new_instance['display_type'];
		$instance['display_design'] = $new_instance['display_design'];
		return $instance;
	}
}
