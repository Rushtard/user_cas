<?php
declare(strict_types=1);

namespace OCA\UserCAS\Bootstrap;

use OCP\IContainer;
use OCA\UserCAS\Service\AppService;
use OCA\UserCAS\Service\UserService;
use OCA\UserCAS\Service\LoggingService;

final class Enforce {
	public static function run(IContainer $c): void {
		if (\OC::$CLI) {
			return;
		}

		$requestUri = $_SERVER['REQUEST_URI'] ?? '';
		\OCP\Log\logger('user_cas')->debug('CAS enforcement running on: ' . $requestUri);

		/** @var UserService $userService */
		$userService = $c->query('UserService');

		/** @var AppService $appService */
		$appService = $c->query('AppService');

		if ($appService->isSetupValid()) {

			$userService->registerBackend($c->query('Backend'));

			$loginScreen = (strpos($requestUri, '/login') !== false && strpos($requestUri, '/apps/user_cas/login') === false);
			$publicShare = (strpos($requestUri, '/index.php/s/') !== false && $appService->arePublicSharesProtected());

			if ($requestUri === '/' || $loginScreen || $publicShare) {

				if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {

					$c->query('UserHooks')->register();

					setcookie("user_cas_enforce_authentication", "0", 0, '/');
					$urlParams = $_REQUEST['redirect_url'] ?? '';
					setcookie("user_cas_redirect_url", $urlParams, 0, '/');

					$appService->registerLogIn();
					$isEnforced = $appService->isEnforceAuthentication($_SERVER['REMOTE_ADDR'] ?? '', $requestUri);

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
}
