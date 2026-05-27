/**
 * Page Top Button - Customizer 親見出し ( h2 ) 化 & 余白拡大の e2e テスト
 *
 * issue #1368 / PR #1369 で行った以下の仕様変更を検証する。
 *
 * - 「Page top button image」が VK_Custom_Html_Control 経由で
 *   `<h2 class="admin-custom-h2">` として親見出しとして出力される
 * - その直下に説明文 3 段落（<p class="description">）が並ぶ
 * - 画像コントロール (`#customize-control-vkExUnit_pagetop_image_url`) には
 *   重複する label / description が出力されない（h2 + description 集約後の状態）
 * - 説明文最終段落 (`.description:last-child`) の margin-bottom が 12px
 *   かつ、画像コントロール上端との実描画 gap が 約 24px（17 × 1.4 ≒ 24）
 * - h2「Page top button image」/ h3「Image size」の上下マージンは
 *   デフォルト（admin-custom-h2 / admin-custom-h3 既定値）から拡大されていない
 *   = 過剰拡大の戻し漏れがないこと
 * - h3「Image size」と Image width / Image height 入力欄が後続している
 *
 * テスト前提:
 * - wp-env が起動済みで、PR ブランチの assets/css/vkExUnit_admin.css が
 *   `customize_controls_enqueue_scripts` 経由で読み込まれる状態であること。
 *   このスタイル enqueue が無いと PR の余白拡大が効かない（PR #1369 の 4 番目の変更点）。
 */
import { test, expect } from '@playwright/test';

const ADMIN_USER = 'admin';
const ADMIN_PASS = 'password';

