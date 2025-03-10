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
 */

declare(strict_types=0);

namespace Ampache\Repository\Model;

use Ampache\Module\System\Dba;
use Ampache\Config\AmpConfig;
use Ampache\Module\System\AmpError;
use Ampache\Module\System\Core;
use PDOStatement;
use SimpleXMLElement;

class Podcast extends database_object implements library_item
{
    protected const DB_TABLENAME = 'podcast';

    /* Variables from DB */
    public $id;
    public $catalog;
    public $feed;
    public $title;
    public $website;
    public $description;
    public $language;
    public $copyright;
    public $generator;
    public $lastbuilddate;
    public $lastsync;
    public $total_count;
    public $episodes;

    public $f_name;
    public $f_website;
    public $f_description;
    public $f_language;
    public $f_copyright;
    public $f_generator;
    public $f_lastbuilddate;
    public $f_lastsync;
    public $link;
    public $f_link;
    public $f_website_link;

    /**
     * Podcast
     * Takes the ID of the podcast and pulls the info from the db
     * @param integer $podcast_id
     */
    public function __construct($podcast_id = 0)
    {
        /* If they failed to pass in an id, just run for it */
        if (!$podcast_id) {
            return false;
        }

        /* Get the information from the db */
        $info = $this->get_info($podcast_id);

        foreach ($info as $key => $value) {
            $this->$key = $value;
        } // foreach info

        return true;
    } // constructor

    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * get_catalogs
     *
     * Get all catalog ids related to this item.
     * @return integer[]
     */
    public function get_catalogs()
    {
        return array($this->catalog);
    }

    /**
     * get_episodes
     * gets all episodes for this podcast
     * @param string $state_filter
     * @return array
     */
    public function get_episodes($state_filter = '')
    {
        $params          = array();
        $sql             = "SELECT `podcast_episode`.`id` FROM `podcast_episode` ";
        $catalog_disable = AmpConfig::get('catalog_disable');
        if ($catalog_disable) {
            $sql .= "LEFT JOIN `catalog` ON `catalog`.`id` = `podcast_episode`.`catalog` ";
        }
        $sql .= "WHERE `podcast_episode`.`podcast`='" . Dba::escape($this->id) . "' ";
        if (!empty($state_filter)) {
            $sql .= "AND `podcast_episode`.`state` = ? ";
            $params[] = $state_filter;
        }
        if ($catalog_disable) {
            $sql .= "AND `catalog`.`enabled` = '1' ";
        }
        $sql .= "ORDER BY `podcast_episode`.`pubdate` DESC";
        $db_results = Dba::read($sql, $params);

        $results = array();
        while ($row = Dba::fetch_assoc($db_results)) {
            $results[] = $row['id'];
        }

        return $results;
    } // get_episodes

    /**
     * format
     * this function takes the object and formats some values
     * @param boolean $details
     * @return boolean
     */
    public function format($details = true)
    {
        $this->f_name          = $this->title;
        $this->f_description   = scrub_out($this->description);
        $this->f_language      = scrub_out($this->language);
        $this->f_copyright     = scrub_out($this->copyright);
        $this->f_generator     = scrub_out($this->generator);
        $this->f_website       = scrub_out($this->website);
        $this->f_lastbuilddate = date("c", (int)$this->lastbuilddate);
        $this->f_lastsync      = date("c", (int)$this->lastsync);
        $this->link            = AmpConfig::get('web_path') . '/podcast.php?action=show&podcast=' . $this->id;
        $this->f_link          = '<a href="' . $this->link . '" title="' . scrub_out($this->f_name) . '">' . scrub_out($this->f_name) . '</a>';
        $this->f_website_link  = "<a target=\"_blank\" href=\"" . $this->website . "\">" . $this->website . "</a>";

        return true;
    }

    /**
     * get_keywords
     * @return array
     */
    public function get_keywords()
    {
        $keywords            = array();
        $keywords['podcast'] = array(
            'important' => true,
            'label' => T_('Podcast'),
            'value' => $this->f_name
        );

        return $keywords;
    }

    /**
     * get_fullname
     *
     * @return string
     */
    public function get_fullname()
    {
        return $this->f_name;
    }

    /**
     * @return null
     */
    public function get_parent()
    {
        return null;
    }

    /**
     * @return array
     */
    public function get_childrens()
    {
        return array('podcast_episode' => $this->get_episodes());
    }

    /**
     * @param string $name
     * @return array
     */
    public function search_childrens($name)
    {
        debug_event(self::class, 'search_childrens ' . $name, 5);

        return array();
    }

