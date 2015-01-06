<?php

$FOLDER = "Notes";

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

if (isset($_GET['id']) && $_GET['id'] != '' && $_GET['id'] != 'note title') {
	$TARGET=$FOLDER."/".$_GET['id'].".htm";

	if (!\OC\Files\Filesystem::file_exists($TARGET)) {
		\OC\Files\Filesystem::touch($TARGET);
	}
}

?>
