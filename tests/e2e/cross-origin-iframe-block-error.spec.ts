/**
 * クロスオリジン iframe があるページでブロックがエラー表示になる不具合 - e2e テスト (#1452)
 *
 * 【不具合の概要】
 * 本文に YouTube・X などクロスオリジン（別ドメイン）の iframe 埋め込みがあるページで、
 * シェアボタン等のブロックを配置すると、編集画面で
 * 「このブロックでエラーが発生したためプレビューできません」というエラー表示に
 * 差し替わり、ブロックの中身を確認できなくなる。
 *
 * 【原因】
 * 影響を受ける各ブロックの edit.js は useEffect 内で
 *   document.querySelector( '.block-editor__container iframe' )
 * を使って最初に見つかった iframe を掴み、その contentWindow.document を読もうと
 * していた。プレビュー領域（エディターキャンバス）が iframe 化されていない環境
 * （WordPress のバージョンやサイトの条件による）では、このセレクタが本文中の
 * クロスオリジン埋め込み iframe を先に掴んでしまい、contentWindow.document への
 * アクセスがブラウザの Same-Origin Policy により SecurityError を投げる。
 * この例外が useEffect 内で発生するため React のエラー境界に捕捉され、
 * ブロック全体がエラー表示に差し替わる。
 *
 * 【修正内容】
 * - セレクタを `iframe[name="editor-canvas"]`（WordPress がエディターキャンバスに
 *   付与する name 属性）に限定し、本文中の埋め込み iframe を掴まないようにした
 * - `contentWindow.document` ではなく `contentDocument` を使用（cross-origin の
 *   iframe に対して例外を投げず null を返す）
 * - 念のため try/catch でも保護し、失敗時は document にフォールバックする
 *
 * 【再現条件を作る: エディターキャンバスの非 iframe 化】
 * Gutenberg のブロックエディタは、編集中の投稿にあるすべてのブロックが
 * apiVersion 3 以上の場合にのみキャンバスを iframe 化する（新規投稿は既定の
 * 空段落ブロック 1 つだけのため、何もしなければ常に iframe 化された状態になる）。
 * このため、素の wp-env 環境では原因の欄で説明した「本文中の埋め込みを先に掴む」
 * 状況を再現できず、テストが常に green のまま何も検出できなくなってしまう。
 *
 * この e2e 専用の mu-plugin ( tests/e2e/mu-plugins/veu-e2e-force-non-iframed-canvas.php )
 * が、apiVersion 2 のダミーブロック（veu-e2e/legacy-canvas-block）をクライアント側に
 * 登録する。このブロックが本文に 1 つあるだけで上記の判定が false になり、キャンバス
 * が非 iframe 化される。
 *
 * 【本文の用意は wp-cli で行う（Block Inserter 経由の UI 操作にしない）】
 * 当初はダミーブロック・クロスオリジン iframe・対象 6 ブロックすべてを Block
 * Inserter 経由の UI 操作で挿入していたが、次のように UI の仕様・状態管理に
 * 起因する不安定さが繰り返し出たため、生のブロックマークアップを `post_content`
 * に直接書き込む方式に変更した。
 * - "Custom HTML" というタイトルが、この検証環境で有効な別プラグインの同名ブロックと
 *   衝突し、期待していた WordPress core の Custom HTML ( core/html ) とは異なる、
 *   HTML/CSS/JS タブ付きのモーダルダイアログを持つブロックが選択されていた
 * - "Block Inserter" ボタンの aria-label が "Close Block Inserter" と部分一致し、
 *   インサーターが開いたままだと2要素にマッチして strict mode violation になった
 * - 6 ブロックを毎回インサーターの「開く→検索→選ぶ→閉じる」で挿入すると
 *   累積の所要時間が長く、テスト全体のタイムアウト（90秒）に達することがあった
 * ブロック名を明示できる `post_content` への直接書き込みなら、タイトルの衝突・
 * ボタンの状態管理・累積の操作時間といった UI 側の事情に一切左右されない。
 * ダミーブロックは save() が null（動的ブロック扱い）、対象 6 ブロックと
 * WordPress core の Custom HTML ( core/html ) もすべて動的レンダリング
 * （save() が無い、または raw content をそのまま返す）のブロックのため、
 * 属性を省略した自己終了タグ・生の HTML でそのまま書け、内容検証
 * （invalid block 警告）の対象にもならない。
 *
 * 【このテストの内容】
 * 上記の方法で、クロスオリジンを指す <iframe>（127.0.0.1 のポート 9 = discard
 * サービス宛て。実サイトへの外部通信を避けつつ、src が自オリジンと異なる時点で
 * ブラウザは cross-origin frame として扱うため、接続が成立しなくても再現条件は
 * 満たす）・非 iframe 化用ダミーブロック・影響対象の 6 ブロックすべてを最初から
 * 含む投稿を 1 件作成し、その編集画面を開いて
 * 1. 「このブロックでエラーが発生したためプレビューできません」相当の
 *    エラー境界表示が出ないこと
 * 2. 6 ブロックすべてが描画されていること
 * 3. ブラウザのコンソールに SecurityError が出ないこと
 * を確認する。
 */
