<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// WordPress -> 4.3
add_filter( 'wp_title', 'vkExUnit_get_wp_head_title', 11 );
// WordPress 4.4 ->
add_filter( 'pre_get_document_title', 'vkExUnit_get_wp_head_title', 11 );

/**
 * ExUnitの機能管理パッケージに登録
 *
 * @return void
 */
function vkExUnit_wp_title_init() {
	$tab_label         = __( '&lt;title&gt; tag setting', 'vk-all-in-one-expansion-unit' );
	$option_name       = 'vkExUnit_wp_title';
	$sanitize_callback = 'vkExUnit_wp_title_validate';
	$render_page       = 'vkExUnit_add_wp_title_page';
	vkExUnit_register_setting( $tab_label, $option_name, $sanitize_callback, $render_page );
}
add_action( 'veu_package_init', 'vkExUnit_wp_title_init' );


/*********************************************
 * Head title
 */
function vkExUnit_get_wp_head_title() {
	global $wp_query;
	$post  = $wp_query->get_queried_object();
	$sep   = ' | ';
	$sep   = apply_filters( 'vkExUnit_get_wp_head_title_sep', $sep );
	$title = '';
	// Meta box から指定がある場合のタイトル整形（最優先）
	if ( is_singular() || ( is_front_page() && 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) ) {
		$post_meta = get_post_meta( $post->ID, 'veu_head_title', true );
		if ( ! empty( $post_meta['title'] ) ) {
			$title = $post_meta['title'];
			if ( ! empty( $post_meta['add_site_title'] ) ) {
				$title .= $sep . get_bloginfo( 'name' );
			}
		}
	}

	if ( ! $title ) {
		if ( is_front_page() ) {
			$options = vkExUnit_get_wp_title_options();
			if ( empty( $options['extend_frontTitle'] ) ) {
				$description = get_bloginfo( 'description' );
				$title       = get_bloginfo( 'name' );
				if ( ! empty( $description ) ) {
					$title .= $sep . $description;
				}
			} else {
				$title = $options['extend_frontTitle'];
			}
		} elseif ( is_home() && ! is_front_page() ) {
			$title = vkExUnit_get_the_archive_title() . $sep . get_bloginfo( 'name' );
		} elseif ( is_archive() ) {
			// タクソノミーのカスタムタイトルタグをチェック
			$custom_title = vkExUnit_get_taxonomy_custom_title( $post );
			if ( ! empty( $custom_title ) ) {
				$title = $custom_title;
			} else {
				$title = vkExUnit_get_the_archive_title() . $sep . get_bloginfo( 'name' );
			}
			// Page
		} elseif ( is_singular() ) {
			$post_meta = get_post_meta( $post->ID, 'veu_head_title', true );
			if ( ! empty( $post_meta['title'] ) ) {
				$title = $post_meta['title'];
				if ( ! empty( $post_meta['add_site_title'] ) ) {
					$title .= $sep . get_bloginfo( 'name' );
				}
			} elseif ( is_page() ) {
				// Sub Pages
				if ( $post->post_parent ) {
					if ( $post->ancestors ) {
						foreach ( $post->ancestors as $post_anc_id ) {
							$post_id = $post_anc_id;
						}
					} else {
						$post_id = $post->ID;
					}
					$title = get_the_title() . $sep . get_the_title( $post_id ) . $sep . get_bloginfo( 'name' );
					// Not Sub Pages
				} else {
					$title = get_the_title() . $sep . get_bloginfo( 'name' );
				}
			} else {
				$title = get_the_title() . $sep . get_bloginfo( 'name' );
			}
			// Search
		} elseif ( is_search() ) {
			if ( get_search_query() ) {
				$title = sprintf( __( 'Search Results for : %s', 'vk-all-in-one-expansion-unit' ), get_search_query() ) . $sep . get_bloginfo( 'name' );
			} else {
				$title = sprintf( __( 'Search Results', 'vk-all-in-one-expansion-unit' ), get_search_query() ) . $sep . get_bloginfo( 'name' );
			}
			// 404
		} elseif ( is_404() ) {
			$title = __( 'Not found', 'vk-all-in-one-expansion-unit' ) . $sep . get_bloginfo( 'name' );
			// Other
		} else {
			$title = get_bloginfo( 'name' );
		}
	}

	// Add Page numner.
	global $paged;
	if ( $paged >= 2 ) {
		$title = '[' . sprintf( __( 'Page of %s', 'vk-all-in-one-expansion-unit' ), $paged ) . '] ' . $title;
	}

	$title = apply_filters( 'vkExUnit_get_wp_head_title', $title );
	// Remove Tags(ex:<i>) & return
	return strip_tags( $title );
}

