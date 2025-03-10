<?php
/* vim:set softtabstop=4 shiftwidth=4 expandtab: */
/**
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

use Ampache\Config\AmpConfig;
use Ampache\Repository\Model\Album;
use Ampache\Repository\Model\Art;
use Ampache\Repository\Model\Rating;
use Ampache\Repository\Model\Share;
use Ampache\Repository\Model\User;
use Ampache\Repository\Model\Userflag;
use Ampache\Module\Api\RefreshReordered\RefreshAlbumSongsAction;
use Ampache\Module\Authorization\Access;
use Ampache\Module\Api\Ajax;
use Ampache\Module\Playback\Stream_Playlist;
use Ampache\Repository\Model\Browse;
use Ampache\Module\Util\Ui;
use Ampache\Module\Util\ZipHandlerInterface;

$web_path = AmpConfig::get('web_path');

/** @var Album $album */
$album->allow_group_disks = true;
// Title for this album
$title = scrub_out($album->f_name);
if ($album->year > 0) {
    $title .= '&nbsp;(' . $album->year . ')';
}
$title .= '&nbsp;-&nbsp;' . (($album->f_album_artist_link) ?: $album->f_artist_link);

$show_direct_play_cfg = AmpConfig::get('directplay');
$show_playlist_add    = Access::check('interface', 25);
$show_direct_play     = $show_direct_play_cfg;
$directplay_limit     = AmpConfig::get('direct_play_limit');
$hide_array           = (AmpConfig::get('hide_single_artist') && $album->artist_count == 1)
    ? array('cel_artist', 'cel_album', 'cel_year', 'cel_drag')
    : array('cel_album', 'cel_year', 'cel_drag');

if ($directplay_limit > 0) {
    $show_playlist_add = ($album->song_count <= $directplay_limit);
    if ($show_direct_play) {
        $show_direct_play = $show_playlist_add;
    }
}

// @todo remove after refactoring
global $dic;
$zipHandler = $dic->get(ZipHandlerInterface::class);
?>
<?php Ui::show_box_top($title, 'info-box'); ?>
<div class="item_right_info">
    <div class="external_links">
        <a href="http://www.google.com/search?q=%22<?php echo rawurlencode($album->f_album_artist_name); ?>%22+%22<?php echo rawurlencode($album->f_name); ?>%22" target="_blank"><?php echo Ui::get_icon('google', T_('Search on Google ...')); ?></a>
        <a href="https://www.duckduckgo.com/s?q=%22<?php echo rawurlencode($album->f_name); ?>%22" target="_blank"><?php echo Ui::get_icon('duckduckgo', T_('Search on DuckDuckGo ...')); ?></a>
        <a href="http://en.wikipedia.org/wiki/Special:Search?search=%22<?php echo rawurlencode($album->f_name); ?>%22&go=Go" target="_blank"><?php echo Ui::get_icon('wikipedia', T_('Search on Wikipedia ...')); ?></a>
        <a href="http://www.last.fm/search?q=%22<?php echo rawurlencode($album->f_album_artist_name); ?>%22+%22<?php echo rawurlencode($album->f_name); ?>%22&type=album" target="_blank"><?php echo Ui::get_icon('lastfm', T_('Search on Last.fm ...')); ?></a>
    <?php if ($album->mbid) { ?>
        <a href="https://musicbrainz.org/release/<?php echo $album->mbid; ?>" target="_blank"><?php echo Ui::get_icon('musicbrainz', T_('Search on Musicbrainz ...')); ?></a>
    <?php } else { ?>
        <a href="https://musicbrainz.org/search?query=%22<?php echo rawurlencode($album->f_name); ?>%22&type=release" target="_blank"><?php echo Ui::get_icon('musicbrainz', T_('Search on Musicbrainz ...')); ?></a>
     <?php } ?>
    </div>
    <?php
    if ($album->name != T_('Unknown (Orphaned)')) {
        $name  = '[' . $album->f_album_artist_name . '] ' . scrub_out($album->f_name);
        $thumb = Ui::is_grid_view('album') ? 32 : 11;
        Art::display('album', $album->id, $name, $thumb);
    } ?>