import { test, expect, type Page } from '@playwright/test';
import { login } from './utils/auth';
import { runWpCli } from './utils/wp-cli';

// このスペックが作成するテスト用投稿のタイトル。
const TEST_POST_TITLE = 'Cross-origin iframe block error test (#1452)';

// エディターキャンバスの非 iframe 化を強制する e2e 専用クエリパラメータ
// （tests/e2e/mu-plugins/veu-e2e-force-non-iframed-canvas.php 参照）。
const FORCE_NON_IFRAMED_CANVAS_PARAM = 'veu_e2e_force_non_iframed_canvas';

// お問い合わせセクション機能を強制的に有効化するオプション名
// （tests/e2e/mu-plugins/veu-e2e-force-contact-section-active.php 参照）。
// このフィルターは設定画面の表示・REST の block-renderer 双方に効くため
// クエリパラメータではなくオプションでゲートしている。beforeAll で立て、
// afterAll で必ず消す（他の e2e スペック・通常アクセスに影響させないため）。
const FORCE_CONTACT_SECTION_ACTIVE_OPTION =
	'veu_e2e_force_contact_section_active';

// issue #1452 で影響を受けた 6 ブロック。
// blockName は各ブロックの block.json の "name"（post_content に直接書く際の
// ブロック識別子）、blockClass はブロックの edit.js が useBlockProps に渡している
// このテストの確認対象は「ブロックがエラー表示に差し替わっていないか」であって、
// ブロックの出力内容そのものではない。そのため描画確認にはサーバー側の出力クラス
// （例: .veu_contact_section_block）を使わない。お問い合わせセクションは問い合わせ
// ページ未設定、CTA は CTA 投稿未登録だと本来の出力クラスが付かない
// 空文字 / 別メッセージを返す作りで、プラグイン設定に依存してしまうため。
// 代わりに、エディターがブロックの種類を問わず必ず付与する `data-type` 属性
// （例: [data-type="vk-blocks/contact-section"]）で「エディター上に当該ブロックが
// 存在するか」だけを見る。これなら設定の有無に左右されない。
const TARGET_BLOCKS: ReadonlyArray< { title: string; blockName: string } > = [
	{ title: 'Share button', blockName: 'vk-blocks/share-button' },
	{ title: 'HTML Sitemap', blockName: 'vk-blocks/sitemap' },
	{
		title: 'Page List Ancestor',
		blockName: 'vk-blocks/page-list-ancestor',
	},
	{ title: 'Child Page Index', blockName: 'vk-blocks/child-page-index' },
	{ title: 'Contact Section', blockName: 'vk-blocks/contact-section' },
	{ title: 'CTA', blockName: 'vk-blocks/cta' },
];

// エディターがブロックごとに必ず付与する data-type 属性のセレクタを組み立てる。
const blockSelector = ( blockName: string ): string =>
	`[data-type="${ blockName }"]`;

