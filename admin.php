<?php

OCP\User::checkAdminUser();

use \OCA\OwnNote\Lib\Backend;

$db_or_folder = OCP\Config::getAppValue('ownnote', 'db_or_folder', '');
$folder = OCP\Config::getAppValue('ownnote', 'folder', '');
if ($db_or_folder == '') {
	// migrate to the version of ownnote that supports folder only 
	if ($folder == '') {
		$db_or_folder = Backend::DB_ONLY;
	} else {
		$db_or_folder = Backend::DB_OR_FOLDER;
	}
	\OCP\Config::setAppValue('ownnote', 'db_or_folder', $db_or_folder);
}

$tmpl = new OCP\Template('ownnote', 'admin');
$tmpl->assign('db_or_folder', $db_or_folder);
$tmpl->assign('folder', OCP\Config::getAppValue('ownnote', 'folder', $folder));
$tmpl->assign('disableAnnouncement', OCP\Config::getAppValue('ownnote', 'disableAnnouncement', ''));

return $tmpl -> fetchPage();