</div>
<?php if (User::is_registered()) { ?>
    <?php if (AmpConfig::get('ratings')) { ?>
    <span id="rating_<?php echo $album->id; ?>_album">
        <?php echo Rating::show($album->id, 'album'); ?>
    </span>
    <span id="userflag_<?php echo $album->id; ?>_album">
        <?php echo Userflag::show($album->id, 'album'); ?>
    </span>
    <?php
        } ?>
<?php
    } ?>
<?php if (AmpConfig::get('show_played_times')) { ?>
    <br />
    <div style="display:inline;">
        <?php echo T_('Played') . ' ' .
            /* HINT: Number of times an object has been played */
            sprintf(nT_('%d time', '%d times', $album->total_count), $album->total_count); ?>
    </div>
<?php } ?>
<div id="information_actions">
    <h3><?php echo T_('Actions'); ?>:</h3>
    <ul>
        <?php if ($show_direct_play) { ?>
        <li>
            <?php echo Ajax::button_with_text('?page=stream&action=directplay&object_type=album&' . $album->get_http_album_query_ids('object_id'), 'play', T_('Play'), 'directplay_full_'); ?>
        </li>
            <?php if (Stream_Playlist::check_autoplay_next()) { ?>
        <li>
            <?php echo Ajax::button_with_text('?page=stream&action=directplay&object_type=album&' . $album->get_http_album_query_ids('object_id') . '&playnext=true', 'play_next', T_('Play next'), 'nextplay_album_'); ?>
        </li>
            <?php
        } ?>
            <?php if (Stream_Playlist::check_autoplay_append()) { ?>
        <li>
            <?php echo Ajax::button_with_text('?page=stream&action=directplay&object_type=album&' . $album->get_http_album_query_ids('object_id') . '&append=true', 'play_add', T_('Play last'), 'addplay_album_'); ?>
        </li>
            <?php
        } ?>
        <?php
    } ?>
        <?php if ($show_playlist_add) { ?>
        <li>
            <?php echo Ajax::button_with_text('?action=basket&type=album_full&' . $album->get_http_album_query_ids('id'), 'add', T_('Add to Temporary Playlist'), 'play_full_'); ?>
        </li>
        <li>
            <?php echo Ajax::button_with_text('?action=basket&type=album_random&' . $album->get_http_album_query_ids('id'), 'random', T_('Random to Temporary Playlist'), 'play_random_'); ?>
        </li>
        <?php
    } ?>
        <?php if (Access::check('interface', 50)) { ?>
            <li>
                <a href="javascript:NavigateTo('<?php echo $web_path; ?>/albums.php?action=update_group_from_tags&amp;album_id=<?php echo $album->id; ?>');" onclick="return confirm('<?php echo T_('Do you really want to update from tags?'); ?>');">
                    <?php echo Ui::get_icon('file_refresh', T_('Update from tags')); ?>
                    <?php echo T_('Update from tags'); ?>
                </a>
            </li>
            <?php
        } ?>
        <?php if (Access::check_function('batch_download') && $zipHandler->isZipable('album')) { ?>
        <li>
            <a class="nohtml" href="<?php echo $web_path; ?>/batch.php?action=album&<?php echo $album->get_http_album_query_ids('id'); ?>">
                <?php echo Ui::get_icon('batch_download', T_('Download')); ?>
                <?php echo T_('Download'); ?>
            </a>
        </li>
        <?php
    } ?>
    </ul>
