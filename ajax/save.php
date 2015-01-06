<?php

$FOLDER = "Notes";

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

\OC\Files\Filesystem::file_put_contents($FOLDER."/".$_POST['editfilename'].".htm", $_POST['content']);

?>
