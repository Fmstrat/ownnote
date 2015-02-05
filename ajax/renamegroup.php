<?php

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

require_once 'ownnote/lib/backend.php';

echo renameGroup("Notes", $_POST['originalgroupname'], $_POST['editgroupname']);

?>
