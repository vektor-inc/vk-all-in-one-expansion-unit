## 使い方

1. term-color-config.php を term-color を使用するプラグインディレクトリに複製
1. term-color-config.php の中身をプラグインの情報にあわせて書き換える
1. プラグインが最初に読み込むPHPファイルなどから require_once( 'inc/term-color-config.php' ); などで読み込む
1. termの色を表示したいテンプレートに以下のように記述

~~~
$taxonomies = get_the_taxonomies();
if ($taxonomies):
	// get $taxonomy name
	$taxonomy = key( $taxonomies );
	$terms  = get_the_terms( get_the_ID(),$taxonomy );
	$term_name	= esc_html($terms[0]->name);
	$term_color = Vk_term_color::get_term_color( $terms[0]->term_id );
	$term_color = ( $term_color ) ? ' style="background-color:'.$term_color.'"': '';
	$term_link = esc_url( get_term_link( $terms[0]->term_id, $taxonomy ) );
	$term = '<a class="padCate"'.$term_color.' href="'.$term_link.'">'.$term_name.'</a>';
endif;
~~~
