<?php

require_once( VEU_DIRECTORY_PATH . '/admin/class-veu-metabox.php' );

class VEU_Metabox_Insert_Items extends VEU_Metabox {

	public function __construct( $args = array() ) {

		$this->args = array(
			'slug'       => 'veu_insert_items',
			'cf_name'    => '',
			'title'      => __( 'Setting of insert items', 'vk-all-in-one-expansion-unit' ),
			'priority'   => 10,
			'post_types' => array( 'page' ),
		);

		parent::__construct( $this->args );

	}

	public function metabox_body( $display = true ) {
		do_action( 'veu_metabox_insert_items' );
	}


} // class VEU_Metabox_Insert_Items {

$veu_metabox_insert_items = new VEU_Metabox_Insert_Items();
