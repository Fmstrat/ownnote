<?php

$FOLDER = "Notes";

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

if (isset($_GET['id']) && $_GET['id'] != '') {
	$TARGET=$FOLDER."/".$_GET['id'];

	if (\OC\Files\Filesystem::file_exists($TARGET)) {
		\OC\Files\Filesystem::unlink($TARGET);
	}
}

?>
