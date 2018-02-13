<?php

OCP\User::checkAdminUser();

$tmpl = new OCP\Template('ownnote', 'admin');
$tmpl->assign('folder', \OC::$server->getConfig()->getAppValue('ownnote', 'folder', ''));

return $tmpl -> fetchPage();

