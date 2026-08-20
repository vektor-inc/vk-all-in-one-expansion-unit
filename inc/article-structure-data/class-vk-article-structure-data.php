<?php
/**
 * VK_Article_Srtuctured_Data
 *
 * @package vektor-inc/vk-all-in-one-expanaion-unit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	 *
	 * @param int     $user_id       更新されたユーザーの ID。 The ID of the updated user.
	 * @param WP_User $old_user_data 更新前のユーザーデータオブジェクト。 The user data object before the update.
	 * @return void
	 */
	public static function update_author_structure_data( $user_id, $old_user_data ) {

		// profile_update は管理画面のユーザー編集画面（本体 wp-admin/user-edit.php が
		// check_admin_referer() で nonce 検証済み）だけでなく、nonce を伴わない経路からも発火する
		// （REST API の PUT/POST /wp/v2/users/<id>（Application Password 認証時は nonce なし。かつ
		// application/x-www-form-urlencoded で送信すれば $_POST も実際に埋まる）、XML-RPC の
		// wp.editProfile、WP-CLI の wp user update、reset_password() 等）。
		// そのためここでの防御層は nonce 検証ではなく capability チェックとする。
		// This hook (profile_update) fires not only from the admin user-edit screen (where core,
		// wp-admin/user-edit.php, has already verified the nonce via check_admin_referer()) but also
		// from routes that carry no nonce at all (the REST API's PUT/POST /wp/v2/users/<id> — no nonce
		// when authenticated via an Application Password, and $_POST is actually populated when the
		// request uses application/x-www-form-urlencoded —, XML-RPC's wp.editProfile, WP-CLI's
		// wp user update, reset_password(), etc.).
		// So the defense-in-depth layer used here is a capability check, not a nonce check.
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- 上記の理由により、この関数では nonce ではなく current_user_can( 'edit_user', $user_id ) で担保する。
		if ( isset( $_POST['author_type'] ) && is_string( $_POST['author_type'] ) ) {
			$author_type = sanitize_text_field( wp_unslash( $_POST['author_type'] ) );
			// 保存できるのは select の選択肢（organization / person）のみ。
			// 想定外の値が届いた場合は保存せず、既存の保存値をそのまま維持する（表示中の select・出力側 get_author_array() の @type 判定を壊さないため）。
			// Only the select's choices (organization / person) may be saved.
			// If an unexpected value arrives, skip saving it and keep the existing stored value
			// (so the select UI currently displayed and the @type check in get_author_array() are not broken).
			if ( in_array( $author_type, array( 'organization', 'person' ), true ) ) {
				update_user_meta( $user_id, 'author_type', $author_type, $old_user_data->author_type );
			}
		}
		if ( isset( $_POST['author_name'] ) && is_string( $_POST['author_name'] ) ) {
			update_user_meta( $user_id, 'author_name', sanitize_text_field( wp_unslash( $_POST['author_name'] ) ), $old_user_data->author_name );
		}
		if ( isset( $_POST['author_url'] ) && is_string( $_POST['author_url'] ) ) {
			// trim() は WPCS のサニタイズ関数として認識されないため、この行のみ抑制する。
			// ここで得た値は「空欄クリアかどうかの判定」と、esc_url_raw() へ渡す前に前後の空白を落とす前処理
			// （末尾の空白が esc_url_raw() によって %20 に変換されて保存されてしまう不具合を防ぐため）にのみ使い、
			// この値自体を保存・出力することはない。保存する値は必ず esc_url_raw() を通した結果を使う。
			// trim() is not recognized as a WPCS sanitizing function, so only this line is suppressed.
			// The value obtained here is used only to detect an intentional clear and to strip leading/trailing
			// whitespace before passing it to esc_url_raw() (to prevent a bug where a trailing space gets
			// converted into %20 and saved as part of the URL); this value itself is never saved or output.
			// The value actually saved always goes through esc_url_raw().
			$author_url_trimmed = trim( wp_unslash( $_POST['author_url'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- 中間値であり保存・出力に使わない。trim() を先に適用するための処理で、直後に esc_url_raw() を通す。
			self::update_author_url_meta(
				$user_id,
				'author_url',
				esc_url_raw( $author_url_trimmed ),
				$author_url_trimmed,
				$old_user_data->author_url
			);
		}
		if ( isset( $_POST['author_sameAs'] ) && is_string( $_POST['author_sameAs'] ) ) {
			// UI（add_user_meta_structure_data_ui()）は type='url' の単一入力欄で、出力側 get_author_array() も
			// 単一の URL 文字列として sameAs にそのまま出力するため、esc_url_raw() で URL としてサニタイズする（author_url と同じ扱い）。
			// The UI (add_user_meta_structure_data_ui()) is a single type='url' input field, and get_author_array()
			// also outputs it as a single URL string in sameAs, so sanitize it as a URL with esc_url_raw() (same handling as author_url).
			$author_sameas_trimmed = trim( wp_unslash( $_POST['author_sameAs'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- 中間値であり保存・出力に使わない。trim() を先に適用するための処理で、直後に esc_url_raw() を通す（author_url と同じ理由）。
			self::update_author_url_meta(
				$user_id,
				'author_sameAs',
				esc_url_raw( $author_sameas_trimmed ),
				$author_sameas_trimmed,
				$old_user_data->author_sameAs
			);
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * URL 系のユーザーメタ（author_url / author_sameAs）を、サニタイズ済みの値で保存する。
	 *
	 * 送信値の前後の空白は、esc_url_raw() に渡す前に呼び出し元で trim() 済み（$trimmed_value）。
	 * これは、末尾の空白を esc_url_raw() がそのまま %20 に変換して保存してしまう不具合を避けるため。
	 *
	 * 送信値が空でないのに esc_url_raw() が空文字を返すのは、許可されないスキーム（javascript: 等）で
	 * 弾かれたケース。これを「意図的な空欄クリア」（送信値自体が空文字）と区別するため、trim() のみ行った
	 * 結果（$trimmed_value）が空文字かどうかで判定する。sanitize_text_field() は使わない。
	 * sanitize_text_field() は <script>...</script> 等のタグを中身ごと除去してしまうため、実際には非空の
	 * 送信値（許可されないスキーム等で esc_url_raw() に弾かれた不正値）を「意図的な空欄クリア」と誤判定し、
	 * 既存の保存値を消してしまう不具合があった。
	 * 不正値（$sanitized_value が空文字 かつ 意図的な空欄クリアではない）のときは保存せず既存の保存値を
	 * 維持する（author_type で「正当な理由なく既存値を上書きしない」とした判断と揃えている）。
	 *
	 * Save a URL-type user meta (author_url / author_sameAs) with its already-sanitized value.
	 *
	 * Leading/trailing whitespace in the submitted value is already trim()'d by the caller before being
	 * passed to esc_url_raw() ($trimmed_value). This avoids a bug where esc_url_raw() would otherwise
	 * convert a trailing space into %20 and save it as part of the URL.
	 *
	 * When esc_url_raw() returns an empty string even though the submitted value was not empty, it means
	 * the value was rejected (e.g. a disallowed scheme such as javascript:). To distinguish this from an
	 * intentional clear (the submitted value itself is empty), this checks whether the trim()-only result
	 * ($trimmed_value) is empty. sanitize_text_field() is deliberately not used here: it strips tags such as
	 * <script>...</script> together with their content, which previously caused an actually non-empty
	 * submission (an invalid value rejected by esc_url_raw(), e.g. a disallowed scheme) to be misdetected as
	 * an intentional clear, silently discarding the existing value without saving anything.
	 * When the value is rejected ($sanitized_value is empty and this is not an intentional clear), it is not
	 * saved and the existing value is kept (consistent with the same policy applied to author_type: never
	 * overwrite an existing value without good reason).
	 *
	 * @param int    $user_id         更新対象のユーザー ID。 The ID of the user being updated.
	 * @param string $meta_key        更新するメタキー（author_url または author_sameAs）。 The meta key to update (author_url or author_sameAs).
	 * @param string $sanitized_value trim() 後に esc_url_raw() を通した値。 The value after trim() followed by esc_url_raw().
	 * @param string $trimmed_value   trim( wp_unslash( $_POST[...] ) ) の結果（空欄クリアの判定用）。 The result of trim( wp_unslash( $_POST[...] ) ) (used to detect an intentional clear).
	 * @param mixed  $prev_value      update_user_meta() に渡す更新前の値。 The previous value passed to update_user_meta().
	 * @return void
	 */
	private static function update_author_url_meta( $user_id, $meta_key, $sanitized_value, $trimmed_value, $prev_value ) {
		$is_intentional_clear = ( '' === $trimmed_value );
		if ( '' !== $sanitized_value || $is_intentional_clear ) {
			update_user_meta( $user_id, $meta_key, $sanitized_value, $prev_value );
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
			$author_id     = $post->post_author;
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
			// $post が取得できない（非特異ページ・外部呼び出し）場合は空配列を返す。
			// Return an empty array when $post is unavailable (non-singular pages or external calls).
			if ( ! $post instanceof WP_Post ) {
				return array();
			}
			$author_id = $post->post_author;
		}

		// $author_id = get_the_author_meta('ID');
		if ( ! isset( $author_id ) ) {
			return;
		}

		// $author_type = get_user_meta( $author_id, 'author_type', true );

		// アイキャッチ画像を ImageObject 形式の配列で取得する（未設定や URL 取得失敗時は空配列）。
		// Get the featured image as an ImageObject-formatted array (empty array when unset or when the URL cannot be retrieved).
		$image = array();
		// 非特異ページでは is_singular() ブロックが実行されず $post_title が未定義になるため初期化しておく。
		// Initialize $post_title because the is_singular() block does not run on non-singular pages, which would leave it undefined.
		$post_title = '';
		if ( is_singular() ) {
			$image      = self::get_article_image_object();
			$post_title = get_the_title();
		}

		$article_array = array(
			'@context'      => 'https://schema.org/',
			'@type'         => 'Article',
			'headline'      => $post_title,
			'image'         => $image,
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

		// アイキャッチ未設定や URL 取得失敗時は image が空配列になるため、image キーを除去する。
		// 空の image は構造化データとして無意味なため、未設定時はキー自体を含めない。
		// Remove the image key when the featured image is unset or its URL could not be retrieved (image is an empty array).
		// An empty image is meaningless for structured data, so omit the key entirely when it is unset.
		if ( empty( $article_array['image'] ) ) {
			unset( $article_array['image'] );
		}

		return $article_array;
	}

	/**
	 * アイキャッチ画像を ImageObject 形式の配列で返す
	 *
	 * 元画像（フル解像度）の URL と実寸を取得し、@type / url / width / height を持つ配列を返す。
	 * url が取得できない場合は空配列を返し、呼び出し元で image キーを省略させる。
	 * width / height が取得できない場合は、実寸と異なる値を出さないために該当キーを含めない。
	 *
	 * Return the featured image as an ImageObject-formatted array.
	 * It retrieves the URL and the actual dimensions of the original (full-resolution) image
	 * and returns an array with @type / url / width / height.
	 * When the URL cannot be retrieved, an empty array is returned so the caller omits the image key.
	 * When width / height cannot be retrieved, those keys are omitted so as not to output dimensions that differ from the actual size.
	 *
	 * @return array ImageObject 形式の配列、または取得できない場合は空配列。
	 */
	private static function get_article_image_object() {

		// アイキャッチが未設定なら空配列を返す。
		// Return an empty array when no featured image is set.
		if ( ! has_post_thumbnail() ) {
			return array();
		}

		// アイキャッチの添付ファイル ID を取得する。
		// Get the attachment ID of the featured image.
		$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		if ( ! $thumbnail_id ) {
			return array();
		}

		// 元画像（フル解像度）の URL・実寸を取得する。戻り値は [0]=url, [1]=width, [2]=height。
		// Retrieve the URL and the actual dimensions of the original (full-resolution) image. The return value is [0]=url, [1]=width, [2]=height.
		$image_src = wp_get_attachment_image_src( $thumbnail_id, 'full' );

		// 取得失敗（false）や URL が空の場合は image を出さないために空配列を返す。
		// Return an empty array so the image is not output when retrieval fails (false) or the URL is empty.
		if ( ! is_array( $image_src ) || empty( $image_src[0] ) ) {
			return array();
		}

		$image = array(
			'@type' => 'ImageObject',
			'url'   => $image_src[0],
		);

		// width / height が取得できた場合のみ追加する。実寸と異なる値は出さない。
		// Add width / height only when they are available. Never output dimensions that differ from the actual size.
		if ( ! empty( $image_src[1] ) && ! empty( $image_src[2] ) ) {
			$image['width']  = $image_src[1];
			$image['height'] = $image_src[2];
		}

		return $image;
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
			// Get the user ID from the post object of the page being displayed.
			global $post;
			// global $post が null だったり post_author を持たない文脈では Warning を出さずに早期 return する。
			// docblock の @return array に合わせ、空配列を返して型契約を守る。
			// Bail out without raising a warning when global $post is null or has no post_author in the current context.
			// Return an empty array to honor the @return array contract in the docblock.
			if ( ! isset( $post->post_author ) ) {
				return array();
			}
			$author_id = $post->post_author;
		}

		// $author_id = get_the_author_meta('ID');
		if ( ! isset( $author_id ) ) {
			return;
		}

		// 存在しないユーザーIDの場合 get_userdata() は false を返すため、display_name 参照前にガードする。
		// get_userdata() returns false for a non-existent user ID, so guard before referencing display_name.
		$author      = get_userdata( $author_id );
		$author_type = get_user_meta( $author_id, 'author_type', true );
		$author_name = get_user_meta( $author_id, 'author_name', true ) ?: ( $author ? $author->display_name : '' );
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
