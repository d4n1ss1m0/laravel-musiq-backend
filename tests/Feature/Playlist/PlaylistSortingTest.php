<?php

namespace Tests\Feature\Playlist;

use App\DTO\Playlist\PlaylistTrackDTO;
use App\Enum\PlaybackSource;
use App\Enum\PlaylistOrderType;
use App\Repositories\Playlist\PlaylistRepository;
use App\Service\PlaybackService\PlaybackService;
use App\Service\PlaylistService\PlaylistService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlaylistSortingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite is required.');
        }
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        foreach (['users', 'playlist_types', 'playlists', 'tracks', 'artists', 'track_artists'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name) {
                $table->id();
                if (in_array($name, ['playlist_types', 'tracks', 'artists'])) {
                    $table->string('name');
                }
                if (in_array($name, ['playlists', 'tracks'])) {
                    $table->string('uuid');
                }
                if ($name === 'playlists') {
                    $table->integer('type');
                    $table->integer('user_id');
                }
                if ($name === 'tracks') {
                    $table->integer('time')->default(10);
                }
                if ($name === 'track_artists') {
                    $table->integer('track_id');
                    $table->integer('artist_id');
                }
            });
        }
        (require database_path('migrations/2024_10_26_160038_create_track_playlists_table.php'))->up();
        (require database_path('migrations/2026_05_10_064140_add_order_in_track_playlists_table.php'))->up();
        (require database_path('migrations/2026_11_28_080000_create_user_playlist_settings_table.php'))->up();
        DB::table('users')->insert(['id' => 1]);
        DB::table('playlist_types')->insert([['id' => 1, 'name' => 'public'], ['id' => 2, 'name' => 'favourite']]);
        DB::table('playlists')->insert(['id' => 1, 'uuid' => 'playlist', 'type' => 1, 'user_id' => 1]);
        DB::table('tracks')->insert([['id' => 1, 'uuid' => 'one', 'name' => 'Zulu'], ['id' => 2, 'uuid' => 'two', 'name' => 'Alpha']]);
        $this->app->make(PlaylistRepository::class)->addTracks(1, [new PlaylistTrackDTO(1, 1), new PlaylistTrackDTO(2, 2)]);
    }

    public function test_saved_sort_is_shared_by_metadata_list_queue_and_playback(): void
    {
        $service = $this->app->make(PlaylistService::class);
        $service->saveSort(1, 'playlist', PlaylistOrderType::TITLE);
        $this->assertSame('title', $service->getPlaylist('playlist', 1)->sort);
        $this->assertSame([2, 1], $service->getTracks('playlist', 1)->pluck('id')->all());
        $this->assertSame(['two', 'one'], array_column($service->getQueue('playlist', 1), 'track_id'));
        $method = new \ReflectionMethod(PlaybackService::class, 'getTracks');
        $tracks = $method->invoke($this->app->make(PlaybackService::class), PlaybackSource::PLAYLIST, 'playlist', 'one', 1);
        $this->assertSame(['two', 'one'], $tracks->pluck('uuid')->all());
        $service->saveSort(1, 'playlist', PlaylistOrderType::ADDED);
        $this->assertSame(1, DB::table('user_playlist_settings')->count());
        $service->saveSort(1, 'playlist', PlaylistOrderType::POSITION);
        $this->assertSame(0, DB::table('user_playlist_settings')->count());
    }

    public function test_favourite_ignores_saved_setting_and_uses_added_desc(): void
    {
        $service = $this->app->make(PlaylistService::class);
        $service->saveSort(1, 'playlist', PlaylistOrderType::POSITION);
        DB::table('playlists')->where('id', 1)->update(['type' => 2]);
        DB::table('user_playlist_settings')->insert(['user_id' => 1, 'playlist_id' => 1, 'sort' => 'position']);
        $this->assertSame('added', $service->getPlaylist('playlist', 1)->sort);
        $this->assertFalse($service->getPlaylist('playlist', 1)->canChangeSort);
        $this->assertSame([2, 1], $service->getTracks('playlist', 1)->pluck('id')->all());
        $this->expectException(ValidationException::class);
        $service->saveSort(1, 'playlist', PlaylistOrderType::TITLE);
    }

    public function test_addition_preserves_full_timestamp(): void
    {
        Carbon::setTestNow('2026-11-28 14:35:47');
        try {
            DB::table('track_playlists')->delete();
            $this->app->make(PlaylistRepository::class)->addTracks(1, [new PlaylistTrackDTO(1, 1)]);
            $this->assertSame('2026-11-28 14:35:47', DB::table('track_playlists')->value('created_at'));
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_artist_sort_uses_first_artist_and_keeps_tracks_without_artists(): void
    {
        DB::table('artists')->insert([
            ['id' => 1, 'name' => 'Zulu'],
            ['id' => 2, 'name' => 'Aaron'],
            ['id' => 3, 'name' => 'Middle'],
        ]);
        DB::table('track_artists')->insert([
            ['id' => 1, 'track_id' => 1, 'artist_id' => 1],
            ['id' => 2, 'track_id' => 1, 'artist_id' => 2],
            ['id' => 3, 'track_id' => 2, 'artist_id' => 3],
        ]);
        $repository = $this->app->make(PlaylistRepository::class);
        $this->assertSame([2, 1], $repository->getPlaylistTracks('playlist', PlaylistOrderType::ARTIST)->pluck('tracks.id')->all());
        DB::table('track_artists')->where('track_id', 2)->delete();
        $this->assertCount(2, $repository->getPlaylistTracks('playlist', PlaylistOrderType::ARTIST)->get());
    }

    public function test_missing_selected_track_is_rejected_before_shuffle(): void
    {
        $method = new \ReflectionMethod(PlaybackService::class, 'getTracks');
        $this->expectException(ValidationException::class);
        $method->invoke($this->app->make(PlaybackService::class), PlaybackSource::PLAYLIST, 'playlist', 'missing', 1, true);
    }
}
