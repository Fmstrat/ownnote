<?php
/**
 * ownCloud - ownnote
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Ben Curtis <ownclouddev@nosolutions.com>
 * @copyright Ben Curtis 2015
 */

namespace OCA\OwnNote\Controller;

use \OCP\AppFramework\ApiController;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http\Response;
use \OCP\AppFramework\Http;
use \OCP\IRequest;
use \OCA\OwnNote\Lib\Backend;

\OCP\App::checkAppEnabled('ownnote');



class OwnnoteAjaxController extends ApiController {

	private $backend;


	public function __construct($appName, IRequest $request){
		parent::__construct($appName, $request);
		$this->backend = new Backend();
	}

	/**
	* AJAX FUNCTIONS
	*/

	/**
	* @NoAdminRequired
	*/
	public function ajaxindex() {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		return $this->backend->getListing($DB_OR_FOLDER, $FOLDER, false);
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxannouncement() {
		return $this->backend->getAnnouncement();
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxcreate($name, $group) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($group))
			return $this->backend->createNote($DB_OR_FOLDER, $FOLDER, $name, $group);
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxdel($name, $group) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($group))
			return $this->backend->deleteNote($DB_OR_FOLDER, $FOLDER, $name, $group);
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxedit($name, $group) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($group))
			return $this->backend->editNote($DB_OR_FOLDER, $FOLDER, $name, $group);
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxsave($name, $group, $content) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($group) && isset($content))
			return $this->backend->saveNote($DB_OR_FOLDER, $FOLDER, $name, $group, $content, 0);
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxren($name, $group, $newname, $newgroup) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($newname) && isset($group) && isset($newgroup))
			return $this->backend->renameNote($DB_OR_FOLDER, $FOLDER, $name, $group, $newname, $newgroup);
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxdelgroup($group) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($group))
			return $this->backend->deleteGroup($DB_OR_FOLDER, $FOLDER, $group);
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxrengroup($group, $newgroup) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($group) && isset($newgroup))
			return $this->backend->renameGroup($DB_OR_FOLDER, $FOLDER, $group, $newgroup);
	}

	/**
	* @NoAdminRequired
	*/
	public function ajaxversion() {
		return $this->backend->getVersion();
	}

	/**
	*/
	public function ajaxsetval($field, $value) {
		return $this->backend->setAdminVal($field, $value);
	}
}
