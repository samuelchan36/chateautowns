var jsValidationErrors = [];
var jsRecaptchaKey ="";

/* GOOGLE AUTOCOMPLETE */
var jsAutoComplete;
var jsAutoCompleteField;
var jsAutoCompleteResult;

/* INPUT VALIDATION */
var jsCleave = [];


$(function() {

	$("form.validateme").submit(_formValidation);
	$("form.validateme").append("<input type=\"hidden\" name=\"jsTimeStamp\" value=\""+$("body").attr("data-stamp")+"\"><input type=\"hidden\" name=\"jsInteractions1\" id=\"jsInteractions1\" value=\"0\"><input type=\"hidden\" name=\"jsInteractions2\" id=\"jsInteractions2\" value=\"0\"><input type=\"hidden\" name=\"jsInteractions3\" id=\"jsInteractions3\" value=\"0\"><input type=\"hidden\" name=\"jsInteractions4\" id=\"jsInteractions4\" value=\"no\">");
	$("form.validateme").on("click", function(){
		$("#jsInteractions4").val("yes");
		return true;
	});

	_addFormFieldValidations($(this));

	$(".js-reveal-hidden").change(function(){
		obj = $($(this).attr("config-reveal-field")).first();
		if (obj.length > 0) {
			if ($(this).val() == $(this).attr("config-trigger-value"))
			{
				obj.addClass("revealed");
			} else {
				obj.removeClass("revealed");
			}
		}
	})


	$("input[type='file']").change(function(){

			$(this).parent().find("div.input-file-list div").html("");
			if ($(this).parent().find("div.input-file-list div").length == 0) $(this).parent().find("div.input-file-list").append("<div></div>");
			uploads = $(this).get(0).files;
			console.log(uploads);
			var _file_input = $(this);
			for (i=0; i< uploads.length ; i++ )
			{
				$(this).parent().find("div.input-file-list div").append("<span data-id='"+i+"'>"+uploads[i].name + " ("+filesizeFormat(uploads[i].size)+")" +"</span>");
				$(this).parent().find("div.input-file-list div span").bind("click", function(){
					if (confirm("Are you sure you want to remove this file?"))
					{
						removeFileFromFileList(_file_input.get(0), $(this).attr("data-id"));
						console.log(_file_input.get(0).files);
						$(this).remove();
					}	
				})
			}				


			return true;
		})

	formTabs();
})


function jsValidateAndSubmit(frm) {
	if (validateTab(frm, frm))
	{
		jsSubmitForm(frm.attr("action"), frm);
	}
}

function jsSubmitStart(obj) {
	console.log("step 2", obj);
	if (obj.hasClass("validation-in-progress") || obj.hasClass("submit-in-progress") ) return false;

	obj.removeClass("validate-in-progress");
	obj.addClass("submit-in-progress");
	
	return true;

}

function jsSubmitEnd(obj, data){
	obj.removeClass("submit-in-progress");
	if (data) jslog(data);
}


var submitInProgress = false;
var myform;
function _formValidation() {

	if (!myform) myform = $(this);

	if (myform.is("[before-submit]"))
	{
		window[myform.attr("before-submit")]();
		return false;
	}
	try
	{
		if (myform.find("[name=\"g-recaptcha-response\"]").length > 0 && myform.find("[name=\"g-recaptcha-response\"]").first().val() == "")
		{
			grecaptcha.execute(jsRecaptchaKey, {action: 'submit'}).then(function(token) {
					myform.find("[name=\"g-recaptcha-response\"]").val(token);
					return _formValidation();
          });
			return false
		}
	} catch(ex){
		jslog(ex);
		return false;
	}


	try
	{

			if (validateTab(myform, myform))
			{
				jsSubmitStart(myform);
				
				/* UPDATE TRACKERS */
			
				$("#jsInteractions1").val(scrollCount);
				$("#jsInteractions2").val(mouseMoveCount);
				$("#jsInteractions3").val(keyCount);

				/* DIRECT SUBMIT */
				if (myform.attr("data-submit-type") == "direct") return true; 


				/* AJAX SUBMIT */
				$.ajax({
						 url: myform.attr("action"),
						type: "POST",
						data:  new FormData(myform.get(0)),
						contentType: false,
						 cache: false,
						processData:false,
						success: function(datatxt) {
							 //console.log(datatxt);
								data = JSON.parse(datatxt);
								jsSubmitEnd(myform, data);
								if (data.response == "ok")
								{
										
										_track(myform);
											

										/* jCRM */
											if (window["jtrack_match"] !== "undefined" && typeof window["jtrack_match"] === 'function' && data.id && jtrack_visitor)
											{
												try
												{
													console.log("match");
													jtrack_match(data.id);
												}
												catch (ex)
												{
													console.log("Unable to match user");
												}
										}
										/* END jCRM */

										if (myform.is("[post-submit]"))
										{
											window[myform.attr("post-submit")](data);
										} else {


										if (data.url) location = data.url;
										else {
											myform.addClass("form-done");
											$("div.registration[data-form="+myform.attr("id")+"]").addClass("form-done");
											if (data.message != "") $("div.inline-response[data-form="+myform.attr("id")+"]").html(data.message);
											$("div.inline-response[data-form="+myform.attr("id")+"]").addClass("reveal-response");
											scrollToElement($(".form-anchor[data-form="+myform.attr("id")+"]"));
													if (data.refresh)
													{
															$("a.close-overlay").addClass("refresh");
													}
													$("div.overlay.active").addClass("post-submit");
										}
}
								} else {
									if (data.error == 100)
									{
										/* Recaptcha validation error */
									}
									error(data.message);
								}
								myform ="";
					  }
					});


			} else {
				myform ="";
			}
			return false;
	} catch(ex){
		jslog(ex);
		return false;
	}
}


