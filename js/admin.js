function ocOwnnoteUrl(url) {
	var newurl = OC.linkTo("ownnote",url).replace("apps/ownnote","index.php/apps/ownnote");
	return newurl;
}

$(document).ready(function() {
	$('#ownnote-folder').change(function() {
		var val = $(this).val();
		$.post(ocOwnnoteUrl("ajax/v0.2/ajaxsetval"), { field: 'folder', value: val }, function (data) {
			console.log('response', data);
		});
	});
	$('#ownnote-type').change(function() {
		var val = $(this).val();
		$.post(ocOwnnoteUrl("ajax/v0.2/ajaxsetval"), { field: 'db_or_folder', value: val }, function (data) {
		 console.log('response', data);
		});
		if (val == "db_only") {
			$('#ownnote-folder-settings').css('display', 'none');
		} else
			$('#ownnote-folder-settings').css('display', 'block');
	});
	$('#ownnote-disableannouncement').change(function() {
		var da = "";
		var c = $(this).is(':checked');
		if (c)
			da = "checked";
		$.post(ocOwnnoteUrl("ajax/v0.2/ajaxsetval"), { field: 'disableAnnouncement', val: da }, function (data) {
			console.log('response', data);
		});
	});
});

