<?php
/* 色の出力 */
/*

色の出力用CSS

★ 色を出力するとCSSファイル側も含めて多くなって速度低下を引き起こすため結局これは読み込んでいない。

★ もし使う場合

各CSSを普通にcssファイルから読み込むようにっ変更してあるので、
ここに記述のCSSはほとんど削除する事ができる。
※ ただ、こっちは削除できてもCSSファイル側の記述が増え湯ので保留している。

 */
/*-------------------------------------------*/
/*
/*-------------------------------------------*/

function lightning_print_css_vk_heading_style() {

	$dynamic_css = '';

	$options = get_option( 'lightning_theme_options' );
	if ( isset( $options['color_key'] ) && isset( $options['color_key_dark'] ) ) {
		$color_key = esc_html( $options['color_key'] );

		$dynamic_css .= '
		.mainSection .entry-body h1.is-style-vk-heading-plain,
		.mainSection .entry-body h2.is-style-vk-heading-plain,
		.mainSection .entry-body h3.is-style-vk-heading-plain,
		.mainSection .entry-body h4.is-style-vk-heading-plain,
		.mainSection .entry-body h5.is-style-vk-heading-plain,
		.mainSection .entry-body h6.is-style-vk-heading-plain {
			color: #212529;
			background-color:unset;
			position: relative;
			border:none;
			padding:unset;
			margin-left: unset;
			margin-right: unset;
			border-radius:unset;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-plain::before,
		.mainSection .entry-body h2.is-style-vk-heading-plain::before,
		.mainSection .entry-body h3.is-style-vk-heading-plain::before,
		.mainSection .entry-body h4.is-style-vk-heading-plain::before,
		.mainSection .entry-body h5.is-style-vk-heading-plain::before,
		.mainSection .entry-body h6.is-style-vk-heading-plain::before,
		.mainSection .entry-body h1.is-style-vk-heading-plain::after,
		.mainSection .entry-body h2.is-style-vk-heading-plain::after,
		.mainSection .entry-body h3.is-style-vk-heading-plain::after,
		.mainSection .entry-body h4.is-style-vk-heading-plain::after,
		.mainSection .entry-body h5.is-style-vk-heading-plain::after,
		.mainSection .entry-body h6.is-style-vk-heading-plain::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-speech_balloon_fill,
		.mainSection .entry-body h2.is-style-vk-heading-speech_balloon_fill,
		.mainSection .entry-body h3.is-style-vk-heading-speech_balloon_fill,
		.mainSection .entry-body h4.is-style-vk-heading-speech_balloon_fill,
		.mainSection .entry-body h5.is-style-vk-heading-speech_balloon_fill,
		.mainSection .entry-body h6.is-style-vk-heading-speech_balloon_fill{
			background-color:' . $color_key . ';
			position: relative;
			border:none;
			padding: 0.6em 0.8em 0.5em;
			margin-left: unset;
			margin-right: unset;
			margin-bottom:1.2em;
			color:#fff;
			border-radius:4px;
			text-align:left;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
			overflow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-speech_balloon_fill::before,
		.mainSection .entry-body h2.is-style-vk-heading-speech_balloon_fill::before,
		.mainSection .entry-body h3.is-style-vk-heading-speech_balloon_fill::before,
		.mainSection .entry-body h4.is-style-vk-heading-speech_balloon_fill::before,
		.mainSection .entry-body h5.is-style-vk-heading-speech_balloon_fill::before,
		.mainSection .entry-body h6.is-style-vk-heading-speech_balloon_fill::before{
			content: "";
			position: absolute;
			top: auto;
			left: 40px;
			bottom: -20px;
			width: auto;
			margin-left: -10px;
			border: 10px solid transparent;
			border-top: 10px solid ' . $color_key . ';
			z-index: 2;
			height: auto;
			background-color: transparent;
		}
		.mainSection .entry-body h1.is-style-vk-heading-speech_balloon_fill::after,
		.mainSection .entry-body h2.is-style-vk-heading-speech_balloon_fill::after,
		.mainSection .entry-body h3.is-style-vk-heading-speech_balloon_fill::after,
		.mainSection .entry-body h4.is-style-vk-heading-speech_balloon_fill::after,
		.mainSection .entry-body h5.is-style-vk-heading-speech_balloon_fill::after,
		.mainSection .entry-body h6.is-style-vk-heading-speech_balloon_fill::after{content:none;}

		.mainSection .entry-body h1.is-style-vk-heading-background_fill,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill{
			background-color:' . $color_key . ';
			position: relative;
			border:none;
			padding: 0.6em 0.7em 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			color:#fff;
			border-radius:4px;
			outline: unset;
			outline-offset: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-background_fill::before,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill::before,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill::before,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill::before,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill::before,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill::before,
		.mainSection .entry-body h1.is-style-vk-heading-background_fill::after,
		.mainSection .entry-body h1.is-style-vk-heading-background_fill::after,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill::after,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill::after,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill::after,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill::after,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-background_fill_stitch,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill_stitch,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill_stitch,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill_stitch,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill_stitch,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill_stitch{
			background-color:' . $color_key . ';
			position: relative;
			padding: 0.6em 0.7em 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			color:#fff;
			border-radius:4px;
			border:none;
			outline: dashed 1px #fff;
			outline-offset: -4px;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-background_fill_stitch::before,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill_stitch::before,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill_stitch::before,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill_stitch::before,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill_stitch::before,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill_stitch::before,
		.mainSection .entry-body h1.is-style-vk-heading-background_fill_stitch::after,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill_stitch::after,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill_stitch::after,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill_stitch::after,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill_stitch::after,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill_stitch::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-background_fill_lightgray,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill_lightgray,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill_lightgray,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill_lightgray,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill_lightgray,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill_lightgray{
			color: #333;
			position: relative;
			border:none;
			background-color: #efefef;
			padding: 0.6em 0.7em 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-radius: 4px;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-background_fill_lightgray::before,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill_lightgray::before,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill_lightgray::before,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill_lightgray::before,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill_lightgray::before,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill_lightgray::before,
		.mainSection .entry-body h1.is-style-vk-heading-background_fill_lightgray::after,
		.mainSection .entry-body h2.is-style-vk-heading-background_fill_lightgray::after,
		.mainSection .entry-body h3.is-style-vk-heading-background_fill_lightgray::after,
		.mainSection .entry-body h4.is-style-vk-heading-background_fill_lightgray::after,
		.mainSection .entry-body h5.is-style-vk-heading-background_fill_lightgray::after,
		.mainSection .entry-body h6.is-style-vk-heading-background_fill_lightgray::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-topborder_background_fill_none,
		.mainSection .entry-body h2.is-style-vk-heading-topborder_background_fill_none,
		.mainSection .entry-body h3.is-style-vk-heading-topborder_background_fill_none,
		.mainSection .entry-body h4.is-style-vk-heading-topborder_background_fill_none,
		.mainSection .entry-body h5.is-style-vk-heading-topborder_background_fill_none,
		.mainSection .entry-body h6.is-style-vk-heading-topborder_background_fill_none{
			color: #333;
			position: relative;
			background-color: unset;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top: 2px solid ' . $color_key . ';
			border-bottom: 1px solid #ccc;
			border-left: unset;
			border-right: unset;
			border-radius: unset;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-topborder_background_fill_none::before,
		.mainSection .entry-body h2.is-style-vk-heading-topborder_background_fill_none::before,
		.mainSection .entry-body h3.is-style-vk-heading-topborder_background_fill_none::before,
		.mainSection .entry-body h4.is-style-vk-heading-topborder_background_fill_none::before,
		.mainSection .entry-body h5.is-style-vk-heading-topborder_background_fill_none::before,
		.mainSection .entry-body h6.is-style-vk-heading-topborder_background_fill_none::before,
		.mainSection .entry-body h1.is-style-vk-heading-topborder_background_fill_none::after,
		.mainSection .entry-body h2.is-style-vk-heading-topborder_background_fill_none::after,
		.mainSection .entry-body h3.is-style-vk-heading-topborder_background_fill_none::after,
		.mainSection .entry-body h4.is-style-vk-heading-topborder_background_fill_none::after,
		.mainSection .entry-body h5.is-style-vk-heading-topborder_background_fill_none::after,
		.mainSection .entry-body h6.is-style-vk-heading-topborder_background_fill_none::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-topborder_background_fill_black,
		.mainSection .entry-body h2.is-style-vk-heading-topborder_background_fill_black,
		.mainSection .entry-body h3.is-style-vk-heading-topborder_background_fill_black,
		.mainSection .entry-body h4.is-style-vk-heading-topborder_background_fill_black,
		.mainSection .entry-body h5.is-style-vk-heading-topborder_background_fill_black,
		.mainSection .entry-body h6.is-style-vk-heading-topborder_background_fill_black{
			position: relative;
			background-color: #191919;
			padding: 0.6em 0.7em 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			color: #fff;
			border-top: 2px solid ' . $color_key . ';
			border-bottom: 1px solid #999;
			border-left: unset;
			border-right: unset;
			border-radius: unset;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-topborder_background_fill_black::before,
		.mainSection .entry-body h2.is-style-vk-heading-topborder_background_fill_black::before,
		.mainSection .entry-body h3.is-style-vk-heading-topborder_background_fill_black::before,
		.mainSection .entry-body h4.is-style-vk-heading-topborder_background_fill_black::before,
		.mainSection .entry-body h5.is-style-vk-heading-topborder_background_fill_black::before,
		.mainSection .entry-body h6.is-style-vk-heading-topborder_background_fill_black::before,
		.mainSection .entry-body h1.is-style-vk-heading-topborder_background_fill_black::after,
		.mainSection .entry-body h2.is-style-vk-heading-topborder_background_fill_black::after,
		.mainSection .entry-body h3.is-style-vk-heading-topborder_background_fill_black::after,
		.mainSection .entry-body h4.is-style-vk-heading-topborder_background_fill_black::after,
		.mainSection .entry-body h5.is-style-vk-heading-topborder_background_fill_black::after,
		.mainSection .entry-body h6.is-style-vk-heading-topborder_background_fill_black::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-double,
		.mainSection .entry-body h2.is-style-vk-heading-double,
		.mainSection .entry-body h3.is-style-vk-heading-double,
		.mainSection .entry-body h4.is-style-vk-heading-double,
		.mainSection .entry-body h5.is-style-vk-heading-double,
		.mainSection .entry-body h6.is-style-vk-heading-double{
			color: #333;
			position: relative;
			border:none;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top: double 3px ' . $color_key . ';
			border-bottom: double 3px ' . $color_key . ';
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-double::before,
		.mainSection .entry-body h2.is-style-vk-heading-double::before,
		.mainSection .entry-body h3.is-style-vk-heading-double::before,
		.mainSection .entry-body h4.is-style-vk-heading-double::before,
		.mainSection .entry-body h5.is-style-vk-heading-double::before,
		.mainSection .entry-body h6.is-style-vk-heading-double::before,
		.mainSection .entry-body h1.is-style-vk-heading-double::after,
		.mainSection .entry-body h2.is-style-vk-heading-double::after,
		.mainSection .entry-body h3.is-style-vk-heading-double::after,
		.mainSection .entry-body h4.is-style-vk-heading-double::after,
		.mainSection .entry-body h5.is-style-vk-heading-double::after,
		.mainSection .entry-body h6.is-style-vk-heading-double::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-double_black,
		.mainSection .entry-body h2.is-style-vk-heading-double_black,
		.mainSection .entry-body h3.is-style-vk-heading-double_black,
		.mainSection .entry-body h4.is-style-vk-heading-double_black,
		.mainSection .entry-body h5.is-style-vk-heading-double_black,
		.mainSection .entry-body h6.is-style-vk-heading-double_black{
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top: double 3px #333;
			border-bottom: double 3px #333;
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-double_black::before,
		.mainSection .entry-body h2.is-style-vk-heading-double_black::before,
		.mainSection .entry-body h3.is-style-vk-heading-double_black::before,
		.mainSection .entry-body h4.is-style-vk-heading-double_black::before,
		.mainSection .entry-body h5.is-style-vk-heading-double_black::before,
		.mainSection .entry-body h6.is-style-vk-heading-double_black::before,
		.mainSection .entry-body h1.is-style-vk-heading-double_black::after,
		.mainSection .entry-body h2.is-style-vk-heading-double_black::after,
		.mainSection .entry-body h3.is-style-vk-heading-double_black::after,
		.mainSection .entry-body h4.is-style-vk-heading-double_black::after,
		.mainSection .entry-body h5.is-style-vk-heading-double_black::after,
		.mainSection .entry-body h6.is-style-vk-heading-double_black::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-double_bottomborder,
		.mainSection .entry-body h2.is-style-vk-heading-double_bottomborder,
		.mainSection .entry-body h3.is-style-vk-heading-double_bottomborder,
		.mainSection .entry-body h4.is-style-vk-heading-double_bottomborder,
		.mainSection .entry-body h5.is-style-vk-heading-double_bottomborder,
		.mainSection .entry-body h6.is-style-vk-heading-double_bottomborder{
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-bottom: double 3px ' . $color_key . ';
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-double_bottomborder::before,
		.mainSection .entry-body h2.is-style-vk-heading-double_bottomborder::before,
		.mainSection .entry-body h3.is-style-vk-heading-double_bottomborder::before,
		.mainSection .entry-body h4.is-style-vk-heading-double_bottomborder::before,
		.mainSection .entry-body h5.is-style-vk-heading-double_bottomborder::before,
		.mainSection .entry-body h6.is-style-vk-heading-double_bottomborder::before,
		.mainSection .entry-body h1.is-style-vk-heading-double_bottomborder::after,
		.mainSection .entry-body h2.is-style-vk-heading-double_bottomborder::after,
		.mainSection .entry-body h3.is-style-vk-heading-double_bottomborder::after,
		.mainSection .entry-body h4.is-style-vk-heading-double_bottomborder::after,
		.mainSection .entry-body h5.is-style-vk-heading-double_bottomborder::after,
		.mainSection .entry-body h6.is-style-vk-heading-double_bottomborder::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-double_bottomborder_black,
		.mainSection .entry-body h2.is-style-vk-heading-double_bottomborder_black,
		.mainSection .entry-body h3.is-style-vk-heading-double_bottomborder_black,
		.mainSection .entry-body h4.is-style-vk-heading-double_bottomborder_black,
		.mainSection .entry-body h5.is-style-vk-heading-double_bottomborder_black,
		.mainSection .entry-body h6.is-style-vk-heading-double_bottomborder_black{
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-bottom: double 3px #333;
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-double_bottomborder_black::before,
		.mainSection .entry-body h2.is-style-vk-heading-double_bottomborder_black::before,
		.mainSection .entry-body h3.is-style-vk-heading-double_bottomborder_black::before,
		.mainSection .entry-body h4.is-style-vk-heading-double_bottomborder_black::before,
		.mainSection .entry-body h5.is-style-vk-heading-double_bottomborder_black::before,
		.mainSection .entry-body h6.is-style-vk-heading-double_bottomborder_black::before,
		.mainSection .entry-body h1.is-style-vk-heading-double_bottomborder_black::after,
		.mainSection .entry-body h2.is-style-vk-heading-double_bottomborder_black::after,
		.mainSection .entry-body h3.is-style-vk-heading-double_bottomborder_black::after,
		.mainSection .entry-body h4.is-style-vk-heading-double_bottomborder_black::after,
		.mainSection .entry-body h5.is-style-vk-heading-double_bottomborder_black::after,
		.mainSection .entry-body h6.is-style-vk-heading-double_bottomborder_black::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-solid,
		.mainSection .entry-body h2.is-style-vk-heading-solid,
		.mainSection .entry-body h3.is-style-vk-heading-solid,
		.mainSection .entry-body h4.is-style-vk-heading-solid,
		.mainSection .entry-body h5.is-style-vk-heading-solid,
		.mainSection .entry-body h6.is-style-vk-heading-solid{
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top: solid 1px ' . $color_key . ';
			border-bottom: solid 1px ' . $color_key . ';
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-solid::before,
		.mainSection .entry-body h2.is-style-vk-heading-solid::before,
		.mainSection .entry-body h3.is-style-vk-heading-solid::before,
		.mainSection .entry-body h4.is-style-vk-heading-solid::before,
		.mainSection .entry-body h5.is-style-vk-heading-solid::before,
		.mainSection .entry-body h6.is-style-vk-heading-solid::before,
		.mainSection .entry-body h1.is-style-vk-heading-solid::after,
		.mainSection .entry-body h2.is-style-vk-heading-solid::after,
		.mainSection .entry-body h3.is-style-vk-heading-solid::after,
		.mainSection .entry-body h4.is-style-vk-heading-solid::after,
		.mainSection .entry-body h5.is-style-vk-heading-solid::after,
		.mainSection .entry-body h6.is-style-vk-heading-solid::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-solid_black,
		.mainSection .entry-body h2.is-style-vk-heading-solid_black,
		.mainSection .entry-body h3.is-style-vk-heading-solid_black,
		.mainSection .entry-body h4.is-style-vk-heading-solid_black,
		.mainSection .entry-body h5.is-style-vk-heading-solid_black,
		.mainSection .entry-body h6.is-style-vk-heading-solid_black{
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top: solid 1px #333;
			border-bottom: solid 1px #333;
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-solid_black::before,
		.mainSection .entry-body h2.is-style-vk-heading-solid_black::before,
		.mainSection .entry-body h3.is-style-vk-heading-solid_black::before,
		.mainSection .entry-body h4.is-style-vk-heading-solid_black::before,
		.mainSection .entry-body h5.is-style-vk-heading-solid_black::before,
		.mainSection .entry-body h6.is-style-vk-heading-solid_black::before,
		.mainSection .entry-body h1.is-style-vk-heading-solid_black::after,
		.mainSection .entry-body h2.is-style-vk-heading-solid_black::after,
		.mainSection .entry-body h3.is-style-vk-heading-solid_black::after,
		.mainSection .entry-body h4.is-style-vk-heading-solid_black::after,
		.mainSection .entry-body h5.is-style-vk-heading-solid_black::after,
		.mainSection .entry-body h6.is-style-vk-heading-solid_black::after{ content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder{
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top: unset;
			border-bottom: solid 1px ' . $color_key . ';
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder::before,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder::before,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder::before,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder::before,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder::before,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder::before,
		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder::after,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder::after,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder::after,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder::after,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder::after,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder::after { content:none; }

		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder_black,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder_black,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder_black,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder_black,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder_black,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder_black {
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top: unset;
			border-bottom: solid 1px #333;
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder_black::before,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder_black::before,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder_black::before,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder_black::before,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder_black::before,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder_black::before,
		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder_black::after,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder_black::after,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder_black::after,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder_black::after,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder_black::after,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder_black::after { content:none;}

		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder_leftkeycolor,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder_leftkeycolor,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder_leftkeycolor,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder_leftkeycolor,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder_leftkeycolor,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder_leftkeycolor{
			color:#333;
			position: relative;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top:none;
			border-right:none;
			border-left:none;
			border-bottom: 1px solid #ccc;
			background-color:transparent;
			text-align:left;
			outline: unset;
			outline-offset: unset;
			border-radius: unset;
			box-shadow: unset;
			overflow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder_leftkeycolor::before,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder_leftkeycolor::before,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder_leftkeycolor::before,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder_leftkeycolor::before,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder_leftkeycolor::before,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder_leftkeycolor::before{ content:none; }
		.mainSection .entry-body h1.is-style-vk-heading-solid_bottomborder_leftkeycolor::after,
		.mainSection .entry-body h2.is-style-vk-heading-solid_bottomborder_leftkeycolor::after,
		.mainSection .entry-body h3.is-style-vk-heading-solid_bottomborder_leftkeycolor::after,
		.mainSection .entry-body h4.is-style-vk-heading-solid_bottomborder_leftkeycolor::after,
		.mainSection .entry-body h5.is-style-vk-heading-solid_bottomborder_leftkeycolor::after,
		.mainSection .entry-body h6.is-style-vk-heading-solid_bottomborder_leftkeycolor::after{
			content: ".";
			line-height: 0;
			display: block;
			overflow: hidden;
			position: absolute;
			left:0;
			bottom: -1px;
			width: 30%;
			border: unset;
			border-top: none;
			border-bottom: 1px solid ' . $color_key . ';
			margin-left: 0;
		}

		.mainSection .entry-body h1.is-style-vk-heading-dotted_bottomborder_black,
		.mainSection .entry-body h2.is-style-vk-heading-dotted_bottomborder_black,
		.mainSection .entry-body h3.is-style-vk-heading-dotted_bottomborder_black,
		.mainSection .entry-body h4.is-style-vk-heading-dotted_bottomborder_black,
		.mainSection .entry-body h5.is-style-vk-heading-dotted_bottomborder_black,
		.mainSection .entry-body h6.is-style-vk-heading-dotted_bottomborder_black{
			color:#333;
			position: relative;
			padding: 0.6em 0 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-top:none;
			border-right:none;
			border-left:none;
			border-bottom: 1px dotted #111;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			border-radius: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-dotted_bottomborder_black::before,
		.mainSection .entry-body h2.is-style-vk-heading-dotted_bottomborder_black::before,
		.mainSection .entry-body h3.is-style-vk-heading-dotted_bottomborder_black::before,
		.mainSection .entry-body h4.is-style-vk-heading-dotted_bottomborder_black::before,
		.mainSection .entry-body h5.is-style-vk-heading-dotted_bottomborder_black::before,
		.mainSection .entry-body h6.is-style-vk-heading-dotted_bottomborder_black::before,
		.mainSection .entry-body h1.is-style-vk-heading-dotted_bottomborder_black::after,
		.mainSection .entry-body h2.is-style-vk-heading-dotted_bottomborder_black::after,
		.mainSection .entry-body h3.is-style-vk-heading-dotted_bottomborder_black::after,
		.mainSection .entry-body h4.is-style-vk-heading-dotted_bottomborder_black::after,
		.mainSection .entry-body h5.is-style-vk-heading-dotted_bottomborder_black::after,
		.mainSection .entry-body h6.is-style-vk-heading-dotted_bottomborder_black::after { content:none;}

		.mainSection .entry-body h1.is-style-vk-heading-both_ends,
		.mainSection .entry-body h2.is-style-vk-heading-both_ends,
		.mainSection .entry-body h3.is-style-vk-heading-both_ends,
		.mainSection .entry-body h4.is-style-vk-heading-both_ends,
		.mainSection .entry-body h5.is-style-vk-heading-both_ends,
		.mainSection .entry-body h6.is-style-vk-heading-both_ends{
			color:#333;
			position: relative;
			border:none;
			display: flex;
			align-items: center;
			text-align: center;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			padding:0;
			border-radius: unset;
			background-color:transparent;
			outline: unset;
			outline-offset: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-both_ends::before,
		.mainSection .entry-body h2.is-style-vk-heading-both_ends::before,
		.mainSection .entry-body h3.is-style-vk-heading-both_ends::before,
		.mainSection .entry-body h4.is-style-vk-heading-both_ends::before,
		.mainSection .entry-body h5.is-style-vk-heading-both_ends::before,
		.mainSection .entry-body h6.is-style-vk-heading-both_ends::before{
			content: "";
			flex-grow: 1;
			border-bottom: 1px solid #333;
			margin-right: 1em;
			top: unset;
			position: unset;
			width: unset;
			border-top: none;
		}
		.mainSection .entry-body h1.is-style-vk-heading-both_ends::after,
		.mainSection .entry-body h2.is-style-vk-heading-both_ends::after,
		.mainSection .entry-body h3.is-style-vk-heading-both_ends::after,
		.mainSection .entry-body h4.is-style-vk-heading-both_ends::after,
		.mainSection .entry-body h5.is-style-vk-heading-both_ends::after,
		.mainSection .entry-body h6.is-style-vk-heading-both_ends::after{
			content: "";
			flex-grow: 1;
			border-bottom: 1px solid #333;
			margin-left: 1em;
			bottom: unset;
			position: unset;
			width: unset;
			border-top: none;
		}

		.mainSection .entry-body h1.is-style-vk-heading-leftborder,
		.mainSection .entry-body h2.is-style-vk-heading-leftborder,
		.mainSection .entry-body h3.is-style-vk-heading-leftborder,
		.mainSection .entry-body h4.is-style-vk-heading-leftborder,
		.mainSection .entry-body h5.is-style-vk-heading-leftborder,
		.mainSection .entry-body h6.is-style-vk-heading-leftborder{
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0.7em 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-left:solid 2px ' . $color_key . ';
			background-color: #efefef;
			text-align:left;
			outline: unset;
			outline-offset: unset;
			border-radius: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-leftborder::before,
		.mainSection .entry-body h2.is-style-vk-heading-leftborder::before,
		.mainSection .entry-body h3.is-style-vk-heading-leftborder::before,
		.mainSection .entry-body h4.is-style-vk-heading-leftborder::before,
		.mainSection .entry-body h5.is-style-vk-heading-leftborder::before,
		.mainSection .entry-body h6.is-style-vk-heading-leftborder::before,
		.mainSection .entry-body h1.is-style-vk-heading-leftborder::after,
		.mainSection .entry-body h2.is-style-vk-heading-leftborder::after,
		.mainSection .entry-body h3.is-style-vk-heading-leftborder::after,
		.mainSection .entry-body h4.is-style-vk-heading-leftborder::after,
		.mainSection .entry-body h5.is-style-vk-heading-leftborder::after,
		.mainSection .entry-body h6.is-style-vk-heading-leftborder::after { content:none;}

		.mainSection .entry-body h1.is-style-vk-heading-leftborder_nobackground,
		.mainSection .entry-body h2.is-style-vk-heading-leftborder_nobackground,
		.mainSection .entry-body h3.is-style-vk-heading-leftborder_nobackground,
		.mainSection .entry-body h4.is-style-vk-heading-leftborder_nobackground,
		.mainSection .entry-body h5.is-style-vk-heading-leftborder_nobackground,
		.mainSection .entry-body h6.is-style-vk-heading-leftborder_nobackground{
			color:#333;
			position: relative;
			border:none;
			padding: 0.6em 0.7em 0.5em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			border-left:solid 2px ' . $color_key . ';
			background-color:transparent;
			text-align:left;
			outline: unset;
			outline-offset: unset;
			border-radius: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-leftborder_nobackground::before,
		.mainSection .entry-body h2.is-style-vk-heading-leftborder_nobackground::before,
		.mainSection .entry-body h3.is-style-vk-heading-leftborder_nobackground::before,
		.mainSection .entry-body h4.is-style-vk-heading-leftborder_nobackground::before,
		.mainSection .entry-body h5.is-style-vk-heading-leftborder_nobackground::before,
		.mainSection .entry-body h6.is-style-vk-heading-leftborder_nobackground::before,
		.mainSection .entry-body h1.is-style-vk-heading-leftborder_nobackground::after,
		.mainSection .entry-body h2.is-style-vk-heading-leftborder_nobackground::after,
		.mainSection .entry-body h3.is-style-vk-heading-leftborder_nobackground::after,
		.mainSection .entry-body h4.is-style-vk-heading-leftborder_nobackground::after,
		.mainSection .entry-body h5.is-style-vk-heading-leftborder_nobackground::after,
		.mainSection .entry-body h6.is-style-vk-heading-leftborder_nobackground::after{content:none;}

		.mainSection .entry-body h1.is-style-vk-heading-diagonal_stripe_bottomborder,
		.mainSection .entry-body h2.is-style-vk-heading-diagonal_stripe_bottomborder,
		.mainSection .entry-body h3.is-style-vk-heading-diagonal_stripe_bottomborder,
		.mainSection .entry-body h4.is-style-vk-heading-diagonal_stripe_bottomborder,
		.mainSection .entry-body h5.is-style-vk-heading-diagonal_stripe_bottomborder,
		.mainSection .entry-body h6.is-style-vk-heading-diagonal_stripe_bottomborder{
			color:#333;
			position: relative;
			border:none;
			padding: 0.5em 0 0.7em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			background-color: transparent;
			outline: unset;
			outline-offset: unset;
			border-radius: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-diagonal_stripe_bottomborder::before,
		.mainSection .entry-body h2.is-style-vk-heading-diagonal_stripe_bottomborder::before,
		.mainSection .entry-body h3.is-style-vk-heading-diagonal_stripe_bottomborder::before,
		.mainSection .entry-body h4.is-style-vk-heading-diagonal_stripe_bottomborder::before,
		.mainSection .entry-body h5.is-style-vk-heading-diagonal_stripe_bottomborder::before,
		.mainSection .entry-body h6.is-style-vk-heading-diagonal_stripe_bottomborder::before{ content: none; }
		.mainSection .entry-body h1.is-style-vk-heading-diagonal_stripe_bottomborder::after,
		.mainSection .entry-body h2.is-style-vk-heading-diagonal_stripe_bottomborder::after,
		.mainSection .entry-body h3.is-style-vk-heading-diagonal_stripe_bottomborder::after,
		.mainSection .entry-body h4.is-style-vk-heading-diagonal_stripe_bottomborder::after,
		.mainSection .entry-body h5.is-style-vk-heading-diagonal_stripe_bottomborder::after,
		.mainSection .entry-body h6.is-style-vk-heading-diagonal_stripe_bottomborder::after{
			content:"";
			position: absolute;
			left: 0;
			bottom: 0;
			width: 100%;
			height: 7px;
			border: none !important;
			background: linear-gradient(
				-45deg,
				rgba(255,255,255,0.1) 25%, ' . $color_key . ' 25%,
				50%, rgba(255,255,255,0.1) 50%,
				rgba(255,255,255,0.1) 75%, ' . $color_key . ' 75%
			);
			background-size: 5px 5px;
			margin-left: 0;
		}

		.mainSection .entry-body h1.is-style-vk-heading-brackets,
		.mainSection .entry-body h2.is-style-vk-heading-brackets,
		.mainSection .entry-body h3.is-style-vk-heading-brackets,
		.mainSection .entry-body h4.is-style-vk-heading-brackets,
		.mainSection .entry-body h5.is-style-vk-heading-brackets,
		.mainSection .entry-body h6.is-style-vk-heading-brackets{
			color:#333;
			position: relative;
			border:none;
			padding: 0.7em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			text-align: center;
			background-color:transparent !important;
			border-bottom:unset !important;
			outline: unset;
			outline-offset: unset;
			border-radius: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-brackets::before,
		.mainSection .entry-body h2.is-style-vk-heading-brackets::before,
		.mainSection .entry-body h3.is-style-vk-heading-brackets::before,
		.mainSection .entry-body h4.is-style-vk-heading-brackets::before,
		.mainSection .entry-body h5.is-style-vk-heading-brackets::before,
		.mainSection .entry-body h6.is-style-vk-heading-brackets::before{
			content:"";
			position: absolute;
			top: 0;
			width: 12px;
			height: 100%;
			display: inline-block;
			border-top: solid 1px ' . $color_key . ';
			border-bottom: solid 1px ' . $color_key . ';
			margin-left:0;
			border-left: solid 1px ' . $color_key . ';
			left: 0;
		}
		.mainSection .entry-body h1.is-style-vk-heading-brackets::after,
		.mainSection .entry-body h2.is-style-vk-heading-brackets::after,
		.mainSection .entry-body h3.is-style-vk-heading-brackets::after,
		.mainSection .entry-body h4.is-style-vk-heading-brackets::after,
		.mainSection .entry-body h5.is-style-vk-heading-brackets::after,
		.mainSection .entry-body h6.is-style-vk-heading-brackets::after{
			content:"";
			position: absolute;
			top: 0;
			width: 12px;
			height: 100%;
			display: inline-block;
			border-top: solid 1px ' . $color_key . ';
			border-bottom: solid 1px ' . $color_key . ';
			margin-left:0;
			border-right: solid 1px ' . $color_key . ';
			right: 0;
			left: auto;
		}

		.mainSection .entry-body h1.is-style-vk-heading-brackets_black,
		.mainSection .entry-body h2.is-style-vk-heading-brackets_black,
		.mainSection .entry-body h3.is-style-vk-heading-brackets_black,
		.mainSection .entry-body h4.is-style-vk-heading-brackets_black,
		.mainSection .entry-body h5.is-style-vk-heading-brackets_black,
		.mainSection .entry-body h6.is-style-vk-heading-brackets_black{
			color:#333;
			position: relative;
			border:none;
			padding: 0.7em;
			margin-bottom:1.2em;
			margin-left: unset;
			margin-right: unset;
			text-align: center;
			background-color:transparent;
			border-bottom:unset;
			outline: unset;
			outline-offset: unset;
			border-radius: unset;
			box-shadow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-brackets_black::before,
		.mainSection .entry-body h2.is-style-vk-heading-brackets_black::before,
		.mainSection .entry-body h3.is-style-vk-heading-brackets_black::before,
		.mainSection .entry-body h4.is-style-vk-heading-brackets_black::before,
		.mainSection .entry-body h5.is-style-vk-heading-brackets_black::before,
		.mainSection .entry-body h6.is-style-vk-heading-brackets_black::before{
			content:"";
			position: absolute;
			top: 0;
			width: 12px;
			height: 100%;
			display: inline-block;
			border-top: solid 1px #333;
			border-bottom: solid 1px #333;
			margin-left:0;
			border-left: solid 1px #333;
			left: 0;
		}
		.mainSection .entry-body h1.is-style-vk-heading-brackets_black::after,
		.mainSection .entry-body h2.is-style-vk-heading-brackets_black::after,
		.mainSection .entry-body h3.is-style-vk-heading-brackets_black::after,
		.mainSection .entry-body h4.is-style-vk-heading-brackets_black::after,
		.mainSection .entry-body h5.is-style-vk-heading-brackets_black::after,
		.mainSection .entry-body h6.is-style-vk-heading-brackets_black::after{
			content:"";
			position: absolute;
			top: 0;
			width: 12px;
			height: 100%;
			display: inline-block;
			border-top: solid 1px #333;
			border-bottom: solid 1px #333;
			margin-left:0;
			border-right: solid 1px #333;
			right: 0;
			left: auto;
		}

		.mainSection .entry-body h1.is-style-vk-heading-small_bottomborder,
		.mainSection .entry-body h2.is-style-vk-heading-small_bottomborder,
		.mainSection .entry-body h3.is-style-vk-heading-small_bottomborder,
		.mainSection .entry-body h4.is-style-vk-heading-small_bottomborder,
		.mainSection .entry-body h5.is-style-vk-heading-small_bottomborder,
		.mainSection .entry-body h6.is-style-vk-heading-small_bottomborder{
			color:#333;
			position: relative;
			border:none;
			padding: 0;
			text-align: center;
			background-color:transparent;
			margin-left: unset;
			margin-right: unset;
			margin-bottom: 3em;
			outline: unset;
			outline-offset: unset;
			border-radius: unset;
			box-shadow: unset;
			overflow: unset;
		}
		.mainSection .entry-body h1.is-style-vk-heading-small_bottomborder::before,
		.mainSection .entry-body h2.is-style-vk-heading-small_bottomborder::before,
		.mainSection .entry-body h3.is-style-vk-heading-small_bottomborder::before,
		.mainSection .entry-body h4.is-style-vk-heading-small_bottomborder::before,
		.mainSection .entry-body h5.is-style-vk-heading-small_bottomborder::before,
		.mainSection .entry-body h6.is-style-vk-heading-small_bottomborder::before{content: none;}
		.mainSection .entry-body h1.is-style-vk-heading-small_bottomborder::after,
		.mainSection .entry-body h2.is-style-vk-heading-small_bottomborder::after,
		.mainSection .entry-body h3.is-style-vk-heading-small_bottomborder::after,
		.mainSection .entry-body h4.is-style-vk-heading-small_bottomborder::after,
		.mainSection .entry-body h5.is-style-vk-heading-small_bottomborder::after,
		.mainSection .entry-body h6.is-style-vk-heading-small_bottomborder::after{
			content: "";
			display: inline-block;
			position: absolute;
			left: 50%;
			margin-left: -19px;
			bottom: -24px;
			width: 38px;
			border-top: solid 2px ' . $color_key . ';
		}

		';

		// delete before after space
		$dynamic_css = trim( $dynamic_css );
		// convert tab and br to space
		$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
		// Change multiple spaces to single space
		$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
		wp_add_inline_style( 'lightning-design-style', $dynamic_css );
	} // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {
}

add_action( 'wp_head', 'lightning_print_css_vk_heading_style', 5 );
