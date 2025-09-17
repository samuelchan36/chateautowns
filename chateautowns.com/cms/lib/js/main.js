var  submitInProgress = false;
var jsCleave = [];

var randomVersion = Math.random();

$(document).ready(function (){

		bindFormFields($("html"));

		$(".fancybox").fancybox({openEffect	: 'elastic', closeEffect	: 'elastic', helpers : {
    		title : {
    			type : 'over'
    		}
    	}});

		$(window).on("keydown", function(ev){
			if (ev.key == "Escape") $("#close-main-overlay").click();
			return true;	
		})

		$(".delete-icon").bind("click", function () { return confirm('Are you sure you want to delete this item?');});
		$(".confirm-icon").bind("click", function () { return confirm('Are you sure you want to proceed with this operation?');});
		$("#bt-cancel").bind("click", function () { location.href ="/cms/index.php?s=" + getQueryVariable("s"); });

		if ($("#notice-area").html() != "")
		{
			$("#notice-area").show();
			setTimeout(function () {
				$("#notice-area").animate({opacity: "0.2", }, "slow", function () {
				});
			}, 5000);
		} else {
			$("#notice-area").hide();
		}



			$("img[data-help]").mouseenter(function(ev){
				if ($("#tooltip").length > 0) return false;
				$("body").append("<div id='tooltip'>" + $(this).attr("data-help") + "</div>");
				$("#tooltip").css("top", ev.pageY- 15).css("left", ev.pageX - 15)
				$("#tooltip").mouseleave(function(){
					console.log('leave');
					$("#tooltip").remove();
				})
			})




		$("input[name='ActivationType'].delayed").click(function() {
			$("div.block-activation-dates").show();
		})
		$("input[name='ActivationType'].instant").click(function() {
			$("div.block-activation-dates").hide();
		})
		$("input[name='PublishingType'].delayed").click(function() {
			$("div.block-publish-dates").show();
		})
		$("input[name='PublishingType'].instant").click(function() {
			$("div.block-publish-dates").hide();
		})
		$("input[name='ActivationType']:checked").click();
		$("input[name='PublishingType']:checked").click();

		$("input.url-source").bind("blur", function () {
			if ($(this).val() == "" || !$(this).is("[data-target]")) return true;
			if ($("#" + $(this).attr("data-target")).length <= 0) return true;
			if ($("#" + $(this).attr("data-target")).val() == "")
			{
				var dataTarget = $(this).attr("data-target");
				$.get( ($(this).is("[data-script]") ? $(this).attr("data-script") : "/cms/system/safe-url") + "?text=" + $(this).val() + "&options=" + $(this).attr("data-target-option"), function (data) {
					$("#" + dataTarget).val(data.output);
				}, "json");	
			}
		});

		$(".js-drop-controller").change(function(){
				if (!$(this).is("[data-target]")) return true;
				var dataTarget = $(this).attr("data-target");
				if ($(this).is("[data-controller]")) {
					url = "/cms/"+$(this).attr("data-controller")+"/get-options?value=" + $(this).val();
				} else {
					url = "/cms/system/get-options?source=" + $(this).attr("data-source") + "&value=" + $(this).val() + "&condition=" + $(this).attr("id");
				}
				
				$.get(url, function (data) {
					console.log(data);
					el = $("#" + dataTarget).get(0);
					el.options.length = 0;
					o = new Option(); o.text = ""; o.value = "";
					el.options[el.options.length] = o;

					for (i=0; i < data.length ; i++ )
					{
						o = new Option(); o.text = data[i].Name; o.value = data[i].ID;
						el.options[el.options.length] = o;
					}
				}, "json");	

		})

		$(".js-unique").change(function(){
				var dataTarget = $(this);
				console.log("/cms/system/check-unique?condition=" + $(this).attr("data-condition") + "&value=" + $(this).val());
				$.get("/cms/system/check-unique?condition=" + $(this).attr("data-condition") + "&value=" + $(this).val(), function (data) {
					console.log(data);
					if (data.trim() != "ok") {
						dataTarget.val("");
						dataTarget.parent().addClass("not-unique");
					} else {
						dataTarget.parent().removeClass("not-unique");
					}
				}, "text");	
				return true;
		})


		if ($('textarea.rich-editor').length > 0)
		{
				tinymce.init({ 
					selector:'textarea.rich-editor',
					max_width: "1920px",
					content_css : "/lib/css/default.css, /css/main.css?v="+randomVersion+", /cms/app/assets/css/tinymce.css?v="+randomVersion+", " + masterCSS,
					font_formats: tinyFonts,
					fontsize_formats: tinySizes,
					style_formats: tinyStyles,
					block_formats: blockFormats,
					style_formats_autohide: true,
					remove_script_host : true,
					branding: false,
					relative_urls: false,
					document_base_url : "https://"+location.hostname+"/",
					body_class: $("textarea.rich-editor").is("[data-editor-class]") ? $("textarea.rich-editor").attr("data-editor-class") : "",
					browser_spellcheck: true,
					valid_elements: "*[*]",
					valid_children: "+a[div],+div[img],+cms",
					templates: $("textarea.rich-editor").is("[data-editor-template]") ?  window[$("textarea.rich-editor").attr("data-editor-template")]: tinyTemplates,
					images_upload_url: "/cms/system/upload-image",
					plugins : "table preview paste visualblocks code jsimage media link fullscreen textcolor spellchecker lists template jsimage",
					menubar: false,
					toolbar: [	"fullscreen | template | styleselect formatselect | cut copy paste | undo redo ",
					"jsimage | media | link unlink | table | forecolor backcolor bold italic underline strikethrough alignleft aligncenter alignright alignjustify outdent indent | bullist numlist subscript superscript",
					" removeformat visualblocks code"],
					visualblocks_default_state: false,
					  end_container_on_empty_block: true
					});
		}

		if ($('textarea.simple-editor').length > 0)
		{
				tinymce.init({ 
						selector:'textarea.simple-editor',
						max_width: "1920px",
						content_css : "/lib/css/default.css, /css/main.css, /cms/app/assets/css/tinymce.css?v=2, " + masterCSS,
						font_formats: tinyFonts,
						fontsize_formats: tinySizes,
						style_formats: tinyStyles,
						block_formats: blockFormats,
						style_formats_autohide: true,
						remove_script_host : true,
						branding: false,
						relative_urls: false,
						document_base_url : "://"+location.hostname+"/",
						body_class: $("textarea.rich-editor").is("[data-editor-class]") ? $("textarea.rich-editor").attr("data-editor-class") : "",
						browser_spellcheck: true,
						valid_elements: "*[*]",
						valid_children: "+a[div],+div[img],+cms",
						templates: tinyTemplates,
						images_upload_url: "/cms/system/upload-image",
						plugins : "table preview paste visualblocks code jsimage media link fullscreen textcolor spellchecker lists template jsimage",
						menubar: false,
						toolbar: [	"formatselect fontselect fontsizeselect | link unlink | forecolor backcolor bold italic underline strikethrough alignleft aligncenter alignright alignjustify outdent indent | bullist numlist subscript superscript | fullscreen code"],
						visualblocks_default_state: false,
					  end_container_on_empty_block: true
					});
		}

		bindFormValidation($("html"));


		$("#bt-apply-filters").click(function(){
			$("#frmFilter").get(0).submit();
			return false;
		})

		$("#bt-reset-filters").click(function(){
//			console.log(location);
//			return false;
			location = location.pathname + "?o=" + getQueryVariable("o") + "&reset=yes";
			return false;
		})

		$("#close-main-overlay").click(closeOverlayWindow);
			$('toggle').each(function(){
				if ($(this).attr("data-value") == $(this).attr("data-on" ))
				{
					$(this).addClass("active");
				}	 else {
					$(this).removeClass("active");
				}
			})

			$('toggle').click(function(){
				if ($(this).hasClass("waiting")) return false;
				$(this).addClass("waiting");
				var editFld = $(this);
				$.get("/cms/" + $(this).attr("data-module") +"/toggle-field?id=" + $(this).attr("data-id") + "&fld=" + $(this).attr("data-field")+ "&value=" + ($(this).hasClass("active") ? $(this).attr("data-off") : $(this).attr("data-on")), function(data){
					editFld.toggleClass("active").removeClass("waiting");
				}, "text");
				return false;

			});

			$('.edit-field').change(function(){
				console.log('/cms/'+ $(this).attr("data-module") +'/update-field?id=' + $(this).attr('data-id') + "&fld=" + $(this).attr("data-field")+ "&value=" + encode($(this).val()));
				$.get('/cms/'+ $(this).attr("data-module") +'/update-field?id=' + $(this).attr('data-id') + "&fld=" + $(this).attr("data-field")+ "&value=" + encode($(this).val()), function(data){
					console.log(data)	;
				  }, 'text')	
			})

	})


