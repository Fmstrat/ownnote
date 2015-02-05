<?php

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');


function startsWith($haystack, $needle) {
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endswith($string, $test) {
	$strlen = strlen($string);
	$testlen = strlen($test);
	if ($testlen > $strlen) return false;
	return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

function checkEvernote($folder, $file) {
	$html = "";
	if ($html = \OC\Files\Filesystem::file_get_contents($folder."/".$file)) {
		$DOM = new DOMDocument;
		$DOM->loadHTML($html);
		$items = $DOM->getElementsByTagName('meta');
		$isEvernote = false;
		for ($i = 0; $i < $items->length; $i++) {
			$item = $items->item($i);
			if ($item->hasAttributes()) {
				$attrs = $item->attributes;
				foreach ($attrs as $a => $attr) {
					if ($attr->name == "name") {
						if ($attr->value == "exporter-version") {
							$isEvernote = true;
							continue;
						}
					}
				}
			}
		}
		if ($isEvernote) {
			$items = $DOM->getElementsByTagName('img');
			$isEvernote = false;
			for ($i = 0; $i < $items->length; $i++) {
				$item = $items->item($i);
				if ($item->hasAttributes()) {
					$attrs = $item->attributes;
					foreach ($attrs as $a => $attr) {
						if ($attr->name == "src") {
							$url = $attr->value;
							if (!startsWith($url, "http") && !startsWith($url, "/") && !startsWith($url,"data")) {
								if ($data = \OC\Files\Filesystem::file_get_contents($folder."/".$url)) {
									$type = pathinfo($url, PATHINFO_EXTENSION);
									$base64 = "data:image/".$type.";base64,".base64_encode($data);
									$html = str_replace($url, $base64, $html);
								}
							}
						}
					}
				}
			}
			\OC\Files\Filesystem::file_put_contents($folder."/".$file, $html);
		}
	}
}

function getListing($FOLDER) {
	// Create directory if it doesn't exist
	if (!\OC\Files\Filesystem::is_dir($FOLDER)) {
		if (!\OC\Files\Filesystem::mkdir($FOLDER))  {
			echo "ERROR: Could not create ownNote directory.";
			exit;
		}
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
					checkEvernote($FOLDER, $tmpfile);
					$tmpfile = substr($tmpfile,0,-1);
					if (!\OC\Files\Filesystem::file_exists($FOLDER."/".$tmpfile)) {
						if (!\OC\Files\Filesystem::rename($FOLDER."/".$file, $FOLDER."/".$tmpfile)) continue;
					} else {
						continue;
					}
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
		return $farray;
	}
}

function createNote($FOLDER, $id) {
	$TARGET=$FOLDER."/".$id.".htm";
	if (!\OC\Files\Filesystem::file_exists($TARGET)) {
		\OC\Files\Filesystem::touch($TARGET);
	}
	return "DONE";
}


function deleteNote($FOLDER, $id) {
	$TARGET=$FOLDER."/".$id;
	if (\OC\Files\Filesystem::file_exists($TARGET)) {
		\OC\Files\Filesystem::unlink($TARGET);
	}
	return "DONE";
}


function editNote($FOLDER, $id) {
	$ret = "";
	if ($html = \OC\Files\Filesystem::file_get_contents($FOLDER."/".$id)) {
		$ret = $html;
	}
	return $ret;
}

function saveNote($FOLDER, $id, $content) {
	\OC\Files\Filesystem::file_put_contents($FOLDER."/".$id.".htm", $content);
	return "DONE";
}

function renameNote($FOLDER, $id, $newid) {
	$ret = "";
	if (!\OC\Files\Filesystem::file_exists($FOLDER."/".$newid.".htm")) {
		if (\OC\Files\Filesystem::rename($FOLDER."/".$id.".htm", $FOLDER."/".$newid.".htm")) {
			$ret = "SUCCESS";
		} else {
			$ret = "FAIL";
		}
	} else {
		$ret = "EXISTS";
	}
	return $ret;
}

function deleteGroup($FOLDER, $delgroup) {
	$ret = "";
	if ($listing = \OC\Files\Filesystem::opendir($FOLDER)) {
		if (!$listing) {
			echo "ERROR: Error listing directory.";
			exit;
		}
		while (($file = readdir($listing)) !== false) {
			if (substr($file,0,1) == "[") {
				$group = "";
				$end = strpos($file, ']');
				$group = substr($file, 1, $end-1);
				if ($group == $delgroup) {
					$filename = substr($file, $end+1, strlen($file)-$end+1);
					$filename = trim($filename);
					if (\OC\Files\Filesystem::rename($FOLDER."/".$file, $FOLDER."/".$filename)) {
						$ret .= "SUCCESS";
					} else {
						$ret .= "FAIL";
					}
				}
			}

		}
	}
	return $ret;
}


function renameGroup($FOLDER, $originalgroupname, $editgroupname) {
	$ret = "";
	if ($listing = \OC\Files\Filesystem::opendir($FOLDER)) {
		if (!$listing) {
			$ret .= "ERROR: Error listing directory.";
		} else {
			while (($file = readdir($listing)) !== false) {
				if (substr($file,0,1) == "[") {
					$group = "";
					$end = strpos($file, ']');
					$group = substr($file, 1, $end-1);
					if ($group == $originalgroupname) {
						$filename = substr($file, $end+1, strlen($file)-$end+1);
						$filename = trim($filename);
						$filename = "[".$editgroupname."] ".$filename;
						if (!\OC\Files\Filesystem::file_exists($FOLDER."/".$filename)) {
							if (\OC\Files\Filesystem::rename($FOLDER."/".$file, $FOLDER."/".$filename)) {
								$ret .= "SUCCESS";
							} else {
								$ret .= "FAIL";
							}
						} else {
							$ret .= "FAIL";
						}
					}
				}
			}
		}
	}
	return $ret;
}

?>
