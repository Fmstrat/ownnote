function ocOwnnoteUrl(url) {
	var newurl = OC.linkTo("ownnote",url).replace("apps/ownnote","index.php/apps/ownnote");
	return newurl;
}

$(document).ready(function() {
	$('#ownnote-folder').change(function() {
		var val = $(this).val();
	        $.post(ocOwnnoteUrl("setval"), { field: 'folder', value: val }, function (data) {
			 console.log('response', data);
        	});
	});
	$('#ownnote-type').change(function() {
		var val = $(this).val();
		if (val == "") {
			$('#ownnote-folder').val('');
			$('#shorten-folder-settings').css('display', 'none');
			$.post(ocOwnnoteUrl("setval"), { field: 'folder', value: '' }, function (data) {
				console.log('response', data);
			});
		} else
			$('#shorten-folder-settings').css('display', 'block');
	});
	$('#ownnote-disableannouncement').change(function() {
		var da = "";
		var c = $(this).is(':checked');
		if (c)
			da = "checked";
	        $.post(ocOwnnoteUrl("setval"), { field: 'disableAnnouncement', val: da }, function (data) {
			 console.log('response', data);
        	});
	});
});

