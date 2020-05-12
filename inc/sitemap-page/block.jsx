;(function(wp){
  const { __ } = wp.i18n
  const { registerBlockType } = wp.blocks
  const { ServerSideRender } = wp.components
  const { Fragment } = wp.element
  const React = wp.element
  const BlockIcon = (
	<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 576 512">
<g>
	<path d="M177.8,325L49,324.9c-20.6,0-37.3,16.7-37.3,37.3l-0.1,110.7c0,20.6,16.7,37.3,37.3,37.4l128.8,0.1
		c20.6,0,37.3-16.7,37.3-37.3l0.1-110.7C215.1,341.8,198.4,325,177.8,325z M57.5,464.3l0.1-93.4L169,371v93.4L57.5,464.3z"/>
	<path d="M532.7,325.3l-128.8-0.1c-20.6,0-37.3,16.7-37.3,37.3l-0.1,110.7c0,10,3.9,19.4,10.9,26.4c7,7.1,16.4,11,26.4,11l128.8,0.1
		c20.6,0,37.3-16.7,37.3-37.3l0.1-110.7C570,342.1,553.2,325.3,532.7,325.3z M412.4,464.6l0.1-93.4l111.4,0.1l-0.1,93.4L412.4,464.6
		z"/>
	<path d="M355.2,0.1L226.4,0c-20.6,0-37.3,16.7-37.3,37.3L189,148c0,10,3.9,19.4,10.9,26.4c7,7.1,16.4,11,26.4,11l128.8,0.1
		c20.6,0,37.3-16.7,37.3-37.3l0.1-110.7C392.5,16.9,375.7,0.1,355.2,0.1z M234.9,139.4L235,46l111.4,0.1l-0.1,93.4L234.9,139.4z"/>
</g>
<polygon points="308.7,237.7 308.8,200.1 272.8,200 272.7,237.7 92.1,237.5 92.1,310.2 128.1,310.2 128.1,273.5 453.4,273.9 
	453.4,310.2 489.4,310.2 489.4,237.9 "/>
	</svg>
  );

  registerBlockType("vk-blocks/sitemap", {
    title: __( 'HTML Sitemap', 'veu-block' ),
    icon: BlockIcon,
    category: "veu-block",
    edit: ({className}) => {
      return (
          <Fragment>
            <div className={`${className} veu_sitemap_block`} >
              <ServerSideRender
                block="vk-blocks/sitemap"
                attributes={{className: className}}
              />
            </div>
          </Fragment>
        )
    },
    save: () => null
  })
})(wp);
