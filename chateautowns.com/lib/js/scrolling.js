/*
	
	SCROLLING LIBRARY
	Version: 1.0
	Last Updated: December 15th, 2018

*/


function updateScrollElementsOnResize() {
		// update scrolling parameters
		scrollPosition = parseInt($(window).scrollTop());
		if (scrollPosition > 0) scrollDirection = "up";
		
		// UPDATE scrolling elements with new data
		for (i=0; i<scrollElements.length ; i++ )
		{
			obj = scrollElements[i].obj;
			_fixed = false; if (obj.css("position") == "fixed") _fixed = true;
			_top = _fixed ? scrollElements[i].top_static : obj.offset().top;
			scrollElements[i].top = _top;
			scrollElements[i].height = obj.outerHeight();
			scrollElements[i].max = obj.outerWidth() * scrollElements[i].ratio;
			scrollElements[i].distance = obj.outerWidth() * scrollElements[i].ratio - obj.outerHeight();
			scrollElements[i].start = scrollElements[i].top -obj.attr("scroll-start") * $(window).outerHeight();
			scrollElements[i].end = scrollElements[i].top -obj.attr("scroll-end") * $(window).outerHeight();
			scrollElements[i].delta = scrollPosition - scrollElements[i].start;

			switch (scrollElements[i].animation)
			{
				case "scroll-up":
						scrollingObject = scrollElements[i].obj.find("img");
						scrollingObject.css("top", "0px");
						scrollElements[i].active = false;
						scrollElements[i].complete = true;
						break;
				case "simple":
						if (WW <= scrollElements[i].minwidth) {
							if (scrollElements[i].active && scrollElements[i].active == "down") scrollElements[i].complete = "down";
							else scrollElements[i].complete = "";
							scrollElements[i].active = false;
							scrollElements[i].obj.removeClass("revealed");
						}
						break;

			}
		}

		detectScrolling();
}


function registerScrollable() {
	//required attributes are:  scroll-start, scroll-animation, everything else is optional and only required for certain animation types
	$('.scrollanim').each(function () {
		_ratio = $(this).is("[scroll-height-ratio]") ? $(this).attr("scroll-height-ratio") : 1;
		_fixed = false; if ($(this).css("position") == "fixed") _fixed = true;
		_top = _fixed ? parseInt($(this).css("top")) : $(this).offset().top;
		if (!$(this).is("[scroll-start]")) $(this).attr("scroll-start", 1);
		scrollElements.push({
			"obj": $(this),  // stores current object
			"top": _top,  // distance to the top of the document
			"top_static": _top,  // distance to the top of the document, value stays static when window is resized
			"height": $(this).outerHeight(),  // element height
			"ratio": _ratio,  // ratio of height / width, used when resizing to adjust distances
			"max": _ratio * $(this).outerWidth(),  // total height of image (adjusted responsively). i.e. if the container is 600px tall, but the image inside is 900px, max would be 900px
			"distance": _ratio  * $(this).outerWidth() - $(this).outerHeight(),  // how much of the image is being clipped (essentially max - height)
			"stop": ($(this).is("[scroll-prevent]") && $(this).attr("scroll-prevent") == "true" ? true : false), // not fully implemented - will be used to "stop" scrolling of the page for the duration of the animation
			"active": false,  // is the element being animated at this point?
			"complete": false,  // has the animation completed
			"step": $(this).is("[scroll-steps]") ? $(this).attr("scroll-steps") : 1,  // the animation will last so many "steps". For scrolling, each step will move the image by (distance / steps).
			"animateby": ($(this).is("[scroll-animation-type]") && $(this).attr("scroll-animation-type") != "") ? $(this).attr("scroll-animation-type") : "step",  // whether to use the default "step" animation, or to tie the animation to the scroll distance. Leave empty to use default (steps)
			"animation": $(this).is("[scroll-animation]") ?$(this).attr("scroll-animation") : "simple",  // type of animation to use. Defaults to simple.
			"start": _top - $(this).attr("scroll-start")  * $(window).outerHeight(),  // when to start the animation. Defaults to 1. Value is expresed as percentage of window height, where 0 means animation will start when the top edge of the element touches the top edge of the window and 1 means animation will start when the start edge of element touches the bottom edge of the window.
			"end": $(this).is("[scroll-end]") ? (_top - $(this).attr("scroll-end") * $(window).outerHeight()) : _top - $(this).attr("scroll-start") * $(window).outerHeight(), // when to stop animation, optional - uses the start value if not specified otherwise.
			"minwidth": $(this).is("[scroll-min-width]") ? $(this).attr("scroll-min-width") : 320, // don't animate if window width is below this threshold. Optional, default value is 768 or higher.
			"delta": scrollPosition - $(this).attr("scroll-start") * $(window).outerHeight(),
			"function_up":  $(this).is("[scroll-function-up]") ? $(this).attr("scroll-function-up") : "",
			"function_down": $(this).is("[scroll-function-down]") ? $(this).attr("scroll-function-down") : "",
			"delay": $(this).is("[scroll-delay]") ? parseInt($(this).attr("scroll-delay")) : 0
		});
	})
}

