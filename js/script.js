
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
				startTimer();
			}
		});
	}

	function ocUrl(url) {
		var newurl = OC.generateUrl("/apps/ownnote/") + url;
		return newurl;
	}

	function resizeFont(s) {
		$('#editable_ifr').contents().find("head").append($("<style type='text/css'>  body{font-size:"+s+"px;}  </style>"));
	}

	var l10n = new Array();
	function translate() {
		var t = $('#ownnote-l10n').html();
		eval(t);
	}

	function trans(s) {
		if (l10n[s])
			return l10n[s];
		else
			return s;
	}

	function deleteNote(id) {
		var n = $(this).attr('n');
		var g = $(this).attr('g');
		$.post(ocUrl("ajax/v0.2/ownnote/ajaxdel"), { name: n, group: g }, function (data) {
			loadListing();
		});
	}

	function editNote(id) {
		var n = $(this).attr('n');
		var g = $(this).attr('g');
		$.post(ocUrl("ajax/v0.2/ownnote/ajaxedit"), { name: n, group: g }, function (data) {
			buildEdit(n, g, data);
		});
	}

	function addNote() {
		$('#newfile').css('display','inline-block');
		$('#new').css('display','none');
                $('#newfilename').focus();
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

	function buildEdit(n, g, data) {
		resizeContainer();
		var name = htmlQuotes(n);
		var group = htmlQuotes(g);
		var html = "";
		html += "<div id='controls'>";
		html += "	<div id='newfile' class='indent'>";
		html += "		<form id='editform' class='note-title-form'>";
		html += "			"+trans("Name")+": <input type='text' class='fileinput' id='editfilename' value='"+name+"'>";
		html += "			&nbsp;&nbsp;"+trans("Group")+": <select id='groupname'></select>";
		html += "			<input type='text' class='newgroupinput' id='newgroupname' placeholder='group title'>";
		html += "			<input type='hidden' id='originalfilename' value='"+name+"'>";
		html += "			<input type='hidden' id='originalgroup' value='"+group+"'>";
		html += "			<div id='quicksave' class='button'>"+trans("Quick Save")+"</div>";
		html += "			<div id='save' class='button'>"+trans("Save")+"</div>";
		html += "			<div id='canceledit' class='button'>"+trans("Cancel")+"</div>";
		html += "		</form>";
		html += "	</div>";
		html += "</div>";
		html += "<div class='listingBlank'><!-- --></div>";
		html += "<div id='editable' class='editable'>";
		html += data;
		html += "</div>";
		document.getElementById("ownnote").innerHTML = html;
		tinymceInit();
		buildGroupSelectOptions(g);
		bindEdit();
	}

	var idle = false;
	var idleTime = 0;
	var idleInterval;
	var origNote;
	var checkDuration = 20;
	var saveTime = 60;
	function startTimer() {
		origNote = tinymce.activeEditor.getContent();
		idleIterval = setInterval(timerIncrement, checkDuration*1000);
		$(document).mousemove(function (e) { notIdle(); });
		$(document).keypress(function (e) { notIdle(); });
		$('#editable_ifr').contents().find("body").mousemove(function (e) { notIdle(); });
		tinymce.activeEditor.on('keyup', function(e) { notIdle(); });
	}

	function notIdle() {
		idle = false;
		idleTime = 0;
	}

	function timerIncrement() {
		idleTime = idleTime + checkDuration;
		if ($('#editable_ifr') && $('#editable_ifr').css('display') == 'block') {
			if (!idle && idleTime >= saveTime) {
				var content = tinymce.activeEditor.getContent();
				if (content != origNote) {
					origNote = content;
					saveNote(true);
				}
				idle = true;
			}
		} else {
			clearInterval(idleInterval);
		}
	}

	function bindEdit() {
		$("#editform").bind("submit", function() { saveNote(false); });
		$("#quicksave").bind("click", function() { saveNote(true); });
		$("#save").bind("click", function() { saveNote(false); });
		$("#canceledit").bind("click", buildListing);
		$("#groupname").bind("change", checkNewGroup);
		$("#editfilename").bind("change", disableQuickSave);
	}

	function disableQuickSave() {
		$('#quicksave').css('background-color','white');
		$('#quicksave').css('color','#888888');
		$("#quicksave").off("click");
	}

	function saveNote(stayinnote) {
		if (stayinnote) {
			$('#quicksave').css('background-color','green');
			$('#quicksave').css('color','white');
		}
		$('#editfilename').val($('#editfilename').val().replace(/\\/g, '-').replace(/\//g, '-'));
		var editfilename = $('#editfilename').val();
		var editgroup = $('#groupname').val();
		var originalfilename = $('#originalfilename').val();
		var originalgroup = $('#originalgroup').val();
		var content = tinymce.activeEditor.getContent();
		if (editgroup.toLowerCase() == "all" || editgroup.toLowerCase() == "not grouped") {
			editgroup = "";
		} else if (editgroup == '_new') {
			$('#newgroupname').val($('#newgroupname').val().replace(/\\/g, '-').replace(/\//g, '-'));
			editgroup = $('#newgroupname').val();
		}
		if (editfilename != originalfilename || editgroup != originalgroup) {
			var c = listing.length;
			var exists = false;
			for (i = 0; i < c; i++) {
				if (listing[i].deleted == 0)
					if (listing[i].group == editgroup && listing[i].name == editfilename) {
						exists = true;
						break;
					}
			}
			if (exists) {
				alert(trans("Filename/group already exists."));
			} else
				$.post(ocUrl("ajax/v0.2/ownnote/ajaxren"), { name: originalfilename, group: originalgroup, newname: editfilename, newgroup: editgroup }, function (data) {
					if (data == "DONE") {
						$.post(ocUrl("ajax/v0.2/ownnote/ajaxsave"), { name: editfilename, group: editgroup, content: content }, function (data) {
							if (!stayinnote)
								loadListing();
							else {
								$('#quicksave').css('background-color','rgba(240, 240, 240, 0.9)');
								$('#quicksave').css('color','#555');
							}
						});
					}
				});
		} else {
			$.post(ocUrl("ajax/v0.2/ownnote/ajaxsave"), { name: editfilename, group: editgroup, content: content }, function (data) {
				if (!stayinnote)
					loadListing();
				else {
					$('#quicksave').css('background-color','rgba(240, 240, 240, 0.9)');
					$('#quicksave').css('color','#555');
				}
			});
		}
		return false;
	}

	var listing;
	var listingtype = "All";
	var sortby = "name";
	var sortorder = "ascending";

	function htmlQuotes(value, reverse){
		if (!reverse) {
			var r = value;
			r = r.replace(/\'/g, '&#39;');
			r = r.replace(/\"/g, '&quot;');
			return r;
		} else {
			var r = value;
			r = r.replace(/&#39;/g, "'");
			r = r.replace(/&quot;/g, '"');
			return r;
		}
	}

	function loadListing() {
		var url = ocUrl("ajax/v0.2/ownnote/ajaxindex");
		$.get(url, function(data) {
			listing = data;
			buildNav(listingtype);
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
		html += "	<div id='new' class='button indent'>"+trans("New")+"</div>";
		html += "	<div id='newfile' class='newfile indent'>";
		html += "		<form id='createform' class='note-title-form'>";
		html += "			<input type='text' class='newfileinput' id='newfilename' value='note title'>";
		html += "			<select id='groupname'></select>";
		html += "			<input type='text' class='newgroupinput' id='newgroupname' placeholder='group title'>";
		html += "			<button id='create' class='button'>"+trans("Create")+"</button>";
		html += "			<div id='cancel' class='button'>"+trans("Cancel")+"</div>";
		html += "		</form>";
		html += "	</div>";
		html += "</div>";
		html += "<div class='listingBlank'><!-- --></div>";
		var c = listing.length;
		if (c == 0) {
			html += "<div class='listingSort'>";
			html += trans("You have no notes to display.");
			html += "</div>";
		} else {
			html += "<div class='listingSort'>";
			if (sortby == "name" && sortorder == "ascending") {
				html += "	<div class='filesort notesort'>";
				html += "		<div class='pointer sorttitle' id='sortname'>"+trans("Name")+"</div>";
				html += "		<div class='sortarrow sortup'><!-- --></div>";
				html += "	</div>";
				html += "	<div class='info'>";
				html += "		<div class='modified notesort'><span class='pointer' id='sortmod'>"+trans("Modified")+"</span></div>";
				html += "	</div>";
				listing.sort(sort_by('name', true, function(a){return a.toUpperCase()}));
			} else if (sortby == "name" && sortorder == "descending") {
				html += "	<div class='filesort notesort'>";
				html += "		<div class='pointer sorttitle' id='sortname'>"+trans("Name")+"</div>";
				html += "		<div class='sortarrow sortdown'><!-- --></div>";
				html += "	</div>";
				html += "	<div class='info'>";
				html += "		<div class='modified notesort'><span class='pointer' id='sortmod'>"+trans("Modified")+"</span></div>";
				html += "	</div>";
				listing.sort(sort_by('name', false, function(a){return a.toUpperCase()}));
			} else if (sortby == "mod" && sortorder == "ascending") {
				html += "	<div class='filesort notesort'>";
				html += "		<div class='pointer sorttitle' id='sortname'>"+trans("Name")+"</div>";
				html += "	</div>";
				html += "	<div class='info'>";
				html += "		<div class='modified notesort'>";
				html += "			<div class='pointer sorttitle' id='sortmod'>"+trans("Modified")+"</div>";
				html += "			<div class='sortarrow sortup'><!-- --></div>";
				html += "		</div>";
				html += "	</div>";
				listing.sort(sort_by('mtime', false, parseInt));
			} else if (sortby == "mod" && sortorder == "descending") {
				html += "	<div class='filesort notesort'>";
				html += "		<div class='pointer sorttitle' id='sortname'>"+trans("Name")+"</div>";
				html += "	</div>";
				html += "	<div class='info'>";
				html += "		<div class='modified notesort'>";
				html += "			<div class='pointer sorttitle' id='sortmod'>"+trans("Modified")+"</div>";
				html += "			<div class='sortarrow sortdown'><!-- --></div>";
				html += "		</div>";
				html += "	</div>";
				listing.sort(sort_by('mtime', true, parseInt));
			}
			html += "</div>";
			for (i = 0; i < c; i++) {
				if (listing[i].deleted == 0)
					if (listingtype == "All" || listing[i].group == listingtype || (listingtype == 'Not grouped' && listing[i].group == '')) {
						var fileclass = 'modified';
						var name = htmlQuotes(listing[i].name);
						var group = htmlQuotes(listing[i].group);
						var file = name;
						if (group != '')
							file = "["+group+"] "+name;
						if (listing[i].timediff < 30)
							fileclass = 'modified latestfile';
						html += "<div class='listing'>";
						html += "	<div id='"+file+"' i='"+listing[i].id+"' n='"+name+"' g='"+group+"' title='"+name+"' class='file pointer'>"+name+"</div>";
						html += "	<div class='info'>";
						if (listing[i].timestring != '')
							html += "		<div class='"+fileclass+"'>"+listing[i].timestring+"</div>";
						else
							html += "		<div class='"+fileclass+"'>"+trans("Just now")+"</div>";
						html += "		<div id='"+file+"' i='"+listing[i].id+"' n='"+name+"' g='"+group+"' class='buttons delete delete-note pointer'><br></div>";
						html += "	</div>";
						html += "</div>";
					}
			}
		}
		document.getElementById("ownnote").innerHTML = html;
		$('#newfilename').css('color', '#A0A0A0');
		buildGroupSelectOptions(listingtype);
		bindListing();
	}

	function buildGroupSelectOptions(current) {
		var $select = $('select#groupname');
		$select.append($('<option value="">Not grouped</option>'));
		$select.append($('<option>').attr('value', '_new').text('New group'));
		$(groups).each(function(i, group) {
			var option = $('<option>').attr('value', group).text(group);
			if(group == current) {
				option.attr('selected', 'selected');
			}
			$select.append(option);
		});
	}

	function bindListing() {
		$(".file").bind("click", editNote);
		$(".delete-note").bind("click", deleteNote);
		$("#sortname").bind("click", sortName);
		$("#sortmod").bind("click", sortMod);
		$("#new").bind("click", addNote);
		$("#cancel").bind("click", cancelNote);
		$("#createform").bind("submit", createNote);
		$("#groupname").bind("change", checkNewGroup);
		$("#newfilename").bind("focus", newNote);
	}

	function checkNewGroup() {
		var selectVal = $('select#groupname').val();
		if(selectVal == '_new') {
			$('#newgroupname').css('display','inline-block');
                        $('#newgroupname').focus();
		} else {
			$('#newgroupname').css('display','none');
		}
		disableQuickSave();
	}

	function newNote() {
		$('#newfilename').css('color', '#000');
		var v = $('#newfilename').val();
		if (v == 'note title')
			$('#newfilename').val('');
	}

	function createNote() {
		var name = $('#newfilename').val();
		var group = $('#groupname').val();
		if (group == '_new') {
			group = $('#newgroupname').val();
		}
		cancelNote();
		$.post(ocUrl("ajax/v0.2/ownnote/ajaxcreate"), { name: name, group: group }, function (data) {
			loadListing();
		});
		return false;
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
		var n = htmlQuotes(name);
		if (active) a = " active";
		if (name == "All" || name == "Not grouped") {
			html += '<li class="group' + a + '" data-type="all">';
			html += '	<a class="name" id="link-'+n+'" role="button" title="'+n+'">'+htmlQuotes(trans(name))+'</a>';
		} else {
			html += '<li id="group-'+n+'-edit" class="group editing">';
			html += '	<ul class="oc-addnew open" style="display: inline-block; width: auto; height: auto;" aria-disabled="false">';
			html += '		<li>';
			html += '			<input id="edit-'+n+'-text" class="oc-addnew-name" type="text" value="'+n+'" style="display: inline;">';
			html += '			<button id="edit-'+n+'" class="new-button primary icon-checkmark-white" style="display: block;"></button>';
			html += '		</li>';
			html += '	</ul>';
			html += '</li>';
			html += '<li id="group-'+n+'" class="group' + a + '" data-type="category">';
			html += '	<a class="name" id="link-'+n+'" role="button" title="'+n+'">'+n+'</a>';
		}
		html += '	<span class="utils">';
		html += '		<a class="icon-rename action edit tooltipped rightwards" group="'+n+'" original-title=""></a>';
		html += '		<a class="icon-delete action delete tooltipped rightwards" group="'+n+'" original-title=""></a>';
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
		html += "<div id='announcement-container'></div>";
		$('#grouplist').html(html);
		bindNav();
		if (disableAnnouncement == "")
			loadAnnouncement();
	}

	function loadAnnouncement() {
		var curAnnouncement = getCookie('curAnnouncement');
		var dismissedAnnouncement = getCookie('dismissedAnnouncement');
		if (curAnnouncement != "") {
			if (curAnnouncement != dismissedAnnouncement) {
				var html = "<div id='app-settings'><div id='app-settings-header'><div id='announcement'>"+curAnnouncement+"</div><div id='announcement-dismiss'><a id='dismissButton' href='javascript:dismissAnnouncement()'>"+trans("Dismiss")+"</a></div></div><div>";
				$('#announcement-container').html(html);
				$('#dismissButton').click(dismissAnnouncement);
			}
		} else {
			var url = ocUrl("ajax/v0.2/ownnote/ajaxannouncement");
			$.ajax({
				url: url,
				success: function(data) {
					if (data != '') {
						if (data.replace(/\n/g,'') != 'NONE') {
							if (data.replace(/\n/g,'') != dismissedAnnouncement) {
								var html = "<div id='app-settings'><div id='app-settings-header'><div id='announcement'>"+data+"</div><div id='announcement-dismiss'><a id='dismissButton' href='javascript:dismissAnnouncement()'>"+trans("Dismiss")+"</a></div></div><div>";
								$('#announcement-container').html(html);
								$('#dismissButton').click(dismissAnnouncement);
							}
							setCookie("curAnnouncement", data.replace(/\n/g,''), 7);
						} else {
							setCookie("curAnnouncement", data.replace(/\n/g,''), 7);
							setCookie("dismissedAnnouncement", data.replace(/\n/g,''), 7);
						}
					}
				},
				cache: false
			});
		}
	}

	function setCookie(cname, cvalue, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1);
			if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
		}
		return "";
	} 

	function dismissAnnouncement() {
		setCookie("dismissedAnnouncement", $('#announcement').html().replace(/\n/g,''), 30);
		$('#announcement-container').html('');
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
		var c = listing.length;
		var exists = false;
		for (i = 0; i < c; i++)
			if (listing[i].deleted == 0)
				if (listing[i].group.toLowerCase() == g.toLowerCase())
					for (j = 0; j < c; j++)
						if (listing[j].deleted == 0)
							if (listing[j].group == '' && listing[i].name.toLowerCase() == listing[j].name.toLowerCase()) {
								exists = true;
								break;
							}
		if (exists)
			alert(trans("An ungrouped file has the same name as a file in this group."));
		else
			$.post(ocUrl("ajax/v0.2/ownnote/ajaxdelgroup"), { group: g }, function (data) {
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
		var c = listing.length;
		if (v != cg && v.toLowerCase() != "all" && v.toLowerCase() != "not grouped") {
			var exists = false;
			for (i = 0; i < c; i++)
				if (listing[i].deleted == 0)
					if (listing[i].group.toLowerCase() == v.toLowerCase()) {
						exists = true;
						break;
					}
			if (exists)
				alert(trans("Group already exists."));
			else
				$.post(ocUrl("ajax/v0.2/ownnote/ajaxrengroup"), { group: cg, newgroup: v }, function (data) {
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

	var disableAnnouncement = "";
	function getSettings() {
		disableAnnouncement = $('#disableAnnouncement').val();
	}

	$(document).ready(function() {
		$.ajaxSetup ({ cache: false });
		translate();
		getSettings();
		loadListing();
	});
	

