<!-- [ .socialSet ] -->
<?php if (is_home() || is_front_page()) {
	$linkUrl = home_url();
	$twitterUrl = home_url();
} else if ( is_single() || is_archive() || ( is_page() && !is_front_page() ) ) {
	// $twitterUrl = home_url().'/?p='.get_the_ID();
	// URL is shortened it's id, but perm link because it does not count URL becomes separately
	$twitterUrl = get_permalink();
	$linkUrl = get_permalink();
} else {
	$linkUrl = get_permalink();
}
?>

<div class="socialSet">

<ul style="margin-left:0px;">

<li class="sb_facebook sb_icon">
<a href="http://www.facebook.com/sharer.php?src=bm&u=<?php echo $linkUrl; ?>&amp;t=<?php echo urlencode(getHeadTitle()); ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600');return false;" ><span class="vk_icon_w_r_sns_fb icon_sns"></span><span class="sns_txt">Facebook</span>
</a>
</li>

<li class="sb_hatena sb_icon">
<a href="http://b.hatena.ne.jp/add?mode=confirm&url=<?php echo $linkUrl; ?>&amp;title=<?php echo urlencode(getHeadTitle()); ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=520');return false;"><span class="vk_icon_w_r_sns_hatena icon_sns"></span><span class="sns_txt">Hatena</span></a>
</li>

<li class="sb_twitter sb_icon">
<a href="http://twitter.com/intent/tweet?url=<?php echo $linkUrl; ?>&amp;text=<?php echo urlencode(getHeadTitle()); ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" ><span class="vk_icon_w_r_sns_twitter icon_sns"></span><span class="sns_txt">twitter</span></a>
</li>

<li class="sb_google sb_icon">
<a href="https://plus.google.com/share?url=<?php echo $linkUrl; ?>&amp;t=<?php echo urlencode(getHeadTitle()); ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><span class="vk_icon_w_r_sns_google icon_sns"></span><span class="sns_txt">Google+</span></a>
</li>

<li class="sb_line sb_icon">
<a href="http://line.naver.jp/R/msg/text/?<?php echo urlencode(getHeadTitle()); ?>%0D%0A<?php echo $linkUrl; ?>"><span class="vk_icon_w_r_sns_line icon_sns"></span><span class="sns_txt">LINE</span></a>
</li>

<li class="sb_pocket"><?php /* do not delete span */?><span></span>
<a data-pocket-label="pocket" data-pocket-count="horizontal" class="pocket-btn" data-save-url="<?php echo $linkUrl; ?>" data-lang="en"></a>
<script type="text/javascript">!function(d,i){if(!d.getElementById(i)){var j=d.createElement("script");j.id=i;j.src="https://widgets.getpocket.com/v1/j/btn.js?v=1";var w=d.getElementById(i);d.body.appendChild(j);}}(document,"pocket-btn-js");</script>
</li>

</ul>

</div>
<!-- [ /.socialSet ] -->