function vkExUnit_add_wp_title_page() {
	$options = vkExUnit_get_wp_title_options();
	?>
<div id="seoSetting" class="sectionBox">
<h3><?php _e( '&lt;title&gt; tag setting', 'vk-all-in-one-expansion-unit' ); ?></h3>
<p>
	<?php
	$sitetitle_link = '<a href="' . get_admin_url() . 'options-general.php" target="_blank">' . __( 'title of the site', 'vk-all-in-one-expansion-unit' ) . '</a>';
	printf( __( 'Normally "%1$s" is placed in the title tags of all the pages.', 'vk-all-in-one-expansion-unit' ), $sitetitle_link );
	?>
<br />
	<?php printf( __( 'For example, it appears in the form of <br />&lt;title&gt;page title | %1$s&lt;/title&gt;<br /> if using a static page.', 'vk-all-in-one-expansion-unit' ), $sitetitle_link ); ?><br />
</p>
<table class="form-table">
	<tr>
		<th><?php _e( 'Homepage', 'vk-all-in-one-expansion-unit' ); ?></th>
		<td>
		<p>
		<?php
		$tagline_link = '<a href="' . get_admin_url() . 'options-general.php" target="_blank">' . __( 'Tagline', 'vk-all-in-one-expansion-unit' ) . '</a>';
		printf( __( 'In the top page will be output usually in the form of <br />&lt;title&gt;%1$s | %2$s&lt;/title&gt;', 'vk-all-in-one-expansion-unit' ), $sitetitle_link, $tagline_link );
		?>
		<br />
		<?php _e( 'However, it may be too long in the above format. If the input to the input field of the following, its contents will be reflected.', 'vk-all-in-one-expansion-unit' ); ?>
		</p>

		<input type="text" name="vkExUnit_wp_title[extend_frontTitle]" value="<?php echo esc_attr( $options['extend_frontTitle'] ); ?>" />
		<?php
		$page_on_front = intval( get_option( 'page_on_front' ) );
		if ( 'page' === get_option( 'show_on_front' ) && $page_on_front ) {
			$edit_url = get_edit_post_link( $page_on_front );
			?>
			<p>* 
				<?php
				$edit_link = '<a href="' . $edit_url . '" target="_blank" rel="noopener noreferrer">' . __( 'Edit screen of the page specified as the front page', 'vk-all-in-one-expansion-unit' ) . '</a>';
				printf( __( 'If you specify the content of the title tag in %s, that will take precedence.', 'vk-all-in-one-expansion-unit' ), $edit_link );
				?>
			</p>
		<?php } ?>
		</td>
	</tr>

	<tr>
		<th><?php _e( 'Page / Posts', 'vk-all-in-one-expansion-unit' ); ?></th>
		<td>
		<p>
		<?php _e( 'Title tags for pages and post can be specified from the VK all in One Expansion Unit Metabox under the content edit area of each edit screen.', 'vk-all-in-one-expansion-unit' ); ?></p>
			<?php if ( get_locale() === 'ja' ) { ?>
				<img style="max-width:100%;border:1px solid #ccc;" src="<?php echo esc_url( VEU_DIRECTORY_URI ); ?>/inc/wp-title/package/images/title-setting-from-page.png" alt="" />
			<?php } ?>

		</td>
	</tr>

	<tr>
		<th><?php _e( 'Category / Tag', 'vk-all-in-one-expansion-unit' ); ?></th>
		<td>
		<p>
		<?php _e( 'Taxonomy title tag can be specified from the VK all in One Expansion Unit Metabox under the content edit area of each edit screen.', 'vk-all-in-one-expansion-unit' ); ?></p>
			<?php if ( get_locale() === 'ja' ) { ?>
				<img style="max-width:100%;border:1px solid #ccc;" src="<?php echo esc_url( VEU_DIRECTORY_URI ); ?>/inc/wp-title/package/images/title-setting-from-taxonomy.png" alt="" />
			<?php } ?>

		</td>
	</tr>
</table>
	<?php submit_button(); ?>
</div>
	<?php
}

function vkExUnit_get_wp_title_options() {
	$options = get_option( 'vkExUnit_wp_title', array() );
	$options = wp_parse_args( $options, vkExUnit_get_wp_title_default() );
	return $options;
}

