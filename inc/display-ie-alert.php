<?php
/**
 * Display IE Alert
 */

/**
 * Judgment IS IE
 */
function vue_is_ie() {
    $ua = mb_strtolower( $_SERVER['HTTP_USER_AGENT'] );  //すべて小文字にしてユーザーエージェントを取得
    if ( strpos( $ua,'msie' ) !== false || strpos( $ua, 'trident' ) !== false ) {
        return true;
    }
    return false;
}

/**
 * IE Alart HTML
 */
function veu_get_alert_html(){
	$html = '<div class="ie_alert">
	<h1 class="ie_alert__title">ご利用のブラウザは安全ではありません</h1>
	<div class="ie_alert__body">
	<p>ご利用の Internet Explorer は古いブラウザで Microsoft社も利用は危険であると発信しています。このウェブサイトも Internet Explorer での表示は保証しておりません。<br>
	最新のモダンブラウザ（<a href="https://www.microsoft.com/ja-jp/edge" target="_blank" rel="noopener">Microsoft Edge</a>や<a href="https://www.google.co.jp/chrome/index.html" target="_blank" rel="noopener">Google Chrome</a>など）</b>をお使いください。
	</p>
	</div>
  </div>';
  return $html;
}

/**
 * Insert IE Alert.
 */
function veu_insert_alert(){
	if ( veu_is_ie() ) {
		?>
		<style type="text/css">
		.ie_alert {
			background-color:#c00;
			color:#fff;
			padding:10px;
		}
		.ie_alert a {
			color:#fff;
			text-decoration:underline;
		}
		.ie_alert__title {
			font-size:16px;
			text-align:center;
		}
		.ie_alert__body p {
			margin:0 0 5px;
			padding:0;
			font-size:11px;
			text-align:center;
		}
		</style>
		<?php
		echo iea_get_alert_html();
	}
}
add_action('wp_body_open','iea_insert_alert');