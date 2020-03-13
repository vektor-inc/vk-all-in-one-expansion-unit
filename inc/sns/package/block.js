
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { ServerSideRender, PanelBody } = wp.components;
const { Fragment } = wp.element;
const { InspectorControls } =
  wp.blockEditor && wp.blockEditor.BlockEdit ? wp.blockEditor : wp.editor;

registerBlockType("vk-blocks-widget/share-button", {
  title: __("Share button", "vk-all-in-one-expansion-unit"),
  icon: 'share',
  category: "vk-blocks-widget",
  edit: ({className}) => {
    return (
        <Fragment>
          <InspectorControls>
            <PanelBody title={__("Share Button setting", "vew-blocks")}>
              <p>{__("if set enable sharebutton, display share buttons.", "vew-blocks")}</p>
            </PanelBody>
          </InspectorControls>

          <div className={`${className} vew_share_button_block`} >
            <ServerSideRender
              block="vk-blocks-widget/share-button"
              attributes={{position: 'After'}}
            />
          </div>
        </Fragment>
      )
  },
  save: () => {
    return null;
  }
});