/* This function is called on every scroll event. It will check all elements registered for scroll events to detect if their animation conditions are met */
function detectScrolling() {

	if ($("body").hasClass("stop-scrolling"))
	{
		return false;
	}

	for (i=0; i<scrollElements.length ; i++ )
	{

		scrollElements[i].delta = scrollPosition - scrollElements[i].start;

		if (WW < scrollElements[i].minwidth)
		{
			continue;
		}

		obj = scrollElements[i].obj;
		if (scrollElements[i].complete) {
			if (scrollPosition < scrollElements[i].top && !scrollElements[i].active) {
			}
		}


		if (scrollDirection == "down" && scrollPosition >= scrollElements[i].start && !scrollElements[i].active && scrollElements[i].complete != "down")
		{
			scrollElements[i].active = "down";
			if (scrollElements[i].stop) {
				$("body").animate({scrollTop: scrollElements[i].top + 10}, 400);
				$("body").addClass("stop-scrolling");
				
			}
			continue;
		}

		if (scrollDirection == "up" && scrollPosition <= scrollElements[i].end && !scrollElements[i].active && scrollElements[i].complete != "up")
		{
			scrollElements[i].active = "up";
			if (scrollElements[i].stop) {
				$("body").animate({scrollTop: scrollElements[i].top + 10}, 400);
				$("body").addClass("stop-scrolling");
				
			}
			continue;
		}

	}

	runScrollAnimations();
}


