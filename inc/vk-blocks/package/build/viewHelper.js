
class viewHelper{constructor(){let hTags=viewHelper.getDom();viewHelper.appendIdHtags(hTags,viewHelper.assignIdHref);}
static isExist(val){return(typeof val!=='undefined'&&val!==null);}
static getDom(){let editor=document.getElementsByClassName('wp-block-vk-blocks-table-of-contents');if(viewHelper.isExist(editor[0])){if(editor[0].parentElement){return editor[0].parentElement.querySelectorAll("h1, h2, h3, h4, h5, h6");}else{return false;}}};static appendIdHtags(hTags,callback){if(viewHelper.isExist(hTags)){for(let i=0;i<hTags.length;i++){let hTag=hTags[i];if(!hTag.id){hTag.id="vk-htags-"+i;}}
callback(hTags);}};static assignIdHref(hTags){let aTags=document.getElementsByClassName('vk_tableOfContents_list_item_link');for(let i=0;i<aTags.length;i++){aTags[i].attributes[0].nodeValue='#'+hTags[i].id;}};}
window.onload=()=>{new viewHelper();};