// このスペックが作ったテスト用投稿だけを強制削除して初期化する。
// セットアップ失敗が flake の原因にならないよう、wp-cli の失敗は throw で表面化する。
const resetPosts = (): void => {
	let ids: string;
	try {
		ids = runWpCli( [
			'post',
			'list',
			'--post_type=post',
			`--title=${ TEST_POST_TITLE }`,
			'--post_status=any',
			'--format=ids',
		] ).trim();
	} catch ( e ) {
		const message = e instanceof Error ? e.message : String( e );
		throw new Error(
			`resetPosts: failed to list "${ TEST_POST_TITLE }" via tests-cli: ${ message }`
		);
	}
	if ( ! ids ) {
		return;
	}
	try {
		runWpCli( [ 'post', 'delete', '--force', ...ids.split( /\s+/ ) ] );
	} catch ( e ) {
		const message = e instanceof Error ? e.message : String( e );
		throw new Error(
			`resetPosts: failed to delete "${ TEST_POST_TITLE }" posts via tests-cli: ${ message }`
		);
	}
};

// 本文にクロスオリジン iframe・キャンバス非 iframe 化用ダミーブロック・影響対象の
// 6 ブロックすべてを含む下書き投稿を wp-cli で直接作成し、投稿 ID を返す。
const createTestPost = (): number => {
	const postContent = [
		// apiVersion 2 のダミーブロック（save() が null のため自己終了タグで書ける）。
		'<!-- wp:veu-e2e/legacy-canvas-block /-->',
		'',
		// クロスオリジン（別ドメイン）を指す <iframe> を含む WordPress core の
		// Custom HTML ブロック。到達不可能なローカルの discard ポート
		// （127.0.0.1:9）宛てのため、外部への実通信は発生しない。
		'<!-- wp:html -->',
		'<iframe src="http://127.0.0.1:9/" width="300" height="150" title="cross-origin embed"></iframe>',
		'<!-- /wp:html -->',
		'',
		// 影響対象の 6 ブロック。すべて動的レンダリング（ServerSideRender）の
		// ブロックのため属性を省略した自己終了タグで書ける。
		...TARGET_BLOCKS.map(
			( { blockName } ) => `<!-- wp:${ blockName } /-->`
		),
	].join( '\n' );

	let postId: string;
	try {
		postId = runWpCli( [
			'post',
			'create',
			'--post_type=post',
			`--post_title=${ TEST_POST_TITLE }`,
			'--post_status=draft',
			`--post_content=${ postContent }`,
			'--porcelain',
		] ).trim();
	} catch ( e ) {
		const message = e instanceof Error ? e.message : String( e );
		throw new Error(
			`createTestPost: failed to create test post via tests-cli: ${ message }`
		);
	}
	return Number( postId );
};

// 編集画面で Welcome guide modal が表示されたら閉じる。
const closeWelcomeModalIfPresent = async ( page: Page ): Promise< void > => {
	const modal = page.locator( '.components-modal__frame' );
	if ( await modal.isVisible() ) {
		await page.click( 'button[aria-label="Close"]' );
		// 固定スリープではなく、モーダルが実際に閉じるまで待つ。
		await modal.waitFor( { state: 'hidden', timeout: 5000 } );
	}
};

// Block Editor が操作可能になるまで待つ。
const waitForBlockEditorReady = async ( page: Page ): Promise< void > => {
	await page
		.getByRole( 'button', { name: 'Block Inserter', exact: true } )
		.waitFor( { timeout: 15000 } );
};

