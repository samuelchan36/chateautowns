var points = [];
var mapFooter;
var mymap = [];
var _mapOptions = [];
var gmaps = [];
var gstyles = [
						//white - 0
						[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]
					];

var gmarkers = [];

var _gmaploaded = false;
function gmaploaded(){
	_gmaploaded = true;
}

function loadgmaps() {

		if ($(".wrp-map").length > 0)
		{
			$(".wrp-map").each(function () {
				gmap = $(this).find(".gmap");
				mp = gmaps.length;
				
				if (!gmap.is("[id]")) {
					console.log("MAP WARNING: ID not specified");
					gmap.attr("id", "gmap_" + mp);
				}
				
				if (!gmap.is("[data-center]")) {
					console.log("MAP ERROR: no center");
					gmap.attr("data-center", "43.669245, -79.392474");
				}
				if (!gmap.is("[data-zoom]")) {
					console.log("MAP WARNING: zoom not specified");
					gmap.attr("data-zoom", 15);
				}
				style = "";
				if (gmap.is("[data-style]")) {
					style = gstyles[parseInt(gmap.attr("data-style"))];
				}

				gmaps[mp] = showGoogleMap(gmap.attr("id"), gmap.attr("data-center"), {zoom: parseInt(gmap.attr("data-zoom"))}, null, style);
				var last_size = "";
				$(this).find("point").each(function (index) {

					if (!$(this).is("[data-pin-size]")) {
						console.log("MAP ERROR: PIN # " + (index + 1)+ " missing size");
						$(this).attr("data-pin-size", "100,100,0,100");
					} else last_size = $(this).attr("data-pin-size");

					if (!$(this).is("[data-center]")) {
						console.log("MAP WARNING: PIN # " + (index + 1)+ " missing center");
						$(this).attr("data-center", "0,0");
					}

					if (!$(this).is("[data-title]")) {
						console.log("MAP WARNING: PIN # " + (index + 1)+ " missing title");
						$(this).attr("data-title", "");
					}

					if (!$(this).is("[data-address]")) {
						console.log("MAP WARNING: PIN # " + (index + 1)+ " missing address");
						$(this).attr("data-address", "");
					}

					if (!$(this).is("[data-pin]")) {
						console.log("MAP ERROR: PIN # " + (index + 1)+ " missing image");
						return true;
					}

					pinsize = last_size.split(",");

					gmarkers.push(addPin(gmaps[mp], [$(this).attr("data-center"), $(this).attr("data-pin"), pinsize[0], pinsize[1], pinsize[2], pinsize[3], $(this).attr("data-title"), $(this).attr("data-address")]));
				});

//				gmaps[mp].set("scrollwheel", false);
			});
		}
	
	if (gmaps.length > 0)
	{
		try
		{
			var markerCluster = new MarkerClusterer(gmaps[0], gmarkers, {imagePath: '/lib/plugins/google/m', imageExtension: "png"});	 
		}
		catch (ex)
		{
		}
		
	}
	
}
		


function showMap(mapId, coords, mapOptions, marker, mapStyle) {
	return showGoogleMap(mapId, coords, mapOptions, marker, mapStyle);
}
function showGoogleMap(mapId, coords, mapOptions, marker, mapStyle) {
	mapStyle = [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}];
	zoomLevel = 15;
	if (mapOptions != "undefined" && mapOptions.hasOwnProperty("zoom"))
	{
		zoomLevel = mapOptions.zoom
	}

	tmp = coords.split(",");
	_mapOptions.push({
			zoom: zoomLevel,
			center: new google.maps.LatLng(tmp[0], tmp[1]),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
//			scrollwheel: false,
//			gestureHandling: 'greedy',
//			draggable: true,
			zoomControl: true,
			streetViewControl: true
        });

		mymap.push(new google.maps.Map(document.getElementById(mapId),_mapOptions[_mapOptions.length-1]));
		if (mapStyle) mymap[mymap.length-1].set("styles", mapStyle);
		  

	if (marker)
	{
		var iconImage = { url: marker[0], size: new google.maps.Size(marker[1], marker[2]), origin: new google.maps.Point(0, 0), anchor: new google.maps.Point(marker[3], marker[4]) };
		marker1 = new google.maps.Marker({
					position: new google.maps.LatLng(tmp[0], tmp[1]),
					animation: google.maps.Animation.DROP,
					map: mymap[mymap.length-1],
					icon: iconImage,
					title: marker[5]
				});

		google.maps.event.addListener(marker1, 'click', (function(marker) {
            return function() {
				window.open(marker[6]);
				return false;
            }
        })(marker));

	}
	return mymap[mymap.length-1];
}

