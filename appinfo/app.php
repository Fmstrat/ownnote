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

\OCP\App::registerAdmin('ownnote', 'admin');

\OC::$server->getNavigationManager()->add(array(
    // the string under which your app will be referenced in owncloud
    'id' => 'ownnote',

    // sorting weight for the navigation. The higher the number, the higher
    // will it be listed in the navigation
    'order' => 10,

    // the route that will be shown on startup
    'href' => \OCP\Util::linkToRoute('ownnote.page.index'),

    // the icon that will be shown in the navigation
    // this file needs to exist in img/
    'icon' => \OCP\Util::imagePath('ownnote', 'app.svg'),

    // the title of your application. This will be used in the
    // navigation or on the settings page of your app
    //'name' => \OC_L10N::get('ownnote')->t('Own Note')
    'name' => \OCP\Util::getL10N('ownnote')->t('Notes')
));
