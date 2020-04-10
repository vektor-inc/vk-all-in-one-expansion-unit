!(function(wp){
  const { __ } = wp.i18n
  const { registerBlockType } = wp.blocks
  const { ServerSideRender, PanelBody } = wp.components
  const { Fragment } = wp.element

  registerBlockType("vk-blocks/contact-section", {
    title: __("Contact section", "vk-all-in-one-expansion-unit"),
    icon: 'phone',
    category: "vk-blocks-cat",
    edit: ({className}) => {
      return (
        <Fragment>
          <div className='veu_contact_section_block'>
            <ServerSideRender
              block="vk-blocks/contact-section"
              attributes={{className: className}}
            />
          </div>
        </Fragment>
      )
    },
    save: () => null
  });
})(wp)