</div>
<?php Ui::show_box_bottom(); ?>
<div id="additional_information">
&nbsp;
</div>
<?php
    define('TABLE_RENDERED', 1);
    $album_suite = $album->get_group_disks_ids();
    foreach ($album_suite as $album_id) {
        $c_album = new Album($album_id);
        $c_album->format();
        $c_title           = scrub_out($c_album->name) . "<span class=\"discnb disc" . $c_album->disk . "\">, " . T_('Disk') . " " . $c_album->disk . "</span>";
        $show_direct_play  = $show_direct_play_cfg;
        $show_playlist_add = Access::check('interface', 25);
        if ($directplay_limit > 0) {
            $show_playlist_add = ($c_album->song_count <= $directplay_limit);
            if ($show_direct_play) {
                $show_direct_play = $show_playlist_add;
            }
        } ?>
    <div class="album_group_disks_title"><span> <?php echo $c_title; ?></span></div>
    <div class="album_group_disks_actions">
        <?php
            if ($show_direct_play) {
                echo Ajax::button('?page=stream&action=directplay&object_type=album&' . $c_album->get_http_album_query_id('object_id'), 'play', T_('Play'), 'directplay_full_' . $c_album->id);
                if (Stream_Playlist::check_autoplay_next()) {
                    echo Ajax::button('?page=stream&action=directplay&object_type=album&' . $c_album->get_http_album_query_id('object_id') . '&playnext=true', 'play_next', T_('Play next'), 'nextplay_album_' . $c_album->id);
                }
                if (Stream_Playlist::check_autoplay_append()) {
                    echo Ajax::button('?page=stream&action=directplay&object_type=album&' . $c_album->get_http_album_query_id('object_id') . '&append=true', 'play_add', T_('Play last'), 'addplay_album_' . $c_album->id);
                }
            }
        if ($show_playlist_add) {
            echo Ajax::button('?action=basket&type=album&' . $c_album->get_http_album_query_id('id'), 'add', T_('Add to Temporary Playlist'), 'play_full_' . $c_album->id);
            echo Ajax::button('?action=basket&type=album_random&' . $c_album->get_http_album_query_id('id'), 'random', T_('Random to Temporary Playlist'), 'play_random_' . $c_album->id);
        } ?>
        <?php if (Access::check('interface', 25)) { ?>
            <?php if (AmpConfig::get('sociable')) { ?>
                <a href="<?php echo AmpConfig::get('web_path') ?>/shout.php?action=show_add_shout&type=album&id=<?php echo $c_album->id ?>"><?php echo Ui::get_icon('comment', T_('Post Shout')) ?></a>
            <?php
            } ?>
            <?php if (AmpConfig::get('share')) { ?>
                <?php echo Share::display_ui('album', $c_album->id, false); ?>
            <?php
            } ?>
        <?php
        } ?>
        <?php if (Access::check_function('batch_download') && $zipHandler->isZipable('album')) { ?>
            <a class="nohtml" href="<?php echo $web_path; ?>/batch.php?action=album&<?php echo $c_album->get_http_album_query_id('id'); ?>"><?php echo Ui::get_icon('batch_download', T_('Download')); ?></a>
        <?php
        } ?>
        <?php if (Access::check('interface', 50)) { ?>
            <a onclick="submitNewItemsOrder('<?php echo $c_album->id ?>', 'reorder_songs_table_<?php echo $c_album->id ?>', 'song_',
                                            '<?php echo AmpConfig::get('web_path') ?>/albums.php?action=set_track_numbers', '<?php echo RefreshAlbumSongsAction::REQUEST_KEY; ?>')">
                <?php echo Ui::get_icon('save', T_('Save Track Order')); ?>
            </a>
            <a href="javascript:NavigateTo('<?php echo $web_path ?>/albums.php?action=update_from_tags&amp;album_id=<?php echo $c_album->id ?>');" onclick="return confirm('<?php echo T_('Do you really want to update from tags?') ?>');">
                <?php echo Ui::get_icon('file_refresh', T_('Update from tags')); ?>
            </a>
            <a id="<?php echo 'edit_album_' . $c_album->id ?>" onclick="showEditDialog('album_row', '<?php echo $c_album->id ?>', '<?php echo 'edit_album_' . $c_album->id ?>', '<?php echo addslashes(T_('Album Edit')) ?>', '')">
                <?php echo Ui::get_icon('edit', T_('Edit')); ?>
            </a>
        <?php
        } ?>
    </div>
    <div id='reordered_list_<?php echo $album_id; ?>'>
    <?php
        $browse = new Browse();
        $browse->set_show_header(false);
        $browse->set_type('song');
        $browse->set_simple_browse(true);
        $browse->set_filter('album', $album_id);
        $browse->set_sort('track', 'ASC');
        $browse->get_objects();
        $browse->show_objects(null, array('hide' => $hide_array));
        $browse->store(); ?>
    </div><br />
<?php
    } ?>
