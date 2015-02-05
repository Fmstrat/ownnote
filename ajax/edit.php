<?php

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

require_once 'ownnote/lib/backend.php';

echo editNote("Notes", $_GET['id']);

?>
