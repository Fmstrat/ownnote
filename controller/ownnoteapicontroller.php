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
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function index() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		return json_encode($this->backend->getListing($FOLDER, false));
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function remoteindex() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		return json_encode($this->backend->getListing($FOLDER, true));
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function announcement() {
		return $this->backend->getAnnouncement();
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function create($name, $group) {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($name) && isset($group))
			return $this->backend->createNote($FOLDER, $name, $group);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function del($name, $group) {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($name) && isset($group))
			return $this->backend->deleteNote($FOLDER, $name, $group);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function edit($name, $group) {
		if (isset($name) && isset($group))
			return $this->backend->editNote($name, $group);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function save($name, $group, $content) {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($name) && isset($group) && isset($content))
			return $this->backend->saveNote($FOLDER, $name, $group, $content, 0);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function ren($name, $group, $newname, $newgroup) {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($name) && isset($newname) && isset($group) && isset($newgroup))
			return $this->backend->renameNote($FOLDER, $name, $group, $newname, $newgroup);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function delgroup($group) {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($group))
			return $this->backend->deleteGroup($FOLDER, $group);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function rengroup($group, $newgroup) {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($group) && isset($newgroup))
			return $this->backend->renameGroup($FOLDER, $group, $newgroup);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function version() {
		return $this->backend->getVersion();
	}

	/**
	* @CORS
	* @NoCSRFRequired
	*/
	public function setval($field, $value) {
		return $this->backend->setAdminVal($field, $value);
	}
}
