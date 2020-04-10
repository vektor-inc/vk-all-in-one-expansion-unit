!(function(wp){
  const { __ } = wp.i18n
  const { registerBlockType } = wp.blocks
  const { ServerSideRender, PanelBody } = wp.components
  const { Fragment } = wp.element

  registerBlockType("vk-blocks/child-page-index", {
    title: __("Child page index", "vk-all-in-one-expansion-unit"),
    icon: 'editor-ul',
    category: "vk-blocks-cat",
    edit: ({className}) => {
      return (
        <Fragment>
          <div className='veu_child_page_list_block'>
            <ServerSideRender
              block="vk-blocks/child-page-index"
              attributes={{className: className}}
            />
          </div>
        </Fragment>
      )
    },
    save: () => null
  });
})(wp);