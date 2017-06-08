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



class OwnnoteApiController extends ApiController {

	private $backend;


	public function __construct($appName, IRequest $request){
		parent::__construct($appName, $request);
		$this->backend = new Backend();
	}

	/**
	* MOBILE FUNCTIONS
	*/

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function index() {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		return $this->backend->getListing($DB_OR_FOLDER, $FOLDER, false);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function mobileindex() {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		return $this->backend->getListing($DB_OR_FOLDER, $FOLDER, true);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function remoteindex() {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		return json_encode($this->backend->getListing($DB_OR_FOLDER, $FOLDER, true));
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function create($name, $group) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($group))
			return $this->backend->createNote($DB_OR_FOLDER, $FOLDER, $name, $group);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function del($name, $group) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($group))
			return $this->backend->deleteNote($DB_OR_FOLDER, $FOLDER, $name, $group);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function edit($name, $group) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($group))
			return $this->backend->editNote($DB_OR_FOLDER, $FOLDER, $name, $group);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function save($name, $group, $content) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($group) && isset($content))
			return $this->backend->saveNote($DB_OR_FOLDER, $FOLDER, $name, $group, $content, 0);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function ren($name, $group, $newname, $newgroup) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($name) && isset($newname) && isset($group) && isset($newgroup))
			return $this->backend->renameNote($DB_OR_FOLDER, $FOLDER, $name, $group, $newname, $newgroup);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function delgroup($group) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($group))
			return $this->backend->deleteGroup($DB_OR_FOLDER, $FOLDER, $group);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function rengroup($group, $newgroup) {
		$DB_OR_FOLDER = \OCP\Config::getAppValue('ownnote', 'db_or_folder', 'folder_only');
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', '');
		if (isset($group) && isset($newgroup))
			return $this->backend->renameGroup($DB_OR_FOLDER, $FOLDER, $group, $newgroup);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function version() {
		return $this->backend->getVersion();
	}
}
