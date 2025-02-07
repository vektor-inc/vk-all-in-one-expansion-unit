import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, BaseControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function ShareButtonEdit( props ) {
	const { attributes } = props;
	const { className } = attributes;

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
