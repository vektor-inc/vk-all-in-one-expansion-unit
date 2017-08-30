<?php

/*-------------------------------------------*/
/*  page widget
/*-------------------------------------------*/
class WP_Widget_vkExUnit_widget_page extends WP_Widget {

	function __construct() {
		$widget_name = vkExUnit_get_short_name() . '_' . __( 'page content to widget', 'vkExUnit' );

		parent::__construct(
			'pudge',
			$widget_name,
			array( 'description' => __( 'Displays a page contents to widget.', 'vkExUnit' ) )
		);
	}

	/*
	$input 保存されてる値
	$value 今のinputタグのvalueの値
	*/
	static public function echo_checked( $input, $value){
		if ( $input === $value ) {
			echo ' checked';
		}
	}

	function form( $instance ) {
		$defaults = array(
			'label'     => '',
			'page_id'   => 2,
			'set_title' => 'title-widget',
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <p>

			<?php //タイトル ?>
			<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Title:' ); ?></label><br/>
			<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo esc_attr( $instance['label'] ); ?>" />
	    <br />
			<br />

			<?php /* タイトルの表示形式の選択 */ ?>

			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'set_title' );  ?>" value="title-widget" <?php $this->echo_checked( $instance['set_title'] , "title-widget");?> />
				<?php _e( '入力したタイトルを表示する', 'vkExUnit' ); ?></label><br/>

			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'set_title' ); ?>" value="title-page" <?php $this->echo_checked( $instance['set_title'] , "title-page");?> />
				<?php _e( '固定ページのタイトルを表示する', 'vkExUnit' ); ?></label><br/>

			<label>
				<input type="radio" name="<?php echo $this->get_field_name( 'set_title' );  ?>" value="title-hidden" <?php $this->echo_checked( $instance['set_title'] , "title-hidden");?> />
				<?php _e( 'タイトルを表示しない', 'vkExUnit' ); ?></label><br/>
        <br/>

		<?php
		// 固定ページ選択プルダウン
		/*-------------------------------------------*/
		// まずは固定ページの情報を取得する
		// 取得する固定ページの条件
		$args = array(
			'post_status' => 'publish,private', // 公開と非公開の記事
		);
		// 固定ページ情報の取得を実行
		$pages = get_pages( $args );
		?>
		<label for="<?php echo $this->get_field_name( 'page_id' ); ?>"><?php _e( 'Display page', 'vkExUnit' ) ?></label>
		<select name="<?php echo $this->get_field_name( 'page_id' ); ?>" id="<?php echo $this->get_field_name( 'page_id' ); ?>" >
		<?php
		// option項目の生成
		// $pages に格納されている固定ページのデータをループしながらoptionを出力
		foreach ( $pages as $page ) {  ?>
		<option value="<?php echo $page->ID; ?>" <?php if ( $instance['page_id'] == $page->ID ) { echo 'selected="selected"'; } ?> ><?php echo $page->post_title; ?></option>
		<?php } ?>
        </select>
        <br/>
        </p>
		<?php
	}

	// 保存・更新する値
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['label'] = $new_instance['label'];
		$instance['page_id'] = $new_instance['page_id'];
		$instance['set_title'] = $new_instance['set_title'];
		return $instance;
	}

	function widget( $args, $instance ) {
		global $is_pagewidget;
		$is_pagewidget = true;
		if ( isset( $instance['page_id'] ) && $instance['page_id'] ) {
			$this->display_page( $args, $instance );
		}
		$is_pagewidget = false;
	}

	/*
	ウィジェットのタイトルに関する情報を出力する関数
	[ 返り値 ]
	$widget_title['display'] : 表示するかどうか
	$widget_title['label'] : ウィジェットのタイトルとして表示する文字
	*/
	static public function widget_title( $instance ){

		$pageid = $instance['page_id'];
		$page = get_page( $pageid );

		if ( $instance['set_title'] != 'title-hidden' ){
			// 非表示じゃない項目が選択されてた場合は true
			$widget_title['display'] = true;
			if ( $instance['set_title'] == 'title-widget' && !$instance['label'] ){
				// ウィジェットのタイトルが選択されているが、タイトルが入力されていない場合は false
				$widget_title['display'] = false;
			}
		} else { // $instance['set_title'] == 'title-hidden'
			// タイトルを表示しないが選択されている場合
			$widget_title['display'] = false;
		}

		// ウィジェットタイトルを選択していて、タイトル入力欄に入力がある場合
		if ( $instance['set_title'] == 'title-widget' && $instance['label'] ) {
			$widget_title['label'] = $instance['label'];
		// 旧バージョンで　タイトルを表示になっていた場合に
		// タイトル表示形式フラグに 固定ページのタイトルを表示するvalueにしておく
		} else if ( ( $instance['set_title'] === true ) || ( $instance['set_title'] == 'title-page' ) ){
			$widget_title['label'] = $page->post_title;
		} else {
			$widget_title['label'] = '';
		}
		return $widget_title;
	}


	function display_page( $args, $instance ) {
		$pageid = $instance['page_id'];
		$page = get_page( $pageid );
		echo $args['before_widget'];

		$widget_title = $this->widget_title( $instance );

		echo PHP_EOL.'<div id="widget-page-'.$pageid.'" class="widget_pageContent">' . PHP_EOL;
		if ( $widget_title['display'] ) {
			echo $args['before_title'] . $widget_title['label'] . $args['after_title'] . PHP_EOL;
		}
		echo apply_filters( 'the_content', $page->post_content );

		if (  current_user_can( 'edit_pages' ) ) { ?>
    <div class="veu_adminEdit">
		<a href="<?php echo site_url(); ?>/wp-admin/post.php?post=<?php echo $pageid ;?>&action=edit" class="btn btn-default btn-sm"><?php _e( 'Edit', 'vkExUnit' );?></a>
    </div>
<?php }
		echo '</div>'.PHP_EOL;
		echo $args['after_widget'];
	}
}

add_action('widgets_init', 'vkExUnit_widget_register_page');
function vkExUnit_widget_register_page(){
	return register_widget("WP_Widget_vkExUnit_widget_page");
}
