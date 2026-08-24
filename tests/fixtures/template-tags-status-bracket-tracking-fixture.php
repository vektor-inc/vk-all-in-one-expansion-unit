<?php
/**
 * Synthetic fixture for TemplateTagsStatusTest's coverage of
 * veu_template_tags_status_extract_function_names()'s brace-depth tracking.
 *
 * veu_template_tags_status_extract_function_names() の波括弧の深さ追跡をカバーするための
 * 合成フィクスチャ。
 *
 * This file is never require'd/executed by anything -- it is only ever passed as a file path to
 * veu_template_tags_status_extract_function_names(), which merely tokenizes it with
 * token_get_all() and never includes it. It is therefore fine for it to reference undefined
 * classes and to contain constructs the real ExUnit-bundled shared files don't currently use;
 * none of that code ever runs.
 *
 * このファイルは一切 require/実行されない。常に
 * veu_template_tags_status_extract_function_names() へファイルパスとして渡されるだけであり、
 * 同関数は token_get_all() でトークン化するだけで include はしない。そのため未定義のクラスを
 * 参照していても、実際の ExUnit 同梱共有ファイルでは現状使っていない書き方を含んでいても問題ない
 * （このコードが実行されることは一切ない）。
 *
 * Covers (see the review that motivated this fixture, Issue #1479):
 * (a) String interpolation ("{$var}") opens with the special T_CURLY_OPEN token but always
 *     closes with a plain '}' token, which can desync brace-depth tracking and make a later
 *     real class method look like a file-scope function.
 * (b) T_ENUM was missing from the class-like token list, so an enum's methods could be
 *     misdetected as file-scope functions (only meaningful on PHP 8.1+, where T_ENUM exists).
 * (c) `Foo::class` also tokenizes its `class` keyword as T_CLASS, which must not be mistaken for
 *     an actual class/interface/trait/enum declaration.
 *
 * @package Vk_All_In_One_Expansion_Unit
 */
function veu_bracket_fixture_before_class() {
	return true;
}

class VEU_Bracket_Fixture_Curly_Interpolation {

	public function uses_curly_interpolation() {
		$var = 'x';
		return "value: {$var}";
	}

	public function should_stay_a_method() {
		return 1;
	}
}

enum VEU_Bracket_Fixture_Enum {
	case One;
	case Two;

	public function should_stay_an_enum_method() {
		return 1;
	}
}

if ( VEU_Bracket_Fixture_Undefined_Class::class === 'x' ) {
	function veu_bracket_fixture_inside_class_const_if() {
		return true;
	}
}

function veu_bracket_fixture_after_everything() {
	return true;
}
