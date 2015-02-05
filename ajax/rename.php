<?php

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

require_once 'ownnote/lib/backend.php';

echo renameNote("Notes", $_POST['originalfilename'], $_POST['editfilename']);

?>
