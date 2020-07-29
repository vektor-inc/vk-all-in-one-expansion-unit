(function(wp){
  const { __ } = wp.i18n
  const { registerBlockType } = wp.blocks
  const ServerSideRender = wp.serverSideRender;
  const { PanelBody, BaseControl, CheckboxControl } = wp.components;
  const { Fragment } = wp.element;
  const { InspectorControls } = wp.blockEditor;
  const React = wp.element

  registerBlockType("vk-blocks/contact-section", {
    title: __("Contact section", "veu-block"),
    icon: 'phone',
	category: "veu-block",
	attributes: {
        vertical: {
            type: "boolean",
			default: false,
        },
    },
    edit: ( props ) => {
		const { attributes, setAttributes, className } = props;
		const { vertical } = attributes;

      return (
        <Fragment>
			<InspectorControls>
				<PanelBody
					title={__("Display conditions", "vk-all-in-one-expansion-unit")}
					initialOpen={false}
				>
					<BaseControl label={__("Set telephone and mail form vertically", "vk-all-in-one-expansion-unit")}>
						<CheckboxControl
							className={ "mb-1" }
							checked={ vertical }
							onChange={ (checked) => setAttributes({ vertical: checked }) }
						/>
					</BaseControl>
				</PanelBody>
			</InspectorControls>
			<div className='veu_contact_section_block'>
				<ServerSideRender
				block="vk-blocks/contact-section"
				attributes={ props.attributes }
				/>
          	</div>
        </Fragment>
      )
    },
    save: () => null
  });
})(wp)
