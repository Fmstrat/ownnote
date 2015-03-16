<?php
\OCP\Util::addScript('ownnote', 'script');
\OCP\Util::addScript('ownnote','tinymce/tinymce.min');
\OCP\Util::addStyle('ownnote', 'style');

$disableAnnouncement = \OCP\Config::getAppValue('ownnote', 'disableAnnouncement', '');

?>

<div id="app">
	<script>
		var disableAnnouncement = "<?php echo $disableAnnouncement; ?>";
	</script>
	<div id="app-navigation">
		<ul id="grouplist">
		</ul>
	</div>
	<div id="app-content">
		<div id="ownnote"></div>
	</div>
</div>
