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

namespace OCA\UserCAS\Service;

use \OCP\IConfig;
use \Psr\Log\LoggerInterface;

/**
 * Class LoggingService
 *
 * @package OCA\UserCAS\Service
 *
 * @author Felix Rupp <kontakt@felixrupp.com>
 * @copyright Felix Rupp <kontakt@felixrupp.com>
 *
 * @since 1.5.0
 */
class LoggingService
{

    /**
     * @since 1.6.1
     */
    const DEBUG = 0;
    /**
     * @since 1.6.1
     */
    const INFO = 1;
    /**
     * @since 1.6.1
     */
    const WARN = 2;
    /**
     * @since 1.6.1
     */
    const ERROR = 3;
    /**
     * @since 1.6.1
     */
    const FATAL = 4;

    /**
     * @var string $appName
     */
    private $appName;

    /**
     * @var \OCP\IConfig $appConfig
     */
    private $config;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    /**
     * LoggingService constructor.
     * @param string $appName
     * @param \OCP\IConfig $config
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct($appName, IConfig $config, LoggerInterface $logger)
    {

        $this->appName = $appName;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param mixed $level
     * @param string $message
     */
    public function write($level, $message)
    {
        switch ($level) {
            case self::DEBUG:
                $this->logger->debug($message, ['app' => $this->appName]);
                break;
            case self::INFO:
                $this->logger->info($message, ['app' => $this->appName]);
                break;
            case self::WARN:
                $this->logger->warning($message, ['app' => $this->appName]);
                break;
            case self::ERROR:
                $this->logger->error($message, ['app' => $this->appName]);
                break;
            case self::FATAL:
                $this->logger->emergency($message, ['app' => $this->appName]);
                break;
            default:
                $this->logger->info($message, ['app' => $this->appName]);
        }
    }
}