    /**
     * @param string $filter_type
     * @return array
     */
    public function get_medias($filter_type = null)
    {
        $medias = array();
        if ($filter_type === null || $filter_type == 'podcast_episode') {
            $episodes = $this->get_episodes('completed');
            foreach ($episodes as $episode_id) {
                $medias[] = array(
                    'object_type' => 'podcast_episode',
                    'object_id' => $episode_id
                );
            }
        }

        return $medias;
    }

    /**
     * @return mixed|null
     */
    public function get_user_owner()
    {
        return null;
    }

    /**
     * @return string
     */
    public function get_default_art_kind()
    {
        return 'default';
    }

    /**
     * get_description
     * @return string
     */
    public function get_description()
    {
        return $this->f_description;
    }

    /**
     * display_art
     * @param integer $thumb
     * @param boolean $force
     */
    public function display_art($thumb = 2, $force = false)
    {
        if (Art::has_db($this->id, 'podcast') || $force) {
            Art::display('podcast', $this->id, $this->get_fullname(), $thumb, $this->link);
        }
    }

    /**
     * update
     * This takes a key'd array of data and updates the current podcast
     * @param array $data
     * @return mixed
     */
    public function update(array $data)
    {
        $feed        = isset($data['feed']) ? $data['feed'] : $this->feed;
        $title       = isset($data['title']) ? filter_var($data['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES) : $this->title;
        $website     = isset($data['website']) ? scrub_in($data['website']) : $this->website;
        $description = isset($data['description']) ? scrub_in($data['description']) : $this->description;
        $generator   = isset($data['generator']) ? scrub_in($data['generator']) : $this->generator;
        $copyright   = isset($data['copyright']) ? scrub_in($data['copyright']) : $this->copyright;

        if (strpos($feed, "http://") !== 0 && strpos($feed, "https://") !== 0) {
            debug_event(self::class, 'Podcast update canceled, bad feed url.', 1);

            return $this->id;
        }

        $sql = 'UPDATE `podcast` SET `feed` = ?, `title` = ?, `website` = ?, `description` = ?, `generator` = ?, `copyright` = ? WHERE `id` = ?';
        Dba::write($sql, array($feed, $title, $website, $description, $generator, $copyright, $this->id));

        $this->feed        = $feed;
        $this->title       = $title;
        $this->website     = $website;
        $this->description = $description;
        $this->generator   = $generator;
        $this->copyright   = $copyright;

        return $this->id;
    }

    /**
     * create
     * @param array $data
     * @param boolean $return_id
     * @return boolean|integer
     */
    public static function create(array $data, $return_id = false)
    {
        $feed = (string) $data['feed'];
        // Feed must be http/https
        if (strpos($feed, "http://") !== 0 && strpos($feed, "https://") !== 0) {
            AmpError::add('feed', T_('Feed URL is invalid'));
        }

        $catalog_id = (int)($data['catalog']);
        if ($catalog_id < 1) {
            AmpError::add('catalog', T_('Target Catalog is required'));
        } else {
            $catalog = Catalog::create_from_id($catalog_id);
            if ($catalog->gather_types !== "podcast") {
                AmpError::add('catalog', T_('Wrong target Catalog type'));
            }
        }

        if (AmpError::occurred()) {
            return false;
        }

        $title         = T_('Unknown');
        $website       = null;
        $description   = null;
        $language      = null;
        $copyright     = null;
        $generator     = null;
        $lastbuilddate = 0;
        $episodes      = false;
        $arturl        = '';

        // don't allow duplicate podcasts
        $sql        = "SELECT `id` FROM `podcast` WHERE `feed`= '" . Dba::escape($feed) . "'";
        $db_results = Dba::read($sql);
        while ($row = Dba::fetch_assoc($db_results, false)) {
            if ((int) $row['id'] > 0) {
                return (int) $row['id'];
            }
        }

        $xmlstr = file_get_contents($feed, false, stream_context_create(Core::requests_options()));
        if ($xmlstr === false) {
            AmpError::add('feed', T_('Can not access the feed'));
        } else {
            $xml = simplexml_load_string($xmlstr);
            if ($xml === false) {
                AmpError::add('feed', T_('Can not read the feed'));
            } else {
                $title            = html_entity_decode((string)$xml->channel->title);
                $website          = (string)$xml->channel->link;
                $description      = html_entity_decode((string)$xml->channel->description);
                $language         = (string)$xml->channel->language;
                $copyright        = html_entity_decode((string)$xml->channel->copyright);
                $generator        = html_entity_decode((string)$xml->channel->generator);
                $lastbuilddatestr = (string)$xml->channel->lastBuildDate;
                if ($lastbuilddatestr) {
                    $lastbuilddate = strtotime($lastbuilddatestr);
                }

                if ($xml->channel->image) {
                    $arturl = (string)$xml->channel->image->url;
                }

                $episodes = $xml->channel->item;
            }
        }

        if (AmpError::occurred()) {
            return false;
        }

        $sql        = "INSERT INTO `podcast` (`feed`, `catalog`, `title`, `website`, `description`, `language`, `copyright`, `generator`, `lastbuilddate`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $db_results = Dba::write($sql, array(
            $feed,
            $catalog_id,
            $title,
            $website,
            $description,
            $language,
            $copyright,
            $generator,
            $lastbuilddate
        ));
        if ($db_results) {
            $podcast_id = (int)Dba::insert_id();
            $podcast    = new Podcast($podcast_id);
            $dirpath    = $podcast->get_root_path();
            if (!is_dir($dirpath)) {
                if (mkdir($dirpath) === false) {
                    debug_event(self::class, 'Cannot create directory ' . $dirpath, 1);
                }
            }
            if (!empty($arturl)) {
                $art = new Art((int)$podcast_id, 'podcast');
                $art->insert_url($arturl);
            }
            Catalog::update_map($catalog_id, 'podcast', (int)$podcast_id);
            if ($episodes) {
                $podcast->add_episodes($episodes);
            }
            if ($return_id) {
                return (int)$podcast_id;
            }

            return true;
        }

        return false;
    }

