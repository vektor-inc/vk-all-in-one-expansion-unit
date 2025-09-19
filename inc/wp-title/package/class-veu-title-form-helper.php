<?php
/**
 * Title Form Helper
 *
 * @package VK All in One Expansion Unit
 */

class VEU_Title_Form_Helper {

	/**
	 * タイトルタグフォームのデータを準備
	 *
	 * @param array $meta_data
	 * @return array
	 */
	private static function prepare_form_data( $meta_data ) {
		return array(
			'title'   => ! empty( $meta_data['title'] ) ? $meta_data['title'] : '',
			'checked' => checked( ! empty( $meta_data['add_site_title'] ), true, false ),
		);
	}

	/**
	 * フォームの共通部分をレンダリング
	 *
	 * @param string $meta_key
	 * @param array  $form_data
	 * @param string $input_id
	 * @param string $input_class
	 * @return string
	 */
	private static function render_form_fields( $meta_key, $form_data, $input_id = '', $input_class = '' ) {
		$id_attr    = $input_id ? ' id="' . esc_attr( $input_id ) . '"' : '';
		$class_attr = $input_class ? ' class="' . esc_attr( $input_class ) . '"' : '';

		$form  = '';
		$form .= '<input type="text" name="' . esc_attr( $meta_key ) . '[title]" value="' . esc_attr( $form_data['title'] ) . '"' . $id_attr . $class_attr . ' />';
		$form .= '<p>' . __( 'If there is any input here, the input will be reflected in the title tag.', 'vk-all-in-one-expansion-unit' ) . '</p>';
		$form .= '<p>' . __( 'Please note that the notation on the page will not be rewritten.', 'vk-all-in-one-expansion-unit' ) . '</p>';
		$form .= '<label>';
		$form .= '<input type="checkbox" name="' . esc_attr( $meta_key ) . '[add_site_title]" ' . $form_data['checked'] . ' />';
		$form .= __( 'Add Separator and Site Title', 'vk-all-in-one-expansion-unit' );
		$form .= '</label>';
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
		$form_fields = self::render_form_fields( $meta_key, $form_data, $input_id, 'regular-text' );

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
