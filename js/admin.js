function ocOwnnoteUrl(url) {
	var newurl = OC.linkTo("ownnote",url).replace("apps/ownnote","index.php/apps/ownnote");
	return newurl;
}

$(document).ready(function() {
	$('#ownnote-folder').change(function() {
		var val = $(this).val();
	        $.post(ocOwnnoteUrl("setval"), { folder: val }, function (data) {
			 console.log('response', data);
        	});
	});
	$('#ownnote-type').change(function() {
		var val = $(this).val();
		if (val == "") {
			$('#shorten-folder-settings').css('display', 'none');
			$.post(ocOwnnoteUrl("setval"), { folder: '' }, function (data) {
				console.log('response', data);
			});
		} else
			$('#shorten-googl-settings').css('display', 'block');
	});
});

