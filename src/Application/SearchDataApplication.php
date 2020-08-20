<?php

declare(strict_types=0);

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

namespace Ampache\Application;

use Core;
use Search;
use UI;

final class SearchDataApplication implements ApplicationInterface
{
    public function run(): void
    {
        Header('content-type: application/x-javascript');

        $search = new Search(null, Core::get_request('type'));

        echo 'var types = ';
        echo $this->arrayToJSON($search->types) . ";\n";
        echo 'var basetypes = ';
        echo $this->arrayToJSON($search->basetypes) . ";\n";
        echo 'removeIcon = \'<a href="javascript: void(0)">' . UI::get_icon('disable', T_('Remove')) . '</a>\';';
    }
    /**
     * @deprecated json_encode should do the trick here
     *
     * @param $array
     * @return string
     */
    private function arrayToJSON($array): string
    {
        $json = '{ ';
        foreach ($array as $key => $value) {
            $json .= '"' . $key . '" : ';
            if (is_array($value)) {
                $json .= $this->arrayToJSON($value);
            } else {
                // Make sure to strip backslashes and convert things to
                // entities in our output
                $json .= '"' . scrub_out(str_replace(['"', '\\'], '', $value)) . '"';
            }
            $json .= ' , ';
        }
        $json = rtrim((string) $json, ', ');

        return $json . ' }';
    }
}
