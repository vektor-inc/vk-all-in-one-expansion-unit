<?php
/**
 * VkExUnit customize.php
 *
 * @package  VkExUnit
 * @author   Kurudrive<kurudrive@gmail.com>
 * @since    28/Sep/2017
 */

/**
 * Add Customize Panel
 */

add_action( 'after_setup_theme', 'veu_add_customize_panel' );

// カスタマイズパネルを出力するかどうかの判別
function veu_add_customize_panel() {
	// 基本的にはカスタマイズ画面で「ExUnit設定」パネルは表示されない
	// if ( apply_filters( 'veu_customize_panel_activation', false ) ) {
		// 各機能からカスタマイザー機能を有効化する指定がされてたら、親パネルである「ExUnit設定」を出力する関数を実行する
		add_action( 'customize_register', 'veu_customize_register' );
		// パネルを表示する = カスタマイザーが利用されるので、独自のコントロールクラスを追加
		add_action( 'customize_register', 'veu_customize_register_add_control', 10 );
	// }
}

// 「ExUnit設定」パネルを出力する関数
function veu_customize_register( $wp_customize ) {
	/*
	  ExUnit Panel
	 /*-------------------------------------------*/
	$wp_customize->add_panel(
		'veu_setting',
		array(
			'priority'       => 1000,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => veu_get_prefix_customize_panel() . ' ' . __( 'Settings', 'vk-all-in-one-expansion-unit' ),
		)
	);

}

/*
  ExUnit Original Controls
/*-------------------------------------------*/
function veu_customize_register_add_control() {

	/*
	  Add text control description
	/*-------------------------------------------*/
	if ( ! class_exists( 'ExUnit_Custom_Text_Control' ) ) {
	class ExUnit_Custom_Text_Control extends WP_Customize_Control {
		public $type         = 'customtext';
		public $description  = ''; // we add this for the extra description
		public $input_before = '';
		public $input_after  = '';
		public function render_content() {
			?>
		<label>
		  <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php $style = ( $this->input_before || $this->input_after ) ? ' style="width:50%"' : ''; ?>
		  <div>
			<?php echo wp_kses_post( $this->input_before ); ?>
		  <input type="text" value="<?php echo esc_attr( $this->value() ); ?>"<?php echo $style; ?> <?php $this->link(); ?> />
			<?php echo wp_kses_post( $this->input_after ); ?>
		  </div>
		  <span><?php echo wp_kses_post( $this->description ); ?></span>
		</label>
			<?php
		} // public function render_content() {
	} // class Custom_Text_Control extends WP_Customize_Control
	}

	/*
	  Add text control description
	/*-------------------------------------------*/
	if ( ! class_exists( 'ExUnit_Custom_Html' ) ) {
	class ExUnit_Custom_Html extends WP_Customize_Control {
		public $type             = 'customtext';
		public $custom_title_sub = ''; // we add this for the extra custom_html
		public $custom_html      = ''; // we add this for the extra custom_html
		public function render_content() {
			if ( $this->label ) {
				// echo '<h2 class="admin-custom-h2">' . wp_kses_post( $this->label ) . '</h2>';
				echo '<h2 class="admin-custom-h2">' . wp_kses_post( $this->label ) . '</h2>';
			}
			if ( $this->custom_title_sub ) {
				echo '<h3 class="admin-custom-h3">' . wp_kses_post( $this->custom_title_sub ) . '</h3>';
			}
			if ( $this->custom_html ) {
				echo '<div>' . wp_kses_post( $this->custom_html ) . '</div>';
			}
			?>
			<?php
		} // public function render_content() {
	} // class VkExUnit_Custom_Html extends WP_Customize_Control
	}

} // function veu_customize_register_add_control(){
