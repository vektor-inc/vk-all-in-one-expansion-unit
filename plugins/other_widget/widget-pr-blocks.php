<?php

/*-------------------------------------------*/
/*  VK PR Blocks
/*-------------------------------------------*/
class WP_Widget_vkExUnit_PR_Blocks extends WP_Widget {

	function __construct() {
		$widget_name = vkExUnit_get_short_name(). '_' . __( 'PR Blocks', 'vkExUnit' );

		parent::__construct(
			'WP_Widget_vkExUnit_PR_Blocks',
			$widget_name,
			array( 'description' => __( 'Displays a circle image or icon font for pr blocks', 'vkExUnit' ) )
		);
	}

	function form( $instance ) {
		$defaults = array(
			'block_count' => 3,

			'label_1' => __( 'Service', 'vkExUnit' ),
			'media_image_1' => '',
			'media_alt_1' => '',
			'iconFont_class_1' => 'fa-file-text-o',
			'iconFont_bgColor_1' => '#337ab7',
			'summary_1' => '',
			'linkurl_1' => '',

			'label_2' => __( 'Company', 'vkExUnit' ),
			'media_image_2' => '',
			'media_alt_2' => '',
			'iconFont_class_2' => 'fa-building-o',
			'iconFont_bgColor_2' => '#337ab7',
			'summary_2' => '',
			'linkurl_2' => '',

			'label_3' => __( 'Recruit', 'vkExUnit' ),
			'media_image_3' => '',
			'media_alt_3' => '',
			'iconFont_class_3' => 'fa-user',
			'iconFont_bgColor_3' => '#337ab7',
			'summary_3' => '',
			'linkurl_3' => '',

			'label_4' => __( 'Contact', 'vkExUnit' ),
			'media_image_4' => '',
			'media_alt_4' => '',
			'iconFont_class_4' => 'fa-envelope',
			'iconFont_bgColor_4' => '#337ab7',
			'summary_4' => '',
			'linkurl_4' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

<?php // select Block count	?>
<p>
<label for="<?php echo $this->get_field_id( 'block_count' ); ?>"><?php _e( 'The choice of the number of columns:', 'vkExUnit' ); ?></label><br/>
<select name="<?php echo $this->get_field_name( 'block_count' ); ?>" id="<?php echo $this->get_field_id( 'block_count' ); ?>-count">
	<option value="3" <?php if ( intval( $instance['block_count'] ) === 3 ) {  echo 'selected'; } ?>><?php _e( '3column', 'vkExUnit' ); ?></option>
	<option value="4" <?php if ( intval( $instance['block_count'] ) === 4 ) {  echo 'selected'; } ?>><?php _e( '4column', 'vkExUnit' ); ?></option>
</select><br>
<?php _e( 'If you change the number of columns, click to "Save" botton and exit the edit page. When restart the edit page, the column input form is increased or decreased.', 'vkExUnit' ); ?>
</p>

<?php // PR Blocks
for ( $i = 1; $i <= intval( $instance['block_count'] ); ) {

	// PR Block admin title
	echo '<h5 class="pr_subTitle">'.__( 'PR Block'.$i.' setting', 'vkExUnit' ).'</h5>';

	// PR Block display title
	echo '<p><label for="'.$this->get_field_id( 'label_'.$i ).'">'.__( 'Title:', 'vkExUnit' ).'</label><br/>'.
		'<input type="text" id="'.$this->get_field_id( 'label_'.$i ).'-title" class="pr-input" name="'.$this->get_field_name( 'label_'.$i ).'" value="'.$instance[ 'label_'.$i ].'" /></p>';

	// icon font class input
	echo '<p><label for="'.$this->get_field_id( 'iconFont_'.$i ).'">'.__( 'Class name of the icon font you want to use:', 'vkExUnit' ).'</label><br/>'.
		'[ <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Font Awesome Icons</a> ]<br/>
			<input type="text" id="'.$this->get_field_id( 'iconFont_class_'.$i ).'-font" class="font_class" name="'.$this->get_field_name( 'iconFont_class_'.$i ).'" value="'.$instance[ 'iconFont_class_'.$i ].'" /><br>'
	.__( 'To choose your favorite icon, and enter the class.', 'vkExUnit' ).'<br>'.__( ' ex:fa-file-text-o', 'vkExUnit' ).'</p>';

	// icon font background color
	echo '<p class="color_picker_wrap">'.
		'<label for="'.$this->get_field_id( 'iconFont_bgColor_'.$i ).'">'.__( 'Icon bg-color:', 'vkExUnit' ).'</label><br/>'.
		'<input type="text" id="'.$this->get_field_id( 'iconFont_bgColor_'.$i ).'-color" class="color_picker" name="'.$this->get_field_name( 'iconFont_bgColor_'.$i ).'" value="'.$instance[ 'iconFont_bgColor_'.$i ].'" /></p>';

	// media uploader imageurl input area
	echo '<p><label for="'.$this->get_field_id( 'media_image_'.$i ).'">'.__( 'Select image:', 'vkExUnit' ).'</label><br/>'.
		'<input type="hidden" class="pr_media_image  '.$this->get_field_id( 'media_image_'.$i ).'" id="'.$this->get_field_id( 'media_image_'.$i ).'-image" name="'.$this->get_field_name( 'media_image_'.$i ).'" value="'.esc_attr( $instance[ 'media_image_'.$i ] ).'" />';

	// media uploader imagealt input area
	echo '<input type="hidden" class="pr_media_alt" id="'.$this->get_field_id( 'media_alt_'.$i ).'-alt" name="'.$this->get_field_name( 'media_alt_'.$i ).'" value="'.esc_attr( $instance[ 'media_alt_'.$i ] ).'" />';

	// media uploader select btn
	echo '<input type="button" class="media_select" value="'.__( 'Select image', 'vkExUnit' ).'" onclick="clickSelectPrBroks(event.target);" />';

	// media uploader clear btn
	echo '<input type="button" class="media_clear" value="'.__( 'Clear image', 'vkExUnit' ).'" onclick="clickClearPrBroks(event.target);" />'.
	'<br />'.__( 'When you have an image. Image is displayed with priority', 'vkExUnit' ).'</p>';

	// media image display
	echo '<div class="media image_pr">';
	if ( ! empty( $instance[ 'media_image_'.$i ] ) ) {
		echo '<img class="media_img" src="'.esc_url( $instance[ 'media_image_'.$i ] ).'" alt="'. esc_attr( $instance[ 'media_alt_'.$i ] ).'" />';
	}
	echo '</div>';

	// summary text
	echo '<p><label for="'.$this->get_field_id( 'summary_'.$i ).'">'.__( 'Summary Text:', 'vkExUnit' ).'</label><br/></p>'.
		'<textarea rows="4" cols="40" id="'.$this->get_field_id( 'summary_'.$i ).'_text" class="pr_input textarea" name="'.$this->get_field_name( 'summary_'.$i ).'">'.$instance[ 'summary_'.$i ].'</textarea>';

	// link_URL
	echo '<p><label for="'.$this->get_field_id( 'linkurl_'.$i ).'">'.__( 'Link URL:', 'vkExUnit' ).'</label><br/>'.
		'<input type="text" id="'.$this->get_field_id( 'linkurl_'.$i ).'_title" class="pr_input" name="'.$this->get_field_name( 'linkurl_'.$i ).'" value="'.$instance[ 'linkurl_'.$i ].'" /></p>';

	$i++;
}
	}


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['block_count'] = $new_instance['block_count'];

		for ( $i = 1; $i <= 4; ) {
			$instance[ 'label_'.$i ] = $new_instance[ 'label_'.$i ];
			$instance[ 'media_image_'.$i ] = $new_instance[ 'media_image_'.$i ];
			$instance[ 'media_alt_'.$i ] = $new_instance[ 'media_alt_'.$i ];
			$instance[ 'iconFont_class_'.$i ] = $new_instance[ 'iconFont_class_'.$i ];
			$instance[ 'iconFont_bgColor_'.$i ] = $new_instance[ 'iconFont_bgColor_'.$i ];
			$instance[ 'summary_'.$i ] = $new_instance[ 'summary_'.$i ];
			$instance[ 'linkurl_'.$i ] = $new_instance[ 'linkurl_'.$i ];
			$i++;
		}
		return $instance;
	}


