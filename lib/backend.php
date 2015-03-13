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

function getTimeString($filetime, $now) {
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
	return $timestring;
}

function getListing($FOLDER, $showdel) {
	// Get the listing from the database
	$uid = \OCP\User::getUser();
	$query = OCP\DB::prepare("SELECT id, name, grouping, mtime, deleted FROM *PREFIX*ownnote WHERE uid=? ORDER BY name");
	$results = $query->execute(Array($uid))->fetchAll();
	// Create directory if it doesn't exist
	$farray = array();
	if ($FOLDER != '') {
		$requery = false;
		// Create the folder if it doesn't exist
		if (!\OC\Files\Filesystem::is_dir($FOLDER)) {
			if (!\OC\Files\Filesystem::mkdir($FOLDER)) {
				echo "ERROR: Could not create ownNote directory.";
				exit;
			}
		}
		// Synchronize files to the database
		$filearr = array();
		if ($listing = \OC\Files\Filesystem::opendir($FOLDER)) {
			if (!$listing) {
				echo "ERROR: Error listing directory.";
				exit;
			}
			while (($file = readdir($listing)) !== false) {
				$tmpfile = $file;
				if ($tmpfile == "." || $tmpfile == "..") continue;
				if (!endswith($tmpfile, ".htm") && !endswith($tmpfile, ".html")) continue;
				if ($info = \OC\Files\Filesystem::getFileInfo($FOLDER."/".$tmpfile)) {
					// Check for EVERNOTE imports and rename them
					if (endswith($tmpfile, ".html")) {
						checkEvernote($FOLDER, $tmpfile);
						$tmpfile = substr($tmpfile,0,-1);
						if (!\OC\Files\Filesystem::file_exists($FOLDER."/".$tmpfile))
							\OC\Files\Filesystem::rename($FOLDER."/".$file, $FOLDER."/".$tmpfile);
					}
					// Separate the name and group name
					$name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $tmpfile);
					$group = "";
					if (substr($name,0,1) == "[") {
						$end = strpos($name, ']');
						$group = substr($name, 1, $end-1);	
						$name = substr($name, $end+1, strlen($name)-$end+1);
						$name = trim($name);
					}
					// Set array for later checking
					$filearr[] = $tmpfile;
					// Check to see if the file is in the DB
					$fileindb = false;
					if ($results)
						foreach($results as $result)
							if ($result['deleted'] == 0)
								if ($name == $result['name'] && $group == $result['grouping']) {
									$fileindb = true;
									// If it is in the DB, check if the filesystem file is newer than the DB
									if ($result['mtime'] < $info['mtime']) {
										// File is newer, this could happen if a user updates a file
										$query = OCP\DB::prepare('UPDATE *PREFIX*ownnote set mtime=?, note=? WHERE id=?');
										$html = "";
										$html = \OC\Files\Filesystem::file_get_contents($FOLDER."/".$tmpfile);
										$query->execute(Array($info['mtime'],$html,$result['id']));
										$requery = true;
									}
								}
					if (! $fileindb) {
						// If it's not in the DB, add it.
						$query = OCP\DB::prepare("INSERT INTO *PREFIX*ownnote (uid, name, grouping, mtime, note) VALUES (?,?,?,?,?)");
						$html = "";
						if ($html = \OC\Files\Filesystem::file_get_contents($FOLDER."/".$tmpfile)) {
						} else {
							$html = "";
						}
						$query->execute(Array($uid,$name,$group,$info['mtime'],$html));
						$id = OCP\DB::insertid('*PREFIX*ownnote');
						$requery = true;
					}
				}
			}
		}
		if ($requery) {
			$query = OCP\DB::prepare("SELECT id, name, grouping, mtime, deleted FROM *PREFIX*ownnote WHERE uid=? ORDER BY name");
			$results = $query->execute(Array($uid))->fetchAll();
		}
		// Now also make sure the files exist, they may not if the user switched folders in admin.
		if ($results)
			foreach($results as $result)
				if ($result['deleted'] == 0) {
					$tmpfile = $result['name'].".htm";
					if ($result['grouping'] != '')
						$tmpfile = '['.$result['grouping'].'] '.$result['name'].'.htm';
					$filefound = false;
					foreach ($filearr as $f)
						if ($f == $tmpfile) {
							$filefound = true;
							break;
						}
					if (! $filefound) {
						$content = editNote($result['name'], $result['grouping']);
						saveNote($FOLDER, $result['name'], $result['grouping'], $content);
					}
				}
	}
	// Now loop through and return the listing
	if ($results) {
		$count = 0;
		$now = new DateTime();
		$filetime = new DateTime();
		foreach($results as $result)
			if ($result['deleted'] == 0 || $showdel == true) {
				$filetime->setTimestamp($result['mtime']);
				$timestring = getTimeString($filetime, $now);
				$f = array();
				$f['id'] = $result['id'];
				$f['name'] = $result['name'];
				$f['group'] = $result['grouping'];
				$f['timestring'] = $timestring;
				$f['mtime'] = $result['mtime'];
				$f['timediff'] = $now->getTimestamp()-$result['mtime'];
				$f['deleted'] = $result['deleted'];
				$farray[$count] = $f;
				$count++;
			}
	}
	return $farray;
}

