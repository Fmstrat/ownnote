function ocOwnnoteUrl() {
	var newurl = OC.linkTo("ownnote","index.php") + "/ajax/v0.2/ajaxsetval";
	return newurl;
}

$(document).ready(function() {
	$('#ownnote-folder').change(function() {
		var val = $(this).val();
	        $.post(ocOwnnoteUrl(), { field: 'folder', value: val }, function (data) {
			 console.log('response', data);
        	});
	});
	$('#ownnote-type').change(function() {
		var val = $(this).val();
		if (val == "") {
			$('#ownnote-folder').val('');
			$('#ownnote-folder-settings').css('display', 'none');
			$.post(ocOwnnoteUrl(), { field: 'folder', value: '' }, function (data) {
				console.log('response', data);
			});
		} else
			$('#ownnote-folder-settings').css('display', 'block');
	});
	$('#ownnote-disableannouncement').change(function() {
		var da = "";
		var c = $(this).is(':checked');
		if (c)
			da = "checked";
	        $.post(ocOwnnoteUrl(), { field: 'disableAnnouncement', value: da }, function (data) {
			 console.log('response', data);
        	});
	});
});

