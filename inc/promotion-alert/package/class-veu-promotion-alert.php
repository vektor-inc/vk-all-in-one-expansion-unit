<?php
/**
 * VEU Promotion Alert
 */

class VEU_Promotion_Alert {

    /**
     * Constructor Define
     */
    public static function init() {
		add_action( 'veu_package_init', array( __CLASS__, 'option_init' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_box' ) );
        // is_singular() で判定するため wp で実行
        add_action( 'wp', array( __CLASS__, 'display_alert' ) );
        add_action( 'wp_head', array( __CLASS__, 'inline_style' ), 5 );
        add_action( 'after_setup_theme', array( __CLASS__, 'content_filter' ) );
	}

    /**
	 * HTML Allowed
	 */
	public static function kses_allowed() {
		return array(
			'div'    => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'h1'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'h2'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'h3'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'h4'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'h5'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'h6'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'p'      => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'ul'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'ol'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'li'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'i'      => array(
				'id'          => array(),
				'class'       => array(),
				'style'       => array(),
				'aria-hidden' => array()
			),
			'a'      => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
				'href'  => array(),
			),
			'span'   => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'button' => array(
				'id'    => array(),
				'type'  => array(),
				'class' => array(),
				'style' => array(),
				'href'  => array(),
			),
            'img'    => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
                'src'   => array(),
                'alt'   => array(),                
            ),
			'style'  => array(),
            '!'    => array(),
		);
	}

    	/**
	 * コンテンツにかけるフィルター
	 */
	public static function content_filter() {
		add_filter( 'veu_promotion_alert_content', 'do_blocks', 9 );
		add_filter( 'veu_promotion_alert_content', 'wptexturize' );
		add_filter( 'veu_promotion_alert_content', 'convert_smilies', 20 );
		add_filter( 'veu_promotion_alert_content', 'shortcode_unautop' );
		add_filter( 'veu_promotion_alert_content', 'prepend_attachment' );
		add_filter( 'veu_promotion_alert_content', 'wp_filter_content_tags' );
		add_filter( 'veu_promotion_alert_content', 'do_shortcode', 11 );
		add_filter( 'veu_promotion_alert_content', 'capital_P_dangit', 11 );
		add_filter( 'veu_promotion_alert_content', 'wp_replace_insecure_home_url' );
	}

    /**
     * Get Post Types
     */
    public static function get_post_types() {

        // 投稿タイプの事前準備
        $post_types_default = array( 
            array(
                'label' => get_post_type_object( 'post' )->label,
                'name'  => 'post'
            ),
            array(
                'label' =>  get_post_type_object( 'page' )->label,
                'name'  => 'page',
            ),
        );
        $post_types_extra = array();
        $extra_post_types   = get_post_types(
            array(
                'public'   => true,
                '_builtin' => false
            ),
            'objects'
        );
        foreach ( $extra_post_types as $post_type ) {
            $post_types_extra[] = array(
                'label' => $post_type->label,
                'name'  => $post_type->name
            );
        }
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
            'alert-hook'     => '',
        );

        // オプション取得
        $options = get_option( 'vkExUnit_PA' );      
        $options = wp_parse_args( $options, $default );

        // 投稿タイプ毎に初期化
        $post_types = self::get_post_types();
        foreach ( $post_types as $post_type ) {
            if ( empty( $options['alert-display'][ $post_type['name'] ] ) ) {
                $options['alert-display'][ $post_type['name'] ] = 'hide';
            }
        }

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

        // 許可されたHTMLタグ
        $allowed_html = self::kses_allowed();

        // サニタイズ
        $options = array();
        $options['alert-text']    = ! empty( $input['alert-text'] ) ?  self::sanitize_space( esc_html( $input['alert-text'] ) ) : '';
        $options['alert-content'] = ! empty( $input['alert-content'] ) ? self::sanitize_space( stripslashes( htmlspecialchars( $input['alert-content'] ) ) ) : '';

        foreach ( $post_types as $post_type ) {
            $options['alert-display'][ $post_type['name'] ] = ! empty( $input['alert-display'][ $post_type['name'] ] ) ? 'display' : 'hide';
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

        // 許可されたHTMLタグ
        $allowed_html = self::kses_allowed();

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
                    <th><?php _e( 'Custom Alert Content', 'vk-all-in-one-expansion-unit' ); ?></th>
                    <td>
                        <textarea name="vkExUnit_PA[alert-content]" style="width:100%;" rows="10"><?php echo $options['alert-content']; ?></textarea>
                        <ul>
                            <li><?php _e( 'If there is any input in "Custom Alert Content", "Alert Text" will not be displayed and will be overwritten by the content entered in "Custom Alert Content".', 'vk-all-in-one-expansion-unit' ); ?></li>
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
                                    <input type="checkbox" name="vkExUnit_PA[alert-display][<?php echo esc_attr( $post_type['name'] ); ?>]" <?php checked( $options['alert-display'][ $post_type['name'] ], 'display' ); ?>>
                                    <?php echo esc_html( $post_type['label'] ); ?>
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
        } elseif ( 'common' === $meta && ! empty( $options['alert-display'][ $post_type ] ) && 'display' === $options['alert-display'][ $post_type ] ) {
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
        $alert_content = '';

        // 表示条件を判定
        $display = self::get_display_condition( get_the_ID() );

        // 表示条件が true の場合はアラートを表示
        if ( ! empty( $display ) ) {

            // オプションを取得
            $options = self::get_options();

            // アラートの中身を作成
            if ( ! empty( $options['alert-content'] ) ) {
                $alert_content  = '<div class="veu_promotion-alert__content--custom">';
                $alert_content .= $options['alert-content'];
                $alert_content .= '</div>';
            } elseif ( ! empty( $options['alert-text'] ) ) {
                $alert_content  = '<div class="veu_promotion-alert__content--text">';
                $alert_content .= '<span class="veu_promotion-alert__icon"><i class="fa-solid fa-circle-info"></i></span>';
                $alert_content .= '<span class="veu_promotion-alert__text">' . $options['alert-text'] . '</span>';
                $alert_content .= '</div>';
            }

            if ( ! empty( $alert_content ) ) {
                $alert = '<div class="veu_promotion-alert" data-nosnippet>' . $alert_content . '</div>';
            }
        }

        return apply_filters( 'veu_promotion_alert_content', htmlspecialchars_decode( $alert ) );
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
        // 許可されたHTMLタグ
        $allowed_html = self::kses_allowed();

        echo wp_kses( $alert, $allowed_html );       
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
        .veu_promotion-alert__content--text {
            border: 1px solid rgba(0,0,0,0.125);
            padding: 0.5em 1em;
            border-radius: var(--vk-size-radius);
            margin-bottom: var(--vk-margin-block-bottom);
            font-size: 0.875rem;
        }
        /* Alert Content部分に段落タグを入れた場合に最後の段落の余白を0にする */
        .veu_promotion-alert__content--text p:last-of-type{
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



