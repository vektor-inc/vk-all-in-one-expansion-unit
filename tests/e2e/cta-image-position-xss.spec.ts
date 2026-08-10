/**
 * CTA 画像位置 - Stored XSS 回帰テスト ( #1434 / #1439 ) - e2e テスト
 *
 * #1434 で CTA の画像位置カスタムフィールド ( vkExUnit_cta_img_position ) に
 * Stored XSS が見つかり #1438 で修正された。このスペックはその修正が壊れていないことを
 * 実ブラウザ経由で継続的に検証する。カバーするケースは次の8つ:
 *
 *   A-1 保存時の検証   : クラシック編集画面のフォームから許可値以外を送信しても、
 *                        DB には許可値 ( right/center/left ) しか保存されないこと。
 *   A-2 出力時のエスケープ : 「不正値が既に DB に保存されている状態」からの出力が
 *                        class 属性を抜け出さずエスケープされること。
 *   B-1 画像位置 left/center/right ( 3ケース ) : クラシック編集画面の実フォーム経由で
 *                        3つの許可値それぞれが保存でき、フロント表示にも反映されること。
 *   B-2 Normal が化けない : ブロックエディタのサイドバーパネルで "Normal" ( 空文字 ) を
 *                        選んで保存しても "Right" に化けないこと。
 *   B-3 タイトルの装飾   : タイトルの <br> ( 改行装飾 ) は残り、<script> は除去されること。
 *   B-4 ボタンアイコン   : アイコン用フィールドから class 以外の属性 ( onXXX等 ) は
 *                        出力に残らないこと。
 *
 * このスペックが標準環境で動くために必要な前提（#1439 で解消したもの）:
 *
 *   - CTA のメタボックスは __back_compat_meta_box => true で登録されており、
 *     ブロックエディタの既定の CTA 編集画面 ( ブロックエディタ ) には描画されない
 *     ( render_meta_box_cta() の3択セレクト: right/center/left )。
 *     一方ブロックエディタ側の独自サイドバーパネルは Normal/Right の2択のみで、
 *     B-1 ( 3値の網羅 ) はクラシック編集画面でしか検証できない。
 *     tests/e2e/mu-plugins/veu-e2e-force-classic-editor.php が、
 *     URL に `veu_e2e_classic=1` が付いている時だけ CTA をクラシックエディタにする。
 *   - A-2 は「不正値が既に DB に保存されている」状態が前提だが、#1438 の修正で
 *     通常の保存経路 ( REST / クラシックフォーム ) からは不正値を保存できなくなった。
 *     tests/e2e/mu-plugins/veu-e2e-bypass-cta-sanitize.php が、wp-cli 実行かつ
 *     専用の環境変数 ( VEU_E2E_BYPASS_CTA_SANITIZE ) 指定時のみサニタイズを無効化し、
 *     この状態を意図的に作れるようにする ( runWpCliBypassingCtaSanitize 経由 )。
 *   - wp-cli の実行は utils/wp-cli.ts の runWpCli 系ヘルパーを使う。
 *     `docker ps --filter name=tests-cli` の先頭1件を使う実装は、他プロジェクトの
 *     wp-env が同時に起動していると別プロジェクトのコンテナを掴むため使わない
 *     ( 詳細は wp-cli.ts のコメント参照 )。
 */
import { test, expect, type Page } from '@playwright/test';
import {
	runWpCli,
	runWpCliBypassingCtaSanitize,
	updatePostMetaTolerateNoop,
	deletePostsTolerateMissing,
	getPostMetaRaw,
} from './utils/wp-cli';
import { login } from './utils/auth';

// class-vk-call-to-action.php の許可値・既定値と一致させる。
const IMAGE_POSITIONS = [ 'right', 'center', 'left' ] as const;
const IMAGE_POSITION_DEFAULT = 'right';

// 画像位置の class 属性から抜け出そうとする攻撃用の値 ( #1434 で実際に悪用可能だった形 )。
const MALICIOUS_IMAGE_POSITION = 'right" onmouseover=alert(1)//';

/**
 * CTA 投稿を1件作成する ( 公開状態 )。
 *
 * @param title 投稿タイトル.
 * @return 作成した投稿の ID.
 */
