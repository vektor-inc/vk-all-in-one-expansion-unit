import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, SelectControl } from '@wordpress/components';

export default function CTAEdit( props ) {
	const { attributes, setAttributes } = props;
	const { postId } = attributes;

	// eslint-disable-next-line
	const blockOption = veuBlockOption;

	// Make choice list of pages
	const options = blockOption.cta_option;
	const ctaPostsExist = blockOption.cta_posts_exist;
	const adminURL = blockOption.admin_url;

	let setting = '';
	if (
		wp.data.select( 'core/editor' ) &&
		wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' ) &&
		wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )
			.vkexunit_cta_each_option
	) {
		setting = wp.data
			.select( 'core/editor' )
			.getEditedPostAttribute( 'meta' ).vkexunit_cta_each_option;
	}

	let editContent;

	// If no CTA registered.
	if ( ctaPostsExist === 'false' ) {
		editContent = (
			<div className="veu-cta-block-edit-alert alert alert-warning">
				<div className="alert-title">
					{ __(
						'No CTA registered.',
						'vk-all-in-one-expansion-unit'
					) }
				</div>
				[{ ' ' }
				<a
					href={ adminURL + 'edit.php?post_type=cta' }
					target="_blank"
					rel="noopener noreferrer"
				>
					{ __( 'Register CTA', 'vk-all-in-one-expansion-unit' ) }
				</a>{ ' ' }
				]
			</div>
		);
		// If CTA is disabled.
	} else if ( setting === 'disable' ) {
		editContent = (
			<div className="veu-cta-block-edit-alert">
				{ __(
					'Because displaying CTA is disabled. The block render no content.',
					'vk-all-in-one-expansion-unit'
				) }
			</div>
		);
		// Normal.
	} else if ( postId !== '' && postId !== null && postId !== undefined ) {
		editContent = (
			<ServerSideRender block="vk-blocks/cta" attributes={ attributes } />
		);
		// New setqting.
	} else {
		editContent = (
			<div className="veu-cta-block-edit-alert alert alert-warning">
				{ __(
					'Please select CTA from Setting sidebar.',
					'vk-all-in-one-expansion-unit'
				) }
			</div>
		);
	}

	const blockProps = useBlockProps( {
		className: `veu-cta-block-edit`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'CTA Setting',
						'vk-all-in-one-expansion-unit'
					) }
					initialOpen={ true }
				>
					<SelectControl
						label={ __(
							'Select CTA',
							'vk-all-in-one-expansion-unit'
						) }
						id="veu-cta-block-select"
						value={ postId }
						options={ options }
						onChange={ ( value ) => {
							setAttributes( { postId: value } );
						} }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>{ editContent }</div>
		</>
	);
}
