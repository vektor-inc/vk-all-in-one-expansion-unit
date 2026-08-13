import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, BaseControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';

export default function ShareButtonEdit( props ) {
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
				'.veu_share_button_block .veu_socialSet'
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
		className: `veu_share_button_block ${ className }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Style Setting',
						'vk-all-in-one-expansion-unit'
					) }
					initialOpen={ true }
				>
					<BaseControl>
						<p>
							{ __(
								'You can configure the icon style from the admin panel under ExUnit > Main Settings > SNS Setting.',
								'vk-all-in-one-expansion-unit'
							) }
						</p>
					</BaseControl>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block="vk-blocks/share-button"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