function createNote($FOLDER, $name, $group) {
	$now = new DateTime();
	$mtime = $now->getTimestamp();
	$uid = \OCP\User::getUser();
	$fileindb = false;
	$filedeldb = false;
	$query = OCP\DB::prepare("SELECT id, name, grouping, mtime, deleted FROM *PREFIX*ownnote WHERE uid=? and name=? and grouping=?");
	$results = $query->execute(Array($uid, $name, $group))->fetchAll();
	foreach($results as $result)
		if ($result['deleted'] == 0)
			$fileindb = true;
		else
			$filedeldb = true;
	if ($filedeldb) {
		$query = OCP\DB::prepare("DELETE FROM *PREFIX*ownnote WHERE uid=? and name=? and grouping=?");
		$results = $query->execute(Array($uid, $name, $group));
	}
	if (! $fileindb) {
		if ($FOLDER != '') {
			$tmpfile = $FOLDER."/".$name.".htm";
			if ($group != '')
				$tmpfile = $FOLDER."/[".$group."] ".$name.".htm";
			if (!\OC\Files\Filesystem::file_exists($tmpfile)) {
				\OC\Files\Filesystem::touch($tmpfile);
			}
			if ($info = \OC\Files\Filesystem::getFileInfo($tmpfile)) {
				$mtime = $info['mtime'];
			}
		}
		$query = OCP\DB::prepare("INSERT INTO *PREFIX*ownnote (uid, name, grouping, mtime, note) VALUES (?,?,?,?,?)");
		$query->execute(Array($uid,$name,$group,$mtime,''));
	}
	return "DONE";
}


function deleteNote($FOLDER, $name, $group) {
	$now = new DateTime();
	$mtime = $now->getTimestamp();
	$uid = \OCP\User::getUser();
	$query = OCP\DB::prepare("UPDATE *PREFIX*ownnote set deleted=1 WHERE uid=? and name=? and grouping=?");
	$results = $query->execute(Array($uid, $name, $group));
	if ($FOLDER != '') {
		$tmpfile = $FOLDER."/".$name.".htm";
		if ($group != '')
			$tmpfile = $FOLDER."/[".$group."] ".$name.".htm";
		if (\OC\Files\Filesystem::file_exists($tmpfile))
			\OC\Files\Filesystem::unlink($tmpfile);
	}
	return "DONE";
}


function editNote($name, $group) {
	$ret = "";
	$uid = \OCP\User::getUser();
	$query = OCP\DB::prepare("SELECT note FROM *PREFIX*ownnote WHERE uid=? and name=? and grouping=?");
	$results = $query->execute(Array($uid, $name, $group))->fetchAll();
	foreach($results as $result)
		$ret = $result['note'];
	return $ret;
}

function saveNote($FOLDER, $name, $group, $content) {
	$now = new DateTime();
	$mtime = $now->getTimestamp();
	$uid = \OCP\User::getUser();
	if ($FOLDER != '') {
		$tmpfile = $FOLDER."/".$name.".htm";
		if ($group != '')
			$tmpfile = $FOLDER."/[".$group."] ".$name.".htm";
			\OC\Files\Filesystem::file_put_contents($tmpfile, $content);
		if ($info = \OC\Files\Filesystem::getFileInfo($tmpfile)) {
			$mtime = $info['mtime'];
		}
	}
	$query = OCP\DB::prepare("UPDATE *PREFIX*ownnote set note=?, mtime=? WHERE uid=? and name=? and grouping=?");
	$results = $query->execute(Array($content, $mtime, $uid, $name, $group));
	return "DONE";
}

function renameNote($FOLDER, $name, $group, $newname, $newgroup) {
	// We actually need to delete and create so that the delete flag exists for syncing clients
	$content = editNote($name, $group);
	createNote($FOLDER, $newname, $newgroup);
	saveNote($FOLDER, $newname, $newgroup, $content);
	deleteNote($FOLDER, $name, $group);
	return "DONE";
}

function deleteGroup($FOLDER, $group) {
	// We actually need to just rename all the notes
	$uid = \OCP\User::getUser();
	$query = OCP\DB::prepare("SELECT id, name, grouping, mtime FROM *PREFIX*ownnote WHERE deleted=0 and uid=? and grouping=?");
	$results = $query->execute(Array($uid, $group))->fetchAll();
	foreach($results as $result) {
		renameNote($FOLDER, $result['name'], $group, $result['name'], '');
	}
	return "DONE";
}

function renameGroup($FOLDER, $group, $newgroup) {
	$uid = \OCP\User::getUser();
	$query = OCP\DB::prepare("SELECT id, name, grouping, mtime FROM *PREFIX*ownnote WHERE deleted=0 and uid=? and grouping=?");
	$results = $query->execute(Array($uid, $group))->fetchAll();
	foreach($results as $result) {
		renameNote($FOLDER, $result['name'], $group, $result['name'], $newgroup);
	}
	return "DONE";
}

?>
