/*
	
	Library of useful javascript functions

	Version: 1.0
	Last Update: December 15th, 2018

*/

function jslog(){
	for (i=0; i< arguments.length ; i++ )
	{
		console.log(arguments[i]);
	}
}

function parseConfig(txt) {
	data = txt.split(",");
	configData = [];
	if (data.length == 1)
	{
		configData[0] = [999999, data[0]];
	} else {
		for (i=0; i< data.length ; i = i + 2 )
		{
			configData[i/2] = [data[i], data[i+1]];
		}
	}
	return configData;
}


function encode(strToEncode) {
	return encodeURIComponent(strToEncode);
}

function getQueryVariable(variable) {
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (pair[0] == variable) {
      return pair[1];
    }
  } 
  return "";
}

function utf8_encode ( str_data ) {

    str_data = str_data.replace(/\r\n/g,"\n");
    var utftext = "";
 
    for (var n = 0; n < str_data.length; n++) {
        var c = str_data.charCodeAt(n);
        if (c < 128) {
            utftext += String.fromCharCode(c);
        } else if((c > 127) && (c < 2048)) {
            utftext += String.fromCharCode((c >> 6) | 192);
            utftext += String.fromCharCode((c & 63) | 128);
        } else {
            utftext += String.fromCharCode((c >> 12) | 224);
            utftext += String.fromCharCode(((c >> 6) & 63) | 128);
            utftext += String.fromCharCode((c & 63) | 128);
        }
    }
 
    return utftext;
}

function bookmark(url, sitename)
{
	if (window.sidebar) window.sidebar.addPanel(pageName, urlAddress,"");
		else if( window.external ) window.external.AddFavorite( urlAddress, pageName); 
			else if(window.opera && window.print) { return true; }
}

function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}

function currencyFormat(nStr) {
	
	return '$' + number_format(nStr, 2);
}

function filesizeFormat(bytes){
  if      (bytes >= 1073741824) { bytes = (bytes / 1073741824).toFixed(2) + " GB"; }
  else if (bytes >= 1048576)    { bytes = (bytes / 1048576).toFixed(2) + " MB"; }
  else if (bytes >= 1024)       { bytes = (bytes / 1024).toFixed(2) + " KB"; }
  else if (bytes > 1)           { bytes = bytes + " bytes"; }
  else if (bytes == 1)          { bytes = bytes + " byte"; }
  else                          { bytes = "0 bytes"; }
  return bytes;
}

/* BROWSER DETECTION */
var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari"
		},
		{
			prop: window.opera,
			identity: "Opera"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};
BrowserDetect.init();


/* COOKIES */

function Set_Cookie( name, value, expires, path, domain, secure ) 
{
		// set time, it's in milliseconds
		var today = new Date();
		today.setTime( today.getTime() );

		/*
		if the expires variable is set, make the correct 
		expires time, the current script below will set 
		it for x number of days, to make it for hours, 
		delete * 24, for minutes, delete * 60 * 24
		*/
		if ( expires )
		{
		expires = expires * 1000 * 60 * 60 * 24;
		}
		var expires_date = new Date( today.getTime() + (expires) );

		document.cookie = name + "=" +escape( value ) +
		( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) + 
		( ( path ) ? ";path=" + path : "" ) + 
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
}


// this fixes an issue with the old method, ambiguous values 
// with this test document.cookie.indexOf( name + "=" );
function Get_Cookie( check_name ) {
	// first we'll split this cookie up into name/value pairs
	// note: document.cookie only returns name=value, not the other components
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false; // set boolean t/f default f
	
	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		// now we'll split apart each name=value pair
		a_temp_cookie = a_all_cookies[i].split( '=' );
		// and trim left/right whitespace while we're at it
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');
		
		// if the extracted name matches passed check_name
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			try
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );	
			}
			catch (ex)
			{
				cookie_value = "";
			}
			
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if ( !b_cookie_found )
	{
		return null;
	}
}		

