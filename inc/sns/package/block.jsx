(function(wp){
  const { __ } = wp.i18n
  const { registerBlockType } = wp.blocks
  const { ServerSideRender, PanelBody } = wp.components
  const { Fragment } = wp.element
  const React = wp.element

  registerBlockType("vk-blocks/share-button", {
    title: __("Share button", "veu-block"),
    icon: 'share',
    category: "veu-block",
    edit: ({className}) => {
      return (
          <Fragment>
            <div className={`${className} veu_share_button_block`} >
              <ServerSideRender
                block="vk-blocks/share-button"
                attributes={{position: 'After'}}
              />
            </div>
          </Fragment>
        )
    },
    save: () => null
  })
})(wp);