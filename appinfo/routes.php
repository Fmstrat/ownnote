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

namespace OCA\OwnNote\AppInfo;

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
$application = new Application();

$application->registerRoutes($this, array('routes' => array(
	array('name' => 'page#index', 'url' => '/', 'verb' => 'GET'),
	array('name' => 'ownnote_api#setval', 'url' => '/setval', 'verb' => 'POST'),
	array('name' => 'ownnote_api#index', 'url' => '/api/v0.2/ownnote', 'verb' => 'GET'),
	array('name' => 'ownnote_api#remoteindex', 'url' => '/api/v0.2/ownnote/remoteindex', 'verb' => 'GET'),
	array('name' => 'ownnote_api#announcement', 'url' => '/api/v0.2/ownnote/announcement', 'verb' => 'GET'),
	array('name' => 'ownnote_api#ren', 'url' => '/api/v0.2/ownnote/ren', 'verb' => 'POST'),
	array('name' => 'ownnote_api#edit', 'url' => '/api/v0.2/ownnote/edit', 'verb' => 'POST'),
	array('name' => 'ownnote_api#del', 'url' => '/api/v0.2/ownnote/del', 'verb' => 'POST'),
	array('name' => 'ownnote_api#save', 'url' => '/api/v0.2/ownnote/save', 'verb' => 'POST'),
	array('name' => 'ownnote_api#create', 'url' => '/api/v0.2/ownnote/create', 'verb' => 'POST'),
	array('name' => 'ownnote_api#delgroup', 'url' => '/api/v0.2/ownnote/delgroup', 'verb' => 'POST'),
	array('name' => 'ownnote_api#rengroup', 'url' => '/api/v0.2/ownnote/rengroup', 'verb' => 'POST'),
        array('name' => 'ownnote_api#preflighted_cors', 'url' => '/api/v0.2/{path}', 'verb' => 'OPTIONS', 'requirements' => array('path' => '.+')),
)));
