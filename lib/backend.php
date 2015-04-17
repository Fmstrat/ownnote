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

function getAnnouncement() {
	$ret = "";
	$url = 'https://raw.githubusercontent.com/Fmstrat/announcements/master/ownnote/announcement.html';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl1, CURLOPT_FRESH_CONNECT, TRUE);
	//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.0; S60/3.0 NokiaN73-1/2.0(2.0617.0.0.7) Profile/MIDP-2.0 Configuration/CLDC-1.1)");
	$result = curl_exec($ch);
	if ($result === FALSE) {
		die('Curl failed: ' . curl_error($ch));
	} else {
		$ret = $result;
	}
	curl_close($ch);
	return $ret;
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

function splitContent($str) {
	$maxlength = 2621440; // 5 Megs (2 bytes per character)
	$count = 0;
	$strarray = array();
	while (true) { 
		if (strlen($str) <= $maxlength) { 
			$strarray[$count++] = $str;
			return $strarray;
		} else {
			$strarray[$count++] = substr($str, 0, $maxlength);
			$str = substr($str, $maxlength);
		}
	}
}

function getListing($FOLDER, $showdel) {
	// Get the listing from the database
	$requery = false;
	$uid = \OCP\User::getUser();
	$query = OCP\DB::prepare("SELECT id, name, grouping, mtime, deleted FROM *PREFIX*ownnote WHERE uid=? ORDER BY name");
	$results = $query->execute(Array($uid))->fetchAll();
	$results2 = $results;
	if ($results)
		foreach($results as $result)
			foreach($results2 as $result2)
				if ($result['id'] != $result2['id'] && $result['name'] == $result2['name'] && $result['grouping'] == $result2['grouping'] && $result['mtime'] == $result2['mtime']) {
					// We have a duplicate that should not exist. Need to remove the offending record first
					$delid = $result['id'];
					if ($result['id'] > $result2['id'])
						$delid = $result2['id'];
					$delquery = OCP\DB::prepare("DELETE FROM *PREFIX*ownnote WHERE id=?");
					$delquery->execute(Array($delid));
					$requery = true;
				}
	if ($requery) {
		$query = OCP\DB::prepare("SELECT id, name, grouping, mtime, deleted FROM *PREFIX*ownnote WHERE uid=? ORDER BY name");
		$results = $query->execute(Array($uid))->fetchAll();
		$requery = false;
	}
	// Create directory if it doesn't exist
	$farray = array();
	if ($FOLDER != '') {
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
						$html = "";
						if ($html = \OC\Files\Filesystem::file_get_contents($FOLDER."/".$tmpfile)) {
						} else {
							$html = "";
						}
						saveNote('', $name, $group, $html, $info['mtime']);
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
						saveNote($FOLDER, $result['name'], $result['grouping'], $content, 0);
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
	$ret = -1;
	$query = OCP\DB::prepare("SELECT id, name, grouping, mtime, deleted FROM *PREFIX*ownnote WHERE uid=? and name=? and grouping=?");
	$results = $query->execute(Array($uid, $name, $group))->fetchAll();
	foreach($results as $result)
		if ($result['deleted'] == 0) {
			$fileindb = true;
			$ret = $result['id'];
		} else {
			$filedeldb = true;
		}
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
		$query = OCP\DB::prepare("INSERT INTO *PREFIX*ownnote (uid, name, grouping, mtime, note, shared) VALUES (?,?,?,?,?,?)");
		$query->execute(Array($uid,$name,$group,$mtime,'',''));
		$ret = OCP\DB::insertid('*PREFIX*ownnote');
	}
	return $ret;
}


function deleteNote($FOLDER, $name, $group) {
	$now = new DateTime();
	$mtime = $now->getTimestamp();
	$uid = \OCP\User::getUser();
	$query = OCP\DB::prepare("UPDATE *PREFIX*ownnote set deleted=1, mtime=? WHERE uid=? and name=? and grouping=?");
	$results = $query->execute(Array($mtime, $uid, $name, $group));
	$query = OCP\DB::prepare("SELECT id FROM *PREFIX*ownnote WHERE uid=? and name=? and grouping=?");
	$results = $query->execute(Array($uid, $name, $group))->fetchAll();
	foreach($results as $result) {
		$query2 = OCP\DB::prepare("DELETE FROM *PREFIX*ownnote_parts WHERE id=?");
		$results2 = $query2->execute(Array($result['id']));
	}
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
	$query = OCP\DB::prepare("SELECT id,note FROM *PREFIX*ownnote WHERE uid=? and name=? and grouping=?");
	$results = $query->execute(Array($uid, $name, $group))->fetchAll();
	foreach($results as $result) {
		$ret = $result['note'];
		if ($ret == '') {
			$query2 = OCP\DB::prepare("SELECT note FROM *PREFIX*ownnote_parts WHERE id=? order by pid");
			$results2 = $query2->execute(Array($result['id']))->fetchAll();
			foreach($results2 as $result2) {
				$ret .= $result2['note'];
			}
		}
	}
	return $ret;
}

function saveNote($FOLDER, $name, $group, $content, $in_mtime) {
	$maxlength = 2621440; // 5 Megs (2 bytes per character)
	$now = new DateTime();
	$mtime = $now->getTimestamp();
	if ($in_mtime != 0)
		$mtime = $in_mtime;
	$uid = \OCP\User::getUser();
	// First check to see if we're creating a new note, createNote handles all of this
	$id = createNote($FOLDER, $name, $group);
	if ($id != -1) {
		if ($FOLDER != '') {
			$tmpfile = $FOLDER."/".$name.".htm";
			if ($group != '')
				$tmpfile = $FOLDER."/[".$group."] ".$name.".htm";
				\OC\Files\Filesystem::file_put_contents($tmpfile, $content);
			if ($info = \OC\Files\Filesystem::getFileInfo($tmpfile)) {
				$mtime = $info['mtime'];
			}
		}
		if (strlen($content) <= $maxlength) {
			$query = OCP\DB::prepare("UPDATE *PREFIX*ownnote set note=?, mtime=? WHERE uid=? and name=? and grouping=?");
			$results = $query->execute(Array($content, $mtime, $uid, $name, $group));
			$query = OCP\DB::prepare("DELETE FROM *PREFIX*ownnote_parts WHERE id=?");
			$results = $query->execute(Array($id));
		} else {
			$query = OCP\DB::prepare("UPDATE *PREFIX*ownnote set note='', mtime=? WHERE uid=? and name=? and grouping=?");
			$results = $query->execute(Array($mtime, $uid, $name, $group));
			$query = OCP\DB::prepare("DELETE FROM *PREFIX*ownnote_parts WHERE id=?");
			$results = $query->execute(Array($id));
			$contentarr = splitContent($content);
			for ($i = 0; $i < count($contentarr); $i++) {
				$query = OCP\DB::prepare("INSERT INTO *PREFIX*ownnote_parts (id, note) values (?,?)");
				$results = $query->execute(Array($id, $contentarr[$i]));
			}
		}

	}
	error_log("---RET---");
	return "DONE";
}

function renameNote($FOLDER, $name, $group, $newname, $newgroup) {
	// We actually need to delete and create so that the delete flag exists for syncing clients
	$content = editNote($name, $group);
	deleteNote($FOLDER, $name, $group);
	createNote($FOLDER, $newname, $newgroup);
	// BUG: Don't need createNote above?
	saveNote($FOLDER, $newname, $newgroup, $content, 0);
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

function getVersion() {
	$v = file_get_contents(__DIR__."/../appinfo/version");
	if ($v)
		return trim($v);
	else
		return "";
}

?>
