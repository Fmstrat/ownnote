<?php

OCP\User::checkAdminUser();

$tmpl = new OCP\Template('ownnote', 'admin');
$tmpl->assign('folder', OCP\Config::getAppValue('ownnote', 'folder', 'Notes'));

return $tmpl -> fetchPage();

