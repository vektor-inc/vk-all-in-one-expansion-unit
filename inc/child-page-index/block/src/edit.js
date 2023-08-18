import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, SelectControl } from '@wordpress/components';
import { withSelect } from '@wordpress/data';

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
