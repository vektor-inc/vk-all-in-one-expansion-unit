
const { __ } = wp.i18n
const { registerBlockType } = wp.blocks
const { ServerSideRender, PanelBody } = wp.components
const { Fragment } = wp.element
const { InspectorControls } =
  wp.blockEditor && wp.blockEditor.BlockEdit ? wp.blockEditor : wp.editor;
const React = wp.element

registerBlockType("vk-blocks/share-button", {
  title: __("Share button", "vk-all-in-one-expansion-unit"),
  icon: 'share',
  category: "vk-blocks-cat",
  edit: ({className}) => {
    return (
        <Fragment>
          <div className={`${className} vew_share_button_block`} >
            <ServerSideRender
              block="vk-blocks/share-button"
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
