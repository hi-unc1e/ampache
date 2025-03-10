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
use Ampache\Module\Authorization\Access;
use Ampache\Module\Api\Ajax;
use Ampache\Module\Playback\Stream;
use Ampache\Module\Playlist\PlaylistLoaderInterface;
use Ampache\Module\System\Core;
use Ampache\Module\Util\ObjectTypeToClassNameMapper;
use Ampache\Module\Util\Ui;
use Ampache\Module\Util\ZipHandlerInterface;

$user_id = (!empty(Core::get_global('user'))) ? Core::get_global('user')->id : -1;
?>
<script>
    function ToggleRightbarVisibility()
    {
        if ($("#rightbar").is(":visible")) {
            $("#rightbar").slideUp();
        } else {
            $("#rightbar").slideDown();
        }
    }
</script>
<ul id="rb_action">
    <li>
        <?php echo Ajax::button('?page=stream&action=basket', 'all', T_('Play'), 'rightbar_play'); ?>
    </li>
    <?php if (Access::check('interface', 25)) { ?>
        <li id="pl_add">
            <?php echo Ui::get_icon('playlist_add', T_('Add to playlist')); ?>
            <ul id="pl_action_additems" class="submenu">
                <li>
                    <?php echo Ajax::text('?page=playlist&action=append_item', T_('Add to New Playlist'), 'rb_create_playlist'); ?>
                </li>
            <?php
            global $dic;
            $playlists = $dic->get(PlaylistLoaderInterface::class)->loadByUserId(
                $user_id
            );
            foreach ($playlists as $playlist) {
                ?>
                <li>
                    <?php echo Ajax::text('?page=playlist&action=append_item&playlist_id=' . $playlist->id, $playlist->get_fullname(), 'rb_append_playlist_' . $playlist->id); ?>
                </li>
            <?php
            } ?>
            </ul>
        </li>
    <?php
} ?>
<?php
// @todo remove after refactoring
global $dic;
$zipHandler = $dic->get(ZipHandlerInterface::class);
if (Access::check_function('batch_download') && $zipHandler->isZipable('tmp_playlist')) { ?>
    <li>
        <a class="nohtml" href="<?php echo AmpConfig::get('web_path'); ?>/batch.php?action=tmp_playlist&amp;id=<?php echo Core::get_global('user')->playlist->id; ?>">
            <?php echo Ui::get_icon('batch_download', T_('Batch download')); ?>
        </a>
    </li>
<?php
    } ?>
    <li>
    <?php echo Ajax::button('?action=basket&type=clear_all', 'delete', T_('Clear Playlist'), 'rb_clear_playlist'); ?>
    </li>
    <li id="rb_add">
      <?php echo Ui::get_icon('add', T_('Add dynamic items')); ?>
        <ul id="rb_action_additems" class="submenu">
            <li>
                <?php echo Ajax::text('?page=random&action=song', T_('Random song'), 'rb_add_random_song'); ?>
            </li>
            <li>
                <?php echo Ajax::text('?page=random&action=artist', T_('Random artist'), 'rb_add_random_artist'); ?>
            </li>
            <li>
                <?php echo Ajax::text('?page=random&action=album', T_('Random album'), 'rb_add_random_album'); ?>
            </li>
            <li>
                <?php echo Ajax::text('?page=random&action=playlist', T_('Random playlist'), 'rb_add_random_playlist'); ?>
            </li>
        </ul>
    </li>
</ul>
<?php
    if (AmpConfig::get('play_type') == 'localplay') {
        require_once Ui::find_template('show_localplay_control.inc.php');
    } ?>
<ul id="rb_current_playlist" class="striped-rows">

<?php
    $objects = array();

    // FIXME :: this is kludgy
    if (!defined('NO_SONGS') && !empty(Core::get_global('user')) && Core::get_global('user')->playlist) {
        $objects = Core::get_global('user')->playlist->get_items();
    } ?>
    <script>
        <?php if (count($objects) > 0 || (AmpConfig::get('play_type') == 'localplay')) { ?>
             $("#content").removeClass("content-right-wild", 500);
             $("#footer").removeClass("footer-wild", 500);
             $("#rightbar").removeClass("hidden");
             $("#rightbar").show("slow");
        <?php
} else { ?>
            $("#content").addClass("content-right-wild", 500);
            $("#footer").addClass("footer-wild", 500);
            $("#rightbar").hide("slow");
        <?php
    } ?>
    </script>
<?php
    // Limit the number of objects we show here
    if (count($objects) > 100) {
        $truncated = (count($objects) - 100);
        $objects   = array_slice($objects, 0, 100, true);
    }

    $normal_array = array('live_stream', 'song', 'video', 'random', 'song_preview');

    foreach ($objects as $object_data) {
        $uid  = $object_data['track_id'];
        $type = array_shift($object_data);
        if (in_array($type, $normal_array)) {
            $class_name = ObjectTypeToClassNameMapper::map($type);
            $object     = new $class_name(array_shift($object_data));
            $object->format();
        } ?>
    <li>
      <?php echo $object->f_link; ?>
        <?php echo Ajax::button('?action=current_playlist&type=delete&id=' . $uid, 'delete', T_('Delete'), 'rightbar_delete_' . $uid, '', 'delitem'); ?>
    </li>
<?php
    } if (!count($objects)) { ?>
    <li><span class="nodata"><?php echo T_('No items'); ?></span></li>
<?php
    } ?>
<?php if (isset($truncated)) { ?>
    <li>
        <?php echo $truncated . ' ' . T_('More'); ?>...
    </li>
<?php
    } ?>
</ul>
<?php
// We do a little magic here to force a reload depending on preference
// We do this last because we want it to load, and we want to know if there is anything
// to even pass
if (count($objects)) {
    Stream::run_playlist_method();
} ?>
