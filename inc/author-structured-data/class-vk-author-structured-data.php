<?php
/**
 * VK_Author_Srtuctured_Data
 *
 * @package vektor-inc/vk-all-in-one-expanaion-unit
 */

/**
 * ユーザー設定に　@typeとsameAsの項目を追加
 */

class VK_Author_Srtuctured_Data {

  public function __construct()
  {
    // add_filter( 'user_contactmethods', array( __CLASS__, 'register_structured_data' ) );
    // add_action( 'user_new_form', array( __CLASS__, 'add_new_structured_data' ) );
    	add_action( 'show_password_fields', array( __CLASS__, 'add_user_meta_structured_data_ui')  );
		add_action( 'profile_update', array( __CLASS__, 'update_structured_data' ), 10, 2 );
		add_action( 'wp_head', array( __CLASS__, 'print_jsonLD_in_head' ), 9999 );
  }

  /**
	 * Add Author Structure Date
	 * @param $bool
	 */
	public static function add_user_meta_structured_data_ui() {
		global $user_id;
		$type = get_user_meta($user_id, 'type', true);
		$sameAs = get_user_meta($user_id, 'sameAs', true);
		?>
		<tr>
		<th><label for='type'>@type</label></th>
		<td>
			<select name='type' id='type'>
			<option value='organization' <?php echo (($type == 'organization')) ? 'selected' : '' ?> >Organization</option>
			<option value='person' <?php echo (($type == 'person')) ? 'selected' : '' ?> >Person</option>
			</select>
		</td>
		</tr>
		<tr>
		<th><label for='sameAs'>sameAs</label></th>
		<td>
			<label><input type='url' name='sameAs' value='<?php echo esc_attr( $sameAs ) ?>'/></label>
		</td>
		</tr>
    <?php
  }

  /**
   * Add New Author Structure Date
   */
  public static function add_new_structured_data(){

    ?>
    <table class='form-table'>
      <tr>
        <th><label for='type'>@type</label></th>
        <td>
          <select name="type" id="type">
            <option value="organization" selected>Organization</option>
            <option value="person">Person</option>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for='sameas'>sameAs</label></th>
        <td>
          <label><input type='url' name='sameAs' value=''/></label>
        </td>
      </tr>
    </table>
    <?php
  }

  /**
   * Update Author Structure Date
   */
  public static function update_structured_data( $user_id, $old_user_data ) {
    if ( isset( $_POST['type'] ) ) {
      update_user_meta( $user_id, 'type', $_POST['type'], $old_user_data->type );
    }
    if ( isset( $_POST['sameAs'] ) ){
      update_user_meta( $user_id, 'sameAs', $_POST['sameAs'], $old_user_data->sameAs );
    }
  }

  /**
   * Register Author Structure Date
   */
  // public static function register_structured_data( $structured_data ) {
  //   $structured_data['type'] = '@type';
  //   $structured_data['sameAs'] = 'sameAs';
  //   return $structured_data;
  // }
  public static function print_jsonLD_in_head() {
	$post_id = get_post(get_the_id());
	$author = get_userdata( $post_id -> post_author );
	$author_id = $author -> ID;
	if( is_single() ){
	  echo veu_asd_generate_jsonLD( $author_id );
	}
  }
  
  public static function veu_asd_generate_jsonLD( $author_id ) {
	$author_id = get_the_author_meta('ID');
	  if ( ! isset( $author_id ) ) {
		  return;
	  }
  
  $author_data = get_userdata( $author_id );
	$author_name = get_user_meta( $author_id, 'nickname', true ) ?: $author_data->display_name;
	$author_type = get_user_meta( $author_id, 'type', true ) ?: 'Organization';
	$sameAs = get_user_meta( $author_id, 'sameAs', true ) ?: '';
  
	$data = [
	  '@type'         => $author_type,
	  'name'          => $author_name,
	  'url'           => home_url( '/' ),
	  'sameAs'        => $sameAs,
	];
  
	  $JSON = '
	<!-- [ VK All in One Expansion Unit Author Structured Data ] -->
  <script type="application/ld+json">
  {
	"@context" : "https://schema.org/",
	"@type" : "Article",
	"author":
	  {
		"@type": "' . esc_attr( $data['@type'] ) .'",
		"name":  "' . esc_attr( $data['name'] ) . '",
		"url": "' . esc_attr( $data['url'] ) . '",
		"sameAs": "' . esc_attr( $data['sameAs'] ) . '"
	  }';
	  $JSON .= '
  }
  </script>
  <!-- [ / VK All in One Expansion Unit Author Structured Data ] -->
  ';
  
	  return $JSON;
  }
}

new VK_Author_Srtuctured_Data();