    /**
     * add_episodes
     * @param SimpleXMLElement $episodes
     * @param integer $afterdate
     * @param boolean $gather
     */
    public function add_episodes($episodes, $afterdate = 0, $gather = false)
    {
        foreach ($episodes as $episode) {
            $this->add_episode($episode, $afterdate);
        }
        $time   = time();
        $params = array($this->id);

        // Select episodes to download
        $dlnb = (int)AmpConfig::get('podcast_new_download');
        if ($dlnb <> 0) {
            $sql = "SELECT `podcast_episode`.`id` FROM `podcast_episode` INNER JOIN `podcast` ON `podcast`.`id` = `podcast_episode`.`podcast` WHERE `podcast`.`id` = ? AND `podcast_episode`.`addition_time` > `podcast`.`lastsync` ORDER BY `podcast_episode`.`pubdate` DESC";
            if ($dlnb > 0) {
                $sql .= " LIMIT " . (string)$dlnb;
            }
            $db_results = Dba::read($sql, $params);
            while ($row = Dba::fetch_row($db_results)) {
                $episode = new Podcast_Episode($row[0]);
                $episode->change_state('pending');
                if ($gather) {
                    $episode->gather();
                }
            }
        }
        // Remove items outside limit
        $keepnb = AmpConfig::get('podcast_keep');
        if ($keepnb > 0) {
            $sql        = "SELECT `podcast_episode`.`id` FROM `podcast_episode` WHERE `podcast_episode`.`podcast` = ? ORDER BY `podcast_episode`.`pubdate` DESC LIMIT " . $keepnb . ",18446744073709551615";
            $db_results = Dba::read($sql, $params);
            while ($row = Dba::fetch_row($db_results)) {
                $episode = new Podcast_Episode($row[0]);
                $episode->remove();
            }
        }
        // update the episode count after adding / removing episodes
        $sql = "UPDATE `podcast`, (SELECT COUNT(`podcast_episode`.`id`) AS `episodes`, `podcast` FROM `podcast_episode` WHERE `podcast_episode`.`podcast` = ? GROUP BY `podcast_episode`.`podcast`) AS `episode_count` SET `podcast`.`episodes` = `episode_count`.`episodes` WHERE `podcast`.`episodes` != `episode_count`.`episodes` AND `podcast`.`id` = `episode_count`.`podcast`;";
        Dba::write($sql, $params);
        Catalog::update_mapping('podcast_episode');
        $this->update_lastsync($time);
    }

