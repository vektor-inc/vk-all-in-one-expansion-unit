<?php
/**
 * VK_Article_Srtuctured_Data
 *
 * @package vektor-inc/vk-all-in-one-expanaion-unit
 */

/**
 * ユーザー設定に　@typeとsameAsの項目を追加
 */

class VK_Article_Srtuctured_Data {

	public function __construct() {
		add_action( 'show_user_profile', array( __CLASS__, 'add_user_meta_structure_data_ui' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'add_user_meta_structure_data_ui' ) );
		add_action( 'profile_update', array( __CLASS__, 'update_author_structure_data' ), 10, 2 );
		add_action( 'wp_head', array( __CLASS__, 'the_article_structure_data' ), 9999 );
	}

	/**
	 * Add Author Structure Date
	 *
	 * @param $bool
	 */
	public static function add_user_meta_structure_data_ui() {
		global $user_id;
		$author_type   = get_user_meta( $user_id, 'author_type', true ) ?: 'Organization';
		$author_name   = get_user_meta( $user_id, 'author_name', true );
		$author_url    = get_user_meta( $user_id, 'author_url', true );
		$author_sameAs = get_user_meta( $user_id, 'author_sameAs', true );

		?>
<h2 style="margin-top:2em;">
	<?php esc_html_e( 'Author information structured data', 'vk-all-in-one-expansion-unit' ); ?>
</h2>
<table class="form-table">
	<tr>
		<th><label for='author_type'>@type</label></th>
		<td>
			<select name='author_type' id='author_type'>
				<option value='organization' <?php echo ( ( $author_type == 'organization' ) ) ? 'selected' : ''; ?>>
					Organization</option>
				<option value='person' <?php echo ( ( $author_type == 'person' ) ) ? 'selected' : ''; ?>>Person</option>
			</select>
			<p class="discription">
				<?php esc_html_e( 'Select Person if the author is an individual and Organization if the author is an organization.', 'vk-all-in-one-expansion-unit' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><label for='author_name'>name</label></th>
		<td>
			<label><input id='author_name' type='text' name='author_name'
					value='<?php echo esc_attr( $author_name ); ?>' /></label>
			<p class="discription">
				<?php esc_html_e( 'If not entered, the display name on the blog will be used.', 'vk-all-in-one-expansion-unit' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><label for='author_url'>url</label></th>
		<td>
			<label><input id='author_url' type='url' name='author_url'
					value='<?php echo esc_attr( $author_url ); ?>' /></label>
			<p class="discription">
				<?php esc_html_e( 'Enter the URL of this user\'s profile page.', 'vk-all-in-one-expansion-unit' ); ?><br />
				<?php esc_html_e( 'If not entered', 'vk-all-in-one-expansion-unit' ); ?><br />
				<?php esc_html_e( 'If @type is individual', 'vk-all-in-one-expansion-unit' ); ?> :
				<?php esc_html_e( 'The URL of the contributor archive page on this site will be used.', 'vk-all-in-one-expansion-unit' ); ?><br />
				<?php esc_html_e( 'If @type is organization', 'vk-all-in-one-expansion-unit' ); ?> :
				<?php esc_html_e( 'The URL of the top page of this homepage is applied.', 'vk-all-in-one-expansion-unit' ); ?><br />
				<?php esc_html_e( '* The URL of the site specified in the contact information of the user profile is not reflected in the url.', 'vk-all-in-one-expansion-unit' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><label for='author_sameAs'>sameAs</label></th>
		<td>
			<label><input id='author_sameAs' type='url' name='author_sameAs'
					value='<?php echo esc_attr( $author_sameAs ); ?>' /></label>
			<p class="discription">
				<?php esc_html_e( 'Specify the profile URL of SNS, Wikipedia, etc.', 'vk-all-in-one-expansion-unit' ); ?></p>
		</td>
	</tr>
</table>
<?php
	}

	/**
	 * Update Author Structure Date
	 */
	public static function update_author_structure_data( $user_id, $old_user_data ) {
		if ( isset( $_POST['author_type'] ) ) {
			update_user_meta( $user_id, 'author_type', $_POST['author_type'], $old_user_data->author_type );
		}
		if ( isset( $_POST['author_name'] ) ) {
			update_user_meta( $user_id, 'author_name', $_POST['author_name'], $old_user_data->author_name );
		}
		if ( isset( $_POST['author_url'] ) ) {
			update_user_meta( $user_id, 'author_url', $_POST['author_url'], $old_user_data->author_url );
		}
		if ( isset( $_POST['author_sameAs'] ) ) {
			update_user_meta( $user_id, 'author_sameAs', $_POST['author_sameAs'], $old_user_data->author_sameAs );
		}
	}

	/**
	 * Print Article Structure Data
	 *
	 * @return void
	 */
	public static function the_article_structure_data() {
		global $post;
		if ( is_single() ) {
			$author_id = $post->post_author;
			$article_array = self::get_article_structure_array( $author_id );
			if ( $article_array && is_array( $article_array ) ) {
				echo '<!-- [ VK All in One Expansion Unit Article Structure Data ] -->';
                echo '<script type="application/ld+json">' . wp_kses( json_encode( $article_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), array() ) . '</script>';
				echo '<!-- [ / VK All in One Expansion Unit Article Structure Data ] -->';
			}
		}
	}

	/**
	 * 記事の構造化データの情報を配列で返す
	 *
	 * @return array $article_array
	 */
	public static function get_article_structure_array( $author_id = '' ) {

		if ( ! $author_id ) {
			// 表示中のページの投稿オブジェクトからユーザーIDを取得
			global $post;
			$author_id = $post->post_author;
		}

		// $author_id = get_the_author_meta('ID');
		if ( ! isset( $author_id ) ) {
			return;
		}

		// $author_type = get_user_meta( $author_id, 'author_type', true );

		if ( is_singular() ) {
			if ( has_post_thumbnail() ) {
				$image_url = get_the_post_thumbnail_url();
			} else {
				$image_url = '';
			};
			$post_title = get_the_title();
		}

		$article_array = array(
			'@context'      => 'https://schema.org/',
			'@type'         => 'Article',
			'headline'      => $post_title,
			'image'         => $image_url,
			'datePublished' => get_the_time( 'c' ),
			'dateModified'  => get_the_modified_time( 'c' ),
			'author'        => self::get_author_array( $author_id ),
		// Google側で必須事項ではなく要件が不明確なのでコメントアウト。
		// "publisher"        => array(
		// "@context"    => "http://schema.org",
		// "@type"       => $author_type,
		// "name"        => get_bloginfo( 'name' ),
		// "description" => get_bloginfo( 'description' ),
		// "logo"        => array(
		// "@type" => "ImageObject",
		// "url"   => get_custom_logo(),
		// ),
		// ),
		);

		return $article_array;
	}

	/**
	 * ユーザー設定ページに登録されている情報を元に著者情報を配列で返す
	 *
	 * @param int $author_id
	 * @return array $author_array
	 */
	public static function get_author_array( $author_id = '' ) {

		if ( ! $author_id ) {
			// 表示中のページの投稿オブジェクトからユーザーIDを取得
			global $post;
			$author_id = $post->post_author;
		}

		// $author_id = get_the_author_meta('ID');
		if ( ! isset( $author_id ) ) {
			return;
		}

		$author      = get_userdata( $author_id );
		$author_type = get_user_meta( $author_id, 'author_type', true );
		$author_name = get_user_meta( $author_id, 'author_name', true ) ?: $author->display_name;
		$author_url  = get_user_meta( $author_id, 'author_url', true ) ?: home_url( '/' );
		if ( 'person' === $author_type ) {
			$author_url = get_user_meta( $author_id, 'author_url', true ) ?: get_author_posts_url( $author_id );
		}
		$author_sameAs = get_user_meta( $author_id, 'author_sameAs', true );

		$author_array = array(
			'@type'  => $author_type,
			'name'   => $author_name,
			'url'    => $author_url,
			'sameAs' => $author_sameAs,
		);

		return $author_array;
	}
}

new VK_Article_Srtuctured_Data();
