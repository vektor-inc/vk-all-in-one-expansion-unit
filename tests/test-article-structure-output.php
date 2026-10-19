<?php
/**
 * Tests for the output target, filters and settings of the Article structured data.
 * Article の構造化データの出力対象・フィルター・設定のテスト。
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Class Article_Structure_Output_Test
 */
class Article_Structure_Output_Test extends WP_UnitTestCase {

	/**
	 * Test post IDs keyed by target name.
	 * 対象名をキーにしたテスト用投稿 ID。
	 *
	 * @var int[]
	 */
	private $post_ids = array();

	/**
	 * Register the test post types and create the test posts.
	 * テスト用の投稿タイプを登録し、テスト用の投稿を作成する。
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		// WooCommerce の商品とカスタム投稿タイプに見立てた投稿タイプを登録する。
		// Register post types that stand in for WooCommerce products and a custom post type.
		register_post_type( 'product', array( 'public' => true ) );
		register_post_type( 'event', array( 'public' => true ) );

		// 各投稿タイプの投稿と添付ファイルを作成する。
		// Create a post of each post type and an attachment.
		$author_id      = self::factory()->user->create( array( 'role' => 'editor' ) );
		$this->post_ids = array(
			'post'    => self::factory()->post->create(
				array(
					'post_type'   => 'post',
					'post_author' => $author_id,
				)
			),
			'event'   => self::factory()->post->create(
				array(
					'post_type'   => 'event',
					'post_author' => $author_id,
				)
			),
			'product' => self::factory()->post->create(
				array(
					'post_type'   => 'product',
					'post_author' => $author_id,
				)
			),
			'page'    => self::factory()->post->create(
				array(
					'post_type'   => 'page',
					'post_author' => $author_id,
				)
			),
		);

		$this->post_ids['attachment'] = self::factory()->attachment->create(
			array(
				'post_parent'    => $this->post_ids['post'],
				'post_mime_type' => 'image/jpeg',
				'post_author'    => $author_id,
			)
		);

		delete_option( VK_Article_Srtuctured_Data::OPTION_NAME );
	}

	/**
	 * Unregister the test post types and remove the option.
	 * テスト用の投稿タイプの登録と option を削除する。
	 *
	 * @return void
	 */
	public function tear_down() {
		unregister_post_type( 'product' );
		unregister_post_type( 'event' );
		delete_option( VK_Article_Srtuctured_Data::OPTION_NAME );
		parent::tear_down();
	}

	/**
	 * Return the URL of a test target.
	 * テスト対象の URL を返す。
	 *
	 * @param string $target 対象名（'home' または $this->post_ids のキー）。 Target name ('home' or a key of $this->post_ids).
	 * @return string URL.
	 */
	private function get_target_url( $target ) {
		if ( 'home' === $target ) {
			return home_url( '/' );
		}
		return get_permalink( $this->post_ids[ $target ] );
	}

