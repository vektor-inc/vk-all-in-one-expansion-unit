import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, BaseControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';

export default function ShareButtonEdit( props ) {
	const { attributes } = props;
	const { className } = attributes;

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
			const shareButtonLinks = editorRoot.querySelectorAll(
				'.veu_share_button_block .veu_socialSet'
			);
			shareButtonLinks.forEach( ( link ) => {
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
