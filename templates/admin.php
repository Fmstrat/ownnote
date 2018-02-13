<?php

\OCP\Util::addScript('ownnote', 'admin');

$folder = $_['folder'];
$l = OCP\Util::getL10N('ownnote');

?>

<div class="section">
        <h2>ownNote</h2>
	<label for="ownnote-type"><?php p($l->t("How would you like to store your notes?")); ?></label><br>
	<select id="ownnote-type">
		<option <?php if ($folder == "") echo "selected"; ?> value=""><?php p($l->t("Database only")); ?></option>
		<option <?php if ($folder != "") echo "selected"; ?> value="folder"><?php p($l->t("Database and folder")); ?></option>
	</select><br>
	<br>
	<div id="ownnote-folder-settings" style="display: <?php if ($folder != "") echo "block"; else echo "none"; ?>">
		<label for="ownnote-folder"><?php p($l->t("Please enter the folder name you would like to use to store notes, with no slashes.")); ?></label><br>
		<input type="text" style="width: 250pt" name="ownnote-folder" id="ownnote-folder" value="<?php p($folder) ?>" /><br>
		<br>
	</div>
</div>
