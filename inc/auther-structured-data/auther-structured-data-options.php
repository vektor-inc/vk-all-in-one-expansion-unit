<?php
/**
 * Author Structured Data
 *
 * @package vk-all-in-one-expanaion-unit
 */

/**
 * ユーザー設定に　@typeとsameAsの項目を追加
 */
class author_srtuctured_data_options {

  public function __construct()
  {
    // add_filter( 'user_contactmethods', array( __CLASS__, 'register_structured_data' ) );
    // add_action( 'user_new_form', array( __CLASS__, 'add_new_structured_data' ) );
    add_action( 'show_password_fields', array( __CLASS__, 'add_structured_data')  );
		add_action( 'profile_update', array( __CLASS__, 'update_structured_data' ), 10, 2 );
  }

  /**
	 * Add Author Structure Date
	 */
	public static function add_structured_data( $bool ) {
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
    return $bool;
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
}

new author_srtuctured_data_options();