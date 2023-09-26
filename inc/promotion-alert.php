<?php
/**
 * VK Promotion Alert
 */

class VK_Promotion_Alert {

    /**
     * Constructor Define
     */
    public static function init() {
		add_action( 'veu_package_init', array( __CLASS__, 'option_init' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_box' ) );
        // is_singular() で判定するため wp で実行
        add_action( 'wp', array( __CLASS__, 'display_alert' ) );
        add_action( 'wp_head', array( __CLASS__, 'inline_style' ), 5 );
	}

    /**
     * Get Post Types
     */
    public static function get_post_types() {

        // 投稿タイプの事前準備
        $post_types_default = array( 'post', 'page' );
        $post_types_extra   = get_post_types(
            array(
                'public' => true,
                '_builtin' => false
            )
        );
        $post_types = array_merge( $post_types_default, $post_types_extra );
        return $post_types;
    }

    /**
     * Get Options
     */
    public static function get_options() {

        // デフォルト値
        $default = array(
            'alert-text'     => '',
            'alert-content'  => '',
            'alert-hook' => '',
        );

        // 投稿タイプ毎に初期化
        $post_types = self::get_post_types();
        foreach ( $post_types as $post_type ) {
            $default['alert-display'][ $post_type ] = 'hide';
        }

        // オプション取得
        $options = get_option( 'vkExUnit_PA' );
        $options = wp_parse_args( $options, $default );

        return $options;
    }


    /**
     * Add Setting Page
     */
    public static function option_init() {
        vkExUnit_register_setting(
			__( 'Promotion Alert', 'vk-all-in-one-expansion-unit' ),           // tab label.
			'vkExUnit_PA',                         // name attr
			array( __CLASS__, 'sanitize_setting' ),      // sanitaise function name
			array( __CLASS__, 'render_setting' )     // setting_page function name
		);
    }

    /**
     * Sanitize Space 
     */
    public static function sanitize_space( $input ) {
        if ( preg_match( '/^(\s)+$/u', $input ) ) {
            return '';
        }
        return $input;
    }

    /**
     * Sanitize Setting
     */
    public static function sanitize_setting( $input ) {

        // 投稿タイプを取得
        $post_types = self::get_post_types();

        // サニタイズ
        $options = array();
        $options['alert-text']    = ! empty( $input['alert-text'] ) ?  self::sanitize_space( esc_html( $input['alert-text'] ) ) : '';
        $options['alert-content'] = ! empty( $input['alert-content'] ) ? self::sanitize_space( wp_kses_post(  $input['alert-content'] ) ) : '';

        foreach ( $post_types as $post_type ) {
            $options['alert-display'][ $post_type ] = ! empty( $input['alert-display'][ $post_type ] ) ? 'display' : 'hide';
        }
        $options['alert-hook'] = ! empty( $input['alert-hook'] ) ? self::sanitize_space( esc_html( $input['alert-hook'] ) ) : '';
        return $options;
    }

    /**
     * Render Setting Page
     */
    public static function render_setting() {

        // 投稿タイプを取得
        $post_types = self::get_post_types();

        // オプションを取得
        $options = self::get_options();
        ?>
        <h3><?php _e( 'Promotion Alert', 'vk-all-in-one-expansion-unit' ); ?></h3>
        <div id="vkExUnit_PA" class="sectionBox">
			<P>
			<?php _e( 'If the article contains advertisements, it\'s necessary to provide a clear notation for general consumers to recognize.', 'vk-all-in-one-expansion-unit' ); ?>
			<br>
			<?php _e( 'By inputting here, you can automatically insert it at the beginning of the article.', 'vk-all-in-one-expansion-unit' ); ?>
			</p>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Alert Text', 'vk-all-in-one-expansion-unit' ); ?></th>
                    <td>
						<p>
                        <input type="text" name="vkExUnit_PA[alert-text]" value="<?php echo esc_attr( $options['alert-text'] ); ?>" class="large-text">
						</p>
						<p>Ex)</p>
						<ul>
						<li><?php _e( 'This article contains affiliate advertisements.', 'vk-all-in-one-expansion-unit' ); ?></li>
						<li><?php _e( 'This article contains promotions.', 'vk-all-in-one-expansion-unit' ); ?></li>
						<li><?php _e( 'This article is posted with products provided by ***.', 'vk-all-in-one-expansion-unit' ); ?></li>
						</ul>
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Alert Content', 'vk-all-in-one-expansion-unit' ); ?></th>
                    <td>
                        <textarea name="vkExUnit_PA[alert-content]" style="width:100%;" rows="10"><?php echo wp_kses_post( $options['alert-content'] ); ?></textarea>
                        <ul>
                            <li><?php _e( 'If there is any input in "Alert Content", "Alert Text" will not be displayed and will be overwritten by the content entered in "Alert Content".', 'vk-all-in-one-expansion-unit' ); ?></li>
                            <li><?php _e( 'You can insert HTML tags here. This is designed to be used by pasting content created in the Block Editor.', 'vk-all-in-one-expansion-unit' ); ?></li>
                        </ul>
                                
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Display Post Types', 'vk-all-in-one-expansion-unit' ); ?></th>
                    <td>
                        <ul class="no-style">
                        <?php foreach ( $post_types as $post_type ) : ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="vkExUnit_PA[alert-display][<?php echo esc_attr( $post_type ); ?>]" <?php checked( $options['alert-display'][ $post_type ], 'display' ); ?>>
                                    <?php echo esc_html( $post_type ); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                        <p><?php _e( 'Settings for individual articles take precedence over settings here.', 'vk-all-in-one-expansion-unit' ); ?></p>
                    </td>
                </tr>
                </table>
                <hr>
                <table class="form-table">
                <tr>
                    <th><?php _e( 'Alert Hook ( Optional )', 'vk-all-in-one-expansion-unit' ); ?></th>
                    <td>
                        <p><?php _e( 'By default, it is output at the top of the content.', 'vk-all-in-one-expansion-unit' ); ?><br><?php _e( 'If you want to change the location of any action hook, enter the action hook name.', 'vk-all-in-one-expansion-unit' ); ?><br><?php _e( 'Ex) lightning_entry_body_prepend', 'vk-all-in-one-expansion-unit' ); ?></p>
                        <input type="text" name="vkExUnit_PA[alert-hook]" value="<?php echo esc_attr( $options['alert-hook'] ); ?>" class="large-text">
                    </td>                    
                </tr>
            </table>
            <?php submit_button(); ?>
        </div>
        <?php
    }

    /**
     * Save Meta Box
     */
    public static function save_meta_box( $post_id ) {

        // Check if our nonce is set.
        if ( ! isset( $_POST['veu_promotion_alert_nonce'] ) ) {
			return $post_id;
		}

        $nonce = $_POST['veu_promotion_alert_nonce'];

        // Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'veu_promotion_alert' ) ) {
			return $post_id;
		}

        /*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

        // Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

        /* OK, it's safe for us to save the data now. */

		// Sanitize the user input.
		$mydata = sanitize_text_field( $_POST['veu_display_promotion_alert'] );

		// Update the meta field.
		update_post_meta( $post_id, 'veu_display_promotion_alert', $mydata );
    }

