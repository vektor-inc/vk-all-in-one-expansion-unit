<?php

class vExUnit_AdWords {
    // singleton instance
    private static $instance;
 
    public static function instance() {
        if ( isset( self::$instance ) )
            return self::$instance;
 
        self::$instance = new vExUnit_AdWords;
        self::$instance->run_init();
        return self::$instance;
    }
 
    private function __construct() {
    }
 

    protected function run_init() {
        add_action('admin_init', array($this, 'option_init' ));
        add_filter('the_content',    array($this, 'set_content' ), 1);
    }
 

    public function option_init() {
        vkExUnit_register_setting(
            __('AdWords', 'vkExUnit'),        // tab label.
            'vkExUnit_AdWords',             // name attr
            array( $this, 'sanitize_config' ),      // sanitaise function name
            array( $this, 'render_configPage' )     // setting_page function name
        );
    }
    

    public function set_content($content){
        $option = $this->get_option();

        $content = preg_replace('/(<span id="more-[0-9]+"><\/span>)/', '$1'.$this->render_ad($option['more']) , $content);

        $content .= $this->render_ad($option['after'],'after');

        return $content;
    }


    private function render_ad( $ads ,$area='more'){
        if( !$ads[0] ) return '';
        $class = "col-md-12";
        if( $ads[1] ) $class="col-md-6";

        $content = '';
        $content .= '<div class="row vkExUnit_AdWord '.$area.'">';
        foreach($ads as $ad){
            if(!$ad) break;

            $content .= '<div class="'.$class.'">';
            $content .= $ad;
            $content .= '</div>';
        }
        $content .= '</div>';

       return $content; 
    }


    public function sanitize_config( $option ){
        if( !$option['more'][0] && $option['more'][1] ){
            $option['more'][0] = $option['more'][1];
            $option['more'][1] = '';
        }
        if( !$option['more'][1] ) unset( $option['more'][1] );

        if( !$option['after'][0] && $option['after'][1] ){
            $option['after'][0] = $option['after'][1];
            $option['after'][1] = '';
        }
        if( !$option['after'][1] ) unset( $option['more'][1] );

        return $option;
    }


    public static function get_option(){
        return get_option( 'vkExUnit_AdWords', array('more'=>array(''),'after'=>array('')) );
    }


    public function render_configPage(){
        $option = $this->get_option();
?>
<h3><?php _e('Meta Keyword', 'vkExUnit'); ?></h3>
<div id="vkExUnit_AdWords" class="sectionBox">
<table class="form-table">
<tr><th><?php _e('AdWords', 'vkExUnit'); ?><br/><br/>
<?php _e('Insert Adsense to more tag and after content.', 'vkExUnit'); ?><br/>
<?php _e('If you want separate adwords area, you fill tow fields.', 'vkExUnit'); ?>
</th>
<td style="max-width:80em;">
    <dl>
        <dt><label for="ad_content_moretag"><?php _e('insert the AdWords [ more tag ]', 'vkExUnit'); ?></label></dt>
        <dd>
        <textarea rows="5" name="vkExUnit_AdWords[more][]" id="ad_content_moretag" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['more'][0] ) && $option['more'][0] )? $option['more'][0]: ''; ?></textarea>
        <br/>
        <textarea rows="5" name="vkExUnit_AdWords[more][]" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['more'][1] ) && $option['more'][1] )? $option['more'][1]: ''; ?></textarea>
        </dd>
    </dl>
    <dl>
        <dt><label for="ad_content_after"><?php _e('insert the AdWords [ after content ]', 'vkExUnit'); ?></label></dt>
        <dd>
        <textarea rows="5" name="vkExUnit_AdWords[after][]" id="ad_content_after" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['after'][0] ) && $option['after'][0] )? $option['after'][0]: ''; ?></textarea>
        <br/>
        <textarea rows="5" name="vkExUnit_AdWords[after][]" value="" style="width:100%;max-width:50em;" /><?php echo (isset( $option['after'][1] ) && $option['after'][1] )? $option['after'][1]: ''; ?></textarea>
        </dd>
    </dl>
</td></tr></table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存"  /></p>
</div>
<?php
    }

}
 
vExUnit_AdWords::instance();