test.describe( 'Page Top Button customizer heading (#1368)', () => {
	// テスト間で同じセクションの DOM を参照するだけなので
	// 並列実行で問題ないが、Customizer のロードはやや重いので
	// シリアル実行にして flakiness を下げる。
	test.describe.configure( { mode: 'serial' } );

	test.beforeEach( async ( { page } ) => {
		// 管理画面にログイン。Core が安定して提供する id ベースのセレクタを使う
		// （言語設定が日本語等になっても落ちないようにするため）。
		await page.goto( '/wp-login.php' );
		// ログイン入力欄が描画された直後に fill するため、明示的に
		// フォーカスをセットしてから値を入れる。
		// （既存 spec と同様、describe 間でフラッキー回避のため。）
		await page.locator( '#user_login' ).click();
		await page.locator( '#user_login' ).fill( ADMIN_USER );
		await page.locator( '#user_pass' ).click();
		await page.locator( '#user_pass' ).fill( ADMIN_PASS );
		await page.locator( '#wp-submit' ).click();
		await page.waitForURL( /wp-admin\// );
		// admin bar が描画されればダッシュボードロード完了とみなす。
		await page.locator( '#wpadminbar' ).waitFor();

		// Customizer の Page Top Button セクションを autofocus で直接開く。
		// `autofocus[section]=veu_pagetop_setting` でセクションが open 状態で
		// 描画されるため、別途クリックする必要がない。
		await page.goto(
			'/wp-admin/customize.php?autofocus[section]=veu_pagetop_setting'
		);
		// セクションコンテナ描画を待つ（Customizer の JS 初期化完了を待つため）。
		await page
			.locator( '#sub-accordion-section-veu_pagetop_setting' )
			.waitFor();
		// h2 (admin-custom-h2) の出現を待って、対象コントロールがレンダリング
		// されたことを確認する（autofocus はコントロール描画前に section
		// だけ作る場合があるため）。
		await page
			.locator(
				'#customize-control-vkExUnit_pagetop_image_heading h2.admin-custom-h2'
			)
			.waitFor();
	} );

	test( '「Page top button image」が h2 (admin-custom-h2) として独立コントロールに出力される', async ( {
		page,
	} ) => {
		// VK_Custom_Html_Control 経由で出力される heading コントロール li を取得。
		const headingControl = page.locator(
			'#customize-control-vkExUnit_pagetop_image_heading'
		);
		await expect( headingControl ).toBeVisible();
		// 中の h2.admin-custom-h2 が「Page top button image」テキストであること。
		const h2 = headingControl.locator( 'h2.admin-custom-h2' );
		await expect( h2 ).toHaveText( 'Page top button image' );
	} );

	test( '見出し直下に説明文 3 段落（.description）が指定の文言で並ぶ', async ( {
		page,
	} ) => {
		// 説明文 3 段落（PR 本文の指定通り）。
		// 3 つ目は `A square (1:1) image is recommended.` と
		// `Images with a very different aspect ratio may show extra empty space.`
		// が 1 段落に結合（半角スペース連結）されている実装に追従する。
		const descriptions = page.locator(
			'#customize-control-vkExUnit_pagetop_image_heading .description'
		);
		await expect( descriptions ).toHaveCount( 3 );

		await expect( descriptions.nth( 0 ) ).toHaveText(
			'Upload an image to replace the default page top button icon.'
		);
		await expect( descriptions.nth( 1 ) ).toHaveText(
			'Recommended formats: SVG, PNG, JPG, GIF, WebP.'
		);
		// 3 段落目は 2 文連結。
		await expect( descriptions.nth( 2 ) ).toHaveText(
			'A square (1:1) image is recommended. Images with a very different aspect ratio may show extra empty space.'
		);
	} );

	test( '画像コントロール (image_url) には重複する label / description が出力されない', async ( {
		page,
	} ) => {
		const imageControl = page.locator(
			'#customize-control-vkExUnit_pagetop_image_url'
		);
		await expect( imageControl ).toBeVisible();

		// 重複「Page top button image」見出し / ラベルが画像コントロール内には
		// 出ていないこと（h2 / h3 / label 要素いずれも対象に含めて検査）。
		const duplicateLabels = imageControl.locator(
			'h2, h3, label.customize-control-title, span.customize-control-title'
		);
		await expect( duplicateLabels ).toHaveCount( 0 );

		// description クラスを持つ要素が画像コントロール側には無いこと
		// （description は直前の heading コントロールに集約済み）。
		const duplicateDescriptions = imageControl.locator( '.description' );
		await expect( duplicateDescriptions ).toHaveCount( 0 );

		// 操作ボタン「Select image」が表示されていること（コントロールの実体は維持）。
		// 表示文言は WP コアの英語版を期待。
		await expect(
			imageControl.locator( 'button.upload-button' )
		).toBeVisible();
	} );

	test( '説明文最終段落の margin-bottom が 12px で、画像コントロール上端との実描画 gap が約 24px', async ( {
		page,
	} ) => {
		// PR #1369 で `.description:last-child` の margin-bottom を 5px → 12px に拡大した
		// スタイルが、`customize_controls_enqueue_scripts` 経由で読み込まれた
		// vkExUnit_admin.css によって効いていることを検証する。
		const measurement = await page.evaluate( () => {
			const heading = document.getElementById(
				'customize-control-vkExUnit_pagetop_image_heading'
			);
			const image = document.getElementById(
				'customize-control-vkExUnit_pagetop_image_url'
			);
			const lastDesc = heading?.querySelector(
				'.description:last-child'
			) as HTMLElement | null;
			if ( ! heading || ! image || ! lastDesc ) {
				return null;
			}
			const lastDescRect = lastDesc.getBoundingClientRect();
			const imageRect = image.getBoundingClientRect();
			return {
				lastDescMarginBottom:
					getComputedStyle( lastDesc ).marginBottom,
				// 説明文最終段落の下端から画像コントロール上端までの実描画 gap (px)。
				gap: imageRect.top - lastDescRect.bottom,
			};
		} );
		expect( measurement ).not.toBeNull();
		if ( measurement ) {
			expect( measurement.lastDescMarginBottom ).toBe( '12px' );
			// PR 本文に「17 × 1.4 ≒ 24px」とある通り 22〜26px の範囲に収まること。
			// 環境依存（フォントレンダリングの小数誤差）で多少ぶれるため許容幅を持たせる。
			expect( measurement.gap ).toBeGreaterThanOrEqual( 22 );
			expect( measurement.gap ).toBeLessThanOrEqual( 26 );
		}
	} );

	test( 'h2「Page top button image」/ h3「Image size」の上下マージンが過剰拡大されていない', async ( {
		page,
	} ) => {
		// PR #1369 では「余白拡大の対象を見出しから説明文〜画像サムネイル間へ修正」
		// （commit 448b73f0）したため、h2 / h3 自体のマージンはデフォルトのまま
		// である必要がある。誤って h2 / h3 のマージンを拡大してしまっていないか
		// を回帰として担保する。
		const margins = await page.evaluate( () => {
			const h2 = document
				.getElementById(
					'customize-control-vkExUnit_pagetop_image_heading'
				)
				?.querySelector( 'h2.admin-custom-h2' ) as HTMLElement | null;
			const h3 = document
				.getElementById(
					'customize-control-vkExUnit_pagetop_image_size_description'
				)
				?.querySelector( 'h3.admin-custom-h3' ) as HTMLElement | null;
			if ( ! h2 || ! h3 ) {
				return null;
			}
			const h2cs = getComputedStyle( h2 );
			const h3cs = getComputedStyle( h3 );
			return {
				h2MarginTop: parseFloat( h2cs.marginTop ),
				h2MarginBottom: parseFloat( h2cs.marginBottom ),
				h3MarginTop: parseFloat( h3cs.marginTop ),
				h3MarginBottom: parseFloat( h3cs.marginBottom ),
			};
		} );
		expect( margins ).not.toBeNull();
		if ( margins ) {
			// admin-custom-h2 / h3 のデフォルト範囲を緩めに確認する。
			// （`em` ベースなので px 換算では小数誤差が出るが、30px を越えるような
			// 大きな上下マージンが付いていない＝拡大されていないことを担保すれば足りる）。
			expect( margins.h2MarginTop ).toBeLessThan( 30 );
			expect( margins.h2MarginBottom ).toBeLessThan( 30 );
			expect( margins.h3MarginTop ).toBeLessThan( 30 );
			expect( margins.h3MarginBottom ).toBeLessThan( 30 );
		}
	} );

	test( 'h3「Image size」と Image width / Image height 入力欄が後続している', async ( {
		page,
	} ) => {
		// 画像コントロールの後ろに「Image size」見出しコントロールが続き、
		// その後に width / height 入力コントロールが続くことを検証する。
		const sizeHeading = page.locator(
			'#customize-control-vkExUnit_pagetop_image_size_description'
		);
		await expect( sizeHeading ).toBeVisible();
		await expect(
			sizeHeading.locator( 'h3.admin-custom-h3' )
		).toHaveText( 'Image size' );

		// width / height 入力欄が後続。
		const widthControl = page.locator(
			'#customize-control-vkExUnit_pagetop_image_width input[type="number"]'
		);
		const heightControl = page.locator(
			'#customize-control-vkExUnit_pagetop_image_height input[type="number"]'
		);
		await expect( widthControl ).toBeVisible();
		await expect( heightControl ).toBeVisible();

		// DOM 順序を念のため確認する。
		// 4 要素（heading → image → size_heading → width → height）の bounding box top で
		// 大小関係が常に
		//   heading < image < size_heading < width <= height
		// になっていれば、視覚的にも DOM 順通りに並んでいる。
		const order = await page.evaluate( () => {
			const ids = [
				'customize-control-vkExUnit_pagetop_image_heading',
				'customize-control-vkExUnit_pagetop_image_url',
				'customize-control-vkExUnit_pagetop_image_size_description',
				'customize-control-vkExUnit_pagetop_image_width',
				'customize-control-vkExUnit_pagetop_image_height',
			];
			return ids.map( ( id ) => {
				const el = document.getElementById( id );
				return el ? el.getBoundingClientRect().top : null;
			} );
		} );
		// 全要素描画済み（null が無い）であること。
		expect( order.every( ( v ) => v !== null ) ).toBeTruthy();
		// 順序が単調増加であること（heading が一番上、height が一番下）。
		for ( let i = 1; i < order.length; i++ ) {
			expect( order[ i ] as number ).toBeGreaterThan(
				order[ i - 1 ] as number
			);
		}
	} );
} );
