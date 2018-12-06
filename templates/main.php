<?php
\OCP\Util::addScript('ownnote', 'script');
\OCP\Util::addScript('ownnote','tinymce/tinymce.min');
\OCP\Util::addStyle('ownnote', 'style');

$disableAnnouncement = true;
$l = OCP\Util::getL10N('ownnote');

$ocVersionArray = OCP\Util::getVersion();
$ocVersion = "";
$oci = 0;
$ocl = sizeof($ocVersionArray);
foreach ($ocVersionArray as $v) {
	$ocVersion .= $v;
	$oci++;
	if ($oci < $ocl)
		$ocVersion .= ".";
}

?>
<input type="hidden" name="nextNonce" id="nextNonce" value="<?php p(\OC::$server->getContentSecurityPolicyNonceManager()->getNonce()) ?>" />
<div id="app">
	<div id="app-navigation">
		<ul id="grouplist">
		</ul>
	</div>
	<div id="app-content">
		<div id="ownnote"></div>
	</div>
	<div id="ownnote-l10n">
		<input type="hidden" id="l10n # day ago" value="<?php p($l->t("# day ago")); ?>">
		<input type="hidden" id="l10n # days ago" value="<?php p($l->t("# days ago")); ?>">
		<input type="hidden" id="l10n # hour ago" value="<?php p($l->t("# hour ago")); ?>">
		<input type="hidden" id="l10n # hours ago" value="<?php p($l->t("# hours ago")); ?>">
		<input type="hidden" id="l10n # minute ago" value="<?php p($l->t("# minute ago")); ?>">
		<input type="hidden" id="l10n # minutes ago" value="<?php p($l->t("# minutes ago")); ?>">
		<input type="hidden" id="l10n # month ago" value="<?php p($l->t("# month ago")); ?>">
		<input type="hidden" id="l10n # months ago" value="<?php p($l->t("# months ago")); ?>">
		<input type="hidden" id="l10n # second ago" value="<?php p($l->t("# second ago")); ?>">
		<input type="hidden" id="l10n # seconds ago" value="<?php p($l->t("# seconds ago")); ?>">
		<input type="hidden" id="l10n # week ago" value="<?php p($l->t("# week ago")); ?>">
		<input type="hidden" id="l10n # weeks ago" value="<?php p($l->t("# weeks ago")); ?>">
		<input type="hidden" id="l10n # year ago" value="<?php p($l->t("# year ago")); ?>">
		<input type="hidden" id="l10n # years ago" value="<?php p($l->t("# years ago")); ?>">
		<input type="hidden" id="l10n All" value="<?php p($l->t("All")); ?>">
		<input type="hidden" id="l10n An ungrouped file has the same name as a file in this group" value="] = "<?php p($l->t("An ungrouped file has the same name as a file in this group")); ?>">
		<input type="hidden" id="l10n Cancel" value="<?php p($l->t("Cancel")); ?>">
		<input type="hidden" id="l10n Create" value="<?php p($l->t("Create")); ?>">
		<input type="hidden" id="l10n Dismiss" value="<?php p($l->t("Dismiss")); ?>">
		<input type="hidden" id="l10n Filename/group already exists" value="] = "<?php p($l->t("Filename/group already exists.")); ?>">
		<input type="hidden" id="l10n Group" value="<?php p($l->t("Group")); ?>">
		<input type="hidden" id="l10n Group already exists" value="] = "<?php p($l->t("Group already exists.")); ?>">
		<input type="hidden" id="l10n Just now" value="<?php p($l->t("Just now")); ?>">
		<input type="hidden" id="l10n Modified" value="<?php p($l->t("Modified")); ?>">
		<input type="hidden" id="l10n Name" value="<?php p($l->t("Name")); ?>">
		<input type="hidden" id="l10n New" value="<?php p($l->t("New")); ?>">
		<input type="hidden" id="l10n Not grouped" value="<?php p($l->t("Not grouped")); ?>">
		<input type="hidden" id="l10n Note" value="<?php p($l->t("Note")); ?>">
		<input type="hidden" id="l10n Notes" value="<?php p($l->t("Notes")); ?>">
		<input type="hidden" id="l10n Quick Save" value="<?php p($l->t("Quick Save")); ?>">
		<input type="hidden" id="l10n Save" value="<?php p($l->t("Save")); ?>">
	</div>
</div>
