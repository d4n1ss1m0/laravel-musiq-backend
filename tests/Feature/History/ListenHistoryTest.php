<?php

namespace Tests\Feature\History;

use App\DTO\History\ListenHistoryDTO;
use App\Models\Track;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\History\HistoryRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Service\HistoryService\HistoryService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Tests\TestCase;

class ListenHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension is required for this test.');
        }

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
        });

        Schema::create('listening_history', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('track_id');
            $table->nullableMorphs('source');
            $table->date('played_date');
            $table->timestamp('last_played_at');
            $table->unsignedInteger('play_count')->default(1);
            $table->unsignedBigInteger('listened_seconds')->default(0);
            $table->timestamps();
        });

        Schema::create('listening_attempts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('history_item_id');
            $table->unsignedInteger('credited_seconds')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('last_reported_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_progress_beyond_track_duration_is_credited_after_attempt_was_finished(): void
    {
        DB::table('users')->insert(['id' => 1]);

        $track = new Track;
        $track->id = 10;
        $track->uuid = '0199a2d0-28cf-7000-8000-000000000001';
        $track->time = 180;

        $trackRepository = $this->mock(
            TrackRepositoryInterface::class,
            function (MockInterface $mock) use ($track): void {
                $mock->shouldReceive('getTrackByUuids')
                    ->twice()
                    ->andReturn(collect([$track]));
            },
        );

        $service = new HistoryService(
            $this->mock(HistoryRepositoryInterface::class),
            $this->mock(PlaylistRepositoryInterface::class),
            $this->mock(ArtistRepositoryInterface::class),
            $trackRepository,
        );

        $startedAt = CarbonImmutable::parse('2026-09-25T10:00:00Z');
        $attemptId = '0199a2d0-28cf-7000-8000-000000000002';

        $service->listen(new ListenHistoryDTO(
            userId: 1,
            attemptId: $attemptId,
            trackId: $track->uuid,
            source: null,
            sourceId: null,
            startedAt: $startedAt,
            listenedSeconds: 170,
            finished: true,
        ));

        $result = $service->listen(new ListenHistoryDTO(
            userId: 1,
            attemptId: $attemptId,
            trackId: $track->uuid,
            source: null,
            sourceId: null,
            startedAt: $startedAt,
            listenedSeconds: 200,
        ));

        $this->assertSame(30, $result['creditedSeconds']);
        $this->assertSame(200, $result['attemptListenedSeconds']);
        $this->assertSame(200, $result['listenedSeconds']);
        $this->assertTrue($result['finished']);
        $this->assertDatabaseHas('listening_attempts', [
            'uuid' => $attemptId,
            'credited_seconds' => 200,
        ]);
    }
}
