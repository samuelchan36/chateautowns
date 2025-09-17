/* holds the position and direction of the scrollbar */
var scrollPosition = 0;
var scrollDirection = "down";

/* window width and height variables, gets updated on resize */
var WW = 0; //includes scrollbars, matches with css
var WH = 0;
var DH = 0;
var wW = 0; // no scrollbars
var wH = 0; // no scrollbars

var scrollElements = []; // list of elements registered for scrolling events

var jsEnableMouseCapture = false;
var mouseEvent = "";
var mouseSpeed = 0;
var mousePosX = 0;
var mousePosY = 0;
var mouseLastMove = 0;
var mouseDirection = "vertical";
var mouseCapture = "";

/* preloading */
var jsEnablePreload = true;
var maxWait = 1500; //how long to wait for preloading
var minWait = 500; //how long to wait for preloading
var preloading = false; // preload in progress?
var preloadAssets = []; //list of assets to preload

/* scrolling */
var jsEnableScrollFeatures = false;

/* slideshows */
var slideshows = [];

/* header */
var jsShiftArticleDown = true; // automatically adds padding to the top of the main article,matching the height of the header, so the header never covers the body
var jsAttachHeaderInstantly = true; // automatically adds padding to the top of the main article,matching the height of the header, so the header never covers the body

var jsLoadVideos = false;
var vm_videos = [];
var activeVideo = 0;

var scrollCount = 0;
var mouseMoveCount = 0;
var keyCount = 0;

var _sections = [];
var  _capture_scroll = false;

$(document).ready(function (){

		// CSS Vars polyfill
		try
		{
			cssVars();	
		}
		catch (ex)
		{
			console.log(ex);
		}
		


		if (jsLoadVideos) loadVimeoVideos();

		if (jsEnablePreload)
		{
				$("img").each(function () {
					preloadAssets.push($(this).attr("src"));
				}); //default: load all images;

				preload(preloadAssets, _init); //calls _init after preloading. Add a 3rd parameter as callback to be called with updates on progress.
		} else {
			_init();
		}	

})