const createCtaPost = ( title: string ): number => {
	const idRaw = runWpCli( [
		'post',
		'create',
		'--post_type=cta',
		`--post_title=${ title }`,
		'--post_status=publish',
		'--porcelain',
	] ).trim();
	return Number( idRaw );
};

/**
 * 通常投稿を1件作成し、指定した CTA を表示するよう vkexunit_cta_each_option を設定する。
 * ( is_cta_id() は post_config が空でなければその値をそのまま表示対象 CTA の ID として返すため、
 * 通常投稿の the_content フィルタ経由でその CTA が描画される )。
 *
 * @param title 投稿タイトル.
 * @param ctaId 表示する CTA の投稿 ID.
 * @return 作成した投稿の ID.
 */
const createHostPost = ( title: string, ctaId: number ): number => {
	const idRaw = runWpCli( [
		'post',
		'create',
		'--post_type=post',
		`--post_title=${ title }`,
		'--post_status=publish',
		'--porcelain',
	] ).trim();
	const hostId = Number( idRaw );
	runWpCli( [
		'post',
		'meta',
		'update',
		String( hostId ),
		'vkexunit_cta_each_option',
		String( ctaId ),
	] );
	return hostId;
};

/**
 * CTA のクラシック編集画面 ( `veu_e2e_classic=1` 付き ) を開く。
 *
 * @param page  Playwright の Page.
 * @param ctaId 開く CTA の投稿 ID.
 */
const gotoClassicCtaEditor = async (
	page: Page,
	ctaId: number
): Promise<void> => {
	await page.goto(
		`/wp-admin/post.php?post=${ ctaId }&action=edit&veu_e2e_classic=1`
	);
	// クラシックメタボックスの画像位置セレクトが描画されるまで待つ。
	await page.locator( '#vkExUnit_cta_img_position' ).waitFor();
};

/**
 * クラシック編集画面の Publish/Update ボタン ( id="publish" は WP core で安定 ) を押し、
 * 保存後の再描画 ( 同じ post.php?...&action=edit への遷移 ) を待つ。
 *
 * @param page  Playwright の Page.
 * @param ctaId 保存対象の CTA の投稿 ID.
 */
const submitClassicForm = async ( page: Page, ctaId: number ): Promise<void> => {
	await page.locator( '#publish' ).click();
	await page.waitForURL( new RegExp( `post\\.php\\?post=${ ctaId }&action=edit` ) );
};

// VK ExUnit のプラグインサイドバー ( PluginSidebar name="veu-settings" ) の
// アクセシブルネーム。ヘッダーのトグルボタンと Options (⋮) メニュー内の
// チェックボックス項目の両方でこの名前が使われる ( src/editor-panel/index.js の
// title={ data.panelTitle || 'VK ExUnit' } / veu_get_name() の既定値 )。
const VEU_SIDEBAR_NAME = 'VK All in One Expansion Unit';