/* This is called every time a scroll event happens. If any element's scrolling animation was triggered, the appropriate animation will be executed */
function runScrollAnimations() {
	for (i=0; i<scrollElements.length ; i++ )
	{
	
		if (scrollElements[i].active)
		{
			obj = scrollElements[i].obj;

			switch (scrollElements[i].animation)
			{
				case "scroll-up":

					scrollingObject = obj.find("img");
						scrolledDistance = parseInt(scrollingObject.css("top"));

						if (Math.abs(scrolledDistance) <= scrollElements[i].distance && scrollDirection == "down")
						{
							scrolledDistance = Math.abs(scrolledDistance);
							if (scrollElements[i].animateby == "step") scrolledDistance += scrollElements[i].distance/scrollElements[i].step; else scrolledDistance = scrollElements[i].delta;
							if (scrolledDistance >= scrollElements[i].distance)
							{
								if (scrollElements[i].stop) $("body").removeClass("stop-scrolling");
								scrollElements[i].active = false;
								scrollElements[i].complete = "down";
								scrolledDistance = scrollElements[i].distance;
							}
							scrolledDistance = - scrolledDistance;
						}

						if (scrolledDistance < 0 && scrollDirection == "up")
						{
							if (scrollElements[i].animateby == "step") scrolledDistance += Math.ceil(scrollElements[i].distance/ scrollElements[i].step); else {
								scrolledDistance = scrollElements[i].delta;				
							}

							if ( scrolledDistance >= scrollElements[i].distance && scrollElements[i].animateby != "step" )
							{
								break;
							}

							if (scrollElements[i].animateby != "step") scrolledDistance = - scrolledDistance;


							if (scrolledDistance >= 0)
							{
								if (scrollElements[i].stop) $("body").removeClass("stop-scrolling");
								scrollElements[i].active = false;
								scrollElements[i].complete = "up";
								scrolledDistance = 0;
							}

						}
						scrollingObject.css("top", scrolledDistance +"px");
						break;

				case "reveal":
							scrollingObject = obj.find("div.reveal");
							elementHeight = scrollingObject.outerHeight();
							if (scrollDirection == "down" && elementHeight < obj.outerHeight())
							{

								elementHeight = Math.min(obj.outerHeight(), scrollPosition - scrollElements[i].start);
								if (elementHeight >= obj.outerHeight())
								{
									if (scrollElements[i].stop) $("body").removeClass("stop-scrolling");
									scrollElements[i].active = false;
									scrollElements[i].complete = "down";
									elementHeight = obj.outerHeight();
								}

							}

							if (scrollDirection == "up" && elementHeight > 0 && scrollElements[i].active)
							{
								elementHeight = Math.max(0, scrollPosition - scrollElements[i].start);
								if (elementHeight <= 0)
								{
									if (scrollElements[i].stop) $("body").removeClass("stop-scrolling");
									scrollElements[i].active = false;
									scrollElements[i].complete = "up";
									elementHeight = 0;
								}
							}

							scrollingObject.height(elementHeight);

						break;
				case "curtain":
						scrollingObject = obj.find("ul.curtain");
						curtainStep = scrollingObject.attr("step");
						if (scrollDirection == "down") {
							scrollingObject.addClass("pulled");
							scrollElements[i].active = false;
							scrollElements[i].complete = "down";
						} 
						if (scrollDirection == "up") {
							scrollingObject.removeClass("pulled");
							scrollElements[i].active = false;
							scrollElements[i].complete = "up";
						} 
						break;
				case "simple":
						if (scrollDirection == "down" && scrollElements[i].active == "down") {

							if (scrollElements[i].delay > 0)
							{
								var delayedObj = scrollElements[i].obj;
								setTimeout(function() {
									delayedObj.addClass("revealed");
								}, scrollElements[i].delay);
							} else 
								obj.addClass("revealed");
							scrollElements[i].active = false;
							scrollElements[i].complete = "down";
						} 
						if (scrollDirection == "up" && scrollElements[i].active == "up") {
							obj.removeClass("revealed");
							scrollElements[i].active = false;
							scrollElements[i].complete = "up";
						} 
						break;
				case "custom":
						if (scrollDirection == "down" && scrollElements[i].active == "down") {
							obj.addClass("revealed");
							scrollElements[i].active = false;
							scrollElements[i].complete = "down";

							if (scrollElements[i].function_down) window[scrollElements[i].function_down]();
						} 
						if (scrollDirection == "up" && scrollElements[i].active == "up") {
							obj.removeClass("revealed");
							scrollElements[i].active = false;
							scrollElements[i].complete = "up";
							if (scrollElements[i].function_up) window[scrollElements[i].function_up]();
						} 
						break;

			}
		}
	}
}

function checkInView(){

	scrollPosition = Math.floor(scrollPosition);
	scrollPositionBottom = Math.floor(scrollPosition + WH);
//	console.log(scrollPosition, scrollPositionBottom, scrollDirection, WH);
	var _isInView = false;
	$(".js-animate").each(function(index){
		_isInView = false;
		itemTop = $(this).offset().top;
		itemBottom = $(this).offset().top + $(this).outerHeight();
		itemTrueTop = Math.floor($(this).offset().top);
		itemTrueBottom = Math.floor($(this).offset().top + $(this).outerHeight());
//		console.log(index + ": START AT: ", itemTrueTop, itemTrueBottom);

		topY = 0;
		bounds = $(this).get(0).getBoundingClientRect();
		if (bounds.height != $(this).height())
		{
			topY = ($(this).height() - bounds.height)/2;
		}
		
		if (window.getComputedStyle($(this).get(0)).transform)
		{
			const matrix  = window.getComputedStyle($(this).get(0)).transform.split(', ');
			if (!isNaN(parseInt(matrix[5]))) topY += parseInt(matrix[5]);
//			console.log("topY", topY);
		}
		if ($(this).css("position") == "relative" && $(this).css("top") && $(this).css("top") != "auto") {
			itemTrueTop -= 1 * parseInt($(this).css("top"));
			itemTrueBottom -= 1 * parseInt($(this).css("top"));
		}
		if (topY)
		{
			itemTrueTop = itemTrueTop - topY;
			itemTrueBottom = itemTrueBottom - topY;
		}

//		console.log(index + ": NEW TOP: ", itemTrueTop, itemTrueBottom);
		

		if (itemTrueBottom < scrollPosition || itemTrueTop > scrollPositionBottom)
		{
			// Not yet, too far up or too far down
			$(this).removeClass("in-view").removeClass("in-view-full").removeClass("in-view-top").removeClass("in-view-bottom").removeClass("in-better-view").addClass("out-of-view");
		} else {
			
			// At least in partial view
			$(this).addClass("in-view").removeClass("out-of-view");
			if ($(this).is("[config-animation-better]"))
			{
				if (itemTrueTop <= scrollPositionBottom - parseInt($(this).attr("config-animation-better")))
				{
					$(this).addClass("in-better-view");
				}
			}
			_isInView = true;
			if (itemTrueTop >= (scrollPosition - 1) && itemTrueBottom <= (scrollPositionBottom + 1))
			{
				// In full view
				$(this).addClass("in-view-full").addClass("in-view-top").addClass("in-view-bottom");
			} else {
//				console.log('partial');
				// Not fully in  view
				if (itemTrueTop < (scrollPositionBottom + 1) && itemTrueBottom > (scrollPositionBottom - 1))
				{
					$(this).removeClass("in-view-full").addClass("in-view-top").removeClass("in-view-bottom");
				}
				if (itemTrueTop < (scrollPosition + 1) && itemTrueBottom > (scrollPosition - 1))
				{
					$(this).removeClass("in-view-full").removeClass("in-view-top").addClass("in-view-bottom");
				}
			}
		}

		if (_isInView)
		{
			if ($(this).is("[config-animation-callback]"))
			{
				window[$(this).attr("config-animation-callback")]();
//				console.log($(this).attr("config-animation-callback"));
			}
		}


	})

}







