<?php

$FOLDER = "Notes";

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

if ($listing = \OC\Files\Filesystem::opendir($FOLDER)) {
        if (!listing) {
                echo "ERROR: Error listing directory.";
                exit;
        }
        while (($file = readdir($listing)) !== false) {
		if (substr($file,0,1) == "[") {
			$group = "";
			$end = strpos($file, ']');
			$group = substr($file, 1, $end-1);
			if ($group == $_POST['group']) {
				$filename = substr($file, $end+1, strlen($file)-$end+1);
				$filename = trim($filename);
				if (\OC\Files\Filesystem::rename($FOLDER."/".$file, $FOLDER."/".$filename)) {
					echo "SUCCESS";
				} else {
					echo "FAIL";
				}
			}
		}

	}
}

?>