function vkExUnit_get_wp_title_default() {
	$default_options = array(
		'extend_frontTitle' => '',
	);
	return apply_filters( 'vkExUnit_wp_title_default', $default_options ); // phpcs:ignore
}

function vkExUnit_wp_title_validate( $input ) {
	$output = array();
	// メイン設定の一括保存時、当該オプション群が $_POST に無いと $input は null で渡るため、
	// offset-on-null 警告と htmlspecialchars(null) の非推奨警告を防ぐ。
	// $input arrives as null when this option group is absent from $_POST during the bulk save, so guard against offset-on-null and the deprecated htmlspecialchars(null) call.
	$output['extend_frontTitle'] = ! empty( $input['extend_frontTitle'] ) ? stripslashes( htmlspecialchars( $input['extend_frontTitle'] ) ) : '';
	return $output;
}

/**
 * タクソノミーのカスタムタイトルタグを取得
 *
 * @param WP_Term $term
 * @return string
 */
function vkExUnit_get_taxonomy_custom_title( $term ) {
	if ( ! is_object( $term ) || ! isset( $term->term_id ) ) {
		return '';
	}

	$term_meta = get_term_meta( $term->term_id, 'veu_taxonomy_title', true );

	if ( empty( $term_meta['title'] ) ) {
		return '';
	}

	$title = $term_meta['title'];

	// サイトタイトルを追加するかチェック
	if ( ! empty( $term_meta['add_site_title'] ) ) {
		$sep    = ' | ';
		$sep    = apply_filters( 'vkExUnit_get_wp_head_title_sep', $sep );
		$title .= $sep . get_bloginfo( 'name' );
	}

	return $title;
}

/**
 * Replace the archive heading of a term archive with the term's Head Title when "Also reflect in the archive page heading" is ON.
 * タームの「アーカイブページの見出しにも反映する」が ON のとき、そのタームのアーカイブ見出しを Head Title の入力値に置き換える
 *
 * The whole heading including the "Category:" prefix is replaced, and the separator and site title are never added.
 * 「カテゴリー:」等の接頭辞を含めて丸ごと置き換え、セパレーターとサイトタイトルは付けない。
 *
 * @param string $title          Archive title from get_the_archive_title(). / get_the_archive_title() のタイトル.
 * @param string $original_title Archive title without prefix (unused). / 接頭辞なしのタイトル（未使用）.
 * @param string $prefix         Archive title prefix (unused). / タイトルの接頭辞（未使用）.
 * @return string Escaped Head Title when the term opts in, otherwise $title unchanged. / 対象タームならエスケープ済みの Head Title、それ以外は $title をそのまま返す.
 */
function veu_filter_term_archive_title( $title, $original_title = '', $prefix = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Keeps the get_the_archive_title filter signature. / フィルターの引数に合わせている.
	// Other plugins may call get_the_archive_title() in the admin (e.g. edit.php?cat=5 makes is_category() true) or in feeds.
	// 管理画面（edit.php?cat=5 等では is_category() が true になる）やフィードで他プラグインが呼んでも変えない。
	if ( is_admin() || is_feed() ) {
		return $title;
	}

	// Only category / tag / custom taxonomy term archives.
	// カテゴリー・タグ・カスタムタクソノミーのタームアーカイブのみ対象。
	if ( ! ( is_category() || is_tag() || is_tax() ) ) {
		return $title;
	}
	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return $title;
	}

	// Replace only when the title is entered and the opt-in flag is ON (data without the flag key is OFF).
	// タイトルが入力済みかつフラグが ON のときだけ置き換える（フラグキーの無い既存データは OFF）。
	$term_meta = get_term_meta( $term->term_id, 'veu_taxonomy_title', true );
	if ( ! is_array( $term_meta ) || empty( $term_meta['title'] ) || empty( $term_meta['reflect_heading'] ) ) {
		return $title;
	}
	// Guard against broken data (e.g. an array title) to avoid an "Array to string conversion" warning in esc_html().
	// DB 破損等で title が文字列でない場合に esc_html() で Array to string conversion が出るのを防ぐ。
	if ( ! is_string( $term_meta['title'] ) ) {
		return $title;
	}

	// Some themes print the archive title without escaping.
	// テーマによってはエスケープせずに出力するため、ここでエスケープする。
	return esc_html( $term_meta['title'] );
}
// Lightning / X-T9 / Katawara rebuild the title at priority 10, so this must run after them.
// Lightning / X-T9 / Katawara が優先度 10 でタイトルを作り直すため、それより後に実行する。
add_filter( 'get_the_archive_title', 'veu_filter_term_archive_title', 20, 3 );
