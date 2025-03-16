import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, BaseControl, CheckboxControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

export default function ContactSectionEdit( props ) {
	const { attributes, setAttributes } = props;
	const { vertical } = attributes;

	// iframe がある場合それを取得
	const iframe = document.querySelector( '.block-editor__container iframe' );
	// iframe の中の document を取得
	const iframeDoc = iframe?.contentWindow?.document;

	// エディターのルート要素を取得
	const editorRoot =
		iframeDoc?.querySelector( '.block-editor-block-list__layout' ) ||
		document.querySelector( '.block-editor-block-list__layout' );

	useEffect( () => {
		if ( editorRoot ) {
			// サイトマップのリンクをクリックできないようにする
			const contsctSectionLinks = editorRoot.querySelectorAll(
				'.veu_contact_section_block .veu_contact'
			);
			contsctSectionLinks.forEach( ( link ) => {
				link.addEventListener( 'click', function ( event ) {
					event.preventDefault();
					link.style.cursor = 'default';
					link.style.boxShadow = 'unset';

					// ホバー効果を無効化
					link.style.color = 'inherit';
					link.style.textDecorationColor = 'inherit';
					link.style.pointerEvents = 'none';
				} );
				link.addEventListener( 'mouseover', function ( event ) {
					event.preventDefault();
					link.style.cursor = 'default';
					link.style.boxShadow = 'unset';

					// ホバー効果を無効化
					link.style.color = 'inherit';
					link.style.textDecorationColor = 'inherit';
					link.style.pointerEvents = 'none';
				} );
			} );
		}
	}, [ editorRoot ] );

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
