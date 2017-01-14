<?php
namespace Vektor\ExUnit\Package\Cta;


/*-------------------------------------------*/
/*  Contact widget
/*-------------------------------------------*/
class Widget_CTA extends \WP_Widget
{

    function __construct()
    {
        $widget_name = vkExUnit_get_short_name().'_'.__( 'CTA', 'vkExUnit' );

        parent::__construct(
            'vkExUnit_cta',
            $widget_name,
            array(
                'description' => sprintf( __( 'Select CTA and display it.', 'vkExUnit' ),vkExUnit_get_little_short_name() ),
                )
        );
    }


    function widget( $args, $instance )
    {
        if ( isset( $instance['id'] ) && $instance['id'] ) {
            echo $args['before_widget'];
            echo CTA::render_cta_content($instance['id']);
            echo $args['before_widget'];
        }
        return;
    }


    function update( $new_instance, $old_instance )
    {
        return array( 'id' => (CTA::POST_TYPE == get_post_type( $new_instance['id']))? $new_instance['id'] : Null);
    }


    function form( $instance )
    {
        $defaults = array(
            'id'    => Null,
        );
        $instance = \wp_parse_args( (array) $instance, $defaults );
        $value = $instance['id'];
        $ctas = CTA::get_ctas(true, '- ');
?>
<div style="padding:1em 0;">
    <?php _e( 'Please select CTA to display.', 'vkExUnit' );?>
</div>
<div style="padding-bottom: 0.5em;">
<select name="<?php echo $this->get_field_name( 'id' ); ?>" style="width: 100%" >
<option value="">[ <?php _e('Please select', 'vkExUnit' ) ?> ]</option>
<?php foreach ( $ctas as $cta ) : ?>
    <option value="<?php echo $cta['key'] ?>" <?php echo($value == $cta['key'])? 'selected':''; ?> ><?php echo $cta['label'] ?></option>
<?php endforeach; ?>
</select>
</div>
<div style="padding-bottom: 1em;">
<a href="<?php echo admin_url( 'edit.php?post_type=cta' ) ?>" class="button button-default" target="_blank"><?php _e( 'Show CTA index page', 'vkExUnit' ); ?></a>
<a href="<?php echo admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_cta_settings' ); ?>" class="button button-default" target="_blank"><?php _e( 'CTA setting', 'vkExUnit' ); ?></a>
</div>
<?php
        return $instance;
    }
}