    /**
     * Display Condition
     */
    public static function get_display_condition( $post_id ) {

        // 通常は false
        $return = false;

        // カスタムフィールドを取得
        $meta = get_post_meta( $post_id, 'veu_display_promotion_alert', true );
        $meta = ! empty( $meta ) ? $meta : 'common';

        // オプションを取得
        $options = self::get_options();

          // 投稿タイプを取得
        $post_type = get_post_type( $post_id );

        // 表示条件を判定
        if ( 'display' === $meta ) {
            // カスタムフィールドが display の場合は true
            $return = true;
        } elseif ( 'common' === $meta && 'display' === $options['alert-display'][ $post_type ] ) {
            // カスタムフィールドが common でオプションが display の場合は true
            $return = true;
        }

        return $return;        
    }

    /**
     * Alert Content
     */
    public static function get_alert_content() {

        // アラートを初期化
        $alert = '';

        // 表示条件を判定
        $display = self::get_display_condition( get_the_ID() );

        // 表示条件が true の場合はアラートを表示
        if ( ! empty( $display ) ) {

            // オプションを取得
            $options = self::get_options();

            // アラートの中身を作成
            if ( ! empty( $options['alert-content'] ) ) {
                $alert = $options['alert-content'];
            } elseif ( ! empty( $options['alert-text'] ) ) {
                $alert = '<div class="veu_promotion-alert" data-nosnippet><span class="veu_promotion-alert-icon"><i class="fa-solid fa-circle-info"></i></span><span class="veu_promotion-alert-text">' . $options['alert-text'] . '</span></div>';
            }

        }

        return $alert;
    }

