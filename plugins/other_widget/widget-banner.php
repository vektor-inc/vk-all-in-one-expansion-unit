<?php

add_action('widgets_init', 'vkExUnit_widget_register_banner');
function vkExUnit_widget_register_banner(){
    return register_widget("WP_Widget_vkExUnit_banner");
}


class WP_Widget_vkExUnit_banner extends WP_Widget
{

    function __construct()
    {
        $widget_name = vkExUnit_get_short_name().'_'.__( 'banner', 'vkExUnit' );

        parent::__construct(
            'vkExUnit_banner',
            $widget_name,
            array(
                'description' => sprintf( __( '*It is necessary to set the "%s" -> "Contact Information" section in "Main setting" page.', 'vkExUnit' ),vkExUnit_get_little_short_name() ),
                )
        );
    }


    function widget( $args, $instance )
    {
        $image = null;
        if (is_numeric($instance['id'])) {
            $image = wp_get_attachment_image_src( $instance['id'], 'full' );
        }
        if (!$image) return;
        echo $args['before_widget'];
        echo '<img src="'.$image[0].'" />';
        echo $args['before_widget'];

        return;
    }


    function update( $new_instance, $old_instance )
    {
        return $new_instance;
    }


    function form( $instance )
    {
        $defaults = array(
            'id'    => Null,
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $image = null;
        if (is_numeric($instance['id'])) {
            $image = wp_get_attachment_image_src( $instance['id'], 'full' );
        }
        ?>
<div style="padding: 0.5em;">
<div id="<?php echo $this->id; ?>">
<div class="_form">
    <input type="hidden" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo $instance['id']; ?>" />
</div>
<div class="_display">
    <?php if ($image): ?>
        <img src="<?php echo $image[0]; ?>" />
    <?php endif; ?>
</div>
<div class="_controller">
<button ><?php _e('set image', 'vkExUnit'); ?></button>
</div>
</div>
</div>
<style type="text/css">#<?php echo $this->id; ?> ._display img{width:100%;}</style>
<script type="text/javascript">
;(function($){
var w=$('#<?php echo $this->id; ?>');var d=w.children('._display');var u;
w.children('._controller').children('button').on('click', function(e){e.preventDefault();
u = wp.media({library:{type:'image'},multiple:false}).on('select', function(e){
    u.state().get('selection').each(function(file){d.children().remove();d.append($('<img>').attr('src',file.toJSON().url));w.children('._form').children('input')[0].value=file.toJSON().id;});
});u.open();return;});
})(jQuery);
</script>
<?php
        return $instance;
    }
}