var _scroll_in_progress = false;
var _capture_scroll = false;
var _scroll_type = "";
var _auto_scroll = false;

var touchDistance = 0;
var touchDistanceX = 0;
var touchType = "";

function captureScroll(){
		if (!_capture_scroll) return false;
		var supportsPassive = false;
		try {
		  window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
			get: function () { supportsPassive = true; } 
		  }));
		} catch(e) {}

		var wheelOpt = supportsPassive ? { passive: false } : false;
		var wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';

		// Touch Scroll
		window.addEventListener('touchstart', function(e) {
			console.log(e);
			touchType = "";
			touchDistance = e.touches[0].clientY;;
			touchDistanceX = e.touches[0].clientX;;
		}, wheelOpt);


		window.addEventListener('touchmove', function(scrollEvent) {
					_scroll_type = "touch";
					if (touchType == "vertical" || Math.abs(scrollEvent.changedTouches[0].clientX - touchDistanceX) < Math.abs(scrollEvent.changedTouches[0].clientY - touchDistance)) {
						touchType = "vertical";

						if (scrollEvent.changedTouches[0].clientY - touchDistance > 0)
						{
							scrollDirection = "up";
						} else {
							scrollDirection = "down";
						};

						scrollEvent.preventDefault();
					} else {
						scrollDirection = "lateral";
						touchType = "horizontal";
						return true;
					}
					if (_scroll_in_progress || _auto_scroll) return false;
					if (!_capture_scroll) return true;
					_scroll_in_progress = true;
					setTimeout(function() { _scroll_in_progress = false; console.log("Event released")}, 1000);		
				
					if (scrollDirection != "lateral")
					{
						if(scrollDirection == "down") {
							scrollDown();
						} else {
							scrollUp();
						}
					} else {
					}
			}, wheelOpt);

		// Mouse Wheel
		 $("body").get(0).addEventListener("wheel", function(e){
//			 console.log("mousewheel", e);
				_scroll_type = "wheel";
				if (!_capture_scroll) return true;
				e.preventDefault();
				if (_scroll_in_progress || _auto_scroll) return false;
				_scroll_in_progress = true;
				setTimeout(function() { _scroll_in_progress = false; }, 1000);		

			  if(typeof e.detail == 'number' && e.detail !== 0) {
				if(e.detail > 0) {
					scrollDown();
				} else if(e.detail < 0){
					scrollUp();
				}
			  } else if (typeof e.wheelDelta == 'number') {
				if(e.wheelDelta < 0) {
					scrollDown();
				} else if(e.wheelDelta > 0) {
					scrollUp();
				}
			  }

			}, wheelOpt );
}



