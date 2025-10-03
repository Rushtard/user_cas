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
 */

use OCA\UserCAS\AppInfo\Application;
use OCA\UserCAS\Service\AppService;
use OCA\UserCAS\Service\LoggingService;
use OCA\UserCAS\Service\UserService;

/** @var Application $app */
$app = new Application();
$c = $app->getContainer();

$requestUri = $_SERVER['REQUEST_URI'] ?? '';

// Désactivation du blocage CLI / PHP 8.4
//if (\OCP_App::isEnabled($c->getAppName()) && !\OC::$CLI) {
if (true) { // On force toujours l'initialisation, même en CLI
    /** @var UserService $userService */
    $userService = $c->query('UserService');

    /** @var AppService $appService */
    $appService = $c->query('AppService');

    // Vérifie que le setup CAS est valide
    if ($appService->isSetupValid()) {

        $userService->registerBackend($c->query('Backend'));

        $loginScreen = (strpos($requestUri, '/login') !== false && strpos($requestUri, '/apps/user_cas/login') === false);
        $publicShare = (strpos($requestUri, '/index.php/s/') !== false && $appService->arePublicSharesProtected());

        if ($requestUri === '/' || $loginScreen || $publicShare) {

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

                $c->query('UserHooks')->register();

                setcookie("user_cas_enforce_authentication", "0", 0, '/');
                $urlParams = $_REQUEST['redirect_url'] ?? '';
                setcookie("user_cas_redirect_url", $urlParams, 0, '/');

                $appService->registerLogIn();
                $isEnforced = $appService->isEnforceAuthentication($_SERVER['REMOTE_ADDR'], $requestUri);

                if ($publicShare) {
                    $isEnforced = true;
                }

                if ($isEnforced && (!isset($_COOKIE['user_cas_enforce_authentication']) || $_COOKIE['user_cas_enforce_authentication'] === '0')) {

                    /** @var LoggingService $loggingService */
                    $loggingService = $c->query("LoggingService");
                    $loggingService->write(LoggingService::DEBUG, 'Enforce Authentication: ' . $isEnforced);
                    setcookie("user_cas_enforce_authentication", '1', 0, '/');

                    if (!$appService->isCasInitialized()) {
                        try {
                            $appService->init();
                            $loggingService->write(LoggingService::DEBUG, 'Redirecting to CAS Server');
                            setcookie("user_cas_redirect_url", urlencode($requestUri), 0, '/');
                            header("Location: " . $appService->linkToRouteAbsolute($c->getAppName() . '.authentication.casLogin'));
                            die();
                        } catch (\OCA\UserCAS\Exception\PhpCas\PhpUserCasLibraryNotFoundException $e) {
                            $loggingService->write(LoggingService::ERROR, 'Fatal error: ' . $e->getMessage());
                        }
                    }
                }
            }
        } else {
            if (strpos($requestUri, '/remote.php') === false && strpos($requestUri, '/webdav') === false && strpos($requestUri, '/dav') === false) {
                $c->query('UserHooks')->register();
            }
        }
    } else {
        $appService->unregisterLogIn();
    }
}
