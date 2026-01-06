<?php

namespace App\Providers;


use App\Repositories\Playlist\PlaylistRepository;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Repositories\RecentlyPlayedTrack\RecentlyPlayedTrackRepository;
use App\Repositories\RecentlyPlayedTrack\RecentlyPlayedTrackRepositoryInterface;
use App\Service\AuthService\AuthService;
use App\Service\AuthService\AuthServiceInterface;
use App\Service\FileService\FileService;
use App\Service\FileService\FileServiceInterface;
use App\Service\ImageService\ImageService;
use App\Service\ImageService\ImageServiceInterface;
use App\Service\JwtService\JwtService;
use App\Service\JwtService\JwtServiceInterface;
use App\Service\MainPage\RecentlyAddedTracks\RecentlyAddedTracksService;
use App\Service\MainPage\RecentlyAddedTracks\RecentlyAddedTracksServiceInterface;
use App\Service\MainPage\RecentlyPlayedPlaylists\RecentlyPlayedPlaylistsService;
use App\Service\MainPage\RecentlyPlayedPlaylists\RecentlyPlayedPlaylistsServiceInterface;
use App\Service\MainPage\RecentlyPlayedTracks\RecentlyPlayedTracksService;
use App\Service\MainPage\RecentlyPlayedTracks\RecentlyPlayedTracksServiceInterface;
use App\Service\MusicStream\MusicStreamService;
use App\Service\MusicStream\MusicStreamServiceInterface;
use App\Repositories\Artist\ArtistRepository;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\Track\TrackRepository;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Service\PlaylistService\PlaylistService;
use App\Service\PlaylistService\PlaylistServiceInterface;
use App\Service\TrackService\TrackService;
use App\Service\TrackService\TrackServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Services
        $this->app->bind(TrackServiceInterface::class, TrackService::class);
        $this->app->bind(PlaylistServiceInterface::class, PlaylistService::class);
        $this->app->bind(RecentlyAddedTracksServiceInterface::class, RecentlyAddedTracksService::class);
        $this->app->bind(RecentlyPlayedPlaylistsServiceInterface::class, RecentlyPlayedPlaylistsService::class);
        $this->app->bind(RecentlyPlayedTracksServiceInterface::class, RecentlyPlayedTracksService::class);

        //Repositories
        $this->app->bind(ArtistRepositoryInterface::class, ArtistRepository::class);
        $this->app->bind(TrackRepositoryInterface::class, TrackRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RecentlyPlayedTrackRepositoryInterface::class, RecentlyPlayedTrackRepository::class);
        $this->app->bind(PlaylistRepositoryInterface::class, PlaylistRepository::class);

        //Infrastructure
        $this->app->bind(ImageServiceInterface::class, ImageService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(MusicStreamServiceInterface::class, MusicStreamService::class);
        $this->app->bind(JwtServiceInterface::class, JwtService::class);
        $this->app->bind(FileServiceInterface::class, FileService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