function bindFormFields($parent){

			$parent.find('input.js-phone-number').each(function(){
					jsCleave.push(
											new Cleave($(this), {
												phone: true,
												phoneRegionCode: 'ca'
											})
										)
				})

			$parent.find('input.js-integer').each(function(){
					jsCleave.push(
											new Cleave($(this), {
																				numeral: true,
																					blocks:[10],
																				delimiter: ""
																			}
																)
										)
				})
			
			$parent.find('input.js-postal-code').each(function(){
					jsCleave.push(
											new Cleave($(this), {
																					blocks:[3,3],
																				delimiter: " "
																			}
																)
										)
				})
																
											
			$('input.js-credit-card').each(function(){
					jsCleave.push(
											new Cleave($(this), {
																				creditCard: true,
																				onCreditCardTypeChanged: function (type) { }
																			}
																)
										)
				})

			$parent.find('input.js-float').each(function(){
					jsCleave.push(
											new Cleave($(this), {
																				numeral: true,
																				numeralThousandsGroupStyle: 'thousand'
																			}
																)
										)
				})

				$parent.find("input.js-email").each(function(){
					$(this).blur(function(){
						if ($(this).val())
						{
							var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
							if (re.test($(this).val()))
							  {
									$(this).removeClass("warning");
									$(this).parent().removeClass("warning");
									$(this).parent().find("span.warning-message").remove();
									return true;
							  } else {
									$(this).addClass("warning");
									$(this).parent().addClass("warning");
									$(this).parent().append("<span class='warning-message'>Notice: This email address appears invalid</span>");
							  }
						}
					})	
				})

				$parent.find("input[config-max-length],textarea[config-max-length]").keydown(function(ev){
					if ($(this).val().length >= parseInt($(this).attr("config-max-length"))) return false;
				})

				$parent.find("input[config-max-length],textarea[config-max-length]").blur(function(ev){
					if ($(this).val().length >= parseInt($(this).attr("config-max-length"))) $(this).val($(this).val().substring(0, $(this).attr("config-max-length")));
					return true;
				})
				
				$parent.find("select.js-select").each(function() {
					minRes = 15;
					if ($(this).is("[placeholder]")) {
						def = $(this).attr("placeholder");
						if ($(this).is("[config-min-results]"))
						{
							minRes = $(this).attr("config-min-results");
						}
						$(this).select2({minimumResultsForSearch: minRes, placeholder: def});
					} else 
						$(this).select2({minimumResultsForSearch: minRes});
				})

					$parent.find("input[type='file']").change(function(){

						$(this).parent().find("div.input-file-list div").html("");
						if ($(this).parent().find("div.input-file-list div").length == 0) $(this).parent().find("div.input-file-list").append("<div></div>");
						uploads = $(this).get(0).files;
						for (i=0; i< uploads.length ; i++ )
						{
							$(this).parent().find("div.input-file-list div").append("<span>"+uploads[i].name + " ("+filesizeFormat(uploads[i].size)+")" +"</span>");
						}				


						return true;
					})

				$parent.find("input.js-calendar").each(function() {
		//			fldid = $(this).attr("id"); 
		//			if ($(this).val() && !$("#" + fldid.replace("_alt","")).val())
		//			{
		//				$("#" + fldid.replace("_alt","")).val()
		//			}
					dp_options = { changeMonth: true, changeYear: true, dateFormat: "M d, yy", altFormat: "@",  altField: "#" + $(this).attr("id").replace("_alt",""), onClose: function(dateText, inst) { fldid = $(this).attr("id"); if($("#" + fldid.replace("_alt","")).val()>=2500000000) $("#" + fldid.replace("_alt","")).val($("#" + fldid.replace("_alt","")).val()/1000); }};
					if ($(this).is("[config-future]"))
					{
						dp_options.minDate = "today";
					}
					$(this).datepicker(dp_options);
				});

				$parent.find("input.js-datepicker").each(function() {

					$(this).datepicker( { changeMonth: true, changeYear: true, minDate: "today", dateFormat: "M d, yy" });
				});

				$parent.find("input.js-time").ptTimeSelect();

	}

	function bindFormValidation($parent){
		$parent.find("form.validateme").submit(function () {
			tinyMCE.triggerSave();

			var myform = $(this);

			if (submitInProgress) return false;
			submitInProgress = true;

			if (myform.hasClass("ajax-submit")){
				myform.find("button[type='submit']").addClass("submit-in-progress");
			}


			label = $(this).find("input[type=submit]").val();
			$(this).find("input[type=submit]").addClass("waiting").val("Sending ...");

			var errors = [];

			$(this).find("input.mandatory, select.mandatory, textarea.mandatory").each(function () {
				if ($(this).hasClass("if-visible") && $(this).is("[config-if-element]") && !$($(this).attr("config-if-element")).hasClass("active"))
				{
					return true;
				}

				if ($(this).val() == "" && (!$(this).attr('type') || $(this).get(0).defaultValue == ""))
				{
					errors.push("Field is empty");
//					console.log($(this).attr("id"));
					$(this).addClass("missing");
					$("label[for=\""+$(this).attr("id")+"\"]").addClass("missing");
					$(this).next(".select2").addClass("missing");
				}
				if ($(this).is('[type="radio"]'))
				{
					radioname = $(this).attr("name");
					if ($('input[name="'+radioname+'"]:checked').length <= 0)
					{
						errors.push("Field is empty");
						$('input[name="'+radioname+'"]').parent().addClass("missing");
						$('input[name="'+radioname+'"]').parent().parent().addClass("missing");
						$('input[name="'+radioname+'"]').parent().parent().parent().addClass("missing");
						$('input[name="'+radioname+'"]').parent().parent().parent().parent().addClass("missing");
					}
				}
			});
			
			

			if ($(this).find("input.agreement-field").length > 0 )
			{
					if ($(this).find("input.agreement-field:checked").length == 0)
					{
						errors.push("Field is empty");
						$(this).find("input.agreement-field").first().parent().addClass("missing");
					}
			}


			$("input[config-max-length],textarea[config-max-length]").each(function(){
				if ($(this).val().length >= parseInt($(this).attr("config-max-length"))) $(this).val($(this).val().substring(0, $(this).attr("config-max-length")));
			})

			

			console.log(errors);
			if (errors.length > 0)
			{
				if ($(".form-anchor[data-for="+$(this).attr("id")+"]").length > 0)
				{
					scrollToArea($(".form-anchor[data-for="+$(this).attr("id")+"]").attr("id"));
				}
				
				$(this).find("input[type=submit]").removeClass("waiting").val(label);
				submitInProgress = false;
				return false;
			}
			var redirectto = "/" + $(this).attr("data-response");

			$("input[type='checkbox']").each(function(){
				if (!$(this).is(":checked")) {
					if ($(this).is("[data-off]"))
					{
						$(this).val($(this).attr("data-off"));
							$(this).attr("checked", "checked").addClass("hidden-checkbox");
					}
				} else {
					if ($(this).is("[data-on]"))
					{
						$(this).val($(this).attr("data-on"));
					}
				}
			})

			$("input.js-integer,input.js-float").each(function(){
				if ($(this).val() == "") $(this).val("0");
			})


			if (myform.hasClass("ajax-submit"))
			{


				$.ajax({
						 url: myform.attr("action"),
						type: "POST",
						data:  new FormData(myform.get(0)),
						contentType: false,
						 cache: false,
						processData:false,
						success: function(datatxt) {
							 console.log(datatxt);
								data = JSON.parse(datatxt);
//								console.log(data);
								myform.find("button[type='submit']").removeClass("submit-in-progress");
								submitInProgress = false;
								if (data.response == "ok")
								{
//									myform.get(0).reset();
									formResponseData = data.data;
									if (myform.is("[data-block-id]")) formResponseData.BlockID = myform.attr("data-block-id"); 
									if (myform.is("[data-post-submit]"))
									{
										eval(myform.attr("data-post-submit") + "()");
									}
								} else {
									error(data.message);
								}
								myform ="";
					  }
					});
				return false;
			} else
				return true;
		})

	}


function hideEditor() {
//	tinymce.activeEditor.save();
	$("#editor-holder").css("display", "none");
	if (activeBlock) activeBlock.html(tinyMCE.get('editor').getContent());


}

function showOverlayWindow(content){
	$("#main-overlay > div").html(content);
	$("#main-overlay").addClass("active");
	$("html").addClass("overlay-active");
	return false;
}

function closeOverlayWindow(content){
	$("#main-overlay").removeClass("active");
	$("#main-overlay > div").html("");
	$("html").removeClass("overlay-active");
	return false;
}
