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


$FOLDER = "Notes";

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
	require_once 'ownnote/lib/backend.php';
	return json_encode(getListing("Notes"));
    }

    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     */
    public function edit() {
	require_once 'ownnote/lib/backend.php';
	if (isset($_GET["note"]))
		return editNote("Notes", $_GET["note"].".htm");
	if (isset($_POST["note"]))
		return editNote("Notes", $_POST["note"].".htm");
    }

    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     */
    public function del() {
	require_once 'ownnote/lib/backend.php';
	if (isset($_GET["note"]))
		return deleteNote("Notes", $_GET["note"].".htm");
	if (isset($_POST["note"]))
		return deleteNote("Notes", $_POST["note"].".htm");
    }

    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     */
    public function ren() {
	require_once 'ownnote/lib/backend.php';
	if (isset($_GET["note"]) && isset($_GET["newnote"]))
		return renameNote("Notes", $_GET["note"], $_GET["newnote"]);
	if (isset($_POST["note"]) && isset($_POST["newnote"]))
		return renameNote("Notes", $_POST["note"], $_POST["newnote"]);
    }

    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     */
    public function save() {
	require_once 'ownnote/lib/backend.php';
	if (isset($_GET["note"]) && isset($_GET["content"]))
		return saveNote("Notes", $_GET["note"], $_GET["content"]);
	if (isset($_POST["note"]) && isset($_POST["content"]))
		return saveNote("Notes", $_POST["note"], $_POST["content"]);
    }

    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     */
    public function create() {
	require_once 'ownnote/lib/backend.php';
	if (isset($_GET["note"]))
		return createNote("Notes", $_GET["note"]);
	if (isset($_POST["note"]))
		return createNote("Notes", $_POST["note"]);
    }

    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     */
    public function delgroup() {
	require_once 'ownnote/lib/backend.php';
	if (isset($_GET["group"]))
		return deleteGroup("Notes", $_GET["group"]);
	if (isset($_POST["group"]))
		return deleteGroup("Notes", $_POST["group"]);
    }

    /**
     * @NoAdminRequired
     * @CORS
     * @NoCSRFRequired
     */
    public function rengroup() {
	require_once 'ownnote/lib/backend.php';
	if (isset($_GET["group"]) && isset($_GET["newgroup"]))
		return renameGroup("Notes", $_GET["group"], $_GET["newgroup"]);
	if (isset($_POST["group"]) && isset($_POST["newgroup"]))
		return renameGroup("Notes", $_POST["group"], $_POST["newgroup"]);
    }
}
