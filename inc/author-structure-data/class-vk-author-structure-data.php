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
    add_action( 'show_user_profile', array( __CLASS__, 'add_user_meta_structure_data_ui') );
    add_action( 'edit_user_profile', array( __CLASS__, 'add_user_meta_structure_data_ui') );
		add_action( 'profile_update', array( __CLASS__, 'update_structure_data' ), 10, 2 );
		add_action( 'wp_head', array( __CLASS__, 'print_jsonLD_in_head' ), 9999 );
  }

  /**
	 * Add Author Structure Date
	 * @param $bool
	 */
	public static function add_user_meta_structure_data_ui() {
		global $user_id;
		$author_type = get_user_meta($user_id, 'author_type', true) ?: 'Organization';
    $author_name = get_user_meta($user_id, 'author_name', true);
    $author_url = get_user_meta($user_id, 'author_url', true);
		$author_sameAs = get_user_meta($user_id, 'author_sameAs', true);

		?>
    <h2>構造化データ</h2>
    <table class="form-table">
      <tr>
        <th><label for='author_type'>@type</label></th>
        <td>
          <select name='author_type' id='author_type'>
          <option value='organization' <?php echo (( $author_type == 'organization' )) ? 'selected' : '' ?> >Organization</option>
          <option value='person' <?php echo (( $author_type == 'person' )) ? 'selected' : '' ?> >Person</option>
          </select>
          <p class="discription">著者が個人のときは Person を、著者が組織のときは Organization を選択してください。</p>
        </td>
      </tr>
      <tr>
        <th><label for='author_name'>name</label></th>
        <td>
          <label><input id='author_name' type='text' name='author_name' value='<?php echo esc_attr( $author_name ) ?>'/></label>
          <p class="discription">未入力の場合、ブログ上の表示名が使用されます。</p>
        </td>
      </tr>
      <tr>
        <th><label for='author_url'>url</label></th>
        <td>
          <label><input id='author_url' type='url' name='author_url' value='<?php echo esc_attr( $author_url ) ?>'/></label>
          <p class="discription">未入力の場合、このホームページのURLが使用されます。</p>
        </td>
      </tr>
      <tr>
        <th><label for='author_sameAs'>sameAs</label></th>
        <td>
          <label><input id='author_sameAs' type='url' name='author_sameAs' value='<?php echo esc_attr( $author_sameAs ) ?>'/></label>
          <p class="discription">SNSやWikipediaなどのプロフィールURLを指定します。</p>
        </td>
      </tr>
    </table>
    <?php
  }

  /**
   * Update Author Structure Date
   */
  public static function update_structure_data( $user_id, $old_user_data ) {
    if ( isset( $_POST['author_type'] ) ) {
      update_user_meta( $user_id, 'author_type', $_POST['author_type'], $old_user_data->author_type );
    }
    if ( isset( $_POST['author_name'] ) ){
      update_user_meta( $user_id, 'author_name', $_POST['author_name'], $old_user_data->author_name );
    }
    if ( isset( $_POST['author_url'] ) ){
      update_user_meta( $user_id, 'author_url', $_POST['author_url'], $old_user_data->author_url );
    }
    if ( isset( $_POST['author_sameAs'] ) ){
      update_user_meta( $user_id, 'author_sameAs', $_POST['author_sameAs'], $old_user_data->author_sameAs );
    }
  }

  /**
   * json-LD
   */
  public static function print_jsonLD_in_head() {
    $post_id = get_post(get_the_id());
    $author = get_userdata( $post_id -> post_author );
    $author_id = $author -> ID;
    if( is_single() ){
      echo self::veu_generate_author_jsonLD( $author_id );
    }
  }

  public static function veu_generate_author_jsonLD( $author_id ) {
    $author_id = get_the_author_meta('ID');
      if ( ! isset( $author_id ) ) {
        return;
      }

    $author = get_userdata( $author_id );
    $author_type = get_user_meta( $author_id, 'author_type', true );
    $author_name = get_user_meta( $author_id, 'author_name', true ) ?: $author->display_name;
    $author_url = get_user_meta( $author_id, 'author_url', true ) ?: home_url( '/' );
    $author_sameAs = get_user_meta( $author_id, 'author_sameAs', true );

    if( is_singular() ) {
      if( has_post_thumbnail()){
        $image_url = get_the_post_thumbnail_url();
      } else {
        $image_url = '';
      };
      $post_title = get_the_title();
    }

    $json_ld = array(
      "@context" => "https://schema.org/",
      "@type" => "Article",
      "headline" => $post_title,
      "image" => array(
        $image_url,
      ),
      "datePublished"    => get_the_time( 'c' ),
      "dateModified"     => get_the_modified_time( 'c' ),
      "author"           => array(
        "@type" => $author_type,
        "name" => $author_name,
        "url" => $author_url,
        "sameAs" => $author_sameAs
      ),
      "publisher"        => array(
        "@context"    => "http://schema.org",
        "@type"       => $author_type,
        "name"        => get_bloginfo( 'name' ),
        "description" => get_bloginfo( 'description' ),
        "logo"        => array(
          "@type" => "ImageObject",
          "url"   => get_custom_logo(),
        ),
      ),
    );

    echo '<script type="application/ld+json">' . json_encode( $json_ld , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>';
  }
}

new VK_Author_Srtuctured_Data();