// this deletes the cookie when called
function Delete_Cookie( name, path, domain ) {
	if ( Get_Cookie( name ) ) document.cookie = name + "=" +
	( ( path ) ? ";path=" + path : "") +
	( ( domain ) ? ";domain=" + domain : "" ) +
	";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}


/* PRELOADING */

function preloadSimple(arrayOfImages) {
    $(arrayOfImages).each(function(){
        $('img')[0].src = this;
    });
}


function preload(assets, callback, callbackProgress) {
	preloading = true;
	var not_ready_yet = true;
	setTimeout(function(){
		not_ready_yet = false;
		if (!preloading) callback();
	}, minWait);
	console.log("start preload");
	setTimeout(function () {
		if (preloading)
		{
			preloading= false;
//			console.log("premature preload");
			callback();
			
		}	
	}, maxWait);
	var count = assets.length;
//	console.log(count);
	if(count == 0) {
        callback();
    }
    var loaded = 0;
//	console.log(assets);
//	console.log("total to load: " + count);
    $(assets).each(function() {
        $("img").on("load", function() {
            loaded++;
//			console.log(this, loaded);
//			console.log(loaded, "remaining : " + (count - loaded));
			progress = Math.ceil(loaded * 100/count);
			if (callbackProgress) {
//				console.log("progress update");
				callbackProgress(progress);
			}
			if (progress >= 75 && preloading)
			{
				preloading= false;
//				console.log("75p preload");
				if (!not_ready_yet) callback();
			} else {
				if (loaded == count && preloading) {
					preloading= false;
//					console.log("full preload");
				   if (!not_ready_yet) callback();
				   
				}
			}
        });
    });

}




		/* MD5 */

		function md5 ( str ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // -    depends on: utf8_encode
    // *     example 1: md5('Kevin van Zonneveld');
    // *     returns 1: '6e658d4bfcb59cc13f96c14450ac40b9'
 
				 
					function RotateLeft(lValue, iShiftBits) {
						return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
					}
				 
					function AddUnsigned(lX,lY) {
						var lX4,lY4,lX8,lY8,lResult;
						lX8 = (lX & 0x80000000);
						lY8 = (lY & 0x80000000);
						lX4 = (lX & 0x40000000);
						lY4 = (lY & 0x40000000);
						lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
						if (lX4 & lY4) {
							return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
						}
						if (lX4 | lY4) {
							if (lResult & 0x40000000) {
								return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
							} else {
								return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
							}
						} else {
							return (lResult ^ lX8 ^ lY8);
						}
					}
				 
					function F(x,y,z) { return (x & y) | ((~x) & z); }
					function G(x,y,z) { return (x & z) | (y & (~z)); }
					function H(x,y,z) { return (x ^ y ^ z); }
					function I(x,y,z) { return (y ^ (x | (~z))); }
				 
					function FF(a,b,c,d,x,s,ac) {
						a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
						return AddUnsigned(RotateLeft(a, s), b);
					}
				 
					function GG(a,b,c,d,x,s,ac) {
						a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
						return AddUnsigned(RotateLeft(a, s), b);
					}
				 
					function HH(a,b,c,d,x,s,ac) {
						a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
						return AddUnsigned(RotateLeft(a, s), b);
					}
				 
					function II(a,b,c,d,x,s,ac) {
						a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
						return AddUnsigned(RotateLeft(a, s), b);
					}
				 
					function ConvertToWordArray(str) {
						var lWordCount;
						var lMessageLength = str.length;
						var lNumberOfWords_temp1=lMessageLength + 8;
						var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
						var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
						var lWordArray=Array(lNumberOfWords-1);
						var lBytePosition = 0;
						var lByteCount = 0;
						while ( lByteCount < lMessageLength ) {
							lWordCount = (lByteCount-(lByteCount % 4))/4;
							lBytePosition = (lByteCount % 4)*8;
							lWordArray[lWordCount] = (lWordArray[lWordCount] | (str.charCodeAt(lByteCount)<<lBytePosition));
							lByteCount++;
						}
						lWordCount = (lByteCount-(lByteCount % 4))/4;
						lBytePosition = (lByteCount % 4)*8;
						lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
						lWordArray[lNumberOfWords-2] = lMessageLength<<3;
						lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
						return lWordArray;
					}
				 
					function WordToHex(lValue) {
						var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
						for (lCount = 0;lCount<=3;lCount++) {
							lByte = (lValue>>>(lCount*8)) & 255;
							WordToHexValue_temp = "0" + lByte.toString(16);
							WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
						}
						return WordToHexValue;
					}
				 
					var x=Array();
					var k,AA,BB,CC,DD,a,b,c,d;
					var S11=7, S12=12, S13=17, S14=22;
					var S21=5, S22=9 , S23=14, S24=20;
					var S31=4, S32=11, S33=16, S34=23;
					var S41=6, S42=10, S43=15, S44=21;
				 
					str = utf8_encode(str);
					x = ConvertToWordArray(str);
					a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;
				 
					for (k=0;k<x.length;k+=16) {
						AA=a; BB=b; CC=c; DD=d;
						a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
						d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
						c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
						b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
						a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
						d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
						c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
						b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
						a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
						d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
						c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
						b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
						a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
						d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
						c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
						b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
						a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
						d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
						c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
						b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
						a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
						d=GG(d,a,b,c,x[k+10],S22,0x2441453);
						c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
						b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
						a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
						d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
						c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
						b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
						a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
						d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
						c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
						b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
						a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
						d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
						c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
						b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
						a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
						d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
						c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
						b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
						a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
						d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
						c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
						b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
						a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
						d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
						c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
						b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
						a=II(a,b,c,d,x[k+0], S41,0xF4292244);
						d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
						c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
						b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
						a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
						d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
						c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
						b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
						a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
						d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
						c=II(c,d,a,b,x[k+6], S43,0xA3014314);
						b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
						a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
						d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
						c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
						b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
						a=AddUnsigned(a,AA);
						b=AddUnsigned(b,BB);
						c=AddUnsigned(c,CC);
						d=AddUnsigned(d,DD);
					}
				 
					var temp = WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);
				 
					return temp.toLowerCase();
}

/* COLOR FUNCTIONS */

function RGBtoHSV(r, g, b) {
    if (arguments.length === 1) {
        g = r.g, b = r.b, r = r.r;
    }
    var max = Math.max(r, g, b), min = Math.min(r, g, b),
        d = max - min,
        h,
        s = (max === 0 ? 0 : d / max),
        v = max / 255;

    switch (max) {
        case min: h = 0; break;
        case r: h = (g - b) + d * (g < b ? 6: 0); h /= 6 * d; break;
        case g: h = (b - r) + d * 2; h /= 6 * d; break;
        case b: h = (r - g) + d * 4; h /= 6 * d; break;
    }

    return {
        h: h,
        s: s,
        v: v
    };
}


function openFullscreen(elem) {
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.msRequestFullscreen();
  }
}