<?php
/*-------------------------------------------*/
/*  Add vkExUnit js
/*-------------------------------------------*/
add_action( 'wp_footer', 'veu_add_smooth_js' );
function veu_add_smooth_js() {
?>
<script type="text/javascript">;(function(w,d){
w.addEventListener('load',function(){
Array.prototype.forEach.call(d.getElementsByTagName('a'),function(a){
	var h=a.getAttribute('href');if(h&&h.indexOf('#')==0){a.addEventListener('click',function(e){
		var h=e.toElement.getAttribute('href');
		var x,s=d.getElementById(h.slice(1));
		x=s==null?0:s.getBoundingClientRect().top;
		w.scrollTo({top:x-w.pageYOffset,behavior:'smooth'})
		e.preventDefault()
	},{passive:false})};
});
},false)})(window,document);</script>
<?php
}