/* SCROLLING */
	function scrollToElement(scroll_to_element, mydelta) {
//		alert(mydelta)
			try
			{
					if (scroll_to_element.length == 0) return false;

						var delta = 0;
						if (mydelta) delta = mydelta;

						//check if element has delta specified
						if (scroll_to_element.is("[config-scroll-offset]")) {
							offset = parseConfig(scroll_to_element.attr("config-scroll-offset"));
							if (offset)
							{
									for (i=0; i<offset.length ; i++ )
									{
										if (WW <= offset[i][0])
										{
											delta += offset[i][1];
											break;
										}
									}
								
							}
						} 
						
						if (scroll_to_element.is(".js-animate"))
						{
							delta += parseInt(scroll_to_element.css("top"));

							if (window.getComputedStyle(scroll_to_element.get(0)).transform)
							{
								const matrix  = window.getComputedStyle(scroll_to_element.get(0)).transform.split(', ');
								topY = parseInt(matrix[5]); if (isNaN(topY)) topY = 0;
							}
							if (topY)
							{
								delta += topY;
							}

						}
//						window.scrollBy(0,1); 
						el = $("html,body");
						
						if (el) {
								_auto_scroll = true;
								el.velocity({scrollTop: parseInt(scroll_to_element.offset().top - delta)+ "px"},{duration: 1000, easing: 'cubic-bezier(0,1,.5,1)', complete: function() {
										_auto_scroll = false;
								}}) 
						} else {
							location.hash = "#" + scroll_to_element.attr("id");	
						};				
			}
			catch (ex)
			{
				jslog(ex);
				_auto_scroll = true;
				if (scroll_to_element.length > 0) location.hash = "#" + scroll_to_element.attr("id");	
				_auto_scroll = false;

			}
	
	}



	function scrollToArea(areaname, delta) {
		return scrollToElement($("#" + areaname), delta);
	}

	

/* FOR SCROLL BY PAGE */

var _last_scroll = 0;
function scrollDown(){
//	console.log(scrollPosition);
//alert($("div.wrapper").first().height());
	_sections = [];
	$("section").each(function(index){
		$(this).attr("data-index", index);
		_sections.push($(this).offset().top);
	})

	for (i=0; i<_sections.length;  i++)
	{
		//console.log(i, _sections[i], scrollPosition, scrollPosition + WH *1.1, _sections[i+1]);
		if (_sections[i] >= scrollPosition)
		{
			if (i > 0 && scrollPosition + WH *1.1 < _sections[i+1])
			{
				if (_sections[i + 1] - _sections[i] > WH *1.1)
				{
						_auto_scroll = true;
//						console.log("scrolling to", scrollPosition + WH); 
						$("html,body").velocity({scrollTop: parseInt(scrollPosition + WH)+ "px"},{duration: 1000, easing: 'cubic-bezier(0,1,.5,1)', complete: function() {
								_auto_scroll = false;
						}})						
						$("html,body").addClass("partial-scroll");
						return false;
				} else {
						_scrollme_to = i;
				}
			} else {
//					console.log("jumping to", _sections[i+1]); 
					_scrollme_to = i +1;
			}
			if (_last_scroll == _scrollme_to && _scrollme_to < _sections.length-1)
			{
				_scrollme_to ++;
			} 
			_last_scroll = _scrollme_to;
			scrollToElement($("section[data-index="+(_scrollme_to)+"]"), 0);
			$("html,body").removeClass("partial-scroll");

			if (window["_scrollDown"])
			{
				window["_scrollDown"](_scrollme_to);
			}


			break;
		}
	}
}


function scrollUp(){
	found = false;
//	console.log(_sections);
	_sections = [];
	$("section").each(function(index){
		$(this).attr("data-index", index);
		_sections.push($(this).offset().top);
	})

	for (i=_sections.length-1; i>=0;  i--)
	{
//		console.log(i, _sections[i], scrollPosition);
		if (_sections[i] <= scrollPosition)
		{
			if (_last_scroll == i) continue;
			found = true;
			_last_scroll = i;
			scrollToElement($("section[data-index="+(i)+"]"), 0);
//			console.log("found ", _last_scroll);
			if (!i) $("header").removeClass("attached");

			if (window["_scrollUp"])
			{
				window["_scrollUp"](i);
			}

			break;
		}
	}
	if (!found)
	{
	}
}