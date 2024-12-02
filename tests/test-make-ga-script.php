<?php
/**
 * Class MakeGAScriptTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */
/*
cd $(wp plugin path --dir vk-all-in-one-expansion-unit)
bash bin/install-wp-tests.sh wordpress_test root 'WordPress' localhost latest
 */

/**
 * GA Script Test Case.
 */
class MakeGAScriptTest extends WP_UnitTestCase {

	function test_make_ga_script() {

		$tests = array(
			// 古いオプションを使用していて値に G- 指定で gtag 指定だった場合
			// 新しい gtag のみ表示
			array(
				'option'  => array(
					'gaId'   => 'G-XXXXXXXXXX',
					'gaType' => 'gaType_gtag',
				),
				'correct' => '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'G-XXXXXXXXXX\');</script>',
			),
			// 古いオプションを使用していて値に U- 指定で UA 指定だった場合
			// 新しい gtag のみ表示
			array(
				'option'  => array(
					'gaId'   => 'UA-XXXXXXXX-XX',
					'gaType' => 'gaType_universal',
				),
				'correct' => '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXXXXX-XX"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'UA-XXXXXXXX-XX\');</script>',
			),
			// 古いオプションを使用していて値に UA- も G- もない場合
			array(
				'option'  => array(
					'gaId' => 'XXXXXXXX-XX',
				),
				'correct' => '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXXXXX-XX"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'UA-XXXXXXXX-XX\');</script>',
			),
			// 古いオプションを使用していて値に UA- がある場合
			array(
				'option'  => array(
					'gaId' => 'UA-XXXXXXXX-XX',
				),
				'correct' => '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXXXXX-XX"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'UA-XXXXXXXX-XX\');</script>',
			),
			// 古いオプションを使用していて値に G- がある場合
			array(
				'option'  => array(
					'gaId' => 'G-XXXXXXXXXX',
				),
				'correct' => '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'G-XXXXXXXXXX\');</script>',
			),
			// 新しいオプションを使用していて値に UA- がある場合
			array(
				'option'  => array(
					'gaId-UA' => 'UA-XXXXXXXX-XX',
				),
				'correct' => '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXXXXX-XX"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'UA-XXXXXXXX-XX\');</script>',
			),
			//
			// since 9.82.0.0
			// 新しいオプションを使用していて値に G- がある場合
			array(
				'option'  => array(
					'gaId-GA4' => 'G-XXXXXXXXXX',
				),
				'correct' => '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'G-XXXXXXXXXX\');</script>',
			),
			// 新しいオプションを使用していて値に G- と UA- がある場合
			array(
				'option'  => array(
					'gaId-GA4' => 'G-XXXXXXXXXX',
					'gaId-UA'  => 'UA-XXXXXXXX-XX',
				),
				'correct' => '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'G-XXXXXXXXXX\');gtag(\'config\', \'UA-XXXXXXXX-XX\');</script>',
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_make_ga_script' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $tests as $key => $test_value ) {
			update_option( 'vkExUnit_ga_options', $test_value['option'] );

			$return = make_ga_script();

			// PHPunit
			// print 'correct ::::' . $test_value['correct'] . PHP_EOL;
			// print 'return  ::::' . $return . PHP_EOL;
			// print PHP_EOL;
			$this->assertEquals( $test_value['correct'], $return );
			delete_option( 'vkExUnit_ga_options' );
		}
		delete_option( 'vkExUnit_ga_options' );
	}
}
