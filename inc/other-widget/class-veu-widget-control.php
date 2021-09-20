<?php

class VEU_Widget_Control {
	public static function update_options($options) {
		update_option('vkExUnit_enable_widgets', $options, true);
	}

	public static function default_options() {
		$_buf = array();
		foreach(veu_widget_packages() as $v) {
			array_push($_buf, $v['id']);
		}
		return $_buf;
	}

	public static function enable_widget_ids() {
		return get_option('vkExUnit_enable_widgets', self::default_options());
		$mother = veu_get_common_options();
		return $mother['enable_widgets'];
	}

	public static function load_widgets() {
		$enable_packages = self::enable_widget_ids();
		foreach(veu_widget_packages() as $package) {
			if( in_array($package['id'], $enable_packages) ) {
				require_once VEU_DIRECTORY_PATH . '/inc/other-widget/' . $package['include'];
			}
		}
	}

	public static function widgets_init() {
		$enable_packages = self::enable_widget_ids();
		foreach(veu_widget_packages() as $package) {
			if( in_array($package['id'], $enable_packages) ) {
				register_widget($package['class']);
			}
		}
	}
}
