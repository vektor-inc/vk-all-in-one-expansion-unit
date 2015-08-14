<?php
class vExUnit_call_responce {
    // singleton instance
    private static $instance;

    public static $posttype_name = 'cta';

    public $content_number = 500;

    public static function instance() {
        if ( isset( self::$instance ) )
            return self::$instance;

        self::$instance = new vExUnit_call_responce;
        self::$instance->run_init();
        return self::$instance;
    }


    private function __construct() {
        /***    do noting    ***/
    }


    protected function run_init() {
        add_action( 'init', array($this, 'set_posttype') );
        add_action( 'admin_init', array($this, 'option_init') );
        add_action( 'admin_menu', array($this, 'add_custom_field') );
        add_action( 'save_post', array($this, 'save_custom_field') );
        add_filter( 'the_content', array($this, 'content_filter'), $this->content_number, 1 );
    }


    public function option_init() {
        vkExUnit_register_setting(
            'CTA',                                  // tab label.
            'vkExUnit_cta_settings',                // name attr
            array( $this, 'sanitize_config' ),      // sanitaise function name
            array( $this, 'render_configPage' )     // setting_page function name
        );
    }


    public function set_posttype(){
        $labels = array(
            'name'          => 'CTA',
            'singular_name' => 'CTA',
            'edit_item'     => __('new CTA', 'vkExUnit'),
            'add_new_item'  => __('add new CTA', 'vkExUnit'),
            'new_item'      => __('new CTA', 'vkExUnit'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'has_archive'         => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'query_var'           => true,
            'rewrite'             => true,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'taxonomies'          => array(),
            'supports'            => array( 'title' ),
        );
        register_post_type( self::$posttype_name , $args );
    }


    public function add_custom_field(){
        $post_types = get_post_types( array( '_builtin' => false, 'public' => true ) );
        while( list($key, $post ) = each( $post_types ) ){
            add_meta_box('vkExUnit_cta', __('Call to Action setting', 'vkExUnit'), array( $this, 'render_meta_box' ), $post, 'normal', 'high');
        }
        add_meta_box('vkExUnit_cta', __('Call to Action setting', 'vkExUnit'), array( $this, 'render_meta_box' ), 'page', 'normal', 'high');
        add_meta_box('vkExUnit_cta', __('Call to Action setting', 'vkExUnit'), array( $this, 'render_meta_box' ), 'post', 'normal', 'high');

        add_meta_box('vkExUnit_cta_url', __('URL setting', 'vkExUnit'), array( $this, 'render_meta_box_cta' ), self::$posttype_name, 'normal', 'high');
    }


    public function render_meta_box(){
        echo '<input type="hidden" name="_nonce_vkExUnit_custom_cta" id="_nonce_vkExUnit__custom_field_metaKeyword" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';

        $ctas    = self::get_ctas(true, '  - ');
        array_unshift( $ctas, array( 'key' => 0, 'label' => __('Follow common setting', 'vkExUnit') ) );
        $ctas[]  = array( 'key' => 'disable', 'label' => __('Disable display', 'vkExUnit') );
        $now     = get_post_meta(get_the_id(),'vkexunit_cta_each_option', true);
        ?>
<input type="hidden" name="_vkExUnit_cta_switch" value="cta_number" />
<table class="form-table"><tr>
<th><?php _e('Post each setting.', 'vkExUnit'); ?></th>
<td><select name="vkexunit_cta_each_option" id="vkexunit_cta_each_option">
<?php foreach($ctas as $cta): ?>
    <option value="<?php echo $cta['key'] ?>" <?php echo($cta['key'] == $now)? 'selected':''; ?> ><?php echo $cta['label'] ?></option>
<?php endforeach; ?>
</select></td></tr></table>
        <?php
    }


    public function render_meta_box_cta(){
        echo '<input type="hidden" name="_nonce_vkExUnit_custom_cta" id="_nonce_vkExUnit__custom_field_metaKeyword" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
        $imgid = get_post_meta(get_the_id(), 'vkExUnit_cta_img', true);
        $cta_image = wp_get_attachment_image_src($imgid);
        $image_position = get_post_meta(get_the_id(), 'vkExUnit_cta_img_position', true);
        ?>
<style>
#message.updated a {display:none;}
#thumbnail_box { max-width:300px; max-height:300px; }
#cta-thumbnail_image { max-width:300px; max-height:300px; }
#cta-thumbnail_image.noimage { display:none; }
#cta-thumbnail_control.add #media_thumb_url_add { display:inline; }
#cta-thumbnail_control.add #media_thumb_url_change,
#cta-thumbnail_control.add #media_thumb_url_remove { display:none; }
#cta-thumbnail_control.change #media_thumb_url_add { display:none; }
#cta-thumbnail_control.change #media_thumb_url_change,
#cta-thumbnail_control.change #media_thumb_url_remove { display:inline; }
</style>
<script type="text/javascript">
jQuery(document).ready(function($){
    var custom_uploader;
    jQuery('.cta-media_btn').click(function(e) {
        e.preventDefault();

        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media({
            title: 'Choose Image',
            library: {type: 'image'},
            button: {text: 'Choose Image'},
            multiple: false,
        });

        custom_uploader.on('select', function() {
            var images = custom_uploader.state().get('selection');
            images.each(function(file){
                jQuery('#cta-thumbnail_image').attr('src', file.toJSON().url).removeClass("noimage");
                jQuery('.vkExUnit_cta_img').val(file.toJSON().id);
                jQuery('#cta-thumbnail_control').removeClass("add").addClass("change");
            });
        });
        custom_uploader.open();
    });
    jQuery('#cta-thumbnail_control #media_thumb_url_remove').on('click', function(){
        jQuery('#cta-thumbnail_image').attr('src', '').addClass("noimage");
        jQuery('.vkExUnit_cta_img').val('');
        jQuery('#cta-thumbnail_control').removeClass("change").addClass("add");
        return false;
    });
});
</script>
<input type="hidden" name="_vkExUnit_cta_switch" value="cta_content" />
<table class="form-table">
<tr>
<th><?php _e('main image', 'vkExUnit'); ?></th>
<td>
    <div id="cta-thumbnail_box" >
        <img id="cta-thumbnail_image" src="<?php echo ($cta_image)? $cta_image[0] : ''; ?>" class="<?php echo ($cta_image)? '' : 'noimage'; ?>" />
    </div>
    <div id="cta-thumbnail_control" class="<?php echo ($cta_image)? 'change' : 'add'; ?>">
        <button id="media_thumb_url_add" class="cta-media_btn"><?php _e('add image', 'vkExUnit'); ?></button>
        <button id="media_thumb_url_change" class="cta-media_btn"><?php _e('change image', 'vkExUnit'); ?></button>
        <button id="media_thumb_url_remove" class=""><?php _e('remove image', 'vkExUnit'); ?></button>
    </div>
    <input type="hidden" name="vkExUnit_cta_img" class="vkExUnit_cta_img" value="<?php echo $imgid; ?>" />
</td>
</tr>
<tr><th><label for="vkExUnit_cta_img_position"><?php _e('image position', 'vkExUnit'); ?></label></th>
<td>
    <select name="vkExUnit_cta_img_position" id="vkExUnit_cta_img_position">
        <option value="right" <?php echo ($image_position == 'right')? 'selected' : ''; ?> ><?php _e('right', 'vkExUnit'); ?></option>
        <option value="center" <?php echo ($image_position == 'center')? 'selected' : ''; ?> ><?php _e('center', 'vkExUnit'); ?></option>
        <option value="left" <?php echo ($image_position == 'left')? 'selected' : ''; ?> ><?php _e('left', 'vkExUnit'); ?></option>
    </select>
</td></tr>
<tr><th>
<label for="vkExUnit_cta_url_title"><?php _e('url title', 'vkExUnit'); ?></label></th><td>
<input type="text" name="vkExUnit_cta_url_title" id="vkExUnit_cta_url_title" value="<?php echo get_post_meta(get_the_id(), 'vkExUnit_cta_url_title', true); ?>" />
</td></tr><tr><th>
<label for="vkExUnit_cta_url"><?php _e('url', 'vkExUnit'); ?></label></th><td>
<input type="url" name="vkExUnit_cta_url" id="vkExUnit_cta_url" placeholder="http://" value="<?php echo get_post_meta(get_the_id(), 'vkExUnit_cta_url', true); ?>" />
</td></tr>
<tr><th><label for="vkExUnit_cta_text"><?php _e('text', 'vkExUnit'); ?>
</th>
<td>
<textarea name="vkExUnit_cta_text" id="vkExUnit_cta_text" rows="10em" cols="50em"><?php echo get_post_meta(get_the_id(), 'vkExUnit_cta_text', true); ?></textarea>
</td></tr>
</table>
<a href="<?php echo admin_url('admin.php?page=vkExUnit_main_setting#vkExUnit_cta_settings'); ?>"><?php _e('cta setting', 'vkExUnit'); ?></a>
        <?php
    }


    public function save_custom_field( $post_id ){
        if( !isset( $_POST['_vkExUnit_cta_switch'] ) ) return $post_id;
        $noonce = isset($_POST['_nonce_vkExUnit_custom_cta']) ? htmlspecialchars($_POST['_nonce_vkExUnit_custom_cta']) : null;

        // if autosave is to deny
        if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
           return $post_id;

        if(!wp_verify_nonce($noonce, plugin_basename(__FILE__))){
            return $post_id;
        }

        if( $_POST['_vkExUnit_cta_switch'] == 'cta_number' ){
            $data = $_POST['vkexunit_cta_each_option'];

            if(get_post_meta($post_id, 'vkexunit_cta_each_option') == ""){
                add_post_meta($post_id, 'vkexunit_cta_each_option', $data, true);
            }elseif($data != get_post_meta($post_id, 'vkexunit_cta_each_option', true)){
                update_post_meta($post_id, 'vkexunit_cta_each_option', $data);
            }elseif(!$data){
                delete_post_meta($post_id, 'vkexunit_cta_each_option', get_post_meta($post_id, 'vkexunit_cta_each_option', true));
            }
            return $post_id;
        }
        elseif( $_POST['_vkExUnit_cta_switch'] == 'cta_content' ){
            $data = $_POST['vkExUnit_cta_img'];
            if(get_post_meta($post_id, 'vkExUnit_cta_img') == ""){
                add_post_meta($post_id, 'vkExUnit_cta_img', $data, true);
            }elseif($data != get_post_meta($post_id, 'vkExUnit_cta_img', true)){
                update_post_meta($post_id, 'vkExUnit_cta_img', $data);
            }elseif(!$data){
                delete_post_meta($post_id, 'vkExUnit_cta_img', get_post_meta($post_id, 'vkExUnit_cta_img', true));
            }

            $data = $_POST['vkExUnit_cta_img_position'];
            if(get_post_meta($post_id, 'vkExUnit_cta_img_position') == ""){
                add_post_meta($post_id, 'vkExUnit_cta_img_position', $data, true);
            }elseif($data != get_post_meta($post_id, 'vkExUnit_cta_img_position', true)){
                update_post_meta($post_id, 'vkExUnit_cta_img_position', $data);
            }elseif(!$data){
                delete_post_meta($post_id, 'vkExUnit_cta_img_position', get_post_meta($post_id, 'vkExUnit_cta_img_position', true));
            }

            $data = stripslashes($_POST['vkExUnit_cta_url_title']);
            if(get_post_meta($post_id, 'vkExUnit_cta_url_title') == ""){
                add_post_meta($post_id, 'vkExUnit_cta_url_title', $data, true);
            }elseif($data != get_post_meta($post_id, 'vkExUnit_cta_url_title', true)){
                update_post_meta($post_id, 'vkExUnit_cta_url_title', $data);
            }elseif(!$data){
                delete_post_meta($post_id, 'vkExUnit_cta_url_title', get_post_meta($post_id, 'vkExUnit_cta_url_title', true));
            }

            $data = $_POST['vkExUnit_cta_url'];

            if(get_post_meta($post_id, 'vkExUnit_cta_url') == ""){
                add_post_meta($post_id, 'vkExUnit_cta_url', $data, true);
            }elseif($data != get_post_meta($post_id, 'vkExUnit_cta_url', true)){
                update_post_meta($post_id, 'vkExUnit_cta_url', $data);
            }elseif(!$data){
                delete_post_meta($post_id, 'vkExUnit_cta_url', get_post_meta($post_id, 'vkExUnit_cta_url', true));
            }

            $data = stripslashes($_POST['vkExUnit_cta_text']);
            if(get_post_meta($post_id, 'vkExUnit_cta_text') == ""){
                add_post_meta($post_id, 'vkExUnit_cta_text', $data, true);
            }elseif($data != get_post_meta($post_id, 'vkExUnit_cta_text', true)){
                update_post_meta($post_id, 'vkExUnit_cta_text', $data);
            }elseif(!$data){
                delete_post_meta($post_id, 'vkExUnit_cta_text', get_post_meta($post_id, 'vkExUnit_cta_text', true));
            }
            return $post_id;
        }
    }


    public static function get_cta_post( $id ){
        $args = array(
            'post_type' => self::$posttype_name,
            'p' => $id,
            'post_count' => 1
        );
        $query = new WP_Query( $args );
        if( !$query->post_count ) return null;

        return $query->posts[0];
    }


    public static function render_cta_content( $id ){
        if( !$id ) return '';
        $post = self::get_cta_post($id);
        if( !$post ) return '';

        include vkExUnit_get_directory() . '/plugins/call_to_action/view.actionbox.php';
        return $content;
    }


    public function is_cta_id( $id=null ){
        if( !$id ) $id = get_the_id();
        if( !$id ) return null;

        $post_config = get_post_meta( $id, 'vkexunit_cta_each_option', true );

        if( $post_config ){
            if( $post_config == 'disable' ) return null;
            return $post_config;
        }

        $post_type = get_post_type( $id );
        $option = self::get_option();
        if( isset( $option[$post_type] ) && is_numeric( $option[$post_type] ) ) return $option[$post_type] ;
        return null;
    }


    public function content_filter( $content ){
        $content .= self::render_cta_content( $this->is_cta_id() );
        return $content;
    }


    public function sanitize_config( $input ){
        $posttypes = array_merge( array( 'post'=>'post', 'page'=>'page' ), get_post_types( array( 'public'=>true, '_builtin'=>false ), 'names' ) );
        $option = get_option( 'vkExUnit_cta_settings' );
        if( !$option ) $current_option = self::get_default_option();

        while(list($key, $value) = each($input)){
            $option[$key] = ( is_numeric( $value ) )? $value : 0 ;
        }
        return $option;
    }


    public static function get_default_option(){
        $option = array();
        $posttypes = array_merge( array( 'post'=>'post', 'page'=>'page'), get_post_types( array( 'public'=>true, '_builtin'=>false ), 'names' ));
        while( list($key, $posttype) = each( $posttypes ) ){
            $option[ $posttype ] = false;
        }
        return $option;
    }


    public static function get_option( $show_label=false ){
        $default = self::get_default_option();
        $option = get_option( 'vkExUnit_cta_settings' );

        if( !$option || !is_array($option) ) return $default;

        $posttypes = array_merge( array( 'post'=>'post', 'page'=>'page' ), get_post_types( array( 'public'=>true, '_builtin'=>false ), 'names' ));

        $output_option = array();
        while(list($key, $value) = each($posttypes)){
            $output_option[$value] = ( isset( $option[$value] ) )? $option[$value] : $default[$value];
        }

        return $output_option;
    }


    public function get_ctas( $show_label=false, $head='' ){
        $args = array(
            'post_type' => self::$posttype_name,
        );
        $query = new WP_Query($args);
        $ctas = array();
        while( list($key, $post) = each( $query->posts ) ){
            if($show_label){
                $ctas[] = array(
                    'key'   => $post->ID,
                    'label' => $head . $post->post_title
                );
            }else{
                $ctas[] = $post->ID;
            }
        }
        return $ctas;
    }


    public function render_configPage(){
        $options = self::get_option();
        $ctas    = self::get_ctas(true, '  - ');
        array_unshift( $ctas, array( 'key' => 0, 'label' => __('Disable display', 'vkExUnit') ) );

        include vkExUnit_get_directory() . '/plugins/call_to_action/view.adminsetting.php';
    }
}
