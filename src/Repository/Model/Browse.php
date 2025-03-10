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

namespace Ampache\Repository\Model;

use Ampache\Module\Api\Ajax;
use Ampache\Config\AmpConfig;
use Ampache\Module\Util\AjaxUriRetrieverInterface;
use Ampache\Module\Util\ObjectTypeToClassNameMapper;
use Ampache\Module\Util\Ui;

/**
 * Browse Class
 *
 * This handles all of the sql/filtering
 * on the data before it's thrown out to the templates
 * it also handles pulling back the object_ids and then
 * calling the correct template for the object we are displaying
 *
 */
class Browse extends Query
{
    private const BROWSE_TYPES = array(
        'song',
        'album',
        'user',
        'artist',
        'live_stream',
        'playlist',
        'playlist_media',
        'playlist_localplay',
        'smartplaylist',
        'catalog',
        'shoutbox',
        'tag',
        'video',
        'wanted',
        'share',
        'song_preview',
        'channel',
        'broadcast',
        'license',
        'tvshow',
        'tvshow_season',
        'tvshow_episode',
        'movie',
        'clip',
        'personal_video',
        'label',
        'pvmsg',
        'podcast',
        'podcast_episode'
    );

    /**
     * @var boolean $show_header
     */
    public $show_header;

    /**
     * @var integer $duration
     */
    public $duration;