function _init() {

		if ($("html").hasClass("loading")) $("html").removeClass("loading");	// content fade in



		scrollPosition = parseInt($(window).scrollTop());

		if (scrollPosition > WH)
		{
			$("header").addClass("attached");
		}

		$(window).bind("resize", function () {
			_windowResize();
		});
		_windowResize(); //trigger onResize event
		
		


		$(window).scroll(function (scrollEvent) {
			scrollCount ++;
			if (scrollPosition < parseInt($(window).scrollTop()) ) scrollDirection = "down"; else if (scrollPosition > parseInt($(window).scrollTop()) ) scrollDirection = "up";
//			jslog("Scroll update", scrollPosition, parseInt($(window).scrollTop()), scrollDirection);
			scrollPosition = parseInt($(window).scrollTop());
			$("body").attr("scroll-direction", scrollDirection);
			$("body").attr("scroll-position", scrollPosition);
			if (scrollPosition <= 1)
			{
				$("body").addClass("page-top");
			} else {
				$("body").removeClass("page-top");
			}
			
			if (scrollPosition >= (jsAttachHeaderInstantly ? 1 : Math.max(1, $("header").height())))
			{
				$("header").addClass("attached");
			}  else {
				$("header").removeClass("attached");
			} 
			if (scrollDirection == "up") {
				$("header").addClass("visible");
//				console.log("now", (jsAttachHeaderInstantly ? $("header").height() : 10), scrollPosition );
				if (scrollPosition <= (jsAttachHeaderInstantly ? $("header").height() : 10)) {
							$("header").removeClass("visible");
				} 
			} else {
				$("header").removeClass("visible");
			}
			

			checkInView();
//			setTimeout(checkInView, 100);

			windowScroll(scrollEvent);
		});
		


		if (jsEnableMouseCapture)
		{
			$(window).mousemove(function (mEvent) {
				mouseMove(mEvent);
			});
			mouseCapture = setInterval(mouseUpdate, 100);
		}

			$(window).on("mousemove", function (mEvent) {
				mouseMoveCount ++;
				return true;
			});

			$(window).on("keydown", function (mEvent) {
				keyCount ++;
				return true;
			});


		if (location.hash)
		{
			var hash = location.hash.replace("#", "");
			setTimeout(function () {
				try
				{
				scrollToArea(hash);
				}
				catch (ex)
				{
				}
			}, 300);
		}

		
		$("header div.burger").click(function() {
			$("body").toggleClass("header-active");
			if ($(this).hasClass("burger-project"))
			{
				$("header").toggleClass("project-active");
				$("body").toggleClass("header-project-active");
			} else {
				$("header").toggleClass("active");
			}
			return false;
		})


		if (location.pathname ==  "/") $("header").addClass("home"); 
		else {
			$("header").removeClass("home");
		}

		$("#mainbody, .scrolling").click(function() {
			$("header").removeClass("active");
			$("body").removeClass("header-active");
		})

		if ($(".js-autoscroll").length >= 1)
		{
			delay = $(".js-autoscroll").attr("config-delay");
			delta = $(".js-autoscroll").attr("config-delta");
			setTimeout(function(){
				scrollToElement($(".js-autoscroll"), delta);
			}, delay);
		}

		$("body").attr("data-article", $("article").first().attr("id")).addClass("article-" + $("article").first().attr("id"));
		if ($("article").first().is("[class]"))
		{
			a_classes = $("article").first().attr("class").split(" ");
			for (i=0; i<  a_classes.length;  i++)
			{
				if (a_classes[i])
				{
					$("body").addClass("article-" + a_classes[i]);
				}
			}
		}
		if (scrollPosition <= 1) {
			$("body").addClass("page-top");
		}


		setTimeout(function() {
			$("div.error").addClass("hide");
		}, 2000);
		
		$("a").click(function(){
			
			if ($(this).is("[href]") && $(this).attr("href").substring(0,4) == "http") {
				$(this).attr("target", "_blank");
			}
			return true;
		})

		// in-document smooth scrolling links
		$("a.scrolling").click(function () {
			tmp = $(this).attr("href");
			lk = tmp.split("#");

			if (lk.length > 1) {
				if(location.pathname == lk[0] || !lk[0]) {
					where = lk[1]; 

					scrollToArea(where, jsAttachHeaderInstantly ? $("header").height() : 0);
					return false;
				} else {
					return true;
				}
			} else 
				where = lk[0];
				scrollToArea(where, jsAttachHeaderInstantly ? $("header").height() : 0);
				return false;

		});	


		createSlideshows();
		createAccordions();
		
		bindJqueryControls($("html"));
	

		//default handler for links with class "print"
		$(".js-print").click(function () {
			window.print();
			return false;
		})

		//default handler for links with class "email-friend"
		$(".js-email-friend").click(function () {
			showEmailFriend();
			return false;
		})


		//default handler for links with class "video"
		$(".js-video").click(function () {
			popupYoutube($(this).attr("rel"));
			return false;
		});

		//default handler for links with class "youtube"
		$(".js-youtube").click(function () {
			popupYoutube($(this).attr("rel"));
			return false;
		});


		//default handler for links with class "image"
		$(".fancybox").click(function(){
				txt = "<div class='fancybox' style='background-image: url("+$(this).attr("href")+");'></div>";
				$.fancybox(
							txt,
							{
								'autoDimensions'	: true,
								'transitionIn'		: 'elastic',
								'transitionOut'		: 'elastic'
							}
						);
				return false;
		});

		
		$("div.overlay a.close-overlay").click(_closeOverlay);
		$(window).on("keydown", function(ev){
			if (ev.key == "Escape") $(".close-overlay").click();
			return true;	
		})



		/* OPTIONAL HELPERS */
		markActive();

		// register elements for scrolling
		if (jsEnableScrollFeatures) registerScrollable();

		//trigger scrolling event if page loads mid-way
		if (scrollPosition > 0) onScroll();

		if (jsEnableScrollFeatures) detectScrolling();

		captureScroll();

		checkInView();

		init(); //calls init in js/main.js 
}

