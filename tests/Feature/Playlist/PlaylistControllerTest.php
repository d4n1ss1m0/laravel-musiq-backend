<?php

namespace Tests\Feature\Playlist;

use App\Enum\PlaylistOrderType;
use App\Http\Controllers\Playlist\PlaylistController;
use App\Service\FileService\FileServiceInterface;
use App\Service\PlaylistService\PlaylistServiceInterface;
use App\Shared\Fields\Fields;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlaylistControllerTest extends TestCase
{
    public function test_queue_receives_authenticated_user_id(): void
    {
        $service = $this->mock(PlaylistServiceInterface::class);
        $service->shouldReceive('getQueue')->once()->with('playlist', 42)->andReturn([]);
        $controller = new PlaylistController($service, $this->mock(FileServiceInterface::class));
        $request = Request::create('/');
        $request->attributes->set(Fields::USER_ID, 42);

        $this->assertSame(200, $controller->getQueue('playlist', $request)->getStatusCode());
    }

    public function test_sort_is_converted_to_enum_and_receives_user_id(): void
    {
        $service = $this->mock(PlaylistServiceInterface::class);
        $service->shouldReceive('saveSort')->once()->with(42, 'playlist', PlaylistOrderType::ADDED);
        $controller = new PlaylistController($service, $this->mock(FileServiceInterface::class));
        $request = Request::create('/', 'PATCH', [Fields::ORDER => 'added']);
        $request->attributes->set(Fields::USER_ID, 42);

        $this->assertSame(200, $controller->saveSort('playlist', $request)->getStatusCode());
    }

    public function test_invalid_sort_is_rejected_before_calling_service(): void
    {
        $service = $this->mock(PlaylistServiceInterface::class);
        $service->shouldNotReceive('saveSort');
        $controller = new PlaylistController($service, $this->mock(FileServiceInterface::class));
        $request = Request::create('/', 'PATCH', [Fields::ORDER => 'unknown']);
        $request->attributes->set(Fields::USER_ID, 42);

        $this->expectException(ValidationException::class);
        $controller->saveSort('playlist', $request);
    }
}
