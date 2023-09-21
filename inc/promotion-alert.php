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
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_box' ) );
        // is_singular() で判定するため wp で実行
        add_action( 'wp', array( __CLASS__, 'display_alert' ) );
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
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Alert Text', 'vk-all-in-one-expansion-unit' ); ?></th>
                    <td>
                        <input type="text" name="vkExUnit_PA[alert-text]" value="<?php echo esc_attr( $options['alert-text'] ); ?>" class="large-text">
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Alert Content', 'vk-all-in-one-expansion-unit' ); ?></th>
                    <td>
                        <textarea name="vkExUnit_PA[alert-content]" style="width:100%;" rows="10"><?php echo wp_kses_post( $options['alert-content'] ); ?></textarea>
                        <ul>
                            <li><?php _e( 'If there is any input in "Alert Content", "Alert Text" will not be displayed and will be overwritten by the content entered in "Alert Content".', 'vk-all-in-one-expansion-unit' ); ?></li>
                            <li><?php _e( 'You can insert HTML tags here.', 'vk-all-in-one-expansion-unit' ); ?></li>
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
                <tr>
                    <th><?php _e( 'Alert Hook', 'vk-all-in-one-expansion-unit' ); ?></th>
                    <td>
                        <input type="text" name="vkExUnit_PA[alert-hook]" value="<?php echo esc_attr( $options['alert-hook'] ); ?>" class="large-text">
                    </td>
                    <p><?php _e( 'By default, it is output at the top of the content.', 'vk-all-in-one-expansion-unit' ); ?><br><?php _e( 'If you want to change the location of any action hook, enter the action hook name.', 'vk-all-in-one-expansion-unit' ); ?><br><?php _e( 'Ex) lightning_entry_body_prepend', 'vk-all-in-one-expansion-unit' ); ?></p>
                </tr>
            </table>
            <?php submit_button(); ?>
        </div>
        <?php
    }

    /**
     * Add Meta Box
     */
    public static function add_meta_box() {
        $post_types = self::get_post_types();
        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'vkExUnit_PA',
                __( 'Promotion Alert', 'vk-all-in-one-expansion-unit' ),
                array( __CLASS__, 'render_meta_box' ),
                $post_type,
                'side',
                'high'
            );
        }
    }

    /**
     * Render Meta Box
     */
    public static function render_meta_box( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'veu_promotion_alert', 'veu_promotion_alert_nonce' );

        // Use get_post_meta to retrieve an existing value from the database.
		$value = get_post_meta( $post->ID, 'alert-display', true );
        ?>
        <div class="veu_promotion-alert-meta-fields">
            <h4><?php _e( 'Promotion Alert Setting', 'vk-all-in-one-expansion-unit' ); ?></h4>
            <select name="alert-display">
                <option value="common" <?php selected( $value, 'common' ); ?>><?php _e( 'Apply common settings', 'vk-all-in-one-expansion-unit' ); ?></option>
                <option value="display" <?php selected( $value, 'display' ); ?>><?php _e( 'Display', 'vk-all-in-one-expansion-unit' ); ?></option>
                <option value="hide" <?php selected( $value, 'hide' ); ?>><?php _e( 'Hide', 'vk-all-in-one-expansion-unit' ); ?></option>
            </select>
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
		$mydata = sanitize_text_field( $_POST['alert-display'] );

		// Update the meta field.
		update_post_meta( $post_id, 'alert-display', $mydata );
    }

    /**
     * Display Condition
     */
    public static function get_display_condition( $post_id ) {

        // 通常は false
        $return = false;

        // カスタムフィールドを取得
        $meta = get_post_meta( $post_id, 'alert-display', true );

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

            // アラートの中身を初期化
            $alert_content = '';

            // アラートの中身を作成
            if ( ! empty( $options['alert-content'] ) ) {
                $alert_content = $options['alert-content'];
            } elseif ( ! empty( $options['alert-text'] ) ) {
                $alert_content = '<span class="veu_promotion-alert-icon"><i class="fa-solid fa-circle-info"></i></span><span class="veu_promotion-alert-text">' . $options['alert-text'] . '</span>';
            }

            // アラートの中身がある場合はアラートを作成
            if ( ! empty( $alert_content ) ) {
                $alert = '<div class="veu_promotion-alert" data-nosnippet>' . $alert_content . '</div>';
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
}
VK_Promotion_Alert::init();
