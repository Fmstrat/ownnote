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

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('ownnote');



class OwnnoteApiController extends ApiController {

	private $userId;

	public function __construct($appName, IRequest $request){
		parent::__construct($appName, $request);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function index() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		require_once 'ownnote/lib/backend.php';
		return json_encode(getListing($FOLDER, false));
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function remoteindex() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		require_once 'ownnote/lib/backend.php';
		return json_encode(getListing($FOLDER, true));
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function announcement() {
		require_once 'ownnote/lib/backend.php';
		return getAnnouncement();
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function create() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		require_once 'ownnote/lib/backend.php';
		if (isset($_GET["name"]) && isset($_GET["group"]))
			return createNote($FOLDER, $_GET["name"], $_GET["group"]);
		if (isset($_POST["name"]) && isset($_POST["group"]))
			return createNote($FOLDER, $_POST["name"], $_POST["group"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function del() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		require_once 'ownnote/lib/backend.php';
		if (isset($_GET["name"]) && isset($_GET["group"]))
			return deleteNote($FOLDER, $_GET["name"], $_GET["group"]);
		if (isset($_POST["name"]) && isset($_POST["group"]))
			return deleteNote($FOLDER, $_POST["name"], $_POST["group"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function edit() {
		require_once 'ownnote/lib/backend.php';
		if (isset($_GET["name"]) && isset($_GET["group"]))
			return editNote($_GET["name"], $_GET["group"]);
		if (isset($_POST["name"]) && isset($_POST["group"]))
			return editNote($_POST["name"], $_POST["group"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function save() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		require_once 'ownnote/lib/backend.php';
		if (isset($_GET["name"]) && isset($_GET["group"]) && isset($_GET["content"]))
			return saveNote($FOLDER, $_GET["name"], $_GET["group"], $_GET["content"]);
		if (isset($_POST["name"]) && isset($_POST["group"]) && isset($_POST["content"]))
			return saveNote($FOLDER, $_POST["name"], $_POST["group"], $_POST["content"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function ren() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		require_once 'ownnote/lib/backend.php';
		if (isset($_GET["name"]) && isset($_GET["newname"]) && isset($_GET["group"]) && isset($_GET["newgroup"]))
			return renameNote($FOLDER, $_GET["name"], $_GET["group"], $_GET["newname"], $_GET["newgroup"]);
		if (isset($_POST["name"]) && isset($_POST["newname"]) && isset($_POST["group"]) && isset($_POST["newgroup"]))
			return renameNote($FOLDER, $_POST["name"], $_POST["group"], $_POST["newname"], $_POST["newgroup"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function delgroup() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		require_once 'ownnote/lib/backend.php';
		if (isset($_GET["group"]))
			return deleteGroup($FOLDER, $_GET["group"]);
		if (isset($_POST["group"]))
			return deleteGroup($FOLDER, $_POST["group"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function rengroup() {
		$FOLDER = \OCP\Config::getAppValue('ownnote', 'folder', 'Notes');
		require_once 'ownnote/lib/backend.php';
		if (isset($_GET["group"]) && isset($_GET["newgroup"]))
			return renameGroup($FOLDER, $_GET["group"], $_GET["newgroup"]);
		if (isset($_POST["group"]) && isset($_POST["newgroup"]))
			return renameGroup($FOLDER, $_POST["group"], $_POST["newgroup"]);
	}

	/**
	* @NoAdminRequired
	* @CORS
	* @NoCSRFRequired
	*/
	public function version() {
		require_once 'ownnote/lib/backend.php';
		return getVersion();
	}

	/**
	* @CORS
	* @NoCSRFRequired
	*/
	public function setval() {
		require_once 'ownnote/lib/setval.php';
		return setAdminVal();
	}
}
