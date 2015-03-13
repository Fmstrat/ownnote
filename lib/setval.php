<?php


OCP\User::checkAdminUser();
\OCP\App::checkAppEnabled('ownnote');

function setAdminVal() {
	if (isset($_POST['folder'])) {
		OCP\Config::setAppValue('ownnote', 'folder', $_POST['folder']);
	}
}
?>
