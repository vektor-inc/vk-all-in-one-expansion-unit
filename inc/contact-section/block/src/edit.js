import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, BaseControl, CheckboxControl } from '@wordpress/components';

export default function ContactSectionEdit( props ) {
	const { attributes, setAttributes } = props;
	const { vertical } = attributes;

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
