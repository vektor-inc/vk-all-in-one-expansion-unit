<?php
/**
 * Class VEU_Page_Exclude_From_List_Pages
 */

// 本来この VEU_Page_Exclude_From_List_Pages クラスだけで完結するが、ExUnit固有の統合 metabox に表示するためのその他の処理を行っている
require_once dirname( __FILE__ ) . '/class-page-exclude-from-list-pages.php';

class VEU_Metabox_Exclude_From_List_Pages extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'     => VEU_Page_Exclude_From_List_Pages::$metabox_id,
			'title'    => VEU_Page_Exclude_From_List_Pages::$metabox_title,
			'cf_name'  => VEU_Page_Exclude_From_List_Pages::$meta_key,
			'priority' => 60,
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

		$form = '';
		if ( $cf_value ) {
			$checked = ' checked';
		} else {
			$checked = '';
		}

		$label = VEU_Page_Exclude_From_List_Pages::$label;

		$form .= '<ul>';
		$form .= '<li class="vk_checklist_item vk_checklist_item-style-vertical">' . '<input type="checkbox" id="' . esc_attr( $this->args['cf_name'] ) . '" name="' . esc_attr( $this->args['cf_name'] ) . '" value="true"' . $checked . '><label class="vk_checklist_item_label" for="' . esc_attr( $this->args['cf_name'] ) . '"> ' . $label . '</label></li>';
		$form .= '</ul>';

		return $form;
	}

}

$veu_metabox_noindex = new VEU_Metabox_Exclude_From_List_Pages();
