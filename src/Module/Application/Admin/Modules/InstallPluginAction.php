<?php
/*
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 * LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
 * Copyright 2001 - 2020 Ampache.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=0);

namespace Ampache\Module\Application\Admin\Modules;

use Ampache\Config\ConfigContainerInterface;
use Ampache\Repository\Model\Plugin;
use Ampache\Repository\Model\User;
use Ampache\Module\Application\ApplicationActionInterface;
use Ampache\Module\Application\Exception\AccessDeniedException;
use Ampache\Module\Authorization\AccessLevelEnum;
use Ampache\Module\Authorization\GuiGatekeeperInterface;
use Ampache\Module\System\Core;
use Ampache\Module\System\LegacyLogger;
use Ampache\Module\Util\UiInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class InstallPluginAction implements ApplicationActionInterface
{
    public const REQUEST_KEY = 'install_plugin';

    private UiInterface $ui;

    private ConfigContainerInterface $configContainer;

    private LoggerInterface $logger;

    public function __construct(
        UiInterface $ui,
        ConfigContainerInterface $configContainer,
        LoggerInterface $logger
    ) {
        $this->ui              = $ui;
        $this->configContainer = $configContainer;
        $this->logger          = $logger;
    }

    public function run(ServerRequestInterface $request, GuiGatekeeperInterface $gatekeeper): ?ResponseInterface
    {
        if ($gatekeeper->mayAccess(AccessLevelEnum::TYPE_INTERFACE, AccessLevelEnum::LEVEL_ADMIN) === false) {
            throw new AccessDeniedException();
        }

        $this->ui->showHeader();

        /* Verify that this plugin exists */
        $plugins = Plugin::get_plugins();
        if (!array_key_exists($_REQUEST['plugin'], $plugins)) {
            $this->logger->error(
                sprintf('Error: Invalid Plugin: %s selected', Core::get_request('plugin')),
                [LegacyLogger::CONTEXT_TYPE => __CLASS__]
            );

            $this->ui->showQueryStats();
            $this->ui->showFooter();

            return null;
        }
        $plugin = new Plugin($_REQUEST['plugin']);
        if (!$plugin->install()) {
            $this->logger->error(
                sprintf('Error: Plugin Install Failed, %s', Core::get_request('plugin')),
                [LegacyLogger::CONTEXT_TYPE => __CLASS__]
            );

            $url   = sprintf('%s/admin/modules.php?action=show_plugins', $this->configContainer->getWebPath());
            $title = T_('There Was a Problem');
            $body  = T_('Unable to install this Plugin');
            $this->ui->showConfirmation($title, $body, $url);

            $this->ui->showQueryStats();
            $this->ui->showFooter();

            return null;
        }

        // Don't trust the plugin to this stuff
        User::rebuild_all_preferences();

        /* Show Confirmation */
        $url   = sprintf('%s/admin/modules.php?action=show_plugins', $this->configContainer->getWebPath());
        $title = T_('No Problem');
        $body  = T_('The Plugin has been enabled');
        $this->ui->showConfirmation($title, $body, $url);

        $this->ui->showQueryStats();
        $this->ui->showFooter();

        return null;
    }
}
