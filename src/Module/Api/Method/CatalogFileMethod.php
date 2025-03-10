<?php
/*
 * vim:set softtabstop=4 shiftwidth=4 expandtab:
 *
 *  LICENSE: GNU Affero General Public License, version 3 (AGPL-3.0-or-later)
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

namespace Ampache\Module\Api\Method;

use Ampache\Config\AmpConfig;
use Ampache\Repository\Model\Catalog;
use Ampache\Repository\Model\Podcast_Episode;
use Ampache\Repository\Model\Song;
use Ampache\Repository\Model\User;
use Ampache\Repository\Model\Video;
use Ampache\Module\Api\Api;
use Ampache\Module\Song\Deletion\SongDeleterInterface;
use Ampache\Module\System\Session;

/**
 * Class CatalogFileMethod
 * @package Lib\ApiMethods
 */
final class CatalogFileMethod
{
    private const ACTION = 'catalog_file';

    /**
     * catalog_file
     * MINIMUM_API_VERSION=420000
     *
     * Perform actions on local catalog files.
     * Single file versions of catalog add, clean and verify.
     * Make sure you remember to urlencode those file names!
     *
     * @param array $input
     * file    = (string) urlencode(FULL path to local file)
     * task    = (string) 'add', 'clean', 'verify', 'remove' (can be comma separated)
     * catalog = (integer) $catalog_id)
     * @return boolean
     */
    public static function catalog_file(array $input)
    {
        if (!Api::check_access('interface', 50, User::get_from_username(Session::username($input['auth']))->id, self::ACTION, $input['api_format'])) {
            return false;
        }
        if (!Api::check_parameter($input, array('catalog', 'file', 'task'), self::ACTION)) {
            return false;
        }
        $file = (string) html_entity_decode($input['file']);
        $task = explode(',', (string)$input['task']);
        if (!$task) {
            $task = array();
        }

        // confirm that a valid task is going to happen
        if (!AmpConfig::get('delete_from_disk') && in_array('remove', $task)) {
            Api::error(T_('Enable: delete_from_disk'), '4703', self::ACTION, 'system', $input['api_format']);

            return false;
        }
        if (!file_exists($file) && !in_array('clean', $task)) {
            /* HINT: Requested object string/id/type ("album", "myusername", "some song title", 1298376) */
            Api::error(sprintf(T_('Not Found: %s'), $file), '4704', self::ACTION, 'file', $input['api_format']);

            return false;
        }
        foreach ($task as $item) {
            if (!in_array($item, array('add', 'clean', 'verify', 'remove'))) {
                /* HINT: Requested object string/id/type ("album", "myusername", "some song title", 1298376) */
                Api::error(sprintf(T_('Bad Request: %s'), $item), '4710', self::ACTION, 'task', $input['api_format']);

                return false;
            }
        }
        $catalog_id = (int) $input['catalog'];
        $catalog    = Catalog::create_from_id($catalog_id);
        if ($catalog->id < 1) {
            /* HINT: Requested object string/id/type ("album", "myusername", "some song title", 1298376) */
            Api::error(sprintf(T_('Not Found: %s'), $catalog_id), '4704', self::ACTION, 'catalog', $input['api_format']);

            return false;
        }
        switch ($catalog->gather_types) {
            case 'podcast':
                $type  = 'podcast_episode';
                $media = new Podcast_Episode(Catalog::get_id_from_file($file, $type));
                break;
            case 'clip':
            case 'tvshow':
            case 'movie':
            case 'personal_video':
                $type  = 'video';
                $media = new Video(Catalog::get_id_from_file($file, $type));
                break;
            case 'music':
            default:
                $type  = 'song';
                $media = new Song(Catalog::get_id_from_file($file, $type));
                break;
        }

        if ($catalog->catalog_type == 'local') {
            foreach ($task as $item) {
                define('API', true);
                unset($SSE_OUTPUT);
                switch ($item) {
                    case 'clean':
                        if ($media->id) {
                            $catalog->clean_file($file, $type);
                        }
                        break;
                    case 'verify':
                        if ($media->id) {
                            Catalog::update_media_from_tags($media, array($type));
                        }
                        break;
                    case 'add':
                        if (!$media->id) {
                            $catalog->add_file($file, array());
                        }
                        break;
                    case 'remove':
                        if ($media->id) {
                            $media->remove();
                        }
                        break;
                }
            }
            Api::message('successfully started: ' . $task . ' for ' . $file, $input['api_format']);
        } else {
            Api::error(T_('Not Found'), '4704', self::ACTION, 'catalog', $input['api_format']);
        }
        Session::extend($input['auth']);

        return true;
    }

    /**
     * @deprecated
     */
    public static function getSongDeleter(): SongDeleterInterface
    {
        global $dic;

        return $dic->get(SongDeleterInterface::class);
    }
}
