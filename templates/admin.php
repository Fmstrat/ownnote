<?php

\OCP\Util::addScript('ownnote', 'admin');

$folder = $_['folder'];

?>

<div class="section">
        <h2>ownNote</h2>
	<label for="ownnote-type">How would you like to store your notes?</label><br>
	<select id="ownnote-type">
		<option <?php if ($folder == "") echo "selected"; ?> value="">Database only</option>
		<option <?php if ($folder != "") echo "selected"; ?> value="folder">Database and folder</option>
	</select><br>
	<br>
	<div id="shorten-folder-settings" style="display: <?php if ($folder != "") echo "block"; else echo "none"; ?>">
		<label for="ownnote-folder">Please enter the folder name you would like to use to store notes, with no slashes.</label><br>
		<input type="text" style="width: 250pt" name="ownnote-folder" id="ownnote-folder" value="<?php echo $folder ?>" />
	</div>
</div>
