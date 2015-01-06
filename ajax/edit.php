<?php

$FOLDER = "Notes";

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

if ($html = \OC\Files\Filesystem::file_get_contents($FOLDER."/".$_GET['id'])) {
	echo $html;
}

?>
