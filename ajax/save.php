<?php

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

require_once 'ownnote/lib/backend.php';

echo saveNote("Notes", $_POST['editfilename'], $_POST['content']);

?>