	function widget( $args, $instance ) {
		echo $args['before_widget'];
		echo PHP_EOL.'<div class="veu_prBlocks row">'.PHP_EOL;

		$widget_block_count = ( isset( $instance['block_count'] )) ? intval( $instance['block_count'] ) : 3;
		
		$col_class = 'col-md-4';
		if( $widget_block_count == 4 ){
			$col_class = 'col-md-3';
		} 
		
		// Print widget area
		for ( $i = 1; $i <= $widget_block_count; ) {
			if ( isset( $instance[ 'label_'.$i ] ) && $instance[ 'label_'.$i ] ) {
				echo '<article class="prArea '.$col_class.'">'.PHP_EOL;
				if ( ! empty( $instance[ 'linkurl_'.$i ] ) ) {
					echo '<a href="'.esc_url( $instance[ 'linkurl_'.$i ] ).'">'.PHP_EOL ;
				}
				// icon font display
				if ( empty( $instance[ 'media_image_'.$i ] ) && ! empty( $instance[ 'iconFont_class_'.$i ] ) ) {
					echo '<div class="circle_icon" style="background-color:'.esc_attr( $instance[ 'iconFont_bgColor_'.$i ] ).'">';
					echo '<i class="fa '.esc_attr( $instance[ 'iconFont_class_'.$i ] ).' font_icon"></i></div>'.PHP_EOL;
					// image display
				} else if ( ! empty( $instance[ 'media_image_'.$i ] ) ) {
					echo '<div class="media_pr">'.PHP_EOL;
					echo '<img class="pr_image" src="'.esc_url( $instance[ 'media_image_'.$i ] ).'" alt="'.esc_attr( $instance[ 'media_alt_'.$i ] ).'" />'.PHP_EOL.'</div><!--//.media_pr -->'.PHP_EOL;
				}
				// title text
				echo '<h1 class="prBox_title">';
				if ( isset( $instance[ 'label_'.$i ] ) && $instance[ 'label_'.$i ] ) {
					echo $instance[ 'label_'.$i ];
				} else {
					_e( 'PR Block', 'vkExUnit' );
				}
				echo '</h1>'.PHP_EOL;
				// summary text
				if ( ! empty( $instance[ 'summary_'.$i ] ) ) {
					echo '<p class="summary">'.nl2br( esc_attr( $instance[ 'summary_'.$i ] ) ).'</p>'.PHP_EOL;
				}
				if ( ! empty( $instance[ 'linkurl_'.$i ] ) ) {
					echo '</a>'.PHP_EOL;
				}

				echo '</article>'.PHP_EOL;
				echo '<!--//.prArea -->'.PHP_EOL;
			}
			$i++;
		}
		echo '</div>' . $args['after_widget'] . PHP_EOL . '<!-- //.pr_blocks -->';
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Widget_vkExUnit_PR_Blocks");' ) );

// uploader js
add_action( 'admin_print_scripts', 'admin_scripts_pr_media' );
function admin_scripts_pr_media() {
	wp_enqueue_media();
	wp_register_script( 'media-pr-blocks', plugin_dir_url( __FILE__ ) . 'js/widget-pr-blocks.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'media-pr-blocks' );
}

// color picker js
add_action( 'admin_enqueue_scripts', 'admin_scripts_pr_color' );
function admin_scripts_pr_color() {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
}

add_action( 'admin_footer-widgets.php', 'print_scripts_pr_color' );
function print_scripts_pr_color() {
	?>
<script type="text/javascript">
(function($){
    function initColorPicker(widget) {
        widget.find( '.color_picker' ).wpColorPicker( {
            change: _.throttle( function() {
                $(this).trigger('change');
            }, 3000 )
        });
    }

    function onFormUpdate(event, widget) {
        initColorPicker(widget);
    }
    $(document).on('widget-added widget-updated', onFormUpdate );
    $(document).ready( function() {
        $('#widgets-right .widget:has(.color_picker)').each( function () {
            initColorPicker( $(this) );
        });
    });
}(jQuery));
</script>
<?php }

// PR blocks admin CSS
add_action( 'admin_print_styles-widgets.php', 'style_prBlocks' );
function style_prBlocks() {
	echo '<style>.media.image_pr{ max-height: 170px; }
.media_img{ max-width: 100%; height: auto; position: relative; z-index: 999;}</style>'.PHP_EOL;
}
