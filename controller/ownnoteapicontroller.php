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
    }
}
