<?php
/**
 * Title Form Helper
 *
 * @package VK All in One Expansion Unit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VEU_Title_Form_Helper {

	/**
	 * タイトルタグフォームのデータを準備
	 *
	 * @param array $meta_data
	 * @return array
	 */
	private static function prepare_form_data( $meta_data ) {
		return array(
			'title'                   => ! empty( $meta_data['title'] ) ? $meta_data['title'] : '',
			'checked'                 => checked( ! empty( $meta_data['add_site_title'] ), true, false ),
			// Data saved before the reflect_heading key existed has no key, so it is treated as OFF.
			// reflect_heading キーが無い既存データはキー無しのため OFF として扱う。
			'reflect_heading_checked' => checked( ! empty( $meta_data['reflect_heading'] ), true, false ),
		);
	}

	/**
	 * フォームの共通部分をレンダリング
	 *
	 * @param string $meta_key
	 * @param array  $form_data
	 * @param string $input_id
	 * @param string $input_class
	 * @param bool   $is_taxonomy true: term edit screen (adds the heading checkbox) / false: post meta box and block editor panel.
	 *                            true: タームの編集画面（見出し反映チェックボックスを出す） / false: 投稿のメタボックス・ブロックエディタパネル.
	 * @return string
	 */
	private static function render_form_fields( $meta_key, $form_data, $input_id = '', $input_class = '', $is_taxonomy = false ) {
		$id_attr    = $input_id ? ' id="' . esc_attr( $input_id ) . '"' : '';
		$class_attr = $input_class ? ' class="' . esc_attr( $input_class ) . '"' : '';

		$form  = '';
		$form .= '<input type="text" name="' . esc_attr( $meta_key ) . '[title]" value="' . esc_attr( $form_data['title'] ) . '"' . $id_attr . $class_attr . ' />';
		$form .= '<p>' . __( 'If there is any input here, the input will be reflected in the title tag.', 'vk-all-in-one-expansion-unit' ) . '</p>';
		// The post form output must stay byte-for-byte identical, so the note is only skipped on the term screen.
		// 投稿側の出力を一切変えないため、注意書きを省くのはタームの画面だけにする。
		if ( ! $is_taxonomy ) {
			$form .= '<p>' . __( 'Please note that the notation on the page will not be rewritten.', 'vk-all-in-one-expansion-unit' ) . '</p>';
		}
		$form .= '<label>';
		$form .= '<input type="checkbox" name="' . esc_attr( $meta_key ) . '[add_site_title]" ' . $form_data['checked'] . ' />';
		$form .= __( 'Add Separator and Site Title', 'vk-all-in-one-expansion-unit' );
		$form .= '</label>';

		// Term edit screen only: checkbox to also reflect the input in the archive page heading.
		// タームの編集画面のみ: アーカイブページの見出しにも反映するチェックボックスを追加。
		if ( $is_taxonomy ) {
			$form .= self::render_reflect_heading_field( $meta_key, $form_data );
		}
		return $form;
	}

	/**
	 * Render the "Also reflect in the archive page heading" checkbox and its description for the term edit screen.
	 * タームの編集画面用に「アーカイブページの見出しにも反映する」チェックボックスと説明文をレンダリング
	 *
	 * @param string $meta_key  Term meta key. / ターム メタキー.
	 * @param array  $form_data Prepared form data. / 整形済みフォームデータ.
	 * @return string HTML of the checkbox and description. / チェックボックスと説明文の HTML.
	 */
	private static function render_reflect_heading_field( $meta_key, $form_data ) {
		$checkbox_id      = $meta_key . '_reflect_heading';
		$description_id   = $checkbox_id . '_description';
		$og_title_note_id = $checkbox_id . '_og_title_description';

		$form  = '<p>';
		$form .= '<label for="' . esc_attr( $checkbox_id ) . '">';
		$form .= '<input type="checkbox" id="' . esc_attr( $checkbox_id ) . '" name="' . esc_attr( $meta_key ) . '[reflect_heading]" value="1" aria-describedby="' . esc_attr( $description_id . ' ' . $og_title_note_id ) . '"' . $form_data['reflect_heading_checked'] . ' />';
		$form .= esc_html__( 'Also reflect in the archive page heading', 'vk-all-in-one-expansion-unit' );
		$form .= '</label>';
		$form .= '</p>';

		// The description is fixed text that holds for both ON and OFF, so it never contradicts an unsaved checkbox state.
		// 保存前にチェックを操作しても表示と状態が食い違わないよう、ON/OFF どちらでも成り立つ固定の文にしている。
		$form .= '<p id="' . esc_attr( $description_id ) . '" class="description">';
		$form .= esc_html__( 'If checked, the heading is also replaced with the entered text (the separator and site title are not added).', 'vk-all-in-one-expansion-unit' );
		$form .= ' ' . esc_html__( 'If unchecked, the heading remains the term name.', 'vk-all-in-one-expansion-unit' );
		$form .= '</p>';
		$form .= '<p id="' . esc_attr( $og_title_note_id ) . '" class="description">';
		$form .= esc_html__( 'The title used when shared on social media (og:title) will also use the entered text.', 'vk-all-in-one-expansion-unit' );
		$form .= ' ' . esc_html__( 'Depending on your theme, it may not be reflected.', 'vk-all-in-one-expansion-unit' );
		$form .= '</p>';

		return $form;
	}

	/**
	 * 投稿用のタイトルタグフォームをレンダリング
	 *
	 * @param string $meta_key
	 * @param array  $post_meta
	 * @return string
	 */
	public static function render_post_form( $meta_key, $post_meta ) {
		$form_data = self::prepare_form_data( $post_meta );
		return self::render_form_fields( $meta_key, $form_data );
	}

	/**
	 * タクソノミー用のタイトルタグフォームをレンダリング
	 *
	 * @param string $meta_key
	 * @param array  $term_meta
	 * @return string
	 */
	public static function render_taxonomy_form_row( $meta_key, $term_meta ) {
		$form_data   = self::prepare_form_data( $term_meta );
		$input_id    = $meta_key . '_title';
		$form_fields = self::render_form_fields( $meta_key, $form_data, $input_id, 'regular-text', true );

		ob_start();
		?>
		<tr class="form-field veu_form-field_section">
			<th scope="row" valign="top">
				<label style="word-break: keep-all;word-break: auto-phrase;" for="<?php echo esc_attr( $input_id ); ?>">
					<?php _e( 'Head Title', 'vk-all-in-one-expansion-unit' ); ?>
				</label>
			</th>
			<td>
				<?php echo $form_fields; ?>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
} // class VEU_Title_Form_Helper {
