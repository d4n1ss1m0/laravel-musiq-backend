<?php

namespace Tests\Feature\YearReview;

use App\Jobs\ProcessUserYearReview;
use App\Models\Auth\User;
use App\Models\YearReview;
use App\Service\JwtService\JwtServiceInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class YearReviewWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension is required for this test.');
        }

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('app.key', str_repeat('x', 32));
        DB::purge('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
        });
        DB::table('users')->insert([['id' => 1], ['id' => 2]]);

        (require database_path('migrations/2026_09_23_080845_create_year_reviews_table.php'))->up();
        (require database_path('migrations/2026_11_22_084604_add_available_field_to_year_reviews_table.php'))->up();
    }

    private function authenticateAs(int $userId = 1): void
    {
        $token = app(JwtServiceInterface::class)->generateAccessToken(User::findOrFail($userId));
        $this->withHeader('token', $token);
    }

    private function createReview(array $attributes = []): YearReview
    {
        return YearReview::query()->create(array_replace([
            'user_id' => 1,
            'year' => 2026,
            'status' => 'success',
            'available_from' => now()->subDay(),
            'viewed' => false,
            'review' => ['year' => 2026, 'pages' => [
                ['page' => 1, 'background' => 'intro.webp', 'blocks' => []],
                ['page' => 2, 'background' => 'stats.webp', 'blocks' => []],
            ]],
        ], $attributes));
    }

    public function test_command_creates_dated_reviews_and_can_be_repeated_without_duplicates(): void
    {
        Bus::fake();

        $this->artisan('year:review', ['year' => 2026])->assertSuccessful();
        $this->assertDatabaseCount('year_reviews', 2);
        foreach (YearReview::all() as $review) {
            $this->assertSame('pending', $review->status);
            $this->assertSame('2026-12-25', $review->available_from->toDateString());
            $this->assertSame([], $review->review);
        }
        Bus::assertDispatchedTimes(ProcessUserYearReview::class, 2);
        Bus::assertDispatched(ProcessUserYearReview::class, fn ($job) => $job->queue === 'year_reviews' && $job->afterCommit === true);

        $this->artisan('year:review', ['year' => 2026])->assertSuccessful();
        $this->assertDatabaseCount('year_reviews', 2);
        Bus::assertDispatchedTimes(ProcessUserYearReview::class, 2);

        $this->artisan('year:review', ['year' => 2026, '--force' => true])->assertSuccessful();
        $this->assertDatabaseCount('year_reviews', 2);
        Bus::assertDispatchedTimes(ProcessUserYearReview::class, 4);
    }

    public function test_command_retries_failed_reviews_and_repairs_missing_availability(): void
    {
        Bus::fake();
        $failed = $this->createReview(['status' => 'failed']);
        $existing = $this->createReview(['user_id' => 2, 'available_from' => null, 'viewed' => true]);

        $this->artisan('year:review', ['year' => 2026])->assertSuccessful();

        $this->assertSame('pending', $failed->fresh()->status);
        $this->assertSame('success', $existing->fresh()->status);
        $this->assertTrue($existing->fresh()->viewed);
        $this->assertSame('2026-12-25', $existing->fresh()->available_from->toDateString());
        Bus::assertDispatchedTimes(ProcessUserYearReview::class, 1);
    }

    public function test_command_rejects_an_unconfigured_year_without_creating_jobs(): void
    {
        Bus::fake();
        $this->artisan('year:review', ['year' => 1999])->assertFailed();
        $this->assertDatabaseCount('year_reviews', 0);
        Bus::assertNothingDispatched();
    }

    public function test_command_updates_existing_availability_from_config_without_regenerating(): void
    {
        Bus::fake();
        $review = $this->createReview(['viewed' => true]);
        $this->createReview(['user_id' => 2]);
        config()->set('year_review.2026.available', '2026-12-30');

        $this->artisan('year:review', ['year' => 2026])->assertSuccessful();

        $review->refresh();
        $this->assertSame('2026-12-30', $review->available_from->toDateString());
        $this->assertSame('success', $review->status);
        $this->assertTrue($review->viewed);
        Bus::assertNothingDispatched();
    }

    public function test_list_returns_only_own_available_successful_previews_with_pagination(): void
    {
        $this->authenticateAs();
        $this->createReview(['viewed' => true]);
        $this->createReview(['year' => 2025]);
        $this->createReview(['user_id' => 2]);
        $this->createReview(['available_from' => now()->addDay()]);
        $this->createReview(['available_from' => null]);
        $this->createReview(['status' => 'pending']);
        $this->createReview(['status' => 'failed']);

        $this->getJson('/api/personal/year-reviews?limit=1')->assertOk()
            ->assertJsonPath('data.pagination.total', 2)
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.year', 2026)
            ->assertJsonPath('data.items.0.viewed', true)
            ->assertJsonPath('data.items.0.page', 1);
        $this->getJson('/api/personal/year-reviews?limit=1&page=2')->assertOk()
            ->assertJsonPath('data.items.0.year', 2025);
        $this->getJson('/api/personal/year-reviews?limit=0')->assertStatus(422);
    }

    public function test_new_preview_remains_unread_until_the_full_report_is_opened(): void
    {
        $this->authenticateAs();
        $review = $this->createReview();
        $this->createReview(['year' => 2025, 'viewed' => true]);
        $this->createReview(['year' => 2027, 'available_from' => now()->addYear()]);
        $this->createReview(['user_id' => 2]);

        $this->getJson('/api/personal/year-review/new')->assertOk()
            ->assertJsonPath('data.year', 2026)
            ->assertJsonPath('data.viewed', false);
        $this->assertFalse($review->fresh()->viewed);

        $this->getJson('/api/personal/year-review/2026')->assertOk()
            ->assertJsonPath('data.year', 2026)
            ->assertJsonCount(2, 'data.pages');
        $this->assertTrue($review->fresh()->viewed);
        $this->getJson('/api/personal/year-review/new')->assertOk()->assertJsonPath('data', null);
    }

    public function test_hidden_and_other_users_reports_cannot_be_opened(): void
    {
        $this->authenticateAs();
        $future = $this->createReview(['available_from' => now()->addDay()]);
        $this->createReview(['user_id' => 2]);
        $this->getJson('/api/personal/year-review/2026')->assertNotFound();
        $this->assertFalse($future->fresh()->viewed);
        $this->getJson('/api/personal/year-review/new')->assertOk()->assertJsonPath('data', null);
        $this->getJson('/api/personal/year-review/1999')->assertNotFound();
    }

    public function test_legacy_double_encoded_reviews_can_still_be_read(): void
    {
        $this->authenticateAs();
        $review = $this->createReview();
        DB::table('year_reviews')->where('id', $review->id)->update([
            'review' => json_encode(json_encode($review->review)),
        ]);

        $this->getJson('/api/personal/year-reviews')->assertOk()->assertJsonPath('data.items.0.year', 2026);
        $this->getJson('/api/personal/year-review/2026')->assertOk()->assertJsonCount(2, 'data.pages');
    }

    public function test_year_review_routes_require_authentication(): void
    {
        foreach (['year-reviews', 'year-review/new', 'year-review/2026'] as $path) {
            $this->getJson('/api/personal/'.$path)->assertUnauthorized();
        }
    }
}
