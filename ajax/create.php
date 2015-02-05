<?php

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

require_once 'ownnote/lib/backend.php';

if (isset($_GET['id']) && $_GET['id'] != '' && $_GET['id'] != 'note title') {
	echo createNote("Notes", $_GET['id']);
}

?>
