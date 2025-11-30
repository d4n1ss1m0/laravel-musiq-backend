<?php

namespace App\Providers;

use App\Application\UseCase\AddTrack\AddTrackByFile\AddTrackByFile;
use App\Application\UseCase\AddTrack\AddTrackByFile\AddTrackByFileInterface;
use App\Application\UseCase\AddTrack\CreateArtist\AddArtistsToTrack;
use App\Application\UseCase\AddTrack\CreateArtist\AddArtistsToTrackInterface;
use App\Application\UseCase\Auth\LoginUser\LoginUser;
use App\Application\UseCase\Auth\LoginUser\LoginUserInterface;
use App\Application\UseCase\Auth\RefreshToken\RefreshToken;
use App\Application\UseCase\Auth\RefreshToken\RefreshTokenInterface;
use App\Application\UseCase\Player\MusicStream\MusicStream;
use App\Application\UseCase\Player\MusicStream\MusicStreamInterface;
use App\Domain\Repositories\Artist\ArtistRepositoryInterface;
use App\Domain\Repositories\Track\TrackRepositoryInterface;
use App\Domain\Repositories\User\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\Artist\ArtistRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\Track\TrackRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\User\UserRepository;
use App\Infrastructure\Services\Auth\JwtService\JwtService;
use App\Infrastructure\Services\Auth\JwtService\JwtServiceInterface;
use App\Infrastructure\Services\MusicStream\MusicStreamService;
use App\Infrastructure\Services\MusicStream\MusicStreamServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //UseCase
        $this->app->bind(RefreshTokenInterface::class, RefreshToken::class);
        $this->app->bind(LoginUserInterface::class, LoginUser::class);
        $this->app->bind(AddTrackByFileInterface::class, AddTrackByFile::class);
        $this->app->bind(AddArtistsToTrackInterface::class, AddArtistsToTrack::class);
        $this->app->bind(MusicStreamInterface::class, MusicStream::class);

        //Repositories
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TrackRepositoryInterface::class, TrackRepository::class);
        $this->app->bind(ArtistRepositoryInterface::class, ArtistRepository::class);

        //Infrastructure
        $this->app->bind(JwtServiceInterface::class, JwtService::class);
        $this->app->bind(MusicStreamServiceInterface::class, MusicStreamService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
