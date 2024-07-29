<?php

/*-------------------------------------------*/
/*  page widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_widget_page extends WP_Widget {

	function __construct() {
		parent::__construct(
			'pudge',
			self::veu_widget_name(),
			array( 'description' => self::veu_widget_description() )
		);
	}

	public static function veu_widget_name() {
		$name = veu_get_prefix() . __( 'Page content to widget', 'vk-all-in-one-expansion-unit' );
		// $name .= ' ( ' . __( 'Not recommended', 'vk-all-in-one-expansion-unit' ) . ' )';
		return $name;
	}

	public static function veu_widget_description() {
		return __( 'Displays a page contents to widget.', 'vk-all-in-one-expansion-unit' );
	}

	/*-------------------------------------------*/
	/*  template-tags
	/*-------------------------------------------*/
	/*
	$input 保存されてる値
	$value 今のinputタグのvalueの値
	*/
	static public function echo_checked( $input, $value ) {
		if ( $input === $value ) {
			echo ' checked';
		}
	}

	static public function is_active_child_page_index( $options ) {
		if (
			! isset( $options['active_childPageIndex'] ) || // 5.7.4 以前を利用で一度も有効化設定を保存していないユーザー
			isset( $options['active_childPageIndex'] ) && $options['active_childPageIndex'] // Activate User
		) {
			return true;
		} else {
			return false;
		}
	}

	static public function is_active_page_list_ancestor( $options ) {
		if (
			! isset( $options['active_pageList_ancestor'] ) || // 5.7.4 以前を利用で一度も有効化設定を保存していないユーザー
			isset( $options['active_pageList_ancestor'] ) && $options['active_pageList_ancestor'] // Activate User
		) {
			return true;
		} else {
			return false;
		}
	}

	/*
	ウィジェットのタイトルに関する情報を出力する関数
	[ 返り値 ]
	$widget_title['display'] : 表示するかどうか // → 5.4.3以降は不要のはず。
	$widget_title['title'] : ウィジェットのタイトルとして表示する文字
	*/
	static public function widget_title( $instance ) {

		$pageid = $instance['page_id'];
		$page   = get_page( $pageid );

		// Set display
		/*-------------------------------------------*/
		// 5.3以前のユーザーで、タイトル表示にチェックしていなかった場合
		if ( $instance['set_title'] == null ) {
			$widget_title['display'] = false;

			// 5.3以前のユーザーで、タイトル表示にチェックがはいっていた場合
		} elseif ( $instance['set_title'] === true ) {
			$widget_title['display'] = true;

		} elseif ( $instance['set_title'] == 'title-hidden' ) {
			$widget_title['display'] = false;

			// ウィジェットのタイトルが選択されている場合は
		} elseif ( $instance['set_title'] == 'title-widget' ) {

			// ウィジェットタイトルが未入力の場合
			if ( empty( $instance['title'] ) ) {
				$widget_title['display'] = false;
			} else {
				$widget_title['display'] = true;
			}

			// 固定ページのタイトルが選択されている場合は
		} elseif ( $instance['set_title'] == 'title-page' ) {
			$widget_title['display'] = true;

		} else {
			$widget_title['display'] = false;
		}

		// Set title
		/*-------------------------------------------*/
		// ウィジェットタイトルを選択していて、タイトル入力欄に入力がある場合
		if ( $instance['set_title'] == 'title-widget' && isset( $instance['title'] ) && $instance['title'] ) {
			$widget_title['title'] = $instance['title'];
			// 旧バージョンで　タイトルを表示になっていた場合に
			// タイトル表示形式フラグに 固定ページのタイトルを表示するvalueにしておく
		} elseif ( ( $instance['set_title'] === true ) || ( $instance['set_title'] == 'title-page' ) ) {
			$widget_title['title'] = $page->post_title;
		} else {
			$widget_title['title'] = null;
		}
		return $widget_title;
	}


	/*-------------------------------------------*/
	/*  form
	/*-------------------------------------------*/
	function form( $instance ) {
		$defaults = array(
			'title'     => '',
			'page_id'   => 2,
			'set_title' => 'title-widget',
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
	  <p>

			<?php //タイトル ?>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			<br />
			<br />

			<?php /* タイトルの表示形式の選択 */ ?>

			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'set_title' ); ?>" value="title-widget" <?php $this->echo_checked( $instance['set_title'], 'title-widget' ); ?> />
				<?php _e( 'Display the entered title', 'vk-all-in-one-expansion-unit' ); ?></label><br/>

			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'set_title' ); ?>" value="title-page" <?php $this->echo_checked( $instance['set_title'], 'title-page' ); ?> />
				<?php _e( 'Display the title of page', 'vk-all-in-one-expansion-unit' ); ?></label><br/>

			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'set_title' ); ?>" value="title-hidden" <?php $this->echo_checked( $instance['set_title'], 'title-hidden' ); ?> />
				<?php _e( 'Do not display titles', 'vk-all-in-one-expansion-unit' ); ?></label><br/>
		<br/>

			<?php
			// 固定ページリスト
			$selected = ( isset( $instance['page_id'] ) ) ? $instance['page_id'] : '';
			$args     = array(
				'name'        => $this->get_field_name( 'page_id' ),
				'selected'    => $selected, // 該当する ID のページを「selected」にし、そのページが選択された状態にする
				'sort_column' => 'menu_order', // 固定ページの順序でソート
				'sort_order'  => 'ASC',
				'post_status' => 'publish,private', // 公開と非公開の記事を取得
			);
			wp_dropdown_pages( $args ); // ページのリストのセレクトボックス (つまり、ドロップダウン) を表示する関数
			?>
	</p>

		<?php $options = veu_get_common_options(); ?>

		<?php if ( $this->is_active_child_page_index( $options ) ) : ?>
		<p>
			<label for="<?php echo $this->get_field_name( 'child_page_index' ); ?>">
				<input type="checkbox" id="<?php echo $this->get_field_name( 'child_page_index' ); ?>" name="<?php echo $this->get_field_name( 'child_page_index' ); ?>"<?php echo ( ! empty( $instance['child_page_index'] ) ) ? ' checked' : ''; ?> />
				<?php _e( 'Display a child page index', 'vk-all-in-one-expansion-unit' ); ?>
			</label>
		</p>
		<?php endif; ?>

		<?php if ( $this->is_active_page_list_ancestor( $options ) ) : ?>
		<p>
			<label for="<?php echo $this->get_field_name( 'page_list_ancestor' ); ?>">
				<input type="checkbox" id="<?php echo $this->get_field_name( 'page_list_ancestor' ); ?>" name="<?php echo $this->get_field_name( 'page_list_ancestor' ); ?>"<?php echo ( ! empty( $instance['page_list_ancestor'] ) ) ? ' checked' : ''; ?> />
				<?php _e( 'Display a page list from ancestor', 'vk-all-in-one-expansion-unit' ); ?>
			</label>
		</p>
		<?php
		endif;

	}

	// 保存・更新する値
	function update( $new_instance, $old_instance ) {
		$instance                       = $old_instance;
		$instance['title']              = wp_kses_post( stripslashes( $new_instance['title'] ) );
		$instance['page_id']            = $new_instance['page_id'];
		$instance['set_title']          = $new_instance['set_title'];
		$instance['child_page_index']   = $new_instance['child_page_index'];
		$instance['page_list_ancestor'] = $new_instance['page_list_ancestor'];
		return $instance;
	}

	/*-------------------------------------------*/
	/*  widget
	/*-------------------------------------------*/
	function widget( $args, $instance ) {
		global $is_pagewidget;
		$is_pagewidget = true;
		if ( isset( $instance['page_id'] ) && $instance['page_id'] ) {
			$this->display_page( $args, $instance );
		}
		$is_pagewidget = false;
	}


	/*-------------------------------------------*/
	/*  display_page
	/*-------------------------------------------*/
	function display_page( $args, $instance ) {
		$pageid = $instance['page_id'];

		// 子ページインデックスや先祖階層リストに投げる
		global $widget_pageid;
		$widget_pageid = $pageid;

		$page = get_page( $pageid );
		
		// ページが存在しない場合は何もしない
		if ( empty( $page ) ) {
			return;
		}
		echo $args['before_widget'];

		$widget_title = $this->widget_title( $instance );

		echo PHP_EOL . '<div id="widget-page-' . $pageid . '" class="widget_pageContent entry-body">' . PHP_EOL;
		if ( $widget_title['display'] ) {
			echo wp_kses_post( $args['before_title'] . $widget_title['title'] . $args['after_title'] ) . PHP_EOL;
		}
		echo apply_filters( 'the_content', $page->post_content );

		$options = veu_get_common_options();
		if ( $this->is_active_child_page_index( $options ) ) {
			if ( ! empty( $instance['child_page_index'] ) ) {
				echo "\n" . apply_filters( 'the_content', '[vkExUnit_childs]' );
			}
		}
		if ( $this->is_active_page_list_ancestor( $options ) ) {
			if ( ! empty( $instance['page_list_ancestor'] ) ) {
				echo "\n" . apply_filters( 'the_content', '[pageList_ancestor]' );
			}
		}

		if ( current_user_can( 'edit_pages' ) ) {
		?>
	<div class="veu_adminEdit">
		<a href="<?php echo site_url(); ?>/wp-admin/post.php?post=<?php echo $pageid; ?>&action=edit" class="btn btn-default btn-sm"><?php _e( 'Edit', 'vk-all-in-one-expansion-unit' ); ?></a>
	</div>
<?php
		}
		echo '</div>' . PHP_EOL;
		echo $args['after_widget'];
	}
}
