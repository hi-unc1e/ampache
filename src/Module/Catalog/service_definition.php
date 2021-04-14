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

declare(strict_types=1);

namespace Ampache\Module\Catalog;

use Ampache\Module\Catalog\Update\UpdateCatalog;
use Ampache\Module\Catalog\Update\UpdateCatalogInterface;
use Ampache\Module\Catalog\Update\UpdateSingleCatalogFile;
use Ampache\Module\Catalog\Update\UpdateSingleCatalogFileInterface;
use function DI\autowire;

return [
    UpdateSingleCatalogFileInterface::class => autowire(UpdateSingleCatalogFile::class),
    UpdateCatalogInterface::class => autowire(UpdateCatalog::class),
    GarbageCollector\CatalogGarbageCollectorInterface::class => autowire(GarbageCollector\CatalogGarbageCollector::class),
    Loader\CatalogLoaderInterface::class => autowire(Loader\CatalogLoader::class),
    MediaDeletionCheckerInterface::class => autowire(MediaDeletionChecker::class),
    Process\CatalogProcessTypeMapperInterface::class => autowire(Process\CatalogProcessTypeMapper::class),
    SingleItemUpdaterInterface::class => autowire(SingleItemUpdater::class),
    ArtItemGathererInterface::class => autowire(ArtItemGatherer::class),
    DataMigratorInterface::class => autowire(DataMigrator::class),
];
