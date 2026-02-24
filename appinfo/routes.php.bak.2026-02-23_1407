<?php

/**
 * ownCloud - user_cas
 *
 * @author Felix Rupp <kontakt@felixrupp.com>
 * @copyright Felix Rupp <kontakt@felixrupp.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\UserCAS\AppInfo;

/** @var \OCA\UserCAS\AppInfo\Application $application */
$application = new \OCA\UserCAS\AppInfo\Application();
$application->registerRoutes($this, array(
    'routes' => [
        array('name' => 'settings#saveSettings', 'url' => '/settings/save', 'verb' => 'POST'),
        array('name' => 'authentication#casLogin', 'url' => '/login', 'verb' => 'GET'),
        array('name' => 'authentication#casLogout', 'url' => '/login', 'verb' => 'POST')
    ]
));