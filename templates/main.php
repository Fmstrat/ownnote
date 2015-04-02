<?php
\OCP\Util::addScript('ownnote', 'script');
\OCP\Util::addScript('ownnote','tinymce/tinymce.min');
\OCP\Util::addStyle('ownnote', 'style');

$disableAnnouncement = \OCP\Config::getAppValue('ownnote', 'disableAnnouncement', '');
?>
<div id="app">
	<div id="app-navigation">
		<ul id="grouplist">
		</ul>
	</div>
	<div id="app-content">
		<div id="ownnote"></div>
	</div>
	<input type=hidden value="<?php echo $disableAnnouncement; ?>">
</div>
