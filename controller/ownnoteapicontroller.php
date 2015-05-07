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

\OCP\User::checkLoggedIn();
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
	public function create() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($_GET["name"]) && isset($_GET["group"]))
			return $this->backend->createNote($FOLDER, $_GET["name"], $_GET["group"]);
		if (isset($_POST["name"]) && isset($_POST["group"]))
			return $this->backend->createNote($FOLDER, $_POST["name"], $_POST["group"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function del() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($_GET["name"]) && isset($_GET["group"]))
			return $this->backend->deleteNote($FOLDER, $_GET["name"], $_GET["group"]);
		if (isset($_POST["name"]) && isset($_POST["group"]))
			return $this->backend->deleteNote($FOLDER, $_POST["name"], $_POST["group"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function edit() {
		if (isset($_GET["name"]) && isset($_GET["group"]))
			return $this->backend->editNote($_GET["name"], $_GET["group"]);
		if (isset($_POST["name"]) && isset($_POST["group"]))
			return $this->backend->editNote($_POST["name"], $_POST["group"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function save() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($_GET["name"]) && isset($_GET["group"]) && isset($_GET["content"]))
			return $this->backend->saveNote($FOLDER, $_GET["name"], $_GET["group"], $_GET["content"], 0);
		if (isset($_POST["name"]) && isset($_POST["group"]) && isset($_POST["content"]))
			return $this->backend->saveNote($FOLDER, $_POST["name"], $_POST["group"], $_POST["content"], 0);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function ren() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($_GET["name"]) && isset($_GET["newname"]) && isset($_GET["group"]) && isset($_GET["newgroup"]))
			return $this->backend->renameNote($FOLDER, $_GET["name"], $_GET["group"], $_GET["newname"], $_GET["newgroup"]);
		if (isset($_POST["name"]) && isset($_POST["newname"]) && isset($_POST["group"]) && isset($_POST["newgroup"]))
			return $this->backend->renameNote($FOLDER, $_POST["name"], $_POST["group"], $_POST["newname"], $_POST["newgroup"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function delgroup() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($_GET["group"]))
			return $this->backend->deleteGroup($FOLDER, $_GET["group"]);
		if (isset($_POST["group"]))
			return $this->backend->deleteGroup($FOLDER, $_POST["group"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function rengroup() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		if (isset($_GET["group"]) && isset($_GET["newgroup"]))
			return $this->backend->renameGroup($FOLDER, $_GET["group"], $_GET["newgroup"]);
		if (isset($_POST["group"]) && isset($_POST["newgroup"]))
			return $this->backend->renameGroup($FOLDER, $_POST["group"], $_POST["newgroup"]);
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
	public function setval() {
		if (isset($_POST['folder'])) {
			return $this->backend->setAdminVal('folder', $_POST["folder"]);
		}
		if (isset($_POST['disableAnnouncement'])) {
			return $this->backend->setAdminVal('disableAnnouncement', $_POST["disableAnnouncement"]);
		}
	}
}
