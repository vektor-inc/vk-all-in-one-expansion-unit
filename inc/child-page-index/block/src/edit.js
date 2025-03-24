import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, SelectControl } from '@wordpress/components';
import { withSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

export default withSelect( ( select ) => {
	return {
		pages: select( 'core' ).getEntityRecords( 'postType', 'page', {
			_embed: true,
			per_page: -1,
		} ),
	};
} )( ( props ) => {
	const { attributes, setAttributes, pages } = props;
	const { postId } = attributes;

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
				'.veu_child_page_list_block .veu_childPage_list'
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

	// Make choice list of pages
	const options = [
		{ label: __( 'This Page', 'vk-all-in-one-expansion-unit' ), value: -1 },
	];

	// Make choice list of pages
	if ( pages !== undefined && pages !== null ) {
		const l = pages.length;
		const parents = [];
		let i = 0;
		for ( i = 0; i < l; i++ ) {
			if ( pages[ i ].parent !== 0 ) {
				parents.push( pages[ i ].parent );
			}
		}
		for ( i = 0; i < l; i++ ) {
			if ( parents.includes( pages[ i ].id ) ) {
				options.push( {
					label: pages[ i ].title.rendered,
					value: pages[ i ].id,
				} );
			}
		}
	}

	// Remove choice of the page
	/*
    const currentPostId = select("core/editor").getCurrentPostId();
    if(currentPostId){
        options = options.filter(option => option.value !== currentPostId)
    }
    */

	const blockProps = useBlockProps( {
		className: `veu_child_page_list_block`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Parent Page',
						'vk-all-in-one-expansion-unit'
					) }
					initialOpen={ true }
				>
					<SelectControl
						label={ __(
							'Parent Page',
							'vk-all-in-one-expansion-unit'
						) }
						value={ postId }
						options={ options }
						onChange={ ( value ) => {
							setAttributes( { postId: parseInt( value, 10 ) } );
						} }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block="vk-blocks/child-page-index"
					attributes={ attributes }
				/>
			</div>
		</>
	);
} );
