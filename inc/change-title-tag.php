<?
/**
 * Change Title Tag
 *
 * @package VK All in One Expansion Unit
 */
if ( ! class_exists( 'VEU_Metabox' ) ) {
	return;
}

class VEU_Metabox_Title_Tag extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => 'veu_title_tag',
			'cf_name'  => 'vkExUnit_title_tag',
			'title'    => __( 'Title Tag', 'vk-all-in-one-expansion-unit' ),
			'priority' => 50,
		);

		parent::__construct( $this->args );

	}

	/**
	 * metabox_body_form
	 * Form inner
	 *
	 * @return [type] [description]
	 */
	public function metabox_body_form( $cf_value ) {
		global $post;
		$post_meta = get_post_meta( $post->ID, $this->args['cf_name'], true );
		$title     = ! empty( $post_meta['title'] ) ? $post_meta['title'] : '';
		$checked   = checked( ! empty( $post_meta['add_site_title'] ), true, false );

		$form  = '';
		$form .= '<input type="text" name="' . esc_attr( $this->args['cf_name'] ) . '[title]" value="' . esc_attr( $title ) . '" />';
		$form .= '<p>' . __( 'If there is any input here, the input will be reflected in the title tag..', 'vk-all-in-one-expansion-unit' ) . '</p>';
		$form .= '<p>' . __( 'Please note that the notation on the page will not be rewritten.', 'vk-all-in-one-expansion-unit' ) . '</p>';
		$form .= '<label>';
		$form .= '<input type="checkbox" name="' . esc_attr( $this->args['cf_name'] ) . '[add_site_title]" ' . $checked . ' />';
		$form .= __( 'Add Separator and Site Title', 'vk-all-in-one-expansion-unit' );
		$form .= '</label>';

		return $form;
	}

} // class VEU_Metabox_Title_Tag {

$VEU_Metabox_Title_Tag = new VEU_Metabox_Title_Tag();