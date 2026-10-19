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

	/**
	 * Option name that stores the settings of this feature.
	 * この機能の設定を保存する option 名。
	 *
	 * @var string
	 */
	const OPTION_NAME = 'vkExUnit_article_structure_data';

	public function __construct() {
		add_action( 'show_user_profile', array( __CLASS__, 'add_user_meta_structure_data_ui' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'add_user_meta_structure_data_ui' ) );
		add_action( 'profile_update', array( __CLASS__, 'update_author_structure_data' ), 10, 2 );
		add_action( 'wp_head', array( __CLASS__, 'the_article_structure_data' ), 9999 );
		add_action( 'veu_package_init', array( __CLASS__, 'register_setting' ) );
	}

	/**
	 * Register the settings section on the ExUnit main setting page.
	 * ExUnit のメイン設定画面に設定セクションを登録する。
	 *
	 * @return void
	 */
	public static function register_setting() {
		vkExUnit_register_setting(
			__( 'Structured data - Article', 'vk-all-in-one-expansion-unit' ),
			self::OPTION_NAME,
			array( __CLASS__, 'sanitize_options' ),
			array( __CLASS__, 'render_setting_page' )
		);
	}

	/**
	 * Return the default options.
	 * 設定の既定値を返す。
	 *
	 * @return array {
	 *     @type array $exclude_post_types Post type name => true for each excluded post type. 除外する投稿タイプ名 => true の配列。
	 * }
	 */
	public static function get_default_options() {
		return array(
			'exclude_post_types' => array(
				'product' => true,
			),
		);
	}

	/**
	 * Return the saved options merged with the defaults.
	 * 保存済みの設定を既定値とマージして返す。
	 *
	 * The defaults are used only when the option has never been saved.
	 * An empty exclude_post_types saved by unchecking everything is kept as is.
	 * 既定値を使うのは一度も保存されていない場合のみ。
	 * 全部のチェックを外して保存した空の exclude_post_types はそのまま使う。
	 *
	 * @return array Options. 設定値。
	 */
	public static function get_options() {
		$options = get_option( self::OPTION_NAME );
		if ( ! is_array( $options ) ) {
			return self::get_default_options();
		}
		$options = wp_parse_args( $options, self::get_default_options() );
		if ( ! is_array( $options['exclude_post_types'] ) ) {
			$options['exclude_post_types'] = array();
		}
		return $options;
	}

	/**
	 * Return the post type names on which the Article structured data is not output.
	 * Article の構造化データを出力しない投稿タイプ名の配列を返す。
	 *
	 * Attachment is always included regardless of the settings.
	 * 添付ファイル（attachment）は設定に関係なく常に含める。
	 *
	 * @return string[] Post type names. 投稿タイプ名の配列。
	 */
	public static function get_exclude_post_types() {
		$options            = self::get_options();
		$exclude_post_types = array( 'attachment' );
		foreach ( $options['exclude_post_types'] as $post_type => $is_excluded ) {
			if ( $is_excluded ) {
				$exclude_post_types[] = (string) $post_type;
			}
		}
		return array_values( array_unique( $exclude_post_types ) );
	}

	/**
	 * Return the post type names listed as checkboxes on the settings page.
	 * 設定画面にチェックボックスとして表示する投稿タイプ名の配列を返す。
	 *
	 * Public post types excluding page and attachment.
	 * The settings page and sanitize_options() both use this list so that they never disagree.
	 * 公開の投稿タイプから固定ページと添付ファイルを除いたもの。
	 * 画面と保存がずれないよう、設定画面と sanitize_options() の両方でこの一覧を使う。
	 *
	 * @return string[] Post type names. 投稿タイプ名の配列。
	 */
	public static function get_selectable_post_types() {
		$post_types = get_post_types( array( 'public' => true ), 'names' );
		return array_values( array_diff( $post_types, array( 'page', 'attachment' ) ) );
	}

	/**
	 * Sanitize the options submitted from the main setting page.
	 * メイン設定画面から送信された設定値をサニタイズする。
	 *
	 * The main setting page saves all sections at once and passes null when no checkbox of this section is checked.
	 * So only the post types listed on the screen (get_selectable_post_types()) are updated from the input,
	 * and the exclusions of the other post types (e.g. product while WooCommerce is inactive) are kept from the previous options.
	 * The key of a deleted post type stays in the option, but it does not affect the output,
	 * and the exclusion comes back when a post type of the same name is registered again.
	 * メイン設定画面は全セクションを一度に保存し、このセクションのチェックが1つも無いと null が渡る。
	 * そのため入力から更新するのは画面に出している投稿タイプ（get_selectable_post_types()）だけにし、
	 * それ以外の投稿タイプ（WooCommerce 停止中の product など）の除外は以前の設定値から引き継ぐ。
	 * 削除済みの投稿タイプのキーは残るが、出力には影響せず、同名の投稿タイプが復活すれば除外も戻る。
	 *
	 * @param mixed $input 送信された値。 Submitted value.
	 * @return array Sanitized options. サニタイズ後の設定値。
	 */
	public static function sanitize_options( $input ) {
		$output = array(
			'exclude_post_types' => array(),
		);

		$selectable_post_types = self::get_selectable_post_types();

		// 画面に出していない投稿タイプの除外は、以前の設定値（既定値とマージ済み）から引き継ぐ。
		// Keep the exclusions of post types not shown on the screen from the previous options (merged with the defaults).
		$previous = self::get_options();
		foreach ( $previous['exclude_post_types'] as $post_type => $is_excluded ) {
			$post_type = sanitize_key( (string) $post_type );
			if ( '' === $post_type || ! $is_excluded || in_array( $post_type, $selectable_post_types, true ) ) {
				continue;
			}
			$output['exclude_post_types'][ $post_type ] = true;
		}

		if ( ! is_array( $input ) || empty( $input['exclude_post_types'] ) || ! is_array( $input['exclude_post_types'] ) ) {
			return $output;
		}

		// 画面に出している投稿タイプだけを入力から反映する（固定ページ・添付ファイル・非公開・存在しない投稿タイプは捨てる）。
		// Apply the input only for the post types shown on the screen (page, attachment, non-public and unknown post types are discarded).
		foreach ( $input['exclude_post_types'] as $post_type => $value ) {
			$post_type = (string) $post_type;
			if ( ! in_array( $post_type, $selectable_post_types, true ) ) {
				continue;
			}
			if ( $value ) {
				$output['exclude_post_types'][ $post_type ] = true;
			}
		}
		return $output;
	}

	/**
	 * Render the settings section on the ExUnit main setting page.
	 * ExUnit のメイン設定画面に設定セクションを出力する。
	 *
	 * @return void
	 */
	public static function render_setting_page() {
		$options = self::get_options();

		// チェックリストに出す投稿タイプを sanitize_options() と同じ一覧にそろえる。
		// Align the post types in the check list with the list used by sanitize_options().
		$post_types_args = array( 'public' => true );
		$hidden_types    = array_values( array_diff( get_post_types( $post_types_args, 'names' ), self::get_selectable_post_types() ) );
		?>
		<div class="sectionBox">
		<h3><?php esc_html_e( 'Structured data - Article', 'vk-all-in-one-expansion-unit' ); ?></h3>
		<table class="form-table">
		<tr>
		<th><?php esc_html_e( 'Post types to exclude', 'vk-all-in-one-expansion-unit' ); ?></th>
		<td>
		<div class="veu_check_list">
		<?php
		veu_the_post_type_check_list(
			array(
				'post_types_args'    => $post_types_args,
				'name'               => self::OPTION_NAME . '[exclude_post_types]',
				'checked'            => $options['exclude_post_types'],
				'exclude_post_types' => $hidden_types,
			)
		);
		?>
		</div>
		<p class="description">
		<?php esc_html_e( 'Article structured data will not be output on single pages of the checked post types.', 'vk-all-in-one-expansion-unit' ); ?><br>
		<?php esc_html_e( 'Check post types for which another plugin already outputs different structured data (such as Product).', 'vk-all-in-one-expansion-unit' ); ?><br>
		<?php esc_html_e( 'Article structured data is not output on Pages (page), so they are not listed.', 'vk-all-in-one-expansion-unit' ); ?>
		</p>
		</td>
		</tr>
		</table>
		<?php submit_button(); ?>
		</div>
		<?php
	}

	/**
	 * Determine whether to output the Article structured data on the current page.
	 * 表示中のページに Article の構造化データを出力するかどうかを判定する。
	 *
	 * Only singular pages are judged, using the queried object as the post.
	 * Top pages and archives always return false without applying the filter,
	 * so that the first post of the loop is never judged in place of the page.
	 * 判定するのは個別ページだけで、投稿はクエリ対象（get_queried_object()）から取る。
	 * トップページ・アーカイブではループ先頭の投稿で判定しないよう、フィルターを通さずに false を返す。
	 *
	 * @return bool True to output. 出力する場合は true。
	 */
	public static function is_output() {
		// 個別ページ以外では出力しない。
		// Do not output on pages other than singular pages.
		if ( ! is_singular() ) {
			return false;
		}

		// 判定の対象になる投稿が無い場合は出力しない。
		// Do not output when there is no post to judge.
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		// 個別投稿ページで、除外対象の投稿タイプでなければ出力する。
		// Output on single post pages whose post type is not excluded.
		$is_output = is_single() && ! in_array( $post->post_type, self::get_exclude_post_types(), true );

		/**
		 * Filters whether to output the Article structured data.
		 * Article の構造化データを出力するかどうかを絞り込む。
		 *
		 * @param bool    $is_output 出力するかどうか。 Whether to output.
		 * @param WP_Post $post      表示中の投稿。 Post being displayed.
		 */
		return (bool) apply_filters( 'veu_article_structure_data_is_output', $is_output, $post );
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
		// 出力対象のページでなければ配列を作らずに終える。
		// Stop without building the array when the page is not an output target.
		if ( ! self::is_output() ) {
			return;
		}

		// is_output() と同じく、投稿はクエリ対象から取る。
		// Take the post from the queried object, as is_output() does.
		$post = get_queried_object();

		// フィルター後の値が空・配列以外なら出力しない。
		// Do not output when the filtered value is empty or not an array.
		$article_array = self::get_article_structure_array( $post->post_author );
		if ( empty( $article_array ) || ! is_array( $article_array ) ) {
			return;
		}

		echo '<!-- [ VK All in One Expansion Unit Article Structure Data ] -->';
		echo '<script type="application/ld+json">' . wp_kses( json_encode( $article_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), array() ) . '</script>';
		echo '<!-- [ / VK All in One Expansion Unit Article Structure Data ] -->';
	}

	/**
	 * 記事の構造化データの情報を配列で返す
	 *
	 * The array is passed through the veu_article_structure_data filter before being returned.
	 * When the array cannot be built (no post or no author), an empty array is returned without applying the filter.
	 * 返す前に veu_article_structure_data フィルターを通す。
	 * 投稿や著者が無く配列を作れない場合は、フィルターを通さずに空配列を返す。
	 *
	 * @param int|string $author_id 著者のユーザー ID。空の場合は表示中の投稿の著者を使う。 Author user ID. Uses the author of the current post when empty.
	 * @return array $article_array Article structured data, or an empty array. Article の構造化データ、または空配列。
	 */
	public static function get_article_structure_array( $author_id = '' ) {
		global $post;

		if ( ! $author_id ) {
			// 表示中のページの投稿オブジェクトからユーザーIDを取得
			// $post が取得できない（非特異ページ・外部呼び出し）場合は空配列を返す。
			// Return an empty array when $post is unavailable (non-singular pages or external calls).
			if ( ! $post instanceof WP_Post ) {
				return array();
			}
			$author_id = $post->post_author;
		}

		// $author_id = get_the_author_meta('ID');
		if ( ! isset( $author_id ) ) {
			return array();
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

		/**
		 * Filters the Article structured data array.
		 * Article の構造化データの配列を絞り込む。
		 *
		 * Return an empty array to stop the output.
		 * 空配列を返すと出力されない。
		 *
		 * @param array        $article_array Article の構造化データ。 Article structured data.
		 * @param int          $author_id     著者のユーザー ID。 Author user ID.
		 * @param WP_Post|null $post          表示中の投稿、または null。 Post being displayed, or null.
		 */
		$article_array = apply_filters( 'veu_article_structure_data', $article_array, (int) $author_id, $post instanceof WP_Post ? $post : null );

		// フィルターが配列以外を返した場合も、戻り値の型をそろえるため空配列にする。
		// Normalize to an empty array when the filter returns a non-array, to keep the return type.
		if ( ! is_array( $article_array ) ) {
			return array();
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

		// 出力する配列を組み立てる（url は上で必ず補完されるため常に出力する）。
		// Build the output array (url is always filled in above, so it is always output).
		$author_array = array(
			'@type' => self::get_author_schema_type( $author_type ),
		);
		// name・sameAs は空なら schema.org として無意味なキーになるため出力しない。
		// Omit name / sameAs when empty, since empty keys are meaningless for schema.org.
		// 配列など文字列以外が保存されていた場合に文字列変換の警告を出さないよう、is_string() で先に判定する。
		// Check is_string() first so that a non-string value (e.g. an array) does not raise an "Array to string conversion" warning.
		if ( is_string( $author_name ) && '' !== $author_name ) {
			$author_array['name'] = $author_name;
		}
		$author_array['url'] = $author_url;
		if ( is_string( $author_sameAs ) && '' !== $author_sameAs ) {
			$author_array['sameAs'] = $author_sameAs;
		}

		return $author_array;
	}

	/**
	 * 保存されている author_type（小文字）を、構造化データの @type として出力する schema.org の型名に変換する。
	 * 未設定・想定外の値は Organization とする（プロフィール画面の既定表示、および get_author_array() の
	 * url 補完が person 以外をサイトトップ URL で補完していることと揃えるため）。
	 *
	 * Convert the saved author_type (lowercase) into the schema.org type name output as @type in structured data.
	 * An empty or unexpected value falls back to Organization (to match the default shown on the profile screen
	 * and the url fallback in get_author_array(), which uses the site top URL for anything other than person).
	 *
	 * @param mixed $author_type ユーザーメタ author_type の保存値。 The saved value of the author_type user meta.
	 * @return string 'Person' または 'Organization'。 'Person' or 'Organization'.
	 */
	public static function get_author_schema_type( $author_type ) {
		if ( 'person' === $author_type ) {
			return 'Person';
		}
		return 'Organization';
	}
}

new VK_Article_Srtuctured_Data();
