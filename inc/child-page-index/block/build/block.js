(()=>{"use strict";var e={n:t=>{var n=t&&t.__esModule?()=>t.default:()=>t;return e.d(n,{a:n}),n},d:(t,n)=>{for(var o in n)e.o(n,o)&&!e.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:n[o]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.wp.blocks,n=window.React;var o,r,l;function a(){return a=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)({}).hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e},a.apply(null,arguments)}var i=function(e){return n.createElement("svg",a({xmlns:"http://www.w3.org/2000/svg",width:24,height:24,fill:"none"},e),o||(o=n.createElement("path",{fill:"#000",d:"m10.89 14.778-3.267.007a.11.11 0 0 0-.102.076l-.25.722c-.022.076.03.152.103.152h1.27c.095 0 .146.122.08.19L6.7 18.105h.007l1.042 3.397c.022.076-.03.144-.103.144h-1.02a.104.104 0 0 1-.102-.076L6 19.83c-.029-.106-.168-.106-.205-.007l-.426 1.223a.1.1 0 0 0 0 .069l.39 1.481c.014.046.058.084.102.084H9.15c.073 0 .125-.076.103-.145l-1.329-4.277c-.014-.038 0-.084.03-.114l3.016-3.176c.066-.069.015-.19-.08-.19"})),r||(r=n.createElement("path",{fill:"#D8141C",d:"m7.022 13-1.99.008a.11.11 0 0 0-.102.076l-.257.721c-.03.076.03.152.103.152h.836c.074 0 .125.076.103.152l-2.37 6.717a.108.108 0 0 1-.206 0l-1.703-4.848a.112.112 0 0 1 .103-.152h.859a.11.11 0 0 1 .103.076l.616 1.748a.108.108 0 0 0 .206 0l.954-2.72a.112.112 0 0 0-.103-.152H.108c-.073 0-.125.076-.103.152l3.127 8.996a.108.108 0 0 0 .205 0l3.787-10.774c.022-.076-.029-.152-.102-.152"})),l||(l=n.createElement("path",{fill:"#000",fillRule:"evenodd",d:"M1.5 6.5v-5h8v5zM0 1a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm1.5 12.278V11.5h8v1.781l1.39-.003q.056 0 .11.003V11a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v2.281q.052-.003.108-.003zM3.368 13l-.1.278H3V13zm6.805 4.985a1 1 0 0 0 .82-.863zM14.5 1.5v5h8v-5zM14 0a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h9a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm.5 16.5v-5h8v5zM13 11a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1zM3 3v2h2V3zm13 0v2h2V3zm0 12v-2h2v2z",clipRule:"evenodd"})))};const c=JSON.parse('{"apiVersion":3,"name":"vk-blocks/child-page-index","title":"Child Page Index","category":"veu-block","description":"Page List of Child Page","textdomain":"vk-all-in-one-expansion-unit","attributes":{"postId":{"type":"number","default":-1}},"supports":{"className":true}}'),u=window.wp.i18n,s=window.wp.blockEditor,d=window.wp.serverSideRender;var v=e.n(d);const p=window.wp.components,f=window.wp.data,b=window.wp.element,h=(0,f.withSelect)((function(e){return{pages:e("core").getEntityRecords("postType","page",{_embed:!0,per_page:-1})}}))((function(e){var t=e.attributes,n=e.setAttributes,o=e.pages,r=t.postId;(0,b.useEffect)((function(){var e,t=document.querySelector(".block-editor__container iframe"),n=(null==t||null===(e=t.contentWindow)||void 0===e?void 0:e.document)||document,o=new MutationObserver((function(){var e=n.querySelector(".block-editor-block-list__layout");if(e){var t=e.querySelectorAll(".veu_child_page_list_block .veu_childPage_list");0!==t.length&&t.forEach((function(e){e.dataset.prevented||(e.dataset.prevented="true",e.addEventListener("click",(function(t){t.preventDefault(),e.style.cursor="default",e.style.boxShadow="unset",e.style.color="inherit",e.style.textDecorationColor="inherit",e.style.pointerEvents="none"})),e.addEventListener("mouseover",(function(t){t.preventDefault(),e.style.cursor="default",e.style.boxShadow="unset",e.style.color="inherit",e.style.textDecorationColor="inherit",e.style.pointerEvents="none"})))}))}})),r=n.querySelector(".block-editor-block-list__layout")||n.body;return r&&o.observe(r,{childList:!0,subtree:!0}),function(){o.disconnect()}}),[]);var l=[{label:(0,u.__)("This Page","vk-all-in-one-expansion-unit"),value:-1}];if(null!=o){var a=o.length,i=[],c=0;for(c=0;c<a;c++)0!==o[c].parent&&i.push(o[c].parent);for(c=0;c<a;c++)i.includes(o[c].id)&&l.push({label:o[c].title.rendered,value:o[c].id})}var d=(0,s.useBlockProps)({className:"veu_child_page_list_block"});return React.createElement(React.Fragment,null,React.createElement(s.InspectorControls,null,React.createElement(p.PanelBody,{title:(0,u.__)("Parent Page","vk-all-in-one-expansion-unit"),initialOpen:!0},React.createElement(p.SelectControl,{label:(0,u.__)("Parent Page","vk-all-in-one-expansion-unit"),value:r,options:l,onChange:function(e){n({postId:parseInt(e,10)})}}))),React.createElement("div",d,React.createElement(v(),{block:"vk-blocks/child-page-index",attributes:t})))}));function m(e){return m="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},m(e)}var y,w,g,_=c.name,k={icon:React.createElement(i,null),edit:h};(0,t.unstable__bootstrapServerSideBlockDefinitions)((y={},g=c,(w=function(e){var t=function(e){if("object"!=m(e)||!e)return e;var t=e[Symbol.toPrimitive];if(void 0!==t){var n=t.call(e,"string");if("object"!=m(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"==m(t)?t:t+""}(w=_))in y?Object.defineProperty(y,w,{value:g,enumerable:!0,configurable:!0,writable:!0}):y[w]=g,y)),(0,t.registerBlockType)(c,k)})();