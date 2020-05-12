;(function(wp){
  const { __ } = wp.i18n
  const { registerBlockType } = wp.blocks
  const { ServerSideRender, PanelBody } = wp.components
  const { Fragment } = wp.element
  const React = wp.element

  registerBlockType("vk-blocks/page-list-ancestor", {
    title: __("Page list ancestor", "veu-block"),
    icon: 'editor-ul',
    category: "veu-block",
    edit: ({className}) => {
      return (
          <Fragment>
            <div className={`${className} veu_page_list_ancestor_block`} >
              <ServerSideRender
                block="vk-blocks/page-list-ancestor"
                attributes={{className: className}}
              />
            </div>
          </Fragment>
        )
    },
    save: () => null
  })
})(wp);