    /**
     * Constructor.
     *
     * @param integer|null $browse_id
     * @param boolean $cached
     */
    public function __construct($browse_id = null, $cached = true)
    {
        parent::__construct($browse_id, $cached);

        if (!$browse_id) {
            $this->set_use_pages(true);
            $this->set_use_alpha(false);
            $this->set_grid_view(true);
        }
        $this->show_header = true;
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * set_simple_browse
     * This sets the current browse object to a 'simple' browse method
     * which means use the base query provided and expand from there
     *
     * @param boolean $value
     */
    public function set_simple_browse($value)
    {
        $this->set_is_simple($value);
    } // set_simple_browse

    /**
     * is_valid_type
     * This sets the current browse object to a 'simple' browse method
     * which means use the base query provided and expand from there
     *
     * @param string $type
     * @return bool
     */
    public function is_valid_type($type)
    {
        return in_array($type, self::BROWSE_TYPES);
    } // set_simple_browse

    /**
     * add_supplemental_object
     * Legacy function, need to find a better way to do that
     *
     * @param string $class
     * @param integer $uid
     * @return boolean
     */
    public function add_supplemental_object($class, $uid)
    {
        $_SESSION['browse']['supplemental'][$this->id][$class] = (int)($uid);

        return true;
    } // add_supplemental_object

    /**
     * get_supplemental_objects
     * This returns an array of 'class', 'id' for additional objects that
     * need to be created before we start this whole browsing thing.
     *
     * @return array
     */
    public function get_supplemental_objects()
    {
        $objects = isset($_SESSION['browse']['supplemental'][$this->id]) ? $_SESSION['browse']['supplemental'][$this->id] : '';

        if (!is_array($objects)) {
            $objects = array();
        }

        return $objects;
    } // get_supplemental_objects

    /**
     * update_browse_from_session
     * Restore the previous start index from something saved into the current session.
     */
    public function update_browse_from_session()
    {
        if ($this->is_simple() && $this->get_start() == 0) {
            $name = 'browse_current_' . $this->get_type();
            if (array_key_exists($name, $_SESSION) && array_key_exists('start', $_SESSION[$name]) && $_SESSION[$name]['start'] > 0) {
                // Checking if value is suitable
                $start = $_SESSION[$name]['start'];
                if ($this->get_offset() > 0) {
                    $set_page = floor($start / $this->get_offset());
                    if ($this->get_total() > $this->get_offset()) {
                        $total_pages = ceil($this->get_total() / $this->get_offset());
                    } else {
                        $total_pages = 0;
                    }

                    if ($set_page >= 0 && $set_page <= $total_pages) {
                        $this->set_start($start);
                    }
                }
            }
        }
    }

    /**
     * show_objects
     * This takes an array of objects
     * and requires the correct template based on the
     * type that we are currently browsing
     *
     * @param array $object_ids
     * @param boolean|array|string $argument
     */
    public function show_objects($object_ids = array(), $argument = false)
    {
        if ($this->is_simple() || !is_array($object_ids) || empty($object_ids)) {
            $object_ids = $this->get_saved();
        } else {
            $this->save_objects($object_ids);
        }

        // Limit is based on the user's preferences if this is not a
        // simple browse because we've got too much here
        if ($this->get_start() >= 0 && (count($object_ids) > $this->get_start()) && !$this->is_simple()) {
            $object_ids = array_slice($object_ids, $this->get_start(), $this->get_offset(), true);
        } else {
            if (!count($object_ids)) {
                $this->set_total(0);
            }
        }

        // Load any additional object we need for this
        $extra_objects = $this->get_supplemental_objects();
        $browse        = $this;

        foreach ($extra_objects as $type => $id) {
            $class_name = ObjectTypeToClassNameMapper::map($type);
            ${$type}    = new $class_name($id);
        }

        $match = '';
        // Format any matches we have so we can show them to the masses
        if ($filter_value = $this->get_filter('alpha_match')) {
            $match = ' (' . (string)$filter_value . ')';
        } elseif ($filter_value = $this->get_filter('starts_with')) {
            $match = ' (' . (string)$filter_value . ')';
        /*} elseif ($filter_value = $this->get_filter('regex_match')) {
            $match = ' (' . (string) $filter_value . ')';
        } elseif ($filter_value = $this->get_filter('regex_not_match')) {
            $match = ' (' . (string) $filter_value . ')';*/
        } elseif ($filter_value = $this->get_filter('catalog')) {
            // Get the catalog title
            $catalog = Catalog::create_from_id((int)((string)$filter_value));
            $match   = ' (' . $catalog->name . ')';
        }

        $type = $this->get_type();

        // Update the session value only if it's allowed on the current browser
        if ($this->is_update_session()) {
            $_SESSION['browse_current_' . $type]['start'] = $browse->get_start();
        }

        // Set the correct classes based on type
        $class = "box browse_" . $type;
        debug_event(self::class, 'Show objects called for type {' . $type . '}', 5);

        // hide some of the useless columns in a browse
        $hide_columns   = array();
        $argument_param = '';
        if (is_array($argument)) {
            if (array_key_exists('hide', $argument) && is_array($argument['hide'])) {
                $hide_columns = $argument['hide'];
            }
            if (!empty($hide_columns)) {
                $argument_param = '&hide=';
                foreach ($hide_columns as $column) {
                    $argument_param .= scrub_in((string)$column) . ',';
                }
                $argument_param = rtrim($argument_param, ',');
            }
        } else {
            $argument_param = ($argument)
                ? '&argument=' . scrub_in((string)$argument)
                : '';
        }

        $limit_threshold = $this->get_threshold();
        // Switch on the type of browsing we're doing
        switch ($type) {
            case 'song':
                $box_title = T_('Songs') . $match;
                Song::build_cache($object_ids, $limit_threshold);
                $box_req = Ui::find_template('show_songs.inc.php');
                break;
            case 'album':
                Album::build_cache($object_ids);
                $box_title         = T_('Albums') . $match;
                $allow_group_disks = false;
                if (is_array($argument)) {
                    $allow_group_disks = $argument['group_disks'];
                    if (array_key_exists('title', $argument)) {
                        $box_title = $argument['title'];
                    }
                }
                if (AmpConfig::get('album_group')) {
                    $allow_group_disks = true;
                }
                $box_req = Ui::find_template('show_albums.inc.php');
                break;
            case 'user':
                $box_title = T_('Browse Users') . $match;
                $box_req   = Ui::find_template('show_users.inc.php');
                break;
            case 'artist':
                $box_title = ($this->is_album_artist())
                    ? T_('Album Artists') . $match
                    : T_('Artists') . $match;
                Artist::build_cache($object_ids, true, $limit_threshold);
                $box_req = Ui::find_template('show_artists.inc.php');
                break;
            case 'live_stream':
                $box_title = T_('Radio Stations') . $match;
                $box_req   = Ui::find_template('show_live_streams.inc.php');
                break;
            case 'playlist':
                Playlist::build_cache($object_ids);
                $box_title = T_('Playlists') . $match;
                $box_req   = Ui::find_template('show_playlists.inc.php');
                break;
            case 'playlist_media':
                $box_title = T_('Playlist Items') . $match;
                $box_req   = Ui::find_template('show_playlist_medias.inc.php');
                break;
            case 'playlist_localplay':
                $box_title = T_('Current Playlist');
                $box_req   = Ui::find_template('show_localplay_playlist.inc.php');
                Ui::show_box_bottom();
                break;
            case 'smartplaylist':
                $box_title = T_('Smart Playlists') . $match;
                $box_req   = Ui::find_template('show_searches.inc.php');
                break;
            case 'catalog':
                $box_title = T_('Catalogs');
                $box_req   = Ui::find_template('show_catalogs.inc.php');
                break;
            case 'shoutbox':
                $box_title = T_('Shoutbox Records');
                $box_req   = Ui::find_template('show_manage_shoutbox.inc.php');
                break;
            case 'tag':
                Tag::build_cache($object_ids);
                $box_title = T_('Genres');
                $box_req   = Ui::find_template('show_tagcloud.inc.php');
                break;
            case 'video':
                Video::build_cache($object_ids);
                $video_type = 'video';
                $box_title  = T_('Videos');
                $box_req    = Ui::find_template('show_videos.inc.php');
                break;
            case 'democratic':
                $box_title = T_('Democratic Playlist');
                $box_req   = Ui::find_template('show_democratic_playlist.inc.php');
                break;
            case 'wanted':
                $box_title = T_('Wanted Albums');
                $box_req   = Ui::find_template('show_wanted_albums.inc.php');
                break;
            case 'share':
                $box_title = T_('Shares');
                $box_req   = Ui::find_template('show_shared_objects.inc.php');
                break;
            case 'song_preview':
                $box_title = T_('Songs');
                $box_req   = Ui::find_template('show_song_previews.inc.php');
                break;
            case 'channel':
                $box_title = T_('Channels');
                $box_req   = Ui::find_template('show_channels.inc.php');
                break;
            case 'broadcast':
                $box_title = T_('Broadcasts');
                $box_req   = Ui::find_template('show_broadcasts.inc.php');
                break;
            case 'license':
                $box_title = T_('Media Licenses');
                $box_req   = Ui::find_template('show_manage_license.inc.php');
                break;
            case 'tvshow':
                $box_title = T_('TV Shows');
                $box_req   = Ui::find_template('show_tvshows.inc.php');
                break;
            case 'tvshow_season':
                $box_title = T_('Seasons');
                $box_req   = Ui::find_template('show_tvshow_seasons.inc.php');
                break;
            case 'tvshow_episode':
                $box_title  = T_('Episodes');
                $video_type = $type;
                $box_req    = Ui::find_template('show_videos.inc.php');
                break;
            case 'movie':
                $box_title  = T_('Movies');
                $video_type = $type;
                $box_req    = Ui::find_template('show_videos.inc.php');
                break;
            case 'clip':
                $box_title  = T_('Clips');
                $video_type = $type;
                $box_req    = Ui::find_template('show_videos.inc.php');
                break;
            case 'personal_video':
                $box_title  = T_('Personal Videos');
                $video_type = $type;
                $box_req    = Ui::find_template('show_videos.inc.php');
                break;
            case 'label':
                $box_title = T_('Labels');
                $box_req   = Ui::find_template('show_labels.inc.php');
                break;
            case 'pvmsg':
                $box_title = T_('Private Messages');
                $box_req   = Ui::find_template('show_pvmsgs.inc.php');
                break;
            case 'podcast':
                $box_title = T_('Podcasts');
                $box_req   = Ui::find_template('show_podcasts.inc.php');
                break;
            case 'podcast_episode':
                $box_title = T_('Podcast Episodes');
                $box_req   = Ui::find_template('show_podcast_episodes.inc.php');
                break;
            default:
                break;
        } // end switch on type

        Ajax::start_container($this->get_content_div(), 'browse_content');
        if ($this->is_show_header()) {
            if (isset($box_req) && isset($box_title)) {
                Ui::show_box_top($box_title, $class);
            }
        }

        if (isset($box_req)) {
            require $box_req;
        }

        if ($this->is_show_header()) {
            if (isset($box_req)) {
                Ui::show_box_bottom();
            }
            echo '<script>';
            echo Ajax::action('?page=browse&action=get_filters&browse_id=' . $this->id . $argument_param, '');
            echo ';</script>';
        } else {
            if (!$this->is_use_pages()) {
                $this->show_next_link($argument);
            }
        }
        Ajax::end_container();
    } // show_object

    /**
     * @param $argument
     */
    public function show_next_link($argument = null)
    {
        // FIXME Can be removed if Browse gets instantiated by the factory
        global $dic;

        $limit       = $this->get_offset();
        $start       = $this->get_start();
        $total       = $this->get_total();
        $next_offset = $start + $limit;
        if ($next_offset <= $total) {
            echo '<a class="jscroll-next" href="' . $dic->get(AjaxUriRetrieverInterface::class)->getAjaxUri() . '?page=browse&action=page&browse_id=' . $this->id . '&start=' . $next_offset . '&xoutput=raw&xoutputnode=' . $this->get_content_div() . '&show_header=false' . $argument . '">' . T_('More') . '</a>';
        }
    }

    /**
     *
     * @param string $type
     * @param string $custom_base
     */
    public function set_type($type, $custom_base = '')
    {
        if (self::is_valid_type($type)) {
            $name = 'browse_' . $type . '_pages';
            if ((filter_has_var(INPUT_COOKIE, $name))) {
                $this->set_use_pages(filter_input(INPUT_COOKIE, $name, FILTER_SANITIZE_STRING,
                        FILTER_FLAG_NO_ENCODE_QUOTES) == 'true');
            }
            $name = 'browse_' . $type . '_alpha';
            if ((filter_has_var(INPUT_COOKIE, $name))) {
                $this->set_use_alpha(filter_input(INPUT_COOKIE, $name, FILTER_SANITIZE_STRING,
                        FILTER_FLAG_NO_ENCODE_QUOTES) == 'true');
            } else {
                $default_alpha = (!AmpConfig::get('libitem_browse_alpha')) ? array() : explode(",",
                    AmpConfig::get('libitem_browse_alpha'));
                if (in_array($type, $default_alpha)) {
                    $this->set_use_alpha(true, false);
                }
            }
            $name = 'browse_' . $type . '_grid_view';
            if ((filter_has_var(INPUT_COOKIE, $name))) {
                $this->set_grid_view(filter_input(INPUT_COOKIE, $name, FILTER_SANITIZE_STRING,
                        FILTER_FLAG_NO_ENCODE_QUOTES) == 'true');
            }

            parent::set_type($type, $custom_base);
        }
    }

    /**
     *
     * @param string $option
     * @param string $value
     */
    public function save_cookie_params($option, $value)
    {
        if ($this->get_type()) {
            $remember_length = time() + 31536000;
            $cookie_options  = [
                'expires' => $remember_length,
                'path' => (string)AmpConfig::get('cookie_path'),
                'domain' => (string)AmpConfig::get('cookie_domain'),
                'secure' => make_bool(AmpConfig::get('cookie_secure')),
                'samesite' => 'Strict'
            ];
            setcookie('browse_' . $this->get_type() . '_' . $option, $value, $cookie_options);
        }
    }

    /**
     *
     * @param boolean $use_pages
     * @param boolean $savecookie
     */
    public function set_use_pages($use_pages, $savecookie = true)
    {
        if ($savecookie) {
            $this->save_cookie_params('pages', $use_pages ? 'true' : 'false');
        }
        $this->_state['use_pages'] = $use_pages;
    }

    /**
     *
     * @return boolean
     */
    public function is_use_pages()
    {
        if (array_key_exists('use_pages', $this->_state)) {
            return make_bool($this->_state['use_pages']);
        }

        return false;
    }

    /**
     *
     * @param boolean $mashup
     */
    public function set_mashup($mashup)
    {
        $this->_state['mashup'] = $mashup;
    }

    /**
     *
     * @return boolean
     */
    public function is_mashup()
    {
        if (array_key_exists('mashup', $this->_state)) {
            return make_bool($this->_state['mashup']);
        }

        return false;
    }

    /**
     *
     * @param boolean $album_artist
     */
    public function set_album_artist($album_artist)
    {
        $this->_state['album_artist'] = $album_artist;
    }

    /**
     *
     * @return boolean
     */
    public function is_album_artist()
    {
        if (array_key_exists('album_artist', $this->_state)) {
            return make_bool($this->_state['album_artist']);
        }

        return false;
    }

    /**
     *
     * @param boolean $grid_view
     * @param boolean $savecookie
     */
    public function set_grid_view($grid_view, $savecookie = true)
    {
        if ($savecookie) {
            $this->save_cookie_params('grid_view', $grid_view ? 'true' : 'false');
        }
        $this->_state['grid_view'] = $grid_view;
    }

    /**
     *
     * @return boolean
     */
    public function is_grid_view()
    {
        if (array_key_exists('grid_view', $this->_state)) {
            return make_bool($this->_state['grid_view']);
        }

        return false;
    }

    /**
     *
     * @param boolean $use_alpha
     * @param boolean $savecookie
     */
    public function set_use_alpha($use_alpha, $savecookie = true)
    {
        if ($savecookie) {
            $this->save_cookie_params('alpha', $use_alpha ? 'true' : 'false');
        }
        $this->_state['use_alpha'] = $use_alpha;

        if ($use_alpha) {
            if (count($this->_state['filter']) == 0) {
                $this->set_filter('regex_match', '^A');
            }
        } else {
            $this->set_filter('regex_not_match', '');
        }
    }

    /**
     *
     * @return boolean
     */
    public function is_use_alpha()
    {
        if (array_key_exists('use_alpha', $this->_state)) {
            return make_bool($this->_state['use_alpha']);
        }

        return false;
    }

    /**
     *
     * @param boolean $show_header
     */
    public function set_show_header($show_header)
    {
        $this->show_header = $show_header;
    }

    /**
     * Allow the current page to be save into the current session
     * @param boolean $update_session
     */
    public function set_update_session($update_session)
    {
        $this->_state['update_session'] = $update_session;
    }

    /**
     *
     * @return boolean
     */
    public function is_show_header()
    {
        return $this->show_header;
    }

    /**
     *
     * @return boolean
     */
    public function is_update_session()
    {
        if (array_key_exists('update_session', $this->_state)) {
            return make_bool($this->_state['update_session']);
        }

        return false;
    }

    /**
     *
     * @param string $threshold
     */
    public function set_threshold($threshold)
    {
        $this->_state['threshold'] = $threshold;
    }

    /**
     *
     * @return string
     */
    public function get_threshold()
    {
        if (array_key_exists('threshold', $this->_state)) {
            return (string)$this->_state['threshold'];
        }

        return '';
    }

    /**
     *
     * @return string
     */
    public function get_css_class()
    {
        $css = '';
        if (!$this->_state['grid_view']) {
            $css = 'disablegv';
        }

        return $css;
    }
}