function addPin(mp, pin) {
	if (pin)
	{
		var iconImage = { url: pin[1], size: new google.maps.Size(parseInt(pin[2]), parseInt(pin[3])), origin: new google.maps.Point(0, 0), anchor: new google.maps.Point(pin[4], pin[5]) };
		tmp = pin[0].split(",");
		pin1 = new google.maps.Marker({
					position: new google.maps.LatLng(tmp[0], tmp[1]),
					animation: google.maps.Animation.DROP,
					map: mp,
					icon: iconImage,
					title: pin[6]
				});
//		console.log(iconImage);
		
		google.maps.event.addListener(pin1, 'click', (function(pin) {
            return function() {
				window.open(pin[7]);
				return false;
            }
        })(pin));
		return pin1;
	}
}

function popupMap(la, lo) {
	txt = "<div id='gmap' style='height: 600px; width: 800px; '> loading ....</div>";
	message(txt, 800, 600);
	setTimeout(function() {

	var mapOptions = {
          zoom: 9,
          center: new google.maps.LatLng(la, lo),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };


        map = new google.maps.Map(document.getElementById('gmap'),mapOptions);

		img = defIcon; 
		marker = addMarker(img, la, lo, "", 0, 0);
		marker.zIndex = 1;

		
			google.maps.event.addListener(marker, 'click', function() {
				showInfo(this);
			});

		points[i].infobox = "";
	}, 1500);
//		addMarker('','','');

}


function addMarker(imag, la, lo, title, offX, offY) {
			
        return new google.maps.Marker({
            position: new google.maps.LatLng(la, lo),
			animation: google.maps.Animation.DROP,
            map: map,
            icon: imag,
			title: title,
			zIndex: 12
        });
}

function addStdMarker(la, lo, title) {
			
        myLatLng = new google.maps.LatLng(la, lo);
        mk = new google.maps.Marker({
            position: myLatLng,
			animation: google.maps.Animation.BOUNCE ,
            map: map,
			title: title,
				zIndex: 12

        });
		return mk;
}

var allMarkers = [];
function loadPoints(gmap, type) {
	
	gmap.setTilt(45);
	var bounds = new google.maps.LatLngBounds();
	var infoWindowContent = [    ];
	for (i =0; i<points.length; i++) {
//		infoWindowContent.push("<div class='infobox-popup-holder popup-"+points[i].type+"'><img src='/img/area/" + points[i].pic+ "'>"+points[i].title+"</div>");
		infoWindowContent.push(points[i].address);
	}

	var infoWindow = new google.maps.InfoWindow(), marker, i;

	for (markerindex =0; markerindex<points.length; markerindex++)
	{
//		img = points[markerindex].type; 
//		marker = addMarker(img, , points[markerindex].title, points[markerindex].offset[0], points[markerindex].offset[1]);

		if (type != "all")
		{
			
			if (points[markerindex].type != type)
			{
				continue;
			}
		}
//console.log(points[markerindex].lat +"; " + points[markerindex].lon+"; " + points[markerindex].type);
		 var position = new google.maps.LatLng(points[markerindex].lat, points[markerindex].lon);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
			icon: "/img/map/" + points[markerindex].type + ".png",
            map: gmap,
            title: points[markerindex].title
        });
        
        // Allow each marker to have an info window    
        google.maps.event.addListener(marker, 'click', (function(marker, markerindex) {
            return function() {
                infoWindow.setContent(infoWindowContent[markerindex]);
                infoWindow.open(gmap, marker);
//				window.open(infoWindowContent[markerindex]);
				return false;
            }
        })(marker, markerindex));

        // Automatically center the map fitting all markers on the screen
     allMarkers.push(marker);   
	}
