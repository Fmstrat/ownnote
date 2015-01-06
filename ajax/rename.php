<?php

$FOLDER = "Notes";

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

if (\OC\Files\Filesystem::rename($FOLDER."/".$_POST['originalfilename'].".htm", $FOLDER."/".$_POST['editfilename'].".htm")) {
	echo "SUCCESS";
} else {
	echo "FAIL";
}

?>