/**
 * VK ExUnit のプラグインサイドバーを開く。
 *
 * 【ヘッダーにアイコンが出ないことがある理由（確定）】
 * Gutenberg の `<PluginSidebar>` は、ヘッダーのピン留めボタンと Options (⋮)
 * メニューの「Plugins」項目を、同一の登録から生成する。ヘッダーアイコンは
 * `isPinned`（ユーザーごとに永続化される設定）に依存する条件付き表示だが、
 * Options メニュー側の項目は登録さえ済んでいれば常に存在する。#1439 のレビューで
 * 「同じスペックなのに麗美さんの環境だけヘッダーにアイコンが出ず、サイドバー自体は
 * 開いているのに B-2 がタイムアウトする」事象が実機で確認されたのは、負荷による
 * 読み込み遅延ではなく、その環境のユーザー設定でこのサイドバーがピン留めされて
 * いなかったため（安藤さんのレビューで機構的にほぼ確定）。ヘッダーのボタンが
 * 短時間で現れない場合は Options メニューを開き、Plugins セクションの
 * チェックボックス項目（同じ VEU_SIDEBAR_NAME）をクリックする。
 *
 * 【冪等にしている理由】ヘッダーボタン・メニュー項目とも開閉の「トグル」であり、
 * 現在の開閉状態を見ずに押すと、既に開いている状態では逆に閉じてしまう。
 * Gutenberg はアクティブな complementary area（開いているサイドバー）もユーザー
 * メタへ永続化するため、一度 B-2 が成功して開いた状態のまま終了すると、次回
 * 実行時は最初から開いた状態でエディタが立ち上がり得る。「前回実行の状態が
 * 次回の実行を壊す」という、本 PR で解消してきたものと同じ種類の環境依存を
 * 生まないよう、既に開いている場合は何もしない。
 *
 * この「既に開いているか」の判定は、`getByLabel('Image position', { exact: true })`
 * を短い待機付きで確認する（ヘッダーボタンの出現判定と同じ形）。呼び出し側は
 * `page.goto()` 直後、エディタの hydration を待たずにこの関数を呼ぶため、
 * 即時判定（`isVisible()` を待機無しで評価）だと「開いているがまだ描画されて
 * いない」状態を「閉じている」と誤判定し、直後の click で逆に閉じてしまう。
 * `exact: true` を付けているのは、`getByLabel` が既定で大文字小文字を無視した
 * 部分一致になり、クラシックメタボックス側のラベル `CTA image position`
 * （`class-vk-call-to-action.php`）まで拾ってしまうため（実際には
 * `__back_compat_meta_box` と `veu_remove_legacy_metabox_on_block_editor` の
 * 二重の仕組みでブロックエディタには出ないが、この判定は「開いているか」という
 * 状態判定に使われるため、誤マッチした場合の結果が「見つからず loud に落ちる」
 * ではなく「開いていると誤認して何もせず先へ進む」に変わる。厳密化しておく）。
 *
 * 【フォールバックが不具合を隠さないか】ヘッダーボタン・Options メニュー項目の
 * どちらも `registerPlugin` + `PluginSidebar` の登録が前提のため、パネル
 * スクリプトの読み込み・登録自体が失敗した場合は両方とも消える。その場合は
 * フォールバックしても最終的に `getByLabel( 'Image position' )` の
 * `waitFor()` がタイムアウトして loud に失敗するため、本当の不具合を
 * 静かに握りつぶす経路にはならない。
 *
 * @param page Playwright の Page.
 */
const openVeuSidebar = async ( page: Page ): Promise<void> => {
	// 既に開いていれば何もしない（トグルを押すと閉じてしまうため）。
	// ページ読み込み直後はまだ描画されていないことがあるため、即時判定 ( isVisible )
	// では「開いているのにまだ描画されていない」状態を閉じていると誤判定し、
	// 直後の click で逆に閉じてしまう。ヘッダーボタンと同じく短い待機で確定させる。
	const alreadyOpen = await page
		.getByLabel( 'Image position', { exact: true } )
		.first()
		.waitFor( { state: 'visible', timeout: 5000 } )
		.then( () => true )
		.catch( () => false );

	if ( alreadyOpen ) {
		return;
	}

	const headerButton = page.getByRole( 'button', { name: VEU_SIDEBAR_NAME } );

	const headerButtonAppeared = await headerButton
		.first()
		.waitFor( { state: 'visible', timeout: 5000 } )
		.then( () => true )
		.catch( () => false );

	if ( headerButtonAppeared ) {
		await headerButton.first().click();
		return;
	}

	// ヘッダーに出ない環境向けのフォールバック: Options (⋮) メニューから開く。
	await page.getByRole( 'button', { name: 'Options' } ).first().click();
	await page
		.getByRole( 'menuitemcheckbox', { name: VEU_SIDEBAR_NAME } )
		.first()
		.click();
};

/**
 * `<select>` に許可値外のオプションを DOM 操作で追加して選択状態にする。
 *
 * 画像位置は `<select>` ( 固定3択 ) のため、ブラウザの通常操作では許可値以外を
 * 送信できない。実際の攻撃者はブラウザの UI 制約を無視して任意の値を POST できるため、
 * サーバー側の検証 ( A-1 ) を確認するにはこの方法で UI 制約を迂回する必要がある。
 *
 * @param page     Playwright の Page.
 * @param selector 対象の `<select>` の CSS セレクタ.
 * @param value    選択させたい値.
 */