    /**
     * add_episode
     * @param SimpleXMLElement $episode
     * @param integer $afterdate
     * @return PDOStatement|boolean
     */
    private function add_episode(SimpleXMLElement $episode, $afterdate = 0)
    {
        debug_event(self::class, 'Adding new episode to podcast ' . $this->id . '...', 4);

        $title       = html_entity_decode((string)$episode->title);
        $website     = (string)$episode->link;
        $guid        = (string)$episode->guid;
        $description = html_entity_decode((string)$episode->description);
        $author      = html_entity_decode((string)$episode->author);
        $category    = html_entity_decode((string)$episode->category);
        $source      = null;
        $time        = 0;
        if ($episode->enclosure) {
            $source = $episode->enclosure['url'];
        }
        $itunes   = $episode->children('itunes', true);
        $duration = (string) $itunes->duration;
        // time is missing hour e.g. "15:23"
        if (preg_grep("/^[0-9][0-9]\:[0-9][0-9]$/", array($duration))) {
            $duration = '00:' . $duration;
        }
        // process a time string "03:23:01"
        $ptime = (preg_grep("/[0-9][0-9]\:[0-9][0-9]\:[0-9][0-9]/", array($duration)))
            ? date_parse((string)$duration)
            : $duration;
        // process "HH:MM:SS" time OR fall back to a seconds duration string e.g "24325"
        $time = (is_array($ptime))
            ? (int) $ptime['hour'] * 3600 + (int) $ptime['minute'] * 60 + (int) $ptime['second']
            : (int) $ptime;


        $pubdate    = 0;
        $pubdatestr = (string)$episode->pubDate;
        if ($pubdatestr) {
            $pubdate = strtotime($pubdatestr);
        }
        if ($pubdate < 1) {
            debug_event(self::class, 'Invalid episode publication date, skipped', 3);

            return false;
        }
        if (!$source) {
            debug_event(self::class, 'Episode source URL not found, skipped', 3);

            return false;
        }
        if (self::get_id_from_source($source) > 0) {
            debug_event(self::class, 'Episode source URL already exists, skipped', 3);

            return false;
        }

        if ($pubdate > $afterdate) {
            $sql = "INSERT INTO `podcast_episode` (`title`, `guid`, `podcast`, `state`, `source`, `website`, `description`, `author`, `category`, `time`, `pubdate`, `addition_time`, `catalog`) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            return Dba::write($sql, array(
                $title,
                $guid,
                $this->id,
                $source,
                $website,
                $description,
                $author,
                $category,
                $time,
                $pubdate,
                time(),
                $this->catalog
            ));
        } else {
            debug_event(self::class, 'Episode published before ' . $afterdate . ' (' . $pubdate . '), skipped', 4);

            return true;
        }
    }

    /**
     * update_lastsync
     * @param integer $time
     * @return PDOStatement|boolean
     */
    private function update_lastsync($time)
    {
        $sql = "UPDATE `podcast` SET `lastsync` = ? WHERE `id` = ?";

        return Dba::write($sql, array($time, $this->id));
    }

    /**
     * sync_episodes
     * @param boolean $gather
     * @return PDOStatement|boolean
     */
    public function sync_episodes($gather = false)
    {
        debug_event(self::class, 'Syncing feed ' . $this->feed . ' ...', 4);

        $xmlstr = file_get_contents($this->feed, false, stream_context_create(Core::requests_options()));
        if ($xmlstr === false) {
            debug_event(self::class, 'Cannot access feed ' . $this->feed, 1);

            return false;
        }
        $xml = simplexml_load_string($xmlstr);
        if ($xml === false) {
            debug_event(self::class, 'Cannot read feed ' . $this->feed, 1);

            return false;
        }

        $this->add_episodes($xml->channel->item, $this->lastsync, $gather);

        return true;
    }

    /**
     * remove
     * @return PDOStatement|boolean
     */
    public function remove()
    {
        $episodes = $this->get_episodes();
        foreach ($episodes as $episode_id) {
            $episode = new Podcast_Episode($episode_id);
            $episode->remove();
        }

        $sql = "DELETE FROM `podcast` WHERE `id` = ?";

        return Dba::write($sql, array($this->id));
    }

    /**
     * get_id_from_source
     *
     * Get episode id from the source url.
     *
     * @param string $url
     * @return integer
     */
    public static function get_id_from_source($url)
    {
        $sql        = "SELECT `id` FROM `podcast_episode` WHERE `source` = ?";
        $db_results = Dba::read($sql, array($url));

        if ($results = Dba::fetch_assoc($db_results)) {
            return (int)$results['id'];
        }

        return 0;
    }

    /**
     * get_root_path
     * @return string
     */
    public function get_root_path()
    {
        $catalog = Catalog::create_from_id($this->catalog);
        if (!$catalog->get_type() == 'local') {
            debug_event(self::class, 'Bad catalog type.', 1);

            return '';
        }

        $dirname = $this->title;

        // create path if it doesn't exist
        if (!is_dir($catalog->path . DIRECTORY_SEPARATOR . $dirname)) {
            static::create_catalog_path($catalog->path . DIRECTORY_SEPARATOR . $dirname);
        }

        return $catalog->path . DIRECTORY_SEPARATOR . $dirname;
    }

    /**
     * create_catalog_path
     * This returns the catalog types that are available
     * @param string $path
     * @return boolean
     */
    private static function create_catalog_path($path)
    {
        if (!is_dir($path)) {
            if (mkdir($path) === false) {
                debug_event(__CLASS__, 'Cannot create directory ' . $path, 2);

                return false;
            }
        }

        return true;
    }
}
