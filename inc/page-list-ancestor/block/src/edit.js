import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { useEffect } from '@wordpress/element';

export default function ContactSectionEdit( props ) {
	const { attributes } = props;
	const { className } = attributes;

	useEffect( () => {
		// エディターキャンバスの iframe だけを対象にする。本文中の外部埋め込み
		// （YouTube 等）を掴まないよう name="editor-canvas" で絞り、cross-origin で
		// 例外を投げず null を返す contentDocument を使う。
		//
		// Target only the editor canvas iframe. Scope the selector to
		// name="editor-canvas" so we never grab an embedded external iframe, and use
		// contentDocument, which returns null instead of throwing for cross-origin.
		let targetDoc = document;
		try {
			const canvas = document.querySelector(
				'iframe[name="editor-canvas"]'
			);
			targetDoc = canvas?.contentDocument || document;
		} catch {
			// 想定外の例外でも画面を落とさないためのフォールバック。
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
				'.veu_post_list_ancestor_block .veu_pageList_ancestor'
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
		className: `veu_post_list_ancestor_block ${ className }`,
	} );

	return (
		<>
			<div { ...blockProps }>
				<ServerSideRender
					block="vk-blocks/page-list-ancestor"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
