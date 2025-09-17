jsShiftArticleDown = false;
jsAttachHeaderInstantly = false;


function init() {

		$(".masthead-video").each(function(index){

			iframe = $(this).find("iframe");
			if (iframe.length > 0)
			{
				vm_videos[index] = new Vimeo.Player(iframe.get(0));
			}

		});
//	if (!$("body").hasClass("article-home")) $("header div.logo img").attr("src", "/img/logo.svg");

//	$("div.scrolling-text").addClass("active");
//	var initialText = $("div.scrolling-text span").html();
//	setInterval(function(){
//		$("div.scrolling-text span").append(" " + initialText);
//	}, 3000);
//
//	if (WW < 760)
//	{
		$("div.slideshow-std > div").slick({autoplay:false, autoplaySpeed: 5000, dots: true, arrows: false});
		$("div.slideshow-alt > div").slick({autoplay:false, autoplaySpeed: 5000, dots: true, arrows: false});
		$("div.transit div.slideshow > div ").slick({autoplay:false, autoplaySpeed: 5000, dots: false, arrows: false});

		$("div.transit-stations > div a").each(function(index){
			$(this).attr("data-index", index);
			$(this).click(function(){
					$("div.transit div.slideshow > div ").slick("slickGoTo", $(this).attr("data-index"));
					return false;
			})
		})


		$("a.play").click(function(){
//				vm_videos[0].play();
				$(".masthead-video").parent().addClass("active");
				return false;
		})

		$("div.masthead-video").click(function(){
			if ($(this).hasClass("active"))
			{
//				vm_videos[0].pause();
				$(".masthead-video").parent().removeClass("active");
			}
				return false;
			
		})


}


function onResize() {
		_ratio = WW/WH;
//		console.log(_ratio);
		_left = 0; _top = 0; _width = WW; _height = WH;
//		console.log(_width, _height);
		if (_ratio <= 3.83)
		{
//			console.log("set the height to full height and width extends");
			_width = WH * 3.83;
			_left = (WW - _width )/2;
		} else {
//			console.log("set the width to full width and height extends");
			_height = WW / 3.83;
			_top = (WH - _height)/2;
		}
//		console.log(_width, _height);
		$("div.masthead-video").width(_width).height(_height).css("left", _left + "px").css("top", _top + "px");

}


function onScroll() {

}



/* CALL THIS AFTER ELEMENT GOES OUT OF VIEW */
function _afterHide(obj) {
  return true;
}

/* CALL THIS AFTER ELEMENT COMES IN VIEW */
function _afterReveal(obj) {
  return true;
}

function runCustomAnimations(){

}


