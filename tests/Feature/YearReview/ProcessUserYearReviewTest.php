<?php

namespace Tests\Feature\YearReview;

use App\Jobs\ProcessUserYearReview;
use App\Models\YearReview;
use App\Service\YearReviewService\YearReviewServiceInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProcessUserYearReviewTest extends TestCase
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

        Schema::create('year_reviews', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('year');
            $table->json('review');
            $table->string('status');
            $table->dateTime('available_from')->nullable();
            $table->timestamps();
        });
    }

    public function test_missing_review_is_skipped_without_generating_a_review(): void
    {
        $this->mock(YearReviewServiceInterface::class)
            ->shouldNotReceive('generateReview');

        Log::shouldReceive('warning')->once()->with(
            'Skipping year review job: review not found.',
            ['year_review_id' => 123],
        );

        (new ProcessUserYearReview(123))->handle();

        $this->assertDatabaseCount('year_reviews', 0);
    }

    public function test_existing_review_is_generated_and_saved(): void
    {
        $yearReview = YearReview::query()->create([
            'user_id' => 42,
            'year' => 2026,
            'review' => [],
            'status' => 'pending',
        ]);
        $review = ['year' => 2026, 'user_id' => 42, 'pages' => []];

        $this->mock(YearReviewServiceInterface::class)
            ->shouldReceive('generateReview')->once()->with(42, 2026)->andReturn($review);

        (new ProcessUserYearReview($yearReview->id))->handle();

        $yearReview->refresh();
        $this->assertSame('success', $yearReview->status);
        $this->assertSame($review, $yearReview->review);
        $this->assertSame($review, json_decode($yearReview->getRawOriginal('review'), true));
        $this->assertSame('2026-12-25', $yearReview->available_from->toDateString());
    }

    public function test_failed_job_marks_only_its_review_as_failed(): void
    {
        $review = YearReview::query()->create([
            'user_id' => 42, 'year' => 2026, 'review' => [], 'status' => 'pending',
        ]);
        $other = YearReview::query()->create([
            'user_id' => 43, 'year' => 2026, 'review' => [], 'status' => 'pending',
        ]);

        (new ProcessUserYearReview($review->id))->failed(new \RuntimeException('Generation failed'));

        $this->assertSame('failed', $review->fresh()->status);
        $this->assertSame('pending', $other->fresh()->status);
    }

    public function test_generation_failure_does_not_save_a_successful_review(): void
    {
        $review = YearReview::query()->create([
            'user_id' => 42, 'year' => 2026, 'review' => [], 'status' => 'pending',
        ]);
        $this->mock(YearReviewServiceInterface::class)->shouldReceive('generateReview')
            ->once()->andThrow(new \RuntimeException('Generation failed'));

        try {
            (new ProcessUserYearReview($review->id))->handle();
            $this->fail('Generation failures must propagate to the queue worker for retries.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Generation failed', $exception->getMessage());
        }

        $this->assertSame('pending', $review->fresh()->status);
        $this->assertSame([], $review->fresh()->review);
    }
}
