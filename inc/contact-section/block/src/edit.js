import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, BaseControl, CheckboxControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

export default function ContactSectionEdit( props ) {
	const { attributes, setAttributes } = props;
	const { vertical } = attributes;

	useEffect( () => {
		const iframe = document.querySelector(
			'.block-editor__container iframe'
		);
		const iframeDoc = iframe?.contentWindow?.document;
		const targetDoc = iframeDoc || document;

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