	/**
	 * Test the_article_structure_data().
	 *
	 * @return void
	 */
	public function test_the_article_structure_data() {

		$test_cases = array(
			// 正常系: 既存どおり出力される。
			// Normal cases: output as before.
			array(
				'test_condition_name' => '設定なしで投稿の個別ページ => 出力される',
				'conditions'          => array(),
				'target'              => 'post',
				'expected'            => true,
			),
			array(
				'test_condition_name' => '設定なしでカスタム投稿タイプの個別ページ => 出力される',
				'conditions'          => array(),
				'target'              => 'event',
				'expected'            => true,
			),
			// 出力しないページ。
			// Pages without output.
			array(
				'test_condition_name' => '設定なしで固定ページ => 出力されない',
				'conditions'          => array(),
				'target'              => 'page',
				'expected'            => false,
			),
			array(
				'test_condition_name' => '設定なしでトップページ => 出力されない',
				'conditions'          => array(),
				'target'              => 'home',
				'expected'            => false,
			),
			array(
				'test_condition_name' => '設定なしで product の個別ページ => 既定で除外されるため出力されない',
				'conditions'          => array(),
				'target'              => 'product',
				'expected'            => false,
			),
			array(
				'test_condition_name' => '設定なしで添付ファイルのページ => 出力されない',
				'conditions'          => array(),
				'target'              => 'attachment',
				'expected'            => false,
			),
			// 設定による除外。
			// Exclusion by the settings.
			array(
				'test_condition_name' => 'event を除外する設定で event の個別ページ => 出力されない',
				'conditions'          => array(
					'option' => array( 'exclude_post_types' => array( 'event' => true ) ),
				),
				'target'              => 'event',
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'event だけを除外する設定で product の個別ページ => 出力される',
				'conditions'          => array(
					'option' => array( 'exclude_post_types' => array( 'event' => true ) ),
				),
				'target'              => 'product',
				'expected'            => true,
			),
			array(
				'test_condition_name' => '全部外して保存した設定（空配列）で product の個別ページ => 出力される',
				'conditions'          => array(
					'option' => array( 'exclude_post_types' => array() ),
				),
				'target'              => 'product',
				'expected'            => true,
			),
			array(
				'test_condition_name' => '全部外して保存した設定（空配列）でも添付ファイルのページ => 出力されない',
				'conditions'          => array(
					'option' => array( 'exclude_post_types' => array() ),
				),
				'target'              => 'attachment',
				'expected'            => false,
			),
			// 出力可否フィルター。
			// Output filter.
			array(
				'test_condition_name' => '出力可否フィルターで false を返すと投稿の個別ページ => 出力されない',
				'conditions'          => array(
					'filters' => array( 'veu_article_structure_data_is_output' => '__return_false' ),
				),
				'target'              => 'post',
				'expected'            => false,
			),
			array(
				'test_condition_name' => '出力可否フィルターで true を返すと固定ページ => 出力される',
				'conditions'          => array(
					'filters' => array( 'veu_article_structure_data_is_output' => '__return_true' ),
				),
				'target'              => 'page',
				'expected'            => true,
			),
			// 配列フィルター。
			// Array filter.
			array(
				'test_condition_name' => '配列フィルターで空配列を返すと投稿の個別ページ => 出力されない',
				'conditions'          => array(
					'filters' => array( 'veu_article_structure_data' => '__return_empty_array' ),
				),
				'target'              => 'post',
				'expected'            => false,
			),
			array(
				'test_condition_name' => '配列フィルターで配列以外を返すと投稿の個別ページ => 出力されない',
				'conditions'          => array(
					'filters' => array(
						'veu_article_structure_data' => function () {
							return 'not an array';
						},
					),
				),
				'target'              => 'post',
				'expected'            => false,
			),
		);

		foreach ( $test_cases as $case ) {
			// 設定とフィルターを用意する。
			// Prepare the option and the filters.
			if ( isset( $case['conditions']['option'] ) ) {
				update_option( VK_Article_Srtuctured_Data::OPTION_NAME, $case['conditions']['option'] );
			}
			$filters = isset( $case['conditions']['filters'] ) ? $case['conditions']['filters'] : array();
			foreach ( $filters as $hook_name => $callback ) {
				add_filter( $hook_name, $callback );
			}

			// 対象ページへ移動して出力を取得する。
			// Move to the target page and capture the output.
			$this->go_to( $this->get_target_url( $case['target'] ) );
			ob_start();
			VK_Article_Srtuctured_Data::the_article_structure_data();
			$output = ob_get_clean();

			$this->assertSame( $case['expected'], ( false !== strpos( $output, '"@type":"Article"' ) ), $case['test_condition_name'] );

			// 後続のケースへ影響させないよう後始末する。
			// Clean up so that later cases are not affected.
			foreach ( $filters as $hook_name => $callback ) {
				remove_filter( $hook_name, $callback );
			}
			delete_option( VK_Article_Srtuctured_Data::OPTION_NAME );
		}
	}

