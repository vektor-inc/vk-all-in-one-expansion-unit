<?php
/**
 * Default Thumbnail test
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Default Thumbnail test.
 */
class DefaultThumbnailTest extends WP_UnitTestCase {

	protected $default_thumbnail_attachment_id;
	protected $post_thumbnail_attachment_id;

	protected $post_id_no_thumbnail;
	protected $post_id_with_thumbnail;

	/**
	 * Set up the test environment.
	 */
	function setUp(): void {
		parent::setUp();

		$factory = new WP_UnitTest_Factory();

		// プラグインディレクトリ内の既存の画像ファイルを使用
		$plugin_dir = dirname( __DIR__ );

		// WP_UnitTest_Factoryを使ってアタッチメントを作成
		$this->default_thumbnail_attachment_id = $factory->attachment->create_upload_object( $plugin_dir . '/screenshot-1.png' );
		$this->post_thumbnail_attachment_id    = $factory->attachment->create_upload_object( $plugin_dir . '/screenshot-2.png' );

		// サムネイルなしの記事を作成
		$this->post_id_no_thumbnail = $factory->post->create(
			array(
				'post_title'   => 'No Thumbnail',
				'post_content' => 'No Thumbnail Content',
			)
		);

		// サムネイルありの記事を作成
		$this->post_id_with_thumbnail = $factory->post->create(
			array(
				'post_title'   => 'With Thumbnail',
				'post_content' => 'With Thumbnail Content',
			)
		);

		// 投稿にサムネイルを明示的に設定
		update_post_meta( $this->post_id_with_thumbnail, '_thumbnail_id', $this->post_thumbnail_attachment_id );
	}

	/**
	 * Enable default thumbnail by setting the option.
	 */
	function enable_default_thumbnail() {
		update_option(
			'veu_defualt_thumbnail',
			array(
				'default_thumbnail_image' => $this->default_thumbnail_attachment_id,
			)
		);
	}

	/**
	 * Disable default thumbnail by deleting the option.
	 */
	function disable_default_thumbnail() {
		delete_option( 'veu_defualt_thumbnail' );
	}

	function test_post_thumbnail_html() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_post_thumbnail_html' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// --------------------------------
		// デフォルトサムネイル無効化
		// --------------------------------
		$this->disable_default_thumbnail();

		// サムネイルなしの記事のサムネイルHTMLを取得
		$html_no_thumbnail = get_the_post_thumbnail( $this->post_id_no_thumbnail, 'full' );
		// サムネイルなしの記事のサムネイルHTMLは空
		$this->assertEmpty( $html_no_thumbnail );

		// サムネイルありの記事のサムネイルHTMLを取得
		$html_with_thumbnail = get_the_post_thumbnail( $this->post_id_with_thumbnail, 'full' );
		// サムネイルありの記事のサムネイルHTMLは空ではない
		$this->assertNotEmpty( $html_with_thumbnail );
		// サムネイルありの記事のサムネイルHTMLは記事に設定した画像のURLを含む
		$this->assertStringContainsString( wp_get_attachment_url( $this->post_thumbnail_attachment_id ), $html_with_thumbnail );

		// --------------------------------
		// デフォルトサムネイル有効化
		// --------------------------------
		$this->enable_default_thumbnail();

		// サムネイルなしの記事のサムネイルHTMLを取得
		$html_no_thumbnail = get_the_post_thumbnail( $this->post_id_no_thumbnail, 'full' );
		// サムネイルなしの記事のサムネイルHTMLは空ではない
		$this->assertNotEmpty( $html_no_thumbnail );
		// サムネイルなしの記事のサムネイルHTMLはデフォルトサムネイルのURLを含む
		$this->assertStringContainsString( wp_get_attachment_url( $this->default_thumbnail_attachment_id ), $html_no_thumbnail );

		// サムネイルありの記事のサムネイルHTMLを取得
		$html_with_thumbnail = get_the_post_thumbnail( $this->post_id_with_thumbnail, 'full' );
		// サムネイルありの記事のサムネイルHTMLは空ではない
		$this->assertNotEmpty( $html_with_thumbnail );
		// サムネイルありの記事のサムネイルHTMLは記事に設定した画像のURLを含む
		$this->assertStringContainsString( wp_get_attachment_url( $this->post_thumbnail_attachment_id ), $html_with_thumbnail );
	}

	function test_has_post_thumbnail() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_has_post_thumbnail' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// --------------------------------
		// デフォルトサムネイル無効化
		// --------------------------------
		$this->disable_default_thumbnail();

		// サムネイルなしの記事はサムネイルなし
		$this->assertFalse( has_post_thumbnail( $this->post_id_no_thumbnail ) );
		// サムネイルありの記事はサムネイルあり
		$this->assertTrue( has_post_thumbnail( $this->post_id_with_thumbnail ) );

		// --------------------------------
		// デフォルトサムネイル有効化
		// --------------------------------
		$this->enable_default_thumbnail();

		// サムネイルなしの記事はサムネイルあり
		$this->assertTrue( has_post_thumbnail( $this->post_id_no_thumbnail ) );
		// サムネイルありの記事はサムネイルあり
		$this->assertTrue( has_post_thumbnail( $this->post_id_with_thumbnail ) );
	}

	function test_post_thumbnail_id() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_post_thumbnail_id' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// --------------------------------
		// デフォルトサムネイル無効化
		// --------------------------------
		$this->disable_default_thumbnail();

		// サムネイルなしの記事のサムネイルIDは0
		$this->assertEquals( 0, get_post_thumbnail_id( $this->post_id_no_thumbnail ) );

		// サムネイルありの記事のサムネイルIDは記事に設定した画像のID
		$this->assertEquals( $this->post_thumbnail_attachment_id, get_post_thumbnail_id( $this->post_id_with_thumbnail ) );

		// --------------------------------
		// デフォルトサムネイル有効化
		// --------------------------------
		$this->enable_default_thumbnail();

		// サムネイルなしの記事のサムネイルIDはデフォルトサムネイルのID
		$this->assertEquals( $this->default_thumbnail_attachment_id, get_post_thumbnail_id( $this->post_id_no_thumbnail ) );

		// サムネイルありの記事のサムネイルIDは記事に設定した画像のID
		$this->assertEquals( $this->post_thumbnail_attachment_id, get_post_thumbnail_id( $this->post_id_with_thumbnail ) );
	}
}
