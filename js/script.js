
	
	function tinymceInit() {
		tinymce.init({
			selector: "div.editable",
			menubar: false,
			plugins: [
				"advlist autolink lists link charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu bdesk_photo autoresize"
			],
			extended_valid_elements: "form[name|id|action|method|enctype|accept-charset|onsubmit|onreset|target],input[id|name|type|value|size|maxlength|checked|accept|src|width|height|disabled|readonly|tabindex|accesskey|onfocus|onblur|onchange|onselect|onclick|onkeyup|onkeydown|required|style],textarea[id|name|rows|cols|maxlength|disabled|readonly|tabindex|accesskey|onfocus|onblur|onchange|onselect|onclick|onkeyup|onkeydown|required|style],option[name|id|value|selected|style],select[id|name|type|value|size|maxlength|checked|width|height|disabled|readonly|tabindex|accesskey|onfocus|onblur|onchange|onselect|onclick|multiple|style]",
			toolbar: "insertfile undo redo | styleselect | bold italic strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link bdesk_photo",
			allow_html_data_urls: true,
			allow_script_urls: true,
			paste_data_images: true,
			width: '100%',
  			height: h-130,
			autoresize_min_height: h-130,
			autoresize_max_height: h-130,
			init_instance_callback : function(editor) {
				resizeFont("13");
			}
		});
	}

	function resizeFont(s) {
		$('#editable_ifr').contents().find("head").append($("<style type='text/css'>  body{font-size:"+s+"px;}  </style>"));
	}

	function deleteNote(id) {
		var url = OC.linkTo("ownnote","ajax/delete.php");
		url += "?id=" + $(this).attr('id');
		$.get(url, function(data) {
			loadListing();
		});
	}

	function editNote(id) {
		var t = this.title;
		var f = $(this).attr('id');
		var g = '';
		if (f[0] == '[') {
			g = f.match(/\[(.*?)\]/i)[1];
		}
		var url = OC.linkTo("ownnote","ajax/edit.php");
		url += "?id=" + f;
		$.get(url, function(data) {
			buildEdit(t, g, data);
		});
	}

	function addNote() {
		$('#newfile').css('display','inline-block');
		$('#new').css('display','none');
	}

	function cancelNote() {
		$('#newfile').css('display','none');
		$('#new').css('display','inline-block');
		$('#newfilename').css('color', '#A0A0A0');
		$('#newfilename').val('note title');
	}

	var h = 200;
	function resizeContainer() {
		var o = $('#ownnote').offset();
		h = $(window).height() - o.top;
	}

	function buildEdit(t, g, data) {
		resizeContainer();
		var html = "";
		html += "<div id='controls'>";
		html += "	<div id='newfile' class='indent'>";
		html += "		Name: <input type='text' class='fileinput' id='editfilename' value='"+t+"'>";
		html += "		&nbsp;&nbsp;Group: <input type='text' class='fileinput' id='editgroup' value='"+g+"'>";
		html += "		<input type='hidden' id='originalfilename' value='"+t+"'>";
		html += "		<input type='hidden' id='originalgroup' value='"+g+"'>";
		html += "		<div id='save' class='button'>Save</div>";
		html += "		<div id='canceledit' class='button'>Cancel</div>";
		html += "	</div>";
		html += "</div>";
		html += "<div class='listingBlank'><!-- --></div>";
		html += "<div id='editable' class='editable'>";
		html += data;
		html += "</div>";
		document.getElementById("ownnote").innerHTML = html;
		tinymceInit();
		bindEdit();
	}

	function bindEdit() {
		$("#save").bind("click", saveNote);
		$("#canceledit").bind("click", buildListing);
	}

	function saveNote() {
		var editfilename = $('#editfilename').val();
		var editgroup = $('#editgroup').val();
		var originalfilename = $('#originalfilename').val();
		var originalgroup = $('#originalgroup').val();
		var content = tinymce.activeEditor.getContent();
		if (editgroup.toLowerCase() == "all" || editgroup.toLowerCase() == "not grouped")
			editgroup = "";
		if (editfilename != originalfilename || editgroup != originalgroup) {
			if (editgroup != '')
				editfilename = '['+editgroup+'] '+editfilename;
			if (originalgroup != '')
				originalfilename = '['+originalgroup+'] '+originalfilename;
			$.post(OC.linkTo("ownnote","ajax/rename.php"), { originalfilename: originalfilename, editfilename: editfilename }, function (data) {
				if (data == "SUCCESS") {
					$.post(OC.linkTo("ownnote","ajax/save.php"), { content: content, editfilename: editfilename }, function (data) {
						loadListing();
					});
				}
			});
		} else {
			if (editgroup != '')
				editfilename = '['+editgroup+'] '+editfilename;
			$.post(OC.linkTo("ownnote","ajax/save.php"), { content: content, editfilename: editfilename }, function (data) {
				loadListing();
			});
		}
	}

	var filelist = "";
	var listing;
	var listingtype = "All";
	var sortby = "name";
	var sortorder = "ascending";

	function loadListing() {
		$.get(OC.linkTo("ownnote","ajax/listing.php"), function(data) {
			filelist = data;
			listing = jQuery.parseJSON(filelist);
			buildNav('All');
			listingtype = "All";
			buildListing();
			if (switchgroup != "") {
				$("[id='link-"+switchgroup+"']").click();
				switchgroup = "";
			}
		});
	}

	var sort_by = function(field, reverse, primer){
		var key = primer ? 
		function(x) {return primer(x[field])} : 
		function(x) {return x[field]};
		reverse = [-1, 1][+!!reverse];
		return function (a, b) {
			return a = key(a), b = key(b), reverse * ((a > b) - (b > a));
		} 
	}

	function buildListing() {
		var html = "";
		html += "<div id='controls'>";
		html += "	<div id='new' class='button indent'>New</div>";
		html += "	<div id='newfile' class='newfile indent'>";
		html += "		<input type='text' class='newfileinput' id='newfilename' value='note title'>";
		html += "		<div id='create' class='button'>Create</div>";
		html += "		<div id='cancel' class='button'>Cancel</div>";
		html += "	</div>";
		html += "</div>";
		html += "<div class='listingBlank'><!-- --></div>";
		var c = listing.length;
		if (c == 0) {
			html += "<div class='listingSort'>";
			html += "You have no notes to display.";
			html += "</div>";
		} else {
			html += "<div class='listingSort'>";
			if (sortby == "name" && sortorder == "ascending") {
				html += "	<div class='filesort notesort'>";
				html += "		<div class='pointer sorttitle' id='sortname'>Name</div>";
				html += "		<div class='sortarrow sortup'><!-- --></div>";
				html += "	</div>";
				html += "	<div class='info'>";
				html += "		<div class='modified notesort'><span class='pointer' id='sortmod'>Modified</span></div>";
				html += "	</div>";
				listing.sort(sort_by('filename', true, function(a){return a.toUpperCase()}));
			} else if (sortby == "name" && sortorder == "descending") {
				html += "	<div class='filesort notesort'>";
				html += "		<div class='pointer sorttitle' id='sortname'>Name</div>";
				html += "		<div class='sortarrow sortdown'><!-- --></div>";
				html += "	</div>";
				html += "	<div class='info'>";
				html += "		<div class='modified notesort'><span class='pointer' id='sortmod'>Modified</span></div>";
				html += "	</div>";
				listing.sort(sort_by('filename', false, function(a){return a.toUpperCase()}));
			} else if (sortby == "mod" && sortorder == "ascending") {
				html += "	<div class='filesort notesort'>";
				html += "		<div class='pointer sorttitle' id='sortname'>Name</div>";
				html += "	</div>";
				html += "	<div class='info'>";
				html += "		<div class='modified notesort'>";
				html += "			<div class='pointer sorttitle' id='sortmod'>Modified</div>";
				html += "			<div class='sortarrow sortup'><!-- --></div>";
				html += "		</div>";
				html += "	</div>";
				listing.sort(sort_by('mtime', false, parseInt));
			} else if (sortby == "mod" && sortorder == "descending") {
				html += "	<div class='filesort notesort'>";
				html += "		<div class='pointer sorttitle' id='sortname'>Name</div>";
				html += "	</div>";
				html += "	<div class='info'>";
				html += "		<div class='modified notesort'>";
				html += "			<div class='pointer sorttitle' id='sortmod'>Modified</div>";
				html += "			<div class='sortarrow sortdown'><!-- --></div>";
				html += "		</div>";
				html += "	</div>";
				listing.sort(sort_by('mtime', true, parseInt));
			}
			html += "</div>";
			for (i = 0; i < c; i++) {
				if (listingtype == "All" || listing[i].group == listingtype || (listingtype == 'Not grouped' && listing[i].group == '')) {
					var fileclass = 'modified';
					if (listing[i].timediff < 30)
						fileclass = 'modified latestfile';
					html += "<div class='listing'>";
					html += "	<div id='"+listing[i].file+"' title='"+listing[i].filename+"' class='file pointer'>"+listing[i].filename+"</div>";
					html += "	<div class='info'>";
					if (listing[i].timestring != '')
						html += "		<div class='"+fileclass+"'>"+listing[i].timestring+" ago</div>";
					else
						html += "		<div class='"+fileclass+"'>Just now</div>";
					html += "		<div id='"+listing[i].file+"' class='buttons delete delete-note pointer'><br></div>";
					html += "	</div>";
					html += "</div>";
				}
			}
		}
		document.getElementById("ownnote").innerHTML = html;
		$('#newfilename').css('color', '#A0A0A0');
		bindListing();
	}

	function bindListing() {
		$(".file").bind("click", editNote);
		$(".delete-note").bind("click", deleteNote);
		$("#sortname").bind("click", sortName);
		$("#sortmod").bind("click", sortMod);
		$("#new").bind("click", addNote);
		$("#cancel").bind("click", cancelNote);
		$("#create").bind("click", createNote);
		$("#newfilename").bind("focus", newNote);
	}

	function newNote() {
		$('#newfilename').css('color', '#000');
		var v = $('#newfilename').val();
		if (v == 'note title')
			$('#newfilename').val('');
	}

	function createNote() {
		var url = OC.linkTo("ownnote","ajax/create.php");
		url += "?id=" + $('#newfilename').val();
		cancelNote();
		$.get(url, function(data) {
			loadListing();
		});
	}

	function sortName() {
		if (sortby == "name")
			if (sortorder == "ascending")
				sortorder = "descending";
			else 
				sortorder = "ascending";
		else {
			sortby = "name";
			sortorder = "ascending";
		}
		buildListing();
	}

	function sortMod() {
		if (sortby == "mod")
			if (sortorder == "ascending")
				sortorder = "descending";
			else 
				sortorder = "ascending";
		else {
			sortby = "mod";
			sortorder = "ascending";
		}
		buildListing();
	}

	function buildNavItem(name, count, active) {
		var html = '';
		var a = ''
		if (active) a = " active";
		if (name == "All" || name == "Not grouped")
			html += '<li class="group' + a + '" data-type="all">';
		else {
			html += '<li id="group-'+name+'-edit" class="group editing">';
			html += '	<ul class="oc-addnew open" style="display: inline-block; width: auto; height: auto;" aria-disabled="false">';
			html += '		<li>';
			html += '			<input id="edit-'+name+'-text" class="oc-addnew-name" type="text" value="'+name+'" style="display: inline;">';
			html += '			<button id="edit-'+name+'" class="new-button primary icon-checkmark-white" style="display: block;"></button>';
			html += '		</li>';
			html += '	</ul>';
			html += '</li>';
			html += '<li id="group-'+name+'" class="group' + a + '" data-type="category">';
		}
		html += '	<a class="name" id="link-'+name+'" role="button" title="'+name+'">'+name+'</a>';
		html += '	<span class="utils">';
		html += '		<a class="icon-rename action edit tooltipped rightwards" group="'+name+'" original-title=""></a>';
		html += '		<a class="icon-delete action delete tooltipped rightwards" group="'+name+'" original-title=""></a>';
		html += '		<span class="action numnotes">'+count+'</span>';
		html += '	</span>';
		html += '</li>';
		return html;
	}

	function sortNav() {
		var list = [];
		for (var j=0; j<groups.length; j++) 
			list.push({'group': groups[j], 'count': counts[j]});
		list.sort(function(a, b) {
			return ((a.group < b.group) ? -1 : ((a.group == b.group) ? 0 : 1));
		});
		for (var k=0; k<list.length; k++) {
			groups[k] = list[k].group;
			counts[k] = list[k].count;
		}
	}

	var groups = new Array();
	var counts = new Array();
	function buildNav(a) {
		groups.length = 0;
		counts.length = 0;
		var html = '';
		var c = listing.length;
		var uncat = 0
                for (i = 0; i < c; i++) {
			if (listing[i].group != '') {
				if ($.inArray(listing[i].group, groups) < 0) {
					groups.push(listing[i].group);
					counts.push(1);
				} else {
					counts[$.inArray(listing[i].group, groups)] += 1;
				}
			} else
				uncat++;
		}
		sortNav();
		var gc = groups.length;
		if (a == "All")
			html += buildNavItem('All', c, true);
		else
			html += buildNavItem('All', c, false);
		if (gc > 0) {
			if (a == "Not grouped")
				html += buildNavItem('Not grouped', uncat, true);
			else
				html += buildNavItem('Not grouped', uncat, false);
		}
                for (i = 0; i < gc; i++) {
			if (a == groups[i])
				html += buildNavItem(groups[i], counts[i], true);
			else
				html += buildNavItem(groups[i], counts[i], false);
		}
		$('#grouplist').html(html);
		bindNav();
	}

	function selectGroup() {
		buildNav(this.title);
		listingtype = this.title;
		buildListing();
		if ($("#app-navigation-toggle").css("display") == "block") {
			setTimeout(closeNav, 250);
		}
	}

	function closeNav() {
		if ($("#app-navigation-toggle").css("display") == "block") {
			$("#app-navigation-toggle").click();
		}
	}

	function bindNav() {
		$(".name").bind("click", selectGroup);
		$(".icon-delete").bind("click", deleteGroup);
		$(".icon-rename").bind("click", editGroup);
		$(".new-button").bind("click", saveGroup);
	}

	function deleteGroup() {
		var g = $(this).attr('group');
		$.post(OC.linkTo("ownnote","ajax/deletegroup.php"), { group: g }, function (data) {
			switchgroup = "All";
			loadListing();
		});
	}

	var cg = "";
	var switchgroup = "";

	function editGroup() {
		var g = $(this).attr('group');
		cg = g;
		var i = 'group-'+g;
		$("[id='"+i+"']").css("display", "none");
		$("[id='"+i+"-edit']").css("display", "inline-block");
	}

	function saveGroup() {
		var v = $("[id='"+this.id+"-text']").val();
		if (v != cg && v.toLowerCase() != "all" && v.toLowerCase() != "not grouped") {
			$.post(OC.linkTo("ownnote","ajax/renamegroup.php"), { originalgroupname: cg, editgroupname: v }, function (data) {
				switchgroup = v;
				cg = "";
				loadListing();
			});
		} else {
			switchgroup = v;
			cg = "";
			loadListing();
		}
	}

	$(document).ready(function() {
		loadListing();
	});
	

