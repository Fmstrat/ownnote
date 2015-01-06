<?php
\OCP\Util::addScript('ownnote', 'script');
\OCP\Util::addScript('ownnote','tinymce/tinymce.min');
\OCP\Util::addStyle('ownnote', 'style');

$FOLDER = "Notes";

?>

<div id="app">
	<div id="app-navigation">
		<ul id="grouplist">
		</ul>
	</div>
	<div id="app-content">
		<div id="ownnote"></div>
	</div>
</div>