	/**
	 * Test is_output().
	 *
	 * @return void
	 */
	public function test_is_output() {

		// フィルターに渡された引数を記録する。
		// Record the arguments passed to the filter.
		$received = array();
		$callback = function ( $is_output, $post ) use ( &$received ) {
			$received = array(
				'is_output' => $is_output,
				'post_id'   => $post instanceof WP_Post ? $post->ID : null,
			);
			return $is_output;
		};
		add_filter( 'veu_article_structure_data_is_output', $callback, 10, 2 );

		$test_cases = array(
			array(
				'test_condition_name' => '投稿の個別ページ => true と WP_Post がフィルターに渡る',
				'target'              => 'post',
				'expected'            => array(
					'return'   => true,
					'received' => array(
						'is_output' => true,
						'post_id'   => $this->post_ids['post'],
					),
				),
			),
			array(
				'test_condition_name' => 'product の個別ページ => false と WP_Post がフィルターに渡る',
				'target'              => 'product',
				'expected'            => array(
					'return'   => false,
					'received' => array(
						'is_output' => false,
						'post_id'   => $this->post_ids['product'],
					),
				),
			),
			array(
				'test_condition_name' => '投稿が無い（null）場合 => false でフィルターは呼ばれない',
				'target'              => null,
				'expected'            => array(
					'return'   => false,
					'received' => array(),
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$received = array();

			// 対象ページへ移動して判定する。
			// Move to the target page and judge.
			if ( null === $case['target'] ) {
				$actual = VK_Article_Srtuctured_Data::is_output( null );
			} else {
				$this->go_to( $this->get_target_url( $case['target'] ) );
				$actual = VK_Article_Srtuctured_Data::is_output( get_post( $this->post_ids[ $case['target'] ] ) );
			}

			$this->assertSame( $case['expected']['return'], $actual, $case['test_condition_name'] );
			$this->assertSame( $case['expected']['received'], $received, $case['test_condition_name'] );
		}

		remove_filter( 'veu_article_structure_data_is_output', $callback, 10 );
	}

	/**
	 * Test the veu_article_structure_data filter in get_article_structure_array().
	 *
	 * @return void
	 */
	public function test_get_article_structure_array() {

		// フィルターに渡された引数を記録し、headline を書き換える。
		// Record the arguments passed to the filter and rewrite the headline.
		$received = array();
		$callback = function ( $article_array, $author_id, $post ) use ( &$received ) {
			$received                  = array(
				'author_id' => $author_id,
				'post_id'   => $post instanceof WP_Post ? $post->ID : null,
			);
			$article_array['headline'] = 'Filtered headline';
			return $article_array;
		};
		add_filter( 'veu_article_structure_data', $callback, 10, 3 );

		$post_author = (int) get_post_field( 'post_author', $this->post_ids['post'] );

		$test_cases = array(
			array(
				'test_condition_name' => '投稿の個別ページ => 書き換えた headline が返り、著者 ID と WP_Post がフィルターに渡る',
				'target'              => 'post',
				'author_id'           => '',
				'expected'            => array(
					'headline' => 'Filtered headline',
					'received' => array(
						'author_id' => $post_author,
						'post_id'   => $this->post_ids['post'],
					),
				),
			),
			array(
				'test_condition_name' => '著者 ID を指定して投稿の個別ページ => 指定した著者 ID がフィルターに渡る',
				'target'              => 'post',
				'author_id'           => (string) $post_author,
				'expected'            => array(
					'headline' => 'Filtered headline',
					'received' => array(
						'author_id' => $post_author,
						'post_id'   => $this->post_ids['post'],
					),
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$received = array();
			$this->go_to( $this->get_target_url( $case['target'] ) );

			$actual = VK_Article_Srtuctured_Data::get_article_structure_array( $case['author_id'] );

			$this->assertSame( $case['expected']['headline'], $actual['headline'], $case['test_condition_name'] );
			$this->assertSame( $case['expected']['received'], $received, $case['test_condition_name'] );
		}

		// 投稿が無く配列を作れない場合は、フィルターを通さず空配列を返す。
		// When the array cannot be built because there is no post, an empty array is returned without the filter.
		$received        = array();
		$GLOBALS['post'] = null;
		$this->assertSame( array(), VK_Article_Srtuctured_Data::get_article_structure_array(), '投稿が無い場合 => 空配列' );
		$this->assertSame( array(), $received, '投稿が無い場合 => フィルターは呼ばれない' );

		remove_filter( 'veu_article_structure_data', $callback, 10 );

		// 配列フィルターで配列以外を返した場合は空配列にそろえる。
		// A non-array returned by the filter is normalized to an empty array.
		$string_callback = function () {
			return 'not an array';
		};
		add_filter( 'veu_article_structure_data', $string_callback );
		$this->go_to( $this->get_target_url( 'post' ) );
		$this->assertSame( array(), VK_Article_Srtuctured_Data::get_article_structure_array(), '配列フィルターで配列以外を返した場合 => 空配列' );
		remove_filter( 'veu_article_structure_data', $string_callback );
	}

	/**
	 * Test sanitize_options().
	 *
	 * @return void
	 */
	public function test_sanitize_options() {

		$test_cases = array(
			array(
				'test_condition_name' => '存在する投稿タイプにチェック => そのまま保存される',
				'input'               => array(
					'exclude_post_types' => array(
						'product' => 'true',
						'event'   => 'true',
					),
				),
				'expected'            => array(
					'exclude_post_types' => array(
						'product' => true,
						'event'   => true,
					),
				),
			),
			array(
				'test_condition_name' => '存在しない投稿タイプ・固定ページ・添付ファイルが混ざる => 捨てられる',
				'input'               => array(
					'exclude_post_types' => array(
						'post'                => 'true',
						'not_exist_post_type' => 'true',
						'page'                => 'true',
						'attachment'          => 'true',
					),
				),
				'expected'            => array(
					'exclude_post_types' => array(
						'post' => true,
					),
				),
			),
			array(
				'test_condition_name' => 'チェックを全部外して保存（null が渡る） => 既定値に戻らず空配列',
				'input'               => null,
				'expected'            => array(
					'exclude_post_types' => array(),
				),
			),
			array(
				'test_condition_name' => 'exclude_post_types が配列以外 => 空配列',
				'input'               => array( 'exclude_post_types' => 'product' ),
				'expected'            => array(
					'exclude_post_types' => array(),
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = VK_Article_Srtuctured_Data::sanitize_options( $case['input'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * Test get_exclude_post_types().
	 *
	 * @return void
	 */
	public function test_get_exclude_post_types() {

		$test_cases = array(
			array(
				'test_condition_name' => '一度も保存していない => 添付ファイルと既定の product',
				'option'              => null,
				'expected'            => array( 'attachment', 'product' ),
			),
			array(
				'test_condition_name' => '全部外して保存した => 添付ファイルのみ（既定値に戻らない）',
				'option'              => array( 'exclude_post_types' => array() ),
				'expected'            => array( 'attachment' ),
			),
			array(
				'test_condition_name' => 'event を除外して保存した => 添付ファイルと event',
				'option'              => array( 'exclude_post_types' => array( 'event' => true ) ),
				'expected'            => array( 'attachment', 'event' ),
			),
			array(
				'test_condition_name' => '保存値が配列以外 => 既定値を使う',
				'option'              => 'broken',
				'expected'            => array( 'attachment', 'product' ),
			),
		);

		foreach ( $test_cases as $case ) {
			if ( null === $case['option'] ) {
				delete_option( VK_Article_Srtuctured_Data::OPTION_NAME );
			} else {
				update_option( VK_Article_Srtuctured_Data::OPTION_NAME, $case['option'] );
			}

			$this->assertSame( $case['expected'], VK_Article_Srtuctured_Data::get_exclude_post_types(), $case['test_condition_name'] );

			delete_option( VK_Article_Srtuctured_Data::OPTION_NAME );
		}
	}
}
