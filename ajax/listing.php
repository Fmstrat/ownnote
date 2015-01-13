<?php

$FOLDER = "Notes";

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');

// Create directory if it doesn't exist
if (!\OC\Files\Filesystem::is_dir($FOLDER)) {
	if (!\OC\Files\Filesystem::mkdir($FOLDER))  {
		echo "ERROR: Could not create ownNote directory.";
		exit;
	}
}

function endswith($string, $test) {
	$strlen = strlen($string);
	$testlen = strlen($test);
	if ($testlen > $strlen) return false;
	return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}


// Loop through and list files
if ($listing = \OC\Files\Filesystem::opendir($FOLDER)) {
	if (!$listing) {
		echo "ERROR: Error listing directory.";
		exit;
	}
	$now = new DateTime();
	$filetime = new DateTime();
	$delete = OCP\Util::imagePath('ownnote','delete.png');
	$farray = array();
	$count = 0;
	while (($file = readdir($listing)) !== false) {
		$tmpfile = $file;
		if ($tmpfile == "." || $tmpfile == "..") continue;
		if (!endswith($tmpfile, ".htm") && !endswith($tmpfile, ".html")) continue;
		if ($info = \OC\Files\Filesystem::getFileInfo($FOLDER."/".$tmpfile)) {
			$filetime->setTimestamp($info['mtime']);
			$difftime = $filetime->diff($now);
			$years = $difftime->y;
			$months = $difftime->m;
			$days = $difftime->d;
			$hours = $difftime->h;
			$minutes = $difftime->i;
			$seconds = $difftime->s;
			$timestring = "";
			if ($timestring == "" && $years == 1) $timestring = "$years year";
			if ($timestring == "" && $years > 0) $timestring = "$years years";
			if ($timestring == "" && $months == 1) $timestring = "$months month";
			if ($timestring == "" && $months > 0) $timestring = "$months months";
			if ($timestring == "" && $days == 1) $timestring = "$days day";
			if ($timestring == "" && $days > 0) $timestring = "$days days";
			if ($timestring == "" && $hours == 1) $timestring = "$hours hour";
			if ($timestring == "" && $hours > 0) $timestring = "$hours hours";
			if ($timestring == "" && $minutes == 1) $timestring = "$minutes minute";
			if ($timestring == "" && $minutes > 0) $timestring = "$minutes minutes";
			if ($timestring == "" && $seconds == 1) $timestring = "$seconds second";
			if ($timestring == "" && $seconds > 0) $timestring = "$seconds seconds";
			if (endswith($tmpfile, ".html")) {
				$tmpfile = substr($tmpfile,0,-1);
				if (!\OC\Files\Filesystem::rename($FOLDER."/".$file, $FOLDER."/".$tmpfile)) continue;
			}
			$filename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $tmpfile);
			$group = "";
			if (substr($filename,0,1) == "[") {
				$end = strpos($filename, ']');
				$group = substr($filename, 1, $end-1);	
				$filename = substr($filename, $end+1, strlen($filename)-$end+1);
				$filename = trim($filename);
			}
			$f = array();
			$f['file'] = $tmpfile;
			$f['filename'] = $filename;
			$f['group'] = $group;
			$f['timestring'] = $timestring;
			$f['mtime'] = $info['mtime'];
			$f['timediff'] = $now->getTimestamp()-$info['mtime'];
			$farray[$count] = $f;
			$count++;
		} else {
			echo "ERROR: Error retrieving file information.";
			exit;
		}
	}
	echo json_encode($farray);
}

?>
