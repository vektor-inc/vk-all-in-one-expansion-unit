import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { useEffect } from '@wordpress/element';

export default function SiteMapEdit( props ) {
	const { attributes } = props;
	const { className } = attributes;

	useEffect( () => {
		// エディターキャンバス（記事プレビュー領域）の iframe だけを対象にする。
		// 本文中の外部埋め込み（YouTube 等）の iframe を誤って掴まないよう、
		// WordPress がエディターキャンバスに付与する name="editor-canvas" で絞り込む。
		// contentWindow.document は cross-origin の iframe に対して SecurityError を
		// 投げるため、例外を投げず null を返す contentDocument を使う。念のため
		// try/catch でも保護し、失敗時は document にフォールバックする。
		//
		// Only target the editor canvas iframe (the post preview area) by
		// scoping the selector to name="editor-canvas" (the name WordPress
		// assigns to it), so we never grab an embedded external iframe (e.g.
		// YouTube) in the post content. contentWindow.document throws
		// SecurityError for cross-origin iframes, so use contentDocument,
		// which returns null instead. Wrapped in try/catch as a safety net,
		// falling back to document on failure.
		let targetDoc = document;
		try {
			const canvas = document.querySelector(
				'iframe[name="editor-canvas"]'
			);
			targetDoc = canvas?.contentDocument || document;
		} catch ( e ) {
			targetDoc = document;
		}

		// eslint-disable-next-line no-undef
		const observer = new MutationObserver( () => {
			const editorRoot = targetDoc.querySelector(
				'.block-editor-block-list__layout'
			);
			if ( ! editorRoot ) {
				return;
			}

			const shareButtonLinks = editorRoot.querySelectorAll(
				'.veu_sitemap_block .veu_sitemap'
			);
			if ( shareButtonLinks.length === 0 ) {
				return;
			}

			shareButtonLinks.forEach( ( link ) => {
				if ( link.dataset.prevented ) {
					return;
				} // 二重適用防止

				link.dataset.prevented = 'true';
				link.addEventListener( 'click', function ( event ) {
					event.preventDefault();
					link.style.cursor = 'default';
					link.style.boxShadow = 'unset';
					link.style.color = 'inherit';
					link.style.textDecorationColor = 'inherit';
					link.style.pointerEvents = 'none';
				} );
				link.addEventListener( 'mouseover', function ( event ) {
					event.preventDefault();
					link.style.cursor = 'default';
					link.style.boxShadow = 'unset';
					link.style.color = 'inherit';
					link.style.textDecorationColor = 'inherit';
					link.style.pointerEvents = 'none';
				} );
			} );
		} );

		const observeTarget =
			targetDoc.querySelector( '.block-editor-block-list__layout' ) ||
			targetDoc.body;
		if ( observeTarget ) {
			observer.observe( observeTarget, {
				childList: true,
				subtree: true,
			} );
		}

		// クリーンアップ
		return () => {
			observer.disconnect();
		};
	}, [] );

	const blockProps = useBlockProps( {
		className: `veu_sitemap_block ${ className }`,
	} );

	return (
		<>
			<div { ...blockProps }>
				<ServerSideRender
					block="vk-blocks/sitemap"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
