jQuery(document).ready(function(){

	jQuery("img.ea-expandable").click(function(){
	jQuery(this).parent().toggleClass('ea-contracted-image');
	jQuery(this).parent().toggleClass('ea-expanded-image');
	isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
	if (isSafari) {
		jQuery(this).addClass("ea-snap-image");
		jQuery(this).parent().addClass("ea-snap-image");
	}
	});
});	