function validateTab(frm, frmTab){

			if (frm.hasClass("validation-in-progress") || frm.hasClass("submit-in-progress") ) return false;
			frm.addClass("validation-in-progress");

			frmTab.find("div.field input, div.field select, div.field textarea").change(function(){
				if ($(this).val()) {
					fieldName = $(this).attr("name");
					if (fieldName)
					{
						$("[name=\""+fieldName+"\"]").removeClass("missing").removeClass("warning");
						$("[name=\""+fieldName+"\"]").parent().removeClass("missing").removeClass("warning");
						if ($(this).attr("type") == "checkbox") $("[name=\""+fieldName+"\"]").parent().parent().removeClass("missing").removeClass("warning");
						$("[name=\""+fieldName+"\"]").parent().find("span.error-message").remove();
						$("[name=\""+fieldName+"\"]").parent().find("span.warning-message").remove();
					}
				}
			})

			frmTab.find("span.error-message").remove();

			var jsValidationErrors = [];
			frmTab.find("div.field.mandatory input, div.field.mandatory select, div.field.mandatory textarea").each(function () {
				
				if ($(this).hasClass("select2-search__field") && $(this).attr("type") == "search") return true;

				if ( $(this).hasClass("if-visible") && !$("." + $(this).attr("js-data-condition")).hasClass("active") )
				{
					return true;
				}

				if ($(this).is('[type="checkbox"]'))
				{
					if (!$(this).is(":checked"))
					{
						jsValidationErrors.push({item: $(this), message: "Field is empty"});
						$(this).parent().addClass("missing");
						$(this).parent().parent().addClass("missing");
						$(this).addClass("missing");
					}
				} else if ($(this).is('[type="radio"]')) {
					radioname = $(this).attr("name");
					if ($('input[name="'+radioname+'"]:checked').length <= 0)
					{
						jsValidationErrors.push({item: $(this), message: "Field is empty"});
						$('input[name="'+radioname+'"]').parent().addClass("missing");
					}
				} else if ($(this).val() == "" || $(this).val() == null)
				{
					jsValidationErrors.push({item: $(this), message: "Field is empty" + $(this).attr("id")});
					$(this).addClass("missing");
					$(this).parent().addClass("missing");
					$(this).parent().append("<span class='error-message'>Field is empty</span>");
				}
			});

			frmTab.find("[config-max-length]").each(function(){
				if ($(this).val().length > parseInt($(this).attr("config-max-length")))
				{
					jsValidationErrors.push({item: $(this), message: "You can only use " + $(this).attr("config-max-length") + " characters for this field"});
					$(this).addClass("missing");
					$(this).parent().addClass("missing");
					$(this).parent().append("<span class='error-message'>You can only use " + $(this).attr("config-max-length") + " characters for this field</span>");

				}	
			})

			frmTab.find("[config-min-length]").each(function(){
				if ($(this).val().length > 0 && $(this).val().length < parseInt($(this).attr("config-min-length")))
				{
					jsValidationErrors.push({item: $(this), message: "This field requires at least " + $(this).attr("config-min-length") + " characters"});

					$(this).addClass("missing");
					$(this).parent().addClass("missing");
					$(this).parent().append("<span class='error-message'>This field requires at least " + $(this).attr("config-min-length") + " characters</span>");

				}	
			})

				frmTab.find(".js-email").each(function(){
					if (!$(this).hasClass("missing"))
					{
						if ($(this).val())
						{
							var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
							if (re.test($(this).val())) {
									$(this).removeClass("warning");
									$(this).parent().removeClass("warning");
									$(this).parent().find("span.warning-message").remove();
									return true;
							  } else {
									$(this).addClass("missing");
									$(this).parent().addClass("missing");
									$(this).parent().find(".warning-message").remove();
									$(this).parent().append("<span class='error-message'>Notice: This email address appears invalid</span>");
									jsValidationErrors.push({item: $(this), message: "Invalid email address"});
							  }
						}
					}
				})


		
			frmTab.find(".js-confirmation-field").each(function(){
				parentField = $("#" + $(this).attr("js-data-related"));
				if ($(this).val() != parentField.val())
				{
					jsValidationErrors.push({item: $(this), message: "Field values must match"});
					$(this).addClass("missing");
					$(this).parent().addClass("missing");
					if ($(this).parent().find(".error-message").length > 0)
					{
						$(this).parent().find(".error-message").html($(this).parent().find(".error-message").html() + "; Values must match");
					} else 
						$(this).parent().append("<span class='error-message'>Values must match</span>");
				}
				
			})

		

		if (frmTab.find("input.js-agreement-field").length > 0 )
		{
				if (frmTab.find("input.js-agreement-field:checked").length == 0)
				{
					jsValidationErrors.push({item: $(this), message: "Field is empty"});
					frmTab.find("input.js-agreement-field").first().parent().addClass("missing");
				}
		}


		if (frmTab.find("[data-custom-validation]").length > 0)
		{
			frmTab.find("[data-custom-validation]").each(function(){
				_custom_func = $(this).attr("data-custom-validation");
				if (window[_custom_func] !== "undefined" && typeof window[_custom_func] === 'function' )
				{
					if (!window[_custom_func]())
					{
						jsValidationErrors.push({item: $(this).find("input").first(), message: "Invalid value"});
					}
				}
			})
		}

		if (jsValidationErrors.length > 0)
		{
				scrollToElement(jsValidationErrors[0].item, $("header").first().outerHeight());
			
//				frm.find("button[type=submit]").removeClass("waiting").val(label);
			frm.removeClass("validation-in-progress");
			return false;
		}

		frm.removeClass("validation-in-progress");
		return true;
}

