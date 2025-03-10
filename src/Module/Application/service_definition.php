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

namespace Ampache\Module\Application;

use function DI\autowire;

return [
    ApplicationRunner::class => autowire(ApplicationRunner::class),
    Song\DeleteAction::class => autowire(Song\DeleteAction::class),
    Song\ConfirmDeleteAction::class => autowire(Song\ConfirmDeleteAction::class),
    Song\ShowLyricsAction::class => autowire(Song\ShowLyricsAction::class),
    Song\ShowSongAction::class => autowire(Song\ShowSongAction::class),
    Album\DeleteAction::class => autowire(Album\DeleteAction::class),
    Album\ConfirmDeleteAction::class => autowire(Album\ConfirmDeleteAction::class),
    Album\UpdateFromTagsAction::class => autowire(Album\UpdateFromTagsAction::class),
    Album\UpdateGroupFromTagsAction::class => autowire(Album\UpdateGroupFromTagsAction::class),
    Album\SetTrackNumbersAction::class => autowire(Album\SetTrackNumbersAction::class),
    Album\ShowMissingAction::class => autowire(Album\ShowMissingAction::class),
    Album\ShowAction::class => autowire(Album\ShowAction::class),
    Artist\DeleteAction::class => autowire(Artist\DeleteAction::class),
    Artist\ConfirmDeleteAction::class => autowire(Artist\ConfirmDeleteAction::class),
    Artist\ShowAction::class => autowire(Artist\ShowAction::class),
    Artist\ShowAllSongsAction::class => autowire(Artist\ShowAllSongsAction::class),
    Artist\UpdateFromTagsAction::class => autowire(Artist\UpdateFromTagsAction::class),
    Artist\ShowMissingAction::class => autowire(Artist\ShowMissingAction::class),
    Stats\ShowUserAction::class => autowire(Stats\ShowUserAction::class),
    Stats\NewestAction::class => autowire(Stats\NewestAction::class),
    Stats\PopularAction::class => autowire(Stats\PopularAction::class),
    Stats\HighestAction::class => autowire(Stats\HighestAction::class),
    Stats\UserflagAction::class => autowire(Stats\UserflagAction::class),
    Stats\RecentAction::class => autowire(Stats\RecentAction::class),
    Stats\ShareAction::class => autowire(Stats\ShareAction::class),
    Stats\UploadAction::class => autowire(Stats\UploadAction::class),
    Stats\GraphAction::class => autowire(Stats\GraphAction::class),
    Stats\ShowAction::class => autowire(Stats\ShowAction::class),
    Logout\LogoutAction::class => autowire(Logout\LogoutAction::class),
    Rss\ShowAction::class => autowire(Rss\ShowAction::class),
    Shout\AddShoutAction::class => autowire(Shout\AddShoutAction::class),
    Shout\ShowAddShoutAction::class => autowire(Shout\ShowAddShoutAction::class),
    Shout\ShowAction::class => autowire(Shout\ShowAction::class),
    Waveform\ShowAction::class => autowire(Waveform\ShowAction::class),
    Search\SearchAction::class => autowire(Search\SearchAction::class),
    Search\SaveAsSmartPlaylistAction::class => autowire(Search\SaveAsSmartPlaylistAction::class),
    Search\SaveAsPlaylistAction::class => autowire(Search\SaveAsPlaylistAction::class),
    Search\DescriptorAction::class => autowire(Search\DescriptorAction::class),
    Search\ShowAction::class => autowire(Search\ShowAction::class),
    CookieDisclaimer\ShowAction::class => autowire(CookieDisclaimer\ShowAction::class),
    DemocraticPlayback\ManageAction::class => autowire(DemocraticPlayback\ManageAction::class),
    DemocraticPlayback\ShowCreateAction::class => autowire(DemocraticPlayback\ShowCreateAction::class),
    DemocraticPlayback\DeleteAction::class => autowire(DemocraticPlayback\DeleteAction::class),
    DemocraticPlayback\CreateAction::class => autowire(DemocraticPlayback\CreateAction::class),
    DemocraticPlayback\ManagePlaylistsAction::class => autowire(DemocraticPlayback\ManagePlaylistsAction::class),
    DemocraticPlayback\ShowPlaylistAction::class => autowire(DemocraticPlayback\ShowPlaylistAction::class),
    WebPlayer\ShowAction::class => autowire(WebPlayer\ShowAction::class),
    WebPlayer\ShowEmbeddedAction::class => autowire(WebPlayer\ShowEmbeddedAction::class),
    Index\ShowAction::class => autowire(Index\ShowAction::class),
    Utility\ShowAction::class => autowire(Utility\ShowAction::class),
    Update\ShowAction::class => autowire(Update\ShowAction::class),
    Update\UpdateAction::class => autowire(Update\UpdateAction::class),
    Video\DeleteAction::class => autowire(Video\DeleteAction::class),
    Video\ConfirmDeleteAction::class => autowire(Video\ConfirmDeleteAction::class),
    Video\ShowVideoAction::class => autowire(Video\ShowVideoAction::class),
    TvShowSeason\DeleteAction::class => autowire(TvShowSeason\DeleteAction::class),
    TvShowSeason\ConfirmDeleteAction::class => autowire(TvShowSeason\ConfirmDeleteAction::class),
    TvShowSeason\ShowAction::class => autowire(TvShowSeason\ShowAction::class),
    Label\DeleteAction::class => autowire(Label\DeleteAction::class),
    Label\ConfirmDeleteAction::class => autowire(Label\ConfirmDeleteAction::class),
    Label\AddLabelAction::class => autowire(Label\AddLabelAction::class),
    Label\ShowAddLabelAction::class => autowire(Label\ShowAddLabelAction::class),
    Label\ShowAction::class => autowire(Label\ShowAction::class),
    Share\ShowCreateAction::class => autowire(Share\ShowCreateAction::class),
    Share\CreateAction::class => autowire(Share\CreateAction::class),
    Share\ShowDeleteAction::class => autowire(Share\ShowDeleteAction::class),
    Share\DeleteAction::class => autowire(Share\DeleteAction::class),
    Share\CleanAction::class => autowire(Share\CleanAction::class),
    Share\ExternalShareAction::class => autowire(Share\ExternalShareAction::class),
    Share\ConsumeAction::class => autowire(Share\ConsumeAction::class),
    Broadcast\ShowDeleteAction::class => autowire(Broadcast\ShowDeleteAction::class),
    Broadcast\DeleteAction::class => autowire(Broadcast\DeleteAction::class),
    Radio\ShowCreateAction::class => autowire(Radio\ShowCreateAction::class),
    Radio\CreateAction::class => autowire(Radio\CreateAction::class),
    Radio\ShowAction::class => autowire(Radio\ShowAction::class),
    Image\ShowAction::class => autowire(Image\ShowAction::class),
    Channel\ShowCreateAction::class => autowire(Channel\ShowCreateAction::class),
    Channel\CreateAction::class => autowire(Channel\CreateAction::class),
    Channel\ShowDeleteAction::class => autowire(Channel\ShowDeleteAction::class),
    Channel\DeleteAction::class => autowire(Channel\DeleteAction::class),
    Mashup\ShowAction::class => autowire(Mashup\ShowAction::class),
    Podcast\ShowCreateAction::class => autowire(Podcast\ShowCreateAction::class),
    Podcast\CreateAction::class => autowire(Podcast\CreateAction::class),
    Podcast\DeleteAction::class => autowire(Podcast\DeleteAction::class),
    Podcast\ConfirmDeleteAction::class => autowire(Podcast\ConfirmDeleteAction::class),
    Podcast\ShowAction::class => autowire(Podcast\ShowAction::class),
    PodcastEpisode\DeleteAction::class => autowire(PodcastEpisode\DeleteAction::class),
    PodcastEpisode\ConfirmDeleteAction::class => autowire(PodcastEpisode\ConfirmDeleteAction::class),
    PodcastEpisode\ShowAction::class => autowire(PodcastEpisode\ShowAction::class),
    Upload\DefaultAction::class => autowire(Upload\DefaultAction::class),
    NowPlaying\ShowAction::class => autowire(NowPlaying\ShowAction::class),
    LostPassword\ShowAction::class => autowire(LostPassword\ShowAction::class),
    LostPassword\SendAction::class => autowire(LostPassword\SendAction::class),
    PrivateMessage\ShowAction::class => autowire(PrivateMessage\ShowAction::class),
    PrivateMessage\ConfirmDeleteAction::class => autowire(PrivateMessage\ConfirmDeleteAction::class),
    PrivateMessage\DeleteAction::class => autowire(PrivateMessage\DeleteAction::class),
    PrivateMessage\SetIsReadAction::class => autowire(PrivateMessage\SetIsReadAction::class),
    PrivateMessage\AddMessageAction::class => autowire(PrivateMessage\AddMessageAction::class),
    PrivateMessage\ShowAddMessageAction::class => autowire(PrivateMessage\ShowAddMessageAction::class),
    Test\ShowAction::class => autowire(Test\ShowAction::class),
    Test\ConfigAction::class => autowire(Test\ConfigAction::class),
    TvShow\ShowAction::class => autowire(TvShow\ShowAction::class),
    TvShow\ConfirmDeleteAction::class => autowire(TvShow\ConfirmDeleteAction::class),
    TvShow\DeleteAction::class => autowire(TvShow\DeleteAction::class),
    Stream\DownloadAction::class => autowire(Stream\DownloadAction::class),
    Stream\DemocraticAction::class => autowire(Stream\DemocraticAction::class),
    Stream\PlaylistRandomAction::class => autowire(Stream\PlaylistRandomAction::class),
    Stream\AlbumRandomAction::class => autowire(Stream\AlbumRandomAction::class),
    Stream\ArtistRandomAction::class => autowire(Stream\ArtistRandomAction::class),
    Stream\PlayItemAction::class => autowire(Stream\PlayItemAction::class),
    Stream\PlayFavoriteAction::class => autowire(Stream\PlayFavoriteAction::class),
    Stream\TmpPlaylistAction::class => autowire(Stream\TmpPlaylistAction::class),
    Stream\BasketAction::class => autowire(Stream\BasketAction::class),
    PhpInfo\ShowAction::class => autowire(PhpInfo\ShowAction::class),
    ShowGet\ShowAction::class => autowire(ShowGet\ShowAction::class),
    SearchData\ShowAction::class => autowire(SearchData\ShowAction::class),
    Register\ValidateAction::class => autowire(Register\ValidateAction::class),
    Register\ShowAddUserAction::class => autowire(Register\ShowAddUserAction::class),
    Register\AddUserAction::class => autowire(Register\AddUserAction::class),
    StatisticGraph\ShowAction::class => autowire(StatisticGraph\ShowAction::class),
    Random\AdvancedAction::class => autowire(Random\AdvancedAction::class),
    Random\GetAdvancedAction::class => autowire(Random\GetAdvancedAction::class),
    Batch\DefaultAction::class => autowire(Batch\DefaultAction::class),
    SmartPlaylist\ShowAction::class => autowire(SmartPlaylist\ShowAction::class),
    SmartPlaylist\UpdatePlaylistAction::class => autowire(SmartPlaylist\UpdatePlaylistAction::class),
    SmartPlaylist\ShowPlaylistAction::class => autowire(SmartPlaylist\ShowPlaylistAction::class),
    SmartPlaylist\DeletePlaylistAction::class => autowire(SmartPlaylist\DeletePlaylistAction::class),
    SmartPlaylist\CreatePlaylistAction::class => autowire(SmartPlaylist\CreatePlaylistAction::class),
    Playlist\ShowAction::class => autowire(Playlist\ShowAction::class),
    Playlist\SortTrackAction::class => autowire(Playlist\SortTrackAction::class),
    Playlist\RemoveDuplicatesAction::class => autowire(Playlist\RemoveDuplicatesAction::class),
    Playlist\AddSongAction::class => autowire(Playlist\AddSongAction::class),
    Playlist\SetTrackNumbersAction::class => autowire(Playlist\SetTrackNumbersAction::class),
    Playlist\ImportPlaylistAction::class => autowire(Playlist\ImportPlaylistAction::class),
    Playlist\ShowImportPlaylistAction::class => autowire(Playlist\ShowImportPlaylistAction::class),
    Playlist\ShowPlaylistAction::class => autowire(Playlist\ShowPlaylistAction::class),
    Playlist\DeletePlaylistAction::class => autowire(Playlist\DeletePlaylistAction::class),
    Playlist\RefreshPlaylistAction::class => autowire(Playlist\RefreshPlaylistAction::class),
    Playlist\CreatePlaylistAction::class => autowire(Playlist\CreatePlaylistAction::class),
    Installation\DefaultAction::class => autowire(Installation\DefaultAction::class),
    Preferences\UpdateUserAction::class => autowire(Preferences\UpdateUserAction::class),
    Preferences\UserAction::class => autowire(Preferences\UserAction::class),
    Preferences\ShowAction::class => autowire(Preferences\ShowAction::class),
    Preferences\AdminAction::class => autowire(Preferences\AdminAction::class),
    Preferences\AdminUpdatePreferencesAction::class => autowire(Preferences\AdminUpdatePreferencesAction::class),
    Preferences\UpdatePreferencesAction::class => autowire(Preferences\UpdatePreferencesAction::class),
    Preferences\GrantAction::class => autowire(Preferences\GrantAction::class),
    Login\DefaultAction::class => autowire(Login\DefaultAction::class),
    LocalPlay\ShowAddInstanceAction::class => autowire(LocalPlay\ShowAddInstanceAction::class),
    LocalPlay\ShowPlaylistAction::class => autowire(LocalPlay\ShowPlaylistAction::class),
    LocalPlay\AddInstanceAction::class => autowire(LocalPlay\AddInstanceAction::class),
    LocalPlay\UpdateInstanceAction::class => autowire(LocalPlay\UpdateInstanceAction::class),
    LocalPlay\EditInstanceAction::class => autowire(LocalPlay\EditInstanceAction::class),
    LocalPlay\ShowInstancesAction::class => autowire(LocalPlay\ShowInstancesAction::class),
    Browse\TagAction::class => autowire(Browse\TagAction::class),
    Browse\FileAction::class => autowire(Browse\FileAction::class),
    Browse\AlbumAction::class => autowire(Browse\AlbumAction::class),
    Browse\AlbumArtistAction::class => autowire(),
    Browse\ArtistAction::class => autowire(Browse\ArtistAction::class),
    Browse\SongAction::class => autowire(Browse\SongAction::class),
    Browse\PlaylistAction::class => autowire(Browse\PlaylistAction::class),
    Browse\SmartPlaylistAction::class => autowire(Browse\SmartPlaylistAction::class),
    Browse\TvShowSeasonAction::class => autowire(Browse\TvShowSeasonAction::class),
    Browse\PodcastEpisodeAction::class => autowire(Browse\PodcastEpisodeAction::class),
    Browse\CatalogAction::class => autowire(Browse\CatalogAction::class),
    Browse\PrivateMessageAction::class => autowire(Browse\PrivateMessageAction::class),
    Browse\LiveStreamAction::class => autowire(Browse\LiveStreamAction::class),
    Browse\TvShowAction::class => autowire(Browse\TvShowAction::class),
    Browse\LabelAction::class => autowire(Browse\LabelAction::class),
    Browse\ChannelAction::class => autowire(Browse\ChannelAction::class),
    Browse\BroadcastAction::class => autowire(Browse\BroadcastAction::class),
    Browse\VideoAction::class => autowire(Browse\VideoAction::class),
    Browse\PodcastAction::class => autowire(Browse\PodcastAction::class),
    Browse\TvShowEpisodeAction::class => autowire(Browse\TvShowEpisodeAction::class),
    Browse\MovieAction::class => autowire(Browse\MovieAction::class),
    Browse\ClipAction::class => autowire(Browse\ClipAction::class),
    Browse\PersonalVideoAction::class => autowire(Browse\PersonalVideoAction::class),
    Art\ClearArtAction::class => autowire(Art\ClearArtAction::class),
    Art\ShowArtDlgAction::class => autowire(Art\ShowArtDlgAction::class),
    Art\FindArtAction::class => autowire(Art\FindArtAction::class),
    Art\UploadArtAction::class => autowire(Art\UploadArtAction::class),
    Art\SelectArtAction::class => autowire(Art\SelectArtAction::class),
    Playback\PlayAction::class => autowire(Playback\PlayAction::class),
    Playback\ChannelAction::class => autowire(Playback\ChannelAction::class),
    Admin\Mail\ShowAction::class => autowire(Admin\Mail\ShowAction::class),
    Admin\Mail\SendMailAction::class => autowire(Admin\Mail\SendMailAction::class),
    Admin\Export\ShowAction::class => autowire(),
    Admin\Export\ExportAction::class => autowire(),
    Admin\Access\ShowAction::class => autowire(),
    Admin\Access\ShowAddAdvancedAction::class => autowire(),
    Admin\Access\ShowDeleteRecordAction::class => autowire(),
    Admin\Access\UpdateRecordAction::class => autowire(),
    Admin\Access\AddHostAction::class => autowire(),
    Admin\Access\DeleteRecordAction::class => autowire(),
    Admin\Access\ShowEditRecordAction::class => autowire(),
    Admin\Access\ShowAddAction::class => autowire(),
    Admin\Catalog\ShowAddCatalogAction::class => autowire(),
    Admin\Catalog\ShowDisabledAction::class => autowire(),
    Admin\Catalog\ShowCustomizeCatalogAction::class => autowire(),
    Admin\Catalog\ShowCatalogsAction::class => autowire(),
    Admin\Catalog\ClearStatsAction::class => autowire(),
    Admin\Catalog\ClearNowPlayingAction::class => autowire(),
    Admin\Catalog\DeleteCatalogAction::class => autowire(),
    Admin\Catalog\ShowDeleteCatalogAction::class => autowire(),
    Admin\Catalog\AddToAllCatalogsAction::class => autowire(),
    Admin\Catalog\UpdateCatalogAction::class => autowire(),
    Admin\Catalog\FullServiceAction::class => autowire(),
    Admin\Catalog\AddToCatalogAction::class => autowire(),
    Admin\Catalog\CleanAllCatalogsAction::class => autowire(),
    Admin\Catalog\CleanCatalogAction::class => autowire(),
    Admin\Catalog\UpdateFileTagsAction::class => autowire(),
    Admin\Catalog\UpdateAllFileTagsActions::class => autowire(),
    Admin\Catalog\GatherMediaArtAction::class => autowire(),
    Admin\Catalog\ImportToCatalogAction::class => autowire(),
    Admin\Catalog\AddCatalogAction::class => autowire(),
    Admin\Catalog\UpdateFromAction::class => autowire(),
    Admin\Catalog\UpdateAllCatalogsAction::class => autowire(),
    Admin\Catalog\EnableDisabledAction::class => autowire(),
    Admin\Catalog\UpdateCatalogSettingsAction::class => autowire(),
    Admin\Index\ShowAction::class => autowire(),
    Admin\License\ShowAction::class => autowire(),
    Admin\License\DeleteAction::class => autowire(),
    Admin\License\ShowCreateAction::class => autowire(),
    Admin\License\ShowEditAction::class => autowire(),
    Admin\License\EditAction::class => autowire(),
    Admin\Shout\ShowAction::class => autowire(),
    Admin\Shout\DeleteAction::class => autowire(),
    Admin\Shout\ShowEditAction::class => autowire(),
    Admin\Shout\EditShoutAction::class => autowire(),
    Admin\Modules\InstallLocalplayAction::class => autowire(),
    Admin\Modules\ShowAction::class => autowire(),
    Admin\Modules\InstallCatalogTypeAction::class => autowire(),
    Admin\Modules\ConfirmUninstallLocalplayAction::class => autowire(),
    Admin\Modules\ConfirmUninstallCatalogType::class => autowire(),
    Admin\Modules\UninstallLocalplayAction::class => autowire(),
    Admin\Modules\UninstallCatalogTypeAction::class => autowire(),
    Admin\Modules\InstallPluginAction::class => autowire(),
    Admin\Modules\ConfirmUninstallPluginAction::class => autowire(),
    Admin\Modules\UninstallPluginAction::class => autowire(),
    Admin\Modules\UpgradePluginAction::class => autowire(),
    Admin\Modules\ShowPluginsAction::class => autowire(),
    Admin\Modules\ShowLocalplayAction::class => autowire(),
    Admin\Modules\ShowCatalogTypesAction::class => autowire(),
    Admin\System\GenerateConfigAction::class => autowire(),
    Admin\System\WriteConfigAction::class => autowire(),
    Admin\System\ResetDbCharsetAction::class => autowire(),
    Admin\System\ShowDebugAction::class => autowire(),
    Admin\System\ClearCacheAction::class => autowire(),
    Admin\User\ShowAction::class => autowire(),
    Admin\User\ShowPreferencesAction::class => autowire(),
    Admin\User\ShowAddUserAction::class => autowire(),
    Admin\User\ShowIpHistoryAction::class => autowire(),
    Admin\User\GenerateRsstokenAction::class => autowire(),
    Admin\User\ShowGenerateRsstokenAction::class => autowire(),
    Admin\User\GenerateApikeyAction::class => autowire(),
    Admin\User\ShowGenerateApikeyAction::class => autowire(),
    Admin\User\DeleteAvatarAction::class => autowire(),
    Admin\User\ShowDeleteAvatarAction::class => autowire(),
    Admin\User\DeleteAction::class => autowire(),
    Admin\User\ConfirmDeleteAction::class => autowire(),
    Admin\User\ShowEditAction::class => autowire(),
    Admin\User\DisableAction::class => autowire(),
    Admin\User\EnableAction::class => autowire(),
    Admin\User\AddUserAction::class => autowire(),
    Admin\User\UpdateUserAction::class => autowire(),
];