const injectAndSelectRawOption = async (
	page: Page,
	selector: string,
	value: string
): Promise<void> => {
	await page.evaluate(
		( { selector: sel, value: v } ) => {
			const select = document.querySelector( sel ) as HTMLSelectElement | null;
			if ( ! select ) {
				throw new Error( `select not found: ${ sel }` );
			}
			const option = document.createElement( 'option' );
			option.value = v;
			option.selected = true;
			select.appendChild( option );
		},
		{ selector, value }
	);
};

test.describe( 'CTA image position / Stored XSS 回帰 (#1434, #1439)', () => {
	test.setTimeout( 60 * 1000 );

	test.beforeEach( async ( { page } ) => {
		await login( page );
	} );

	test( 'A-1 保存時の検証: クラシック編集画面から許可値以外を送信しても DB には許可値しか保存されない', async ( {
		page,
	} ) => {
		const ctaId = createCtaPost( 'A1 Save Validation CTA' );
		try {
			// 保存前の値を先にアサートする ( 保存が空振りしても一致してしまう誤判定を防ぐ )。
			// left をベースラインとして書き込み、既定値 right とは異なることを確認しておく。
			updatePostMetaTolerateNoop( ctaId, 'vkExUnit_cta_img_position', 'left' );
			expect( getPostMetaRaw( ctaId, 'vkExUnit_cta_img_position' ) ).toEqual( [
				'left',
			] );

			await gotoClassicCtaEditor( page, ctaId );
			// ブラウザの <select> 制約を無視し、攻撃用の値を直接選択させて実フォームで送信する。
			await injectAndSelectRawOption(
				page,
				'#vkExUnit_cta_img_position',
				MALICIOUS_IMAGE_POSITION
			);
			await submitClassicForm( page, ctaId );

			// 送信した攻撃用の値そのものは保存されず、既定値 ( right ) に丸められていること。
			// ( ベースラインが left だったため、変化していれば「実際に保存処理が走った」ことも保証できる )。
			expect( getPostMetaRaw( ctaId, 'vkExUnit_cta_img_position' ) ).toEqual( [
				IMAGE_POSITION_DEFAULT,
			] );
		} finally {
			deletePostsTolerateMissing( [ ctaId ] );
		}
	} );

	test( 'A-2 出力時のエスケープ: 不正値が既に DB に保存されていてもフロント出力で class 属性を抜け出さない', async ( {
		page,
	} ) => {
		const ctaId = createCtaPost( 'A2 Output Escaping CTA' );
		let hostId = 0;
		try {
			// 画像が無いと image_position は出力に使われないため、ダミーの添付ID ( 実在しなくてよい ) を設定する。
			updatePostMetaTolerateNoop( ctaId, 'vkExUnit_cta_img', '999999' );

			// 通常の保存経路では作れない「不正値が既に DB にある状態」を、
			// wp-cli + サニタイズ迂回 mu-plugin ( wp-cli 実行時のみ発動 ) で作る。
			runWpCliBypassingCtaSanitize( [
				'post',
				'meta',
				'update',
				String( ctaId ),
				'vkExUnit_cta_img_position',
				MALICIOUS_IMAGE_POSITION,
			] );

			// 前提となる「不正値が本当に DB に入っている」ことを先に確認する。
			expect( getPostMetaRaw( ctaId, 'vkExUnit_cta_img_position' ) ).toEqual( [
				MALICIOUS_IMAGE_POSITION,
			] );

			hostId = createHostPost( 'A2 Output Escaping Host Post', ctaId );

			await page.goto( `/?p=${ hostId }` );

			// class 属性を抜け出して新しい属性 ( onmouseover ) が生成されていないこと。
			await expect( page.locator( '[onmouseover]' ) ).toHaveCount( 0 );

			// 画像位置クラスを持つ要素自体は1つだけ描画され、そのクラス値が
			// エスケープされた攻撃用文字列を含む1つの属性値として保持されていること
			// ( ブラウザが返す class 属性値は HTML エンティティ復元後の文字列 )。
			const imageWrapper = page.locator( '.cta_body_image' );
			await expect( imageWrapper ).toHaveCount( 1 );
			const classAttr = await imageWrapper.getAttribute( 'class' );
			expect( classAttr ).toBe(
				`cta_body_image cta_body_image_${ MALICIOUS_IMAGE_POSITION }`
			);
		} finally {
			deletePostsTolerateMissing( [ ctaId, hostId ].filter( Boolean ) );
		}
	} );

	for ( const position of IMAGE_POSITIONS ) {
		test( `B-1 画像位置 ${ position }: クラシック編集画面から保存でき、フロント表示にも反映される`, async ( {
			page,
		} ) => {
			const ctaId = createCtaPost( `B1 Position ${ position } CTA` );
			let hostId = 0;
			try {
				// 画像が無いと位置クラスが出力されないため、ダミーの添付IDを設定する。
				updatePostMetaTolerateNoop( ctaId, 'vkExUnit_cta_img', '999999' );

				// 保存前の値を先にアサートする。対象の position とは異なる値をベースラインにする。
				const baseline = position === 'right' ? 'left' : 'right';
				updatePostMetaTolerateNoop(
					ctaId,
					'vkExUnit_cta_img_position',
					baseline
				);
				expect(
					getPostMetaRaw( ctaId, 'vkExUnit_cta_img_position' )
				).toEqual( [ baseline ] );

				await gotoClassicCtaEditor( page, ctaId );
				await page
					.locator( '#vkExUnit_cta_img_position' )
					.selectOption( position );
				await submitClassicForm( page, ctaId );

				// DB に対象の position が保存されていること ( baseline から実際に変化していること )。
				expect(
					getPostMetaRaw( ctaId, 'vkExUnit_cta_img_position' )
				).toEqual( [ position ] );

				// フロント表示にも反映されていること。
				hostId = createHostPost(
					`B1 Position ${ position } Host Post`,
					ctaId
				);
				await page.goto( `/?p=${ hostId }` );
				await expect(
					page.locator( `.cta_body_image_${ position }` )
				).toHaveCount( 1 );
			} finally {
				deletePostsTolerateMissing( [ ctaId, hostId ].filter( Boolean ) );
			}
		} );
	}

	test( 'B-2 Normal が化けない: ブロックエディタのサイドバーで Normal を保存しても Right に化けない', async ( {
		page,
	} ) => {
		const ctaId = createCtaPost( 'B2 Normal CTA' );
		try {
			// 保存前の値を先にアサートする。まず Right ( 空文字ではない値 ) をベースラインにする。
			updatePostMetaTolerateNoop(
				ctaId,
				'vkExUnit_cta_img_position',
				IMAGE_POSITION_DEFAULT
			);
			expect( getPostMetaRaw( ctaId, 'vkExUnit_cta_img_position' ) ).toEqual( [
				IMAGE_POSITION_DEFAULT,
			] );

			// クエリパラメータを付けないため、既定のブロックエディタで開く
			// ( CTA 投稿タイプは __back_compat_meta_box によりクラシックメタボックスが
			//   ブロックエディタに描画されないだけで、投稿タイプ自体の既定エディタはブロックエディタ )。
			await page.goto( `/wp-admin/post.php?post=${ ctaId }&action=edit` );

			// 独自パネル ( PluginSidebar name="veu-settings" ) を開く。
			// ヘッダーのトグルボタンに出ない環境でも Options メニュー経由で開けるよう
			// openVeuSidebar() でフォールバックする ( 詳細はその docblock を参照 )。
			await openVeuSidebar( page );

			// getByLabel は既定で大文字小文字を無視した部分一致になり、クラシック
			// メタボックス側のラベル "CTA image position" まで拾ってしまうため
			// exact: true で厳密化する ( openVeuSidebar() の判定と同じ理由 )。
			const positionSelect = page.getByLabel( 'Image position', {
				exact: true,
			} );
			await positionSelect.waitFor();
			await positionSelect.selectOption( '' ); // "Normal" は空文字.

			// 保存ボタンのラベルは WordPress のバージョンで Update / Save と変わるため決め打ちしない。
			await page
				.getByRole( 'region', { name: 'Editor top bar' } )
				.getByRole( 'button', { name: /^(Saved|Save|Update)$/, exact: true } )
				.click();

			// 保存完了はスナックバー通知で判定する（固定スリープにしない）。
			await page
				.locator( '.components-snackbar' )
				.filter( { hasText: /updated/i } )
				.first()
				.waitFor( { timeout: 10000 } );

			// DB には「行が無い」( [] ) ではなく「空文字で1行存在する」( [""] ) 状態で保存され、
			// Right ( "right" ) に化けていないこと。
			expect( getPostMetaRaw( ctaId, 'vkExUnit_cta_img_position' ) ).toEqual( [
				'',
			] );
		} finally {
			deletePostsTolerateMissing( [ ctaId ] );
		}
	} );

	test( 'B-3 タイトルの装飾: <br> による改行は残り、<script> は除去される', async ( {
		page,
	} ) => {
		const ctaId = createCtaPost( 'B3 Title Placeholder' );
		let hostId = 0;
		try {
			await gotoClassicCtaEditor( page, ctaId );

			// タイトル欄はクラシック編集画面共通の #title ( ブロックエディタの iframe を経由しない )。
			await page.locator( '#title' ).fill(
				'Line1<br>Line2<script>window.__veuXssFired = true;</script>'
			);
			await submitClassicForm( page, ctaId );

			hostId = createHostPost( 'B3 Title Host Post', ctaId );
			await page.goto( `/?p=${ hostId }` );

			const titleLocator = page.locator( '.cta_title' );
			// <br> は保持されるため2行に分かれて描画される。
			await expect( titleLocator.locator( 'br' ) ).toHaveCount( 1 );
			// <script> タグ自体が DOM に存在しないこと ( wp_kses_post で除去 )。
			await expect( titleLocator.locator( 'script' ) ).toHaveCount( 0 );
			// script が実行されていないこと ( 対照として window フラグが立っていないことを確認 )。
			const scriptFired = await page.evaluate(
				() => ( window as unknown as Record<string, unknown> ).__veuXssFired
			);
			expect( scriptFired ).toBeUndefined();
		} finally {
			deletePostsTolerateMissing( [ ctaId, hostId ].filter( Boolean ) );
		}
	} );

	test( 'B-4 ボタンアイコン: アイコン用フィールドから class 以外の属性は出力に残らない', async ( {
		page,
	} ) => {
		const ctaId = createCtaPost( 'B4 Button Icon CTA' );
		let hostId = 0;
		try {
			await gotoClassicCtaEditor( page, ctaId );

			// ボタンが描画されるには URL とボタンテキストの両方が必要 ( view-actionbox.php 参照 )。
			// メタボックス自体の外側 <div> にも同じ id="vkExUnit_cta_url" が付与されているため
			// ( add_meta_box() の第1引数と URL 入力欄の id が偶然一致している )、
			// input 要素であることまでセレクタで明示して一意にする。
			await page
				.locator( 'input#vkExUnit_cta_url' )
				.fill( 'https://example.com/' );
			await page.locator( '#vkExUnit_cta_button_text' ).fill( 'Click me' );
			// class 以外の属性 ( onclick ) を含む <i> タグを入力する。
			await page
				.locator( '#vkExUnit_cta_button_icon_before' )
				.fill( '<i class="fa fa-star" onclick="alert(1)"></i>' );
			await submitClassicForm( page, ctaId );

			hostId = createHostPost( 'B4 Button Icon Host Post', ctaId );
			await page.goto( `/?p=${ hostId }` );

			// アイコン用に新規生成される <i class="... font_icon" aria-hidden="true"> にのみ
			// class 属性を含み、onclick 等の属性は出力に残っていないこと。
			const icon = page.locator( '.cta_body_link i.font_icon' );
			await expect( icon ).toHaveCount( 1 );
			await expect( icon ).toHaveAttribute( 'class', /fa-star/ );
			await expect( icon ).not.toHaveAttribute( 'onclick', /.*/ );
			// ページ全体には SNS シェアボタン等、本機能と無関係な正当な onclick 属性がある
			// ( 例: sb_icon_inner の window.open ポップアップ ) ため、CTA の描画範囲 ( .veu_cta )
			// に限定して onclick が混入していないことを確認する。
			await expect(
				page.locator( '.veu_cta [onclick]' )
			).toHaveCount( 0 );
		} finally {
			deletePostsTolerateMissing( [ ctaId, hostId ].filter( Boolean ) );
		}
	} );
} );