    /**
     * Display Alert Content Filter Hook
     */
    public static function display_alert_filter( $content ) {

        // アラートを取得
        $alert = self::get_alert_content();

        // 文頭にアラートを追加
        $content = $alert . $content;
       
        return $content;       
    }

    /**
     * Display Alert Content Action Hook
     */
    public static function display_alert_action() {

        // アラートを取得
        $alert = self::get_alert_content();

         echo wp_kses_post( $alert );       
    }

    /**
     * Display Alert
     */
    public static function display_alert() {

        // オプションを取得
        $options = self::get_options();
        if ( is_singular() ) {
            if ( ! empty( $options['alert-hook'] ) ) {
                add_action( $options['alert-hook'], array( __CLASS__, 'display_alert_action' ) );
            } else {
                add_filter( 'the_content', array( __CLASS__, 'display_alert_filter' ) );           
            }
        }
    }

    /**
     * Inline Style
     */
    public static function inline_style() {

        $dynamic_css = '
        .veu_promotion-alert {
            border: 1px solid rgba(0,0,0,0.125);
            padding: 0.5em 1em;
            border-radius: var(--vk-size-radius);
            margin-bottom: var(--vk-margin-block-bottom);
            font-size: 0.875rem;
        }
        /* Alert Content部分に段落タグを入れた場合に最後の段落の余白を0にする */
        .veu_promotion-alert p:last-of-type{
            margin-bottom:0;
            margin-top: 0;
        }
        ';
    
        // delete before after space
        $dynamic_css = trim( $dynamic_css );
        // convert tab and br to space
        $dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
        // Change multiple spaces to single space
        $dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
        wp_add_inline_style( 'vkExUnit_common_style', $dynamic_css );
    }
}
VK_Promotion_Alert::init();

if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_Promotion_Alert extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_display_promotion_alert',
			'cf_name'  => 'veu_display_promotion_alert',
			'title'    => __( 'Promotion Alert Setting', 'vk-all-in-one-expansion-unit' ),
			'priority' => 50,
		);

		parent::__construct( $this->args );

	}

	/**
	 * metabox_body_form
	 * Form inner
	 *
	 * @return [type] [description]
	 */
	public function metabox_body_form( $cf_value ) {

		$form = '';

        // Add an nonce field so we can check for it later.
		wp_nonce_field( 'veu_promotion_alert', 'veu_promotion_alert_nonce' );

        $form .= '<div class="veu_promotion-alert-meta-fields">';
        $form .= '<h4>' . __( 'Promotion Alert Setting', 'vk-all-in-one-expansion-unit' ) . '</h4>';
        $form .= '<select name="veu_display_promotion_alert">';
        $form .= '<option value="common" ' . selected( $cf_value, 'common', false ) . '>' . __( 'Apply common settings', 'vk-all-in-one-expansion-unit' ) . '</option>';
        $form .= '<option value="display" ' .  selected( $cf_value, 'display', false ) . '>' . __( 'Display', 'vk-all-in-one-expansion-unit' ). '</option>';
        $form .= '<option value="hide" ' . selected( $cf_value, 'hide', false ) . '>' . __( 'Hide', 'vk-all-in-one-expansion-unit' ) . '</option>';
        $form .= '</select>';
        $form .= '</div>';

		return $form;
	}

} // class VEU_Metabox_CTA {

$veu_metabox_promotion_alert = new VEU_Metabox_Promotion_Alert();