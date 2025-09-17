var jsSearchInProgress = false;
var jsSearchPending = false;
function jsAttachSearch() {
	$(".js-search-field").keyup(function(e){
		jslog(e.keyCode);
		_searchCount = parseInt($(".js-search-results").attr("config-results"));
		_searchIndex = parseInt($(".js-search-results").attr("config-current"));
		_searchIndexNext = _searchIndex + 1; if (_searchIndexNext > _searchCount) _searchIndexNext = 1;
		_searchIndexPrev = _searchIndex - 1; if (_searchIndexPrev < 1) _searchIndexPrev = _searchCount;
		switch (e.keyCode)
		{
			case 27: //cancel
				$(".js-search-close").click();
				return false;
			case 40: //down
				$(".js-search-results").attr("config-current", _searchIndexNext);
				$(".js-search-results a").removeClass("selected");
				$(".js-search-results a:nth-child("+_searchIndexNext+")").addClass("selected");
				return false;
			case 38: //up
				$(".js-search-results").attr("config-current", _searchIndexPrev);
				$(".js-search-results a").removeClass("selected");
				$(".js-search-results a:nth-child("+_searchIndexPrev+")").addClass("selected");
				return false;
			case 13: //enter
				if(_searchIndex == 0) location.href = "/do-search?q=" + encode($(this).val());
				else location.href = $(".js-search-results a:nth-child("+_searchIndex+")").attr("href");
				return false;
			default:
				if ($(this).val().length <= 2) return true;
				jsDoSearch();

		}
	})

	$(".js-search-close").click(function(){
			$("div.js-search-results").html("");
//			$(".js-search-input").removeClass("open").removeClass("active");
			$(".js-search-input").removeClass("open");
			//$("body").removeClass("search-active");
			$(".js-search-input input").val("");
			return false;
	})

	$(".js-search-full").click(function(){
			location.href = "/do-search?q=" + $("#q").val();
			return false;
	})

}

	function jsDoSearch() {
		what = $("#q").val();
		jslog("searching for " + what);
				if (jsSearchInProgress) {
					jslog("search in progress");
					if (jsSearchPending) return true;
					else {
						jslog("search pending");
						setTimeout(function() {
							jsSearchPending = false;
							jsDoSearch();
						}, 200);
						jsSearchPending = true;
					}
				}
				try
				{
					jslog(what);
					$.get("/do-quick-search?q=" + what, function(data){
						jslog(data);
						$("div.js-search-results").html("");
						$("div.js-search-results").unbind("keydown");
						for (i=0; i<data.length ;i++ )
						{
							console.log(data[i].name);
							$("div.js-search-results").append("<a href='"+data[i].url +"'>"+data[i].name+"<small>"+data[i].type+"</small></a>").show();
//							data[i].url.replace("?q=" + what, "")
						}
//						$(".js-search-results a:first-child").addClass("selected");
						$(".js-search-results").attr("config-results", data.length).attr("config-current", 0);
						jsSearchInProgress = false;
						$(".js-search-input").addClass("open");
					}, "json")

				}
				catch (ex)
				{
					jslog(ex);
				}

	}