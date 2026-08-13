import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, BaseControl, CheckboxControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

export default function ContactSectionEdit( props ) {
	const { attributes, setAttributes } = props;
	const { vertical } = attributes;

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
				'.veu_contact_section_block .veu_contact'
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
		className: `veu_contact_section_block`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Display conditions',
						'vk-all-in-one-expansion-unit'
					) }
					initialOpen={ false }
				>
					<BaseControl>
						<CheckboxControl
							label={ __(
								'Set telephone and mail form vertically',
								'vk-all-in-one-expansion-unit'
							) }
							className={ 'mb-1' }
							checked={ vertical }
							onChange={ ( checked ) =>
								setAttributes( { vertical: checked } )
							}
						/>
					</BaseControl>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block="vk-blocks/contact-section"
					attributes={ props.attributes }
				/>
			</div>
		</>
	);
}
