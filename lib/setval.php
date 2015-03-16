<?php


OCP\User::checkAdminUser();
\OCP\App::checkAppEnabled('ownnote');

function setAdminVal() {
	if (isset($_POST['folder'])) {
		OCP\Config::setAppValue('ownnote', 'folder', $_POST['folder']);
	}
	if (isset($_POST['disableAnnouncement'])) {
		OCP\Config::setAppValue('ownnote', 'disableAnnouncement', $_POST['disableAnnouncement']);
	}
}
?>