function windowScroll(scrollEvent) {
	if (jsEnableScrollFeatures) detectScrolling();
	onScroll(scrollEvent);
}

function mouseMove(mEvent) {
	mouseEvent = mEvent;
}

function mouseUpdate(){
	d = new Date(); tm = d.getTime();
	diffX =  Math.abs(mouseEvent.screenX - mousePosX);
	diffY = Math.abs(mouseEvent.screenY - mousePosY);
	diffT = mouseLastMove - tm;
	if (diffX <= 10 && diffY > 10) mouseDirection = "vertical";
	if (diffX > 10 && diffY <= 10) mouseDirection = "horizontal";
	if (diffX > 10 && diffY > 10) mouseDirection = "diagonal";

	mousePosX = mouseEvent.screenX;
	mousePosY = mouseEvent.screenY;
	mouseLastMove = tm;
}

function _windowResize() {

		w = window.innerWidth;
		WW = window.innerWidth;
		wW = $(window).width();
		wH = $(window).height();
		WH = window.innerHeight;
		DH = $("body").outerHeight();
		
		if (jsShiftArticleDown) $("article").first().css("padding-top", $("header").outerHeight() + "px");

		if (jsEnableScrollFeatures) updateScrollElementsOnResize();
		
		console.log(WH, $("header").outerHeight(), $("footer").outerHeight());
		$("#mainbody").css("min-height", WH - $("header").outerHeight() - $("footer").outerHeight());

		alignHeights();

		setTimeout(checkInView, 200);

		try
		{
			document.documentElement.style.setProperty('--vh', window.innerHeight * 0.01 + "px");
		}
		catch (ex)
		{
			console.log(ex);
		}

		onResize();
}




function createSlideshows() {

		$(".js-slideshow").each(function() {
			obj = $(this).find(".slideshow");
			hasPager = obj.attr("config-pager") == "true" ? true : false;				
			customPager = "";
			switch (obj.attr("config-pager"))
			{
				case "true":
					hasPager = true;
					break;
				case "custom":
					customPager = $(obj.attr("config-pager-custom"));
					hasPager = true;
					customPager.find("a").each(function(index){
						$(this).attr("data-slide-index", index);
					})
					break;
				default:
					hasPager = false;
			}

			hasArrows = obj.attr("config-controls") == "true" ? true : false;				
			autoPlay = obj.attr("config-autoplay") == "true" ? true : false;				
			slidePause  = obj.is("[config-pause]") ? obj.attr("config-pause")  : "5000";				
			transitionFade = obj.attr("config-fade") == "true" ? true : false;				
			slideshow = obj.slick({
				fade: transitionFade,
				autoplay: autoPlay,
				autoplaySpeed: slidePause,
				arrows: hasArrows,
				dots: hasPager
			  });	

			slideshows.push({id: $(this).attr("id"), slider: slideshow});
		})

}

function createAccordions(){


	$(".js-accordion").each(function(){
		var _collapsible = true; if ($(this).is("[no-collapse]")) _collapsible = false;
		var _active = false; if ($(this).is("[active]")) _active = $(this).attr("[active]");
		console.log(_collapsible, _active);
		$(this).accordion({
				header: $(this).is("[config-header]") ? $(this).attr("config-header") : "h4", 
				heightStyle: "content",  
				collapsible:  _collapsible, 
				"active":  _active
		});
	})
}




/* STANDARD HELPERS */

// finds all elements with class .keep-height and ensures that all its children that have a class named the same as parent's "rel" attribute have the same height when the browser resizes. I.e: <ul class="keep-height" rel="unify"><li class="unify"></li><li class="unify"></li></ul>
function alignHeights() {

	$(".js-align-heights").each(function () {
		threshold = 0;
		if ($(this).is("[config-min-width]")) threshold = parseInt($(this).attr("config-min-width"));

		el = $($(this).attr("config-elements"));
		el.css("min-height", 0);

		if (WW < threshold) return false;

		var mh = 0;
		el.each(function () {
			if (mh <= $(this).outerHeight()) mh = $(this).outerHeight();
		});

		el.css("min-height", mh);
	});
}