test.describe( '編集画面：クロスオリジン iframe があるとブロックがエラー表示になる不具合 (#1452)', () => {
	test.describe.configure( { mode: 'serial' } );
	test.setTimeout( 90 * 1000 );

	test.beforeAll( () => {
		resetPosts();
		// お問い合わせセクションブロックを登録させるためのマーカーを立てる
		// （mu-plugin veu-e2e-force-contact-section-active.php 参照）。
		runWpCli( [
			'option',
			'update',
			FORCE_CONTACT_SECTION_ACTIVE_OPTION,
			'1',
		] );
	} );

	test.afterAll( () => {
		// マーカーを消し、他の e2e スペック・通常アクセスへの影響を残さない。
		// `wp option delete` は対象が存在しないと非ゼロ終了するため
		// （beforeAll が option update に到達できず失敗した場合に起こり得る）、
		// ここで throw させると本来の失敗理由の上に後片付けのエラーが重なって
		// 読みにくくなるだけでなく、後続の resetPosts() までスキップされ、
		// マーカーが立ったまま残ってしまう（サイト全体への影響が復活する）。
		// そのため削除の失敗は握りつぶし、resetPosts() は必ず実行する。
		try {
			runWpCli( [
				'option',
				'delete',
				FORCE_CONTACT_SECTION_ACTIVE_OPTION,
			] );
		} catch ( e ) {
			const message = e instanceof Error ? e.message : String( e );
			// 後片付け失敗を握りつぶしたことに気づけるよう、テストランナーの
			// ログへ必ず出力する（utils/wp-cli.ts の
			// deletePostsTolerateMissing() と同じ方針）。
			// eslint-disable-next-line no-console
			console.warn(
				`afterAll: マーカーの削除に失敗しました ( 無視 ): ${ message }`
			);
		}
		resetPosts();
	} );

	test.beforeEach( async ( { page } ) => {
		// ログイン ( id ベースのロケータを使う共通ヘルパー。utils/auth.ts 参照 )。
		await login( page );
	} );

	test( '本文にクロスオリジン iframe がある投稿で対象 6 ブロックがエラー表示にならず、コンソールに SecurityError も出ない', async ( {
		page,
	} ) => {
		// --- コンソールエラー・ページ内未捕捉例外を収集する ---
		// 元の不具合は useEffect 内の例外を React が拾いブロックをエラー表示に
		// 差し替えるものだが、環境によっては拾われずコンソールにのみ SecurityError が
		// 出るケースもあり得るため、表示確認とは別に必ずコンソールも監視する。
		const consoleErrors: string[] = [];
		page.on( 'console', ( msg ) => {
			if ( msg.type() === 'error' ) {
				consoleErrors.push( msg.text() );
			}
		} );
		page.on( 'pageerror', ( err ) => {
			consoleErrors.push( err.message );
		} );

		// --- クロスオリジン iframe・ダミーブロック・対象 6 ブロックを含む投稿を開く ---
		const postId = createTestPost();
		await page.goto(
			`/wp-admin/post.php?post=${ postId }&action=edit&${ FORCE_NON_IFRAMED_CANVAS_PARAM }=1`
		);
		await closeWelcomeModalIfPresent( page );
		await waitForBlockEditorReady( page );

		// --- キャンバスが非 iframe 化されていること（#1452 の再現条件）を確認 ---
		await expect(
			page.locator( 'iframe[name="editor-canvas"]' )
		).toHaveCount( 0 );

		const errorBoundaryText = page.getByText(
			'This block has encountered an error and cannot be previewed.'
		);

		// --- 6 ブロックのいずれかが描画されるか、エラー境界表示が現れるまで待つ ---
		// ServerSideRender の REST 取得は並行して走るため、いずれか一つが
		// 決着すれば「読み込みが進んだ」と判断してよい。
		// Promise.race だと、先に決着しなかった残りの waitFor が待ち続け、
		// テスト終了でページが閉じられた際に誰も catch しない reject
		// （unhandled rejection）を起こすため、ロケータの OR で 1 本の
		// await にまとめる。
		const anyTargetBlock = page.locator(
			TARGET_BLOCKS.map( ( { blockName } ) =>
				blockSelector( blockName )
			).join( ',' )
		);
		await expect(
			anyTargetBlock.or( errorBoundaryText ).first()
		).toBeVisible( { timeout: 30000 } );

		// --- エラー境界表示の有無を先に確認する ---
		// 出ていれば「エラー表示になっている」と一目で分かるメッセージで失敗させる
		// （個々のブロック描画待ちのタイムアウトだけでは、原因がエラー表示への
		// 差し替えだと読み取れないため）。
		await expect(
			errorBoundaryText,
			'いずれかのブロックが "This block has encountered an error and cannot be previewed." のエラー境界表示になっている'
		).toHaveCount( 0 );

		// --- エラーでなければ 6 ブロックすべてがエディター上に存在すること ---
		for ( const { title, blockName } of TARGET_BLOCKS ) {
			await expect(
				page.locator( blockSelector( blockName ) ).first(),
				`"${ title }" ブロックがエディター上に見つからない`
			).toBeVisible( { timeout: 30000 } );
		}

		// --- ブラウザのコンソールに SecurityError が出ていないこと ---
		const securityErrors = consoleErrors.filter( ( message ) =>
			/SecurityError/i.test( message )
		);
		expect(
			securityErrors,
			`Unexpected SecurityError in console: ${ JSON.stringify(
				securityErrors
			) }`
		).toEqual( [] );
	} );
} );