function _track(myform) {
	try
	{
		ga_event = "CompleteRegistration";
		adw_event = "";
		fb_event = "CompleteRegistration";

		if (myform.is("[data-event-name]")) ga_event = myform.attr("data-event-name");
		if (myform.is("[data-adwords]")) adw_event = myform.attr("data-adwords");
		if (myform.is("[data-facebook]")) fb_event = myform.attr("data-facebook");
		console.log(ga_event, adw_event, fb_event);

		gtag('event', 'Signup', {'event_category': "Conversions", 'event_label': ga_event, 'value': "0"});
		if (adw_event) {
			adw_events = adw_event.split(";");
			for (i=0; i< adw_events.length; i++)
			{
				gtag('event', 'conversion', {'send_to': adw_events[i]});
			}
			
//			console.log("tracked adwords", adw_event);
		}

		fb_events = fb_event.split(";");
		for (i=0; i< fb_events.length; i++)
		{
			fbq('track', fb_events[i]);
		}

			
			//trackEvent(eventname); // define this function in main.js if necessary.
	}
	catch (ex)
	{
		//console.log(ex);
	}

}



/* GOOGLE AUTOCOMPLETE */

function jsGeolocate() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
}


  function initAutocomplete() {

		jsAutoComplete = new google.maps.places.Autocomplete( jsAutoCompleteField.get(0), {types: ['geocode']});
		jsAutoComplete.addListener('place_changed', fillInAddress);
  }

  function fillInAddress() {
		try
		{
			var place = jsAutoComplete.getPlace();
			
			jsAutoCompleteResult = { address: ["", ""], zip:"",  city: "", province: [], country: []}
			for (i=0; i< place.address_components.length ; i++ )
			{
					if (place.address_components[i].types[0] == "country") jsAutoCompleteResult.country = [place.address_components[i].short_name, place.address_components[i].long_name];
					if (place.address_components[i].types[0] == "administrative_area_level_1") jsAutoCompleteResult.province = [place.address_components[i].short_name, place.address_components[i].long_name];
					if (place.address_components[i].types[0] == "locality") jsAutoCompleteResult.city = place.address_components[i].long_name;
					if (place.address_components[i].types[0] == "postal_code") jsAutoCompleteResult.zip = place.address_components[i].long_name;
					if (place.address_components[i].types[0] == "street_number") jsAutoCompleteResult.address[0] = place.address_components[i].long_name;
					if (place.address_components[i].types[0] == "route") jsAutoCompleteResult.address[1] = place.address_components[i].long_name;
			}

			jsAutoCompleteProcess();	
		}
		catch (ex)
		{
			;
		}
		
  }

  function formTabs() {
		$("form.has-tabs").each(function() {
			var frmobj = $(this);
			$(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div").each(function(index){
				$(this).attr("data-id", index + 1);
				if (index == 0) $(this).addClass("active");
				$(this).click(function(){
					if ($(this).hasClass("completed")) {
						tgt = index + 1;
						frmobj.find(".form-tab").removeClass("active");
						$(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div").removeClass("active");
						frmobj.find(".form-tab[data-id=\""+tgt+"\"]").addClass("active");
						$(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div[data-id=\""+tgt+"\"]").addClass("active");
					}
				})
			})



			frmobj.find(".form-tab button.tab-back").click(function(){
				tgt = $(this).attr("data-target");
				frmobj.find(".form-tab").removeClass("active");
				$(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div").removeClass("active");
				frmobj.find(".form-tab[data-id=\""+tgt+"\"]").addClass("active");
				$(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div[data-id=\""+tgt+"\"]").addClass("active");
			})

			frmobj.find(".form-tab button.tab-forward").click(function(){
				tgt = $(this).attr("data-target");
				jslog($(".form-tab[data-id=\"" + (tgt-1) + "\"]"));
				if (validateTab(frmobj, frmobj.find(".form-tab[data-id=\"" + (tgt-1) + "\"]").first()))
				{
					console.log($(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div.active").length);
					$(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div.active").addClass("completed");
					frmobj.find(".form-tab").removeClass("active");
					$(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div").removeClass("active");
					frmobj.find(".form-tab[data-id=\""+tgt+"\"]").addClass("active");
					$(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"] > div[data-id=\""+tgt+"\"]").addClass("active");
					scrollToElement($(".form-tab-nav[data-form=\""+frmobj.attr("id")+"\"]").first());
				}
			})

	  });

  }


  function _addFormFieldValidations(_parent){
		_parent.find('.js-phone-number').each(function(){
			jsCleave.push(
									new Cleave($(this), {
										phone: true,
										phoneRegionCode: 'ca'
									})
								)
		})

		_parent.find('.js-integer').each(function(){
			jsCleave.push(
									new Cleave($(this), {
																		numeral: true,
																			blocks:[10],
																		delimiter: ""
																	}
														)
								)
		})
	
		_parent.find('.js-postal-code').each(function(){
			jsCleave.push(
									new Cleave($(this), {
																			blocks:[3,3],
																		delimiter: " "
																	}
														)
								)
		})
														
									
		_parent.find('.js-credit-card').each(function(){
			jsCleave.push(
									new Cleave($(this), {
																		creditCard: true,
																		onCreditCardTypeChanged: function (type) { }
																	}
														)
								)
		})

		_parent.find('.js-number').each(function(){
			jsCleave.push(
									new Cleave($(this), {
																		numeral: true,
																		numeralThousandsGroupStyle: 'thousand'
																	}
														)
								)
		})

		_parent.find(".js-email").each(function(){
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

  }

function removeFileFromFileList(domInput, index) {
	  const dt = new DataTransfer();
	  const input = domInput;
	  const { files } = input;
	  index = parseInt(index);
	  for (let i = 0; i < files.length; i++) {
		const file = files[i];
		if (index !== i) {
		  dt.items.add(file); // here you exclude the file. thus removing it.
		}
	  }

	  
	  /* Assigning data transfer object files to the 'input' variable will not write the data transfer files to it because it doesn't have the reference to the element: Instead write, */
	  domInput.files = dt.files; // Assign the updates list
}