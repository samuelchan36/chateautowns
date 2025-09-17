$(document).ready(function () { 
		
		$( "#slides-body" ).sortable({stop: function() {
			var neworder=[];
			$( "#slides-body div.slide-row").each(function(index) {
				neworder.push($(this).attr("data-id"));

			})
			console.log("/cms/"+dropzoneManager+"/order-slide?id=" + $("#dropzone").attr("data-id") + "&data=" + neworder.join(","));
			$.get("/cms/"+dropzoneManager+"/order-slide?id=" + $("#dropzone").attr("data-id") + "&data=" + neworder.join(","), function(data) {
				console.log(data);
				$(".slide-row").each(function(index){
					$(this).attr("data-id", index);
					$(this).find("[data-id]").attr("data-id", index);
					$(this).find(".data-slide-id").val(index);
				})
			})
		}});
		$( "#slides-body" ).disableSelection();

		bindEvents();
		Dropzone.autoDiscover = false;
		console.log("/cms/"+dropzoneManager+"/upload-slide?id=" + $("#dropzone").attr("data-id"));
		var myDropzone = new Dropzone("#dropzone", { maxFiles: 20, maxFilesize: 64, url: "/cms/"+dropzoneManager+"/upload-slide?id=" + $("#dropzone").attr("data-id")});
		myDropzone.on("success", function(file, jsondata) {
			console.log(jsondata);
			data = JSON.parse(jsondata);
			if (data.ret == "ok")
			{
				
				$("#slides-body").append(data.html);
				bindEvents();
			
			} else {
			}
		});
	

});

function bindEvents() {
		$("#slides-body a.delete").unbind("click");
		$("#slides-body a.delete").click(function() {
			console.log("/cms/"+dropzoneManager+"/delete-slide?id=" + $("#dropzone").attr("data-id") + "&fileid=" + $(this).attr("data-id"));
			$.get("/cms/"+dropzoneManager+"/delete-slide?id=" + $("#dropzone").attr("data-id") + "&fileid=" + $(this).attr("data-id"), function(data) {
				console.log(data);
			});
			$(this).parent().parent().addClass("deleted");	
			return false;
		})

		$("#slides-body .file-data").unbind("change");
		$("#slides-body .file-data").change(function() {
			console.log("/cms/"+dropzoneManager+"/update-slide?id=" + $("#dropzone").attr("data-id") + "&fileid=" + $(this).attr("data-id") + "&field=" + $(this).attr("data-name") + "&value=" + encode($(this).val()));
			$.get("/cms/"+dropzoneManager+"/update-slide?id=" + $("#dropzone").attr("data-id") + "&fileid=" + $(this).attr("data-id") + "&field=" + $(this).attr("data-name") + "&value=" + encode($(this).val()), function(data) {
				console.log(data);
			});

			return false;
		})


}