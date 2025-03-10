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

namespace Ampache\Module\User\Activity\TypeHandler;

use Ampache\MockeryTestCase;
use Ampache\Repository\Model\Artist;
use Ampache\Repository\Model\ModelFactoryInterface;
use Ampache\Repository\UserActivityRepositoryInterface;
use Mockery\MockInterface;

class ArtistActivityTypeHandlerTest extends MockeryTestCase
{
    /** @var UserActivityRepositoryInterface|MockInterface|null */
    private MockInterface $useractivityRepository;

    /** @var ModelFactoryInterface|MockInterface|null */
    private MockInterface $modelFactory;

    private ?ArtistActivityTypeHandler $subject;

    public function setUp(): void
    {
        $this->useractivityRepository = $this->mock(UserActivityRepositoryInterface::class);
        $this->modelFactory           = $this->mock(ModelFactoryInterface::class);

        $this->subject = new ArtistActivityTypeHandler(
            $this->useractivityRepository,
            $this->modelFactory
        );
    }

    public function testRegisterActivityRegisterArtistActivity(): void
    {
        $artist = $this->mock(Artist::class);

        $objectId           = 666;
        $objectType         = 'some-object-type';
        $action             = 'some-action';
        $userId             = 42;
        $date               = 123;
        $artistName         = 'some-artist-name';
        $musicBrainzId      = 'some-mbid';

        $this->modelFactory->shouldReceive('createArtist')
            ->with($objectId)
            ->once()
            ->andReturn($artist);

        $artist->shouldReceive('format')
            ->withNoArgs()
            ->once();

        $artist->f_name = $artistName;
        $artist->mbid   = $musicBrainzId;

        $this->useractivityRepository->shouldReceive('registerArtistEntry')
            ->with(
                $userId,
                $action,
                $objectType,
                $objectId,
                $date,
                $artistName,
                $musicBrainzId
            )
            ->once();

        $this->subject->registerActivity(
            $objectId,
            $objectType,
            $action,
            $userId,
            $date
        );
    }

    public function testRegisterActivityRegisterGenericActivity(): void
    {
        $artist = $this->mock(Artist::class);

        $objectId            = 666;
        $objectType          = 'some-object-type';
        $action              = 'some-action';
        $userId              = 42;
        $date                = 123;

        $this->modelFactory->shouldReceive('createArtist')
            ->with($objectId)
            ->once()
            ->andReturn($artist);

        $artist->shouldReceive('format')
            ->withNoArgs()
            ->once();
        $artist->f_name = '';

        $this->useractivityRepository->shouldReceive('registerGenericEntry')
            ->with(
                $userId,
                $action,
                $objectType,
                $objectId,
                $date
            )
            ->once();

        $this->subject->registerActivity(
            $objectId,
            $objectType,
            $action,
            $userId,
            $date
        );
    }
}
