<?php
/**
 * Class VkNavMenuClassCustomTest
 *
 * @package vektor-inc/vk-all-in-one-expansion-unit
 */

class VkNavMenuClassCustomTest extends WP_UnitTestCase {

	function test_class_name_custom() {

		$tests = array(
			array(
				'test_name'  => 'Add class name',
				'content'    => '<div class="aaa">test</div>',
				'class_name' => 'test_class',
				'add'        => true,
				'expected'   => '<div class="aaa test_class">test</div>',
			),
			array(
				'test_name'  => 'Delete class name (最後にあるクラスを削除する場合)',
				'content'    => '<div class="aaa test_class">test</div>',
				'class_name' => 'test_class',
				'add'        => false,
				'expected'   => '<div class="aaa">test</div>',
			),
			array(
				'test_name'  => 'Delete class name (最初にあるクラスを削除する場合)',
				'content'    => '<div class="test_class aaa">test</div>',
				'class_name' => 'test_class',
				'add'        => false,
				'expected'   => '<div class="aaa">test</div>',
			),
			array(
				'test_name'  => 'Delete class name (最初にあるクラスを削除する場合 / 複数のクラスがある場合)',
				'content'    => '<div class="test_class aaa bbb">test</div>',
				'class_name' => 'test_class',
				'add'        => false,
				'expected'   => '<div class="aaa bbb">test</div>',
			),
			array(
				'test_name'  => 'Delete class name (真ん中にあるクラスを削除する場合 / 複数のクラスがある場合)',
				'content'    => '<div class="aaa test_class bbb">test</div>',
				'class_name' => 'test_class',
				'add'        => false,
				'expected'   => '<div class="aaa bbb">test</div>',
			),
		);

		foreach ( $tests as $key => $test_value ) {
			$return = VkNavMenuClassCustom::class_name_custom( $test_value['content'], $test_value['class_name'], $test_value['add'] );

			$this->assertEquals( $test_value['expected'], $return, $test_value['test_name'] );
		}
	}
}
