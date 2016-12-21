<?php

/*-------------------------------------------*/
/*  Button Widget
/*-------------------------------------------*/
class WP_Widget_Button extends WP_Widget {

    static $button_otherlabels = array(
        'primary' => 'Key Color(.primary)',
        'default' => 'No paint(.default)',
        'success' => 'Light green(.success)',
        'info'    => 'Light blue(.info)',
        'warning' => 'Orange(.warning)',
        'danger'  => 'Red(.danger)',
    );

    static $default = array(
        'maintext'     => '',
        'subtext'      => '',
        'linkurl'      => '',
        'blank'        => false,
        'size'         => '',
        'color'        => 'primary'
    );

    function __construct() {
        $widget_name = 'VK_' . __( 'Button', 'vkExUnit' );

        parent::__construct(
            'vkExUnit_button',
            $widget_name,
            array( 'description' => __( 'You can set buttons for arbitrary text.' , 'vkExUnit' ) )
        );
    }


    function widget( $args, $instance )
    {
        $options = self::default_options( $instance );

        $classes = array(
            'btn',
            'btn-block'
        );
        $classes[] = 'btn-' . $options['color'];
        if (in_array($options['size'], array('sm', 'lg')))
            $classes[] = 'btn-' . $options['size'];
    ?>
    <?php echo $args['before_widget']; ?>
    <?php if ( $options['linkurl'] && $options['maintext'] ): ?>
    <div class="veu_button">
        <a type="button" class="<?php echo implode(' ', $classes); ?>" href="<?php echo $options['linkurl']; ?>" <?php if($options['blank']) echo 'target="_blank"'; ?> >
            <span class="button_bt_txt"><?php echo htmlspecialchars($options['maintext']); ?></span>
            <?php if ($options['subtext']): ?>
                <br>
                <span class="veu_caption"><?php echo htmlspecialchars($options['subtext']); ?></span>
            <?php endif; ?>
        </a>
    </div>
    <?php endif; ?>
    <?php echo $args['after_widget']; ?>
    <?php
    }

    public static function default_options( $option=array() )
    {
        return wp_parse_args( $option, static::$default );
    }


    function form( $instance ) {
        $instance = self::default_options($instance);

        ?>
        <div class="warp" style="padding: 1em 0;line-height: 2.5em;">

        <?php _e('Main text(Required):', 'vkExUnit'); ?>
        <input type="text" id="<?php echo $this->get_field_id('maintext'); ?>" name="<?php echo $this->get_field_name('maintext') ?>" style="width:100%; margin-bottom: 0.5em;" value="<?php echo $instance['maintext']; ?>">

        <br/>
        <?php _e('Sub text:', 'vkExUnit'); ?>
        <input type="text" id="<?php echo $this->get_field_id('subtext'); ?>" name="<?php echo $this->get_field_name('subtext') ?>" style="width:100%; margin-bottom: 0.5em;" value="<?php echo $instance['subtext']; ?>">

        <br/>
         <?php _e('Link URL(Required):', 'vkExUnit'); ?>
        <input type="text" id="<?php echo $this->get_field_id('linkurl'); ?>" name="<?php echo $this->get_field_name('linkurl') ?>" value="<?php echo $instance['linkurl']; ?>" style="width: 100%" />

        <br/>
        <input type="checkbox" id="<?php echo $this->get_field_id('blank'); ?>" name="<?php echo $this->get_field_name('blank') ?>" value="true" <?php if($instance['blank']) echo 'checked'; ?>  />
        <label for="<?php echo $this->get_field_id('blank'); ?>"><?php _e('Open with new tab', 'vkExUnit'); ?></label>

        <br/>
        <label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Size', 'vkExUnit'); ?> :</label>
        <select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size') ?>">
            <option value="sm" <?php if($instance['size'] == 'sm') echo 'selected'; ?> ><?php _e('Small', 'vkExUnit'); ?></option>
            <option value="md" <?php if(!in_array($instance['size'], ['sm', 'lg'])) echo 'selected'; ?> ><?php _e('Medium', 'vkExUnit'); ?></option>
            <option value="lg" <?php if($instance['size'] == 'lg') echo 'selected'; ?> ><?php _e('Large', 'vkExUnit'); ?></option>
        </select>

        <br/>
        <label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Button color:', 'vkExUnit');?> </label>
        <select id="<?php echo $this->get_field_id('color'); ?>" name="<?php echo $this->get_field_name('color'); ?>">
        <?php
        if ( !isset($instance['color']) || !$instance['color'] ) $instance['color'] = $default['color'];
        foreach( static::$button_otherlabels as $key => $label ): ?>
            <option value="<?php echo $key; ?>" <?php if ( $instance['color'] == $key ) echo 'selected'; ?> >
            <?php _e($label, 'vkExUnit'); ?>
            </option>
        <?php endforeach; ?>
        </select>
        </div>
        <?php
    }


    function update( $new_instance, $old_instance ) {
        $opt = array();
        $opt['maintext'] = $new_instance['maintext'];
        $opt['subtext']  = $new_instance['subtext'];
        $opt['linkurl']  = $new_instance['linkurl'];
        $opt['blank']    = (isset($new_instance['blank']) && $new_instance['blank'] == 'true');
        $opt['size']     = in_array($new_instance['size'], array('sm', 'lg'))? $new_instance['size'] : 'md';
        $opt['color']    = in_array($new_instance['color'], array_keys(self::$button_otherlabels))? $new_instance['color'] : static::$button_default;
        return $opt;
    }

    public static function dummy(){
        __( 'Key Color(.primary)', 'vkExUnit' );
        __( 'No paint(.default)', 'vkExUnit' );
        __( 'Light green(.success)', 'vkExUnit' );
        __( 'Light blue(.info)', 'vkExUnit' );
        __( 'Orange(.warning)', 'vkExUnit' );
        __( 'Red(.danger)', 'vkExUnit' );
    }
}

add_action('widgets_init', 'vkExUnit_widget_button');
function vkExUnit_widget_button(){
    return register_widget("WP_Widget_Button");
}