//	if (type == "all") gmap.fitBounds(bounds);	

}

// Sets the map on all markers in the array.
function setAllMap(map) {
  for (var i = 0; i < allMarkers.length; i++) {
    allMarkers[i].setMap(map);
  }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
  setAllMap(null);
}

// Shows any markers currently in the array.
function showMarkers() {
  setAllMap(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
  clearMarkers();
  markers = [];
}

function showTraffic() {
		var trafficLayer = new google.maps.TrafficLayer();
        trafficLayer.setMap(map);
}

function showInfo(obj) {
	mid = obj.zIndex;
//	if (points[mid].link != "") {
//		location =points[mid].link;
//		return true;
//	}
//	return false;
	if (!points[mid].infobox)
	{
			loc = new google.maps.LatLng(points[mid].la, points[mid].lo);
			boxtxt = points[mid].title;
			myOptions = {
				 content: "<div style='text-align: left; padding: 1em;'>" + boxtxt + "</div>"
				,boxStyle: {
				   border: "1px solid #00529b"
				   ,borderRadius: "6px"
				   ,backgroundColor: "#fff"
				   ,color: "#000000"
				  ,textAlign: "center"
				  ,fontSize: "1.5em"
				  ,width: "auto"
				  ,padding: "1em"
				  ,zIndex: 15
				 }
				,disableAutoPan: false
				,pixelOffset: new google.maps.Size(10, -15)
				,position: loc
				,closeBoxURL: "http://ch.preview.thebrandfactory.com/img/close-sm.jpg"
				,isHidden: false
				,pane: "mapPane"
				,enableEventPropagation: true
			};

			points[mid].infobox = new InfoBox(myOptions);
			points[mid].infobox.open(map);
	} else {
		if (points[mid].infobox.isHidden)
		{
			points[mid].infobox.show();
		} else {
			points[mid].infobox.hide();
		}
	}
}

function addLabel(la, lo,  title) {

			loc = new google.maps.LatLng(la, lo);
			myOptions = {
				 content: "<nobr>" + title  + "</nobr>"
				,boxStyle: {
				   border: "1px solid #00529b"
				   ,borderRadius: "6px"
				   ,backgroundColor: "#00529b"
				   ,color: "#ffffff"
				  ,textAlign: "center"
				  ,fontSize: "13px"
				  ,width: "auto"
				  ,padding: "4px 15px"
				,zIndex: 10
				 }
				,disableAutoPan: true
				,pixelOffset: new google.maps.Size(10, -15)
				,position: loc
				,closeBoxURL: ""
				,isHidden: false
				,pane: "mapPane"
				,enableEventPropagation: true
			};
			label = new InfoBox(myOptions);
			label.open(map);

}


function addImageLabel(la, lo,  imgdata, offX, offY) {
//	return true;
			loc = new google.maps.LatLng(la, lo);
			myOptions = {
				 content: imgdata
				,boxStyle: {
				   border: "none"
				  ,textAlign: "center"
				  ,width: "auto"
				 }
				,disableAutoPan: true
				,pixelOffset: new google.maps.Size(offX, offY)
				,position: loc
				,closeBoxURL: ""
				,isHidden: false
				,pane: "mapPane"
				,enableEventPropagation: true
			};
			label = new InfoBox(myOptions);
			label.open(map);

}