// adjusts the height of any element to match a given aspect ratio at any resolution. The aspect ratio is stored in the element's "rel" attribute. Optionally, a "mobile-rel" attribute can override the aspect ratio for mobile devices.
function keepRatios() {

	$(".js-keep-ratio").each(function () {
		if (!$(this).is("[config-aspect-ratio]")) return false;

		ratio = parseConfig($(this).attr("config-aspect-ratio"));

		for (i=0; i<= ratio.length ; i++ )
		{
			if (ratio[i][0] >= WW)
			{
				if (parseFloat(ratio[i][1]) > 0) $(this).height($(this).width() / parseFloat(ratio[i][1]));
			}
		}

		
	});

}

// simple class to ensure an "a" elements gets an "active" class attached if its url matches the current location. Apply the class "mark-active" to a common parent element to trigger the functionality.
function markActive() {
	$(".js-mark-active").each(function () {
		$(this).find("a").removeClass("active");
		var found = [];
		$(this).find("a").each(function () {

			if ($(this).attr("href") == location.pathname || $(this).attr("href") == location.pathname + "/home" || (location.pathname.search($(this).attr("href")) >= 0 && !$(this).is("[config-exact-match]")))
			{
				found.push($(this));
			}
		});
		if (found.length) found[found.length-1].addClass("active");
	});
}


// automatic send to friend functionality. Will load a popup with contents from the file templates/pages/en/send-friend.html. 
function showEmailFriend() {
	$.get("/email-friend", function (data) {
		message(data);
	});
	return false;
}

function loadVimeoVideos() {
		$(".video.vimeo").each(function(index){
			if (!$(this).is("[data-video-id]"))
			{
				$(this).attr("data-video-id", index).attr("muted", "");
//				$(this).find("iframe").attr("data-video-id", index);
				$(this).find(".play-video").attr("data-video-id", index);
			}

			iframe = $(this).find("iframe");
			if (iframe.length > 0)
			{
				vm_videos[index] = new Vimeo.Player(iframe.get(0));
			}


			if ($(this).attr("data-autoplay") == "yes")
			{
				try
				{
						vm_videos[index].play();	
				}
				catch (ex)
				{
				}
				
			}
		})
}

function error(err){
	$("div.error").html("");
	$("div.error").removeClass("hide");
	$("div.error").html("<p>" + err + "</p>");
		setTimeout(function() {
			$("div.error").addClass("hide");
		}, 2000);

}

function showOverlay(_content){
	$("div.overlay > div").html(_content);
	console.log(_content);
	$("div.overlay").addClass("active");
	$("html").addClass("overlay-active");
	return false;
}


function _closeOverlay(){
	$("div.overlay").removeClass("active");
	$("html").removeClass("overlay-active");
	$("div.overlay > div").html("");
	return false;

}

function bindJqueryControls(_parent){

		_parent.find(".js-date-picker").each(function(){
			pickerOptions = {};
			if ($(this).is("[config-min-date]")) pickerOptions.minDate = $(this).attr("config-min-date");
			if ($(this).is("[config-exclude-days]")) pickerOptions.beforeShowDay = function(date){
				var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
				isSunday = date.getDay();
				return excludeDays.indexOf(string) != -1 || isSunday == 0 ? [false] : [true];
			}
			
			$(this).datepicker(pickerOptions);	
		})

		_parent.find(".js-select").each(function(){
			opts = {placeholder: $(this).attr("placeholder")};
			if ($(this).is("[config-min-results]")) opts.minimumResultsForSearch =parseInt($(this).attr("config-min-results")); else opts.minimumResultsForSearch  = 30;
			if ($(this).is("[config-custom-class]")) opts.dropdownCssClass = $(this).attr("config-custom-class");
			

			$(this).select2(opts);	
		})

}