(()=>{"use strict";var e={n:t=>{var n=t&&t.__esModule?()=>t.default:()=>t;return e.d(n,{a:n}),n},d:(t,n)=>{for(var o in n)e.o(n,o)&&!e.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:n[o]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.wp.blocks,n=window.React;var o,l,a;function r(){return r=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)({}).hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e},r.apply(null,arguments)}var i=function(e){return n.createElement("svg",r({xmlns:"http://www.w3.org/2000/svg",width:24,height:24,fill:"none"},e),o||(o=n.createElement("path",{fill:"#000",d:"m12.39 14.778-3.267.008a.11.11 0 0 0-.102.075l-.25.722c-.022.076.03.152.103.152h1.27c.095 0 .146.122.08.19L8.2 18.105h.007l1.042 3.397c.022.076-.03.145-.103.145h-1.02a.104.104 0 0 1-.102-.076L7.5 19.83c-.029-.107-.168-.107-.205-.008l-.426 1.223a.1.1 0 0 0 0 .069l.39 1.481c.014.046.058.084.102.084h3.288c.073 0 .125-.076.103-.145l-1.329-4.277c-.014-.038 0-.084.03-.114l3.016-3.176c.066-.069.015-.19-.08-.19"})),l||(l=n.createElement("path",{fill:"#D8141C",d:"m8.522 13-1.99.008a.11.11 0 0 0-.102.076l-.257.721c-.03.076.03.152.103.152h.836c.074 0 .125.076.103.152l-2.37 6.717a.108.108 0 0 1-.206 0l-1.703-4.848a.112.112 0 0 1 .103-.152h.859a.11.11 0 0 1 .103.076l.616 1.748a.108.108 0 0 0 .206 0l.954-2.72a.112.112 0 0 0-.103-.152H1.608c-.074 0-.125.076-.103.152l3.127 8.996a.108.108 0 0 0 .205 0l3.787-10.774c.022-.076-.029-.152-.102-.152"})),a||(a=n.createElement("path",{fill:"#000",fillRule:"evenodd",d:"M5.5 6h14a.5.5 0 0 1 .5.5v6.422l-1.389-1.235c-1.172-1.043-2.861-.11-2.861 1.324V14h-2.018c.283.433.36.996.154 1.5h1.864v4.489c0 1.536 1.918 2.442 3.023 1.16l1.55-1.799 2.271-.094c1.63-.068 2.186-2.083 1.065-3.08l-2.27-2.019q.11-.31.111-.657v-7a2 2 0 0 0-2-2h-14a2 2 0 0 0-2 2v6.778h1.269l.231-.65V6.5a.5.5 0 0 1 .5-.5m3 4.75h8v-1.5h-8zm8.75 2.26c0-.12.063-.2.142-.238a.2.2 0 0 1 .11-.02.2.2 0 0 1 .113.056l5.047 4.488a.27.27 0 0 1 .072.297.26.26 0 0 1-.08.122.2.2 0 0 1-.122.042l-2.292.095c-.404.018-.78.203-1.044.509l-1.559 1.809a.2.2 0 0 1-.114.076.2.2 0 0 1-.117-.012c-.087-.035-.156-.115-.156-.245z",clipRule:"evenodd"})))};const c=JSON.parse('{"apiVersion":3,"name":"vk-blocks/cta","title":"CTA","category":"veu-block","description":"Display CTA Content","textdomain":"vk-all-in-one-expansion-unit","attributes":{"postId":{"type":"string","default":""}},"supports":{"className":true}}'),s=window.wp.i18n,u=window.wp.blockEditor,d=window.wp.serverSideRender;var p=e.n(d);const v=window.wp.components,m=window.wp.element;function b(e){return b="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},b(e)}var f,y,w,h=c.name,k={icon:React.createElement(i,null),edit:function(e){var t,n=e.attributes,o=e.setAttributes,l=n.postId,a=document.querySelector(".block-editor__container iframe"),r=null==a||null===(t=a.contentWindow)||void 0===t?void 0:t.document,i=(null==r?void 0:r.querySelector(".block-editor-block-list__layout"))||document.querySelector(".block-editor-block-list__layout");(0,m.useEffect)((function(){i&&i.querySelectorAll(".veu-cta-block").forEach((function(e){e.addEventListener("click",(function(t){t.preventDefault(),e.style.cursor="default",e.style.boxShadow="unset",e.style.color="inherit",e.style.textDecorationColor="inherit",e.style.pointerEvents="none"})),e.addEventListener("mouseover",(function(t){t.preventDefault(),e.style.cursor="default",e.style.boxShadow="unset",e.style.color="inherit",e.style.textDecorationColor="inherit",e.style.pointerEvents="none"}))}))}),[i]);var c,d=veuBlockOption,b=d.cta_option,f=d.cta_posts_exist,y=d.admin_url,w="";wp.data.select("core/editor")&&wp.data.select("core/editor").getEditedPostAttribute("meta")&&wp.data.select("core/editor").getEditedPostAttribute("meta").vkexunit_cta_each_option&&(w=wp.data.select("core/editor").getEditedPostAttribute("meta").vkexunit_cta_each_option),c="false"===f?React.createElement("div",{className:"veu-cta-block-edit-alert alert alert-warning"},React.createElement("div",{className:"alert-title"},(0,s.__)("No CTA registered.","vk-all-in-one-expansion-unit")),"["," ",React.createElement("a",{href:y+"edit.php?post_type=cta",target:"_blank",rel:"noopener noreferrer"},(0,s.__)("Register CTA","vk-all-in-one-expansion-unit"))," ","]"):"disable"===w?React.createElement("div",{className:"veu-cta-block-edit-alert"},(0,s.__)("Because displaying CTA is disabled. The block render no content.","vk-all-in-one-expansion-unit")):""!==l&&null!=l?React.createElement(p(),{block:"vk-blocks/cta",attributes:n}):React.createElement("div",{className:"veu-cta-block-edit-alert alert alert-warning"},(0,s.__)("Please select CTA from Setting sidebar.","vk-all-in-one-expansion-unit"));var h=(0,u.useBlockProps)({className:"veu-cta-block-edit"});return React.createElement(React.Fragment,null,React.createElement(u.InspectorControls,null,React.createElement(v.PanelBody,{title:(0,s.__)("CTA Setting","vk-all-in-one-expansion-unit"),initialOpen:!0},React.createElement(v.SelectControl,{label:(0,s.__)("Select CTA","vk-all-in-one-expansion-unit"),id:"veu-cta-block-select",value:l,options:b,onChange:function(e){o({postId:e})}}))),React.createElement("div",h,c))}};(0,t.unstable__bootstrapServerSideBlockDefinitions)((f={},w=c,(y=function(e){var t=function(e){if("object"!=b(e)||!e)return e;var t=e[Symbol.toPrimitive];if(void 0!==t){var n=t.call(e,"string");if("object"!=b(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"==b(t)?t:t+""}(y=h))in f?Object.defineProperty(f,y,{value:w,enumerable:!0,configurable:!0,writable:!0}):f[y]=w,f)),(0,t.registerBlockType)(c,k)})();