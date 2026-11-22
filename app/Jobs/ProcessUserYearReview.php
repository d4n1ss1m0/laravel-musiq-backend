<?php

namespace App\Jobs;

use App\Models\YearReview;
use App\Service\YearReviewService\YearReviewServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessUserYearReview implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $id) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $yearReview = YearReview::query()
            ->where('id', $this->id)
            ->first();

        if ($yearReview === null) {
            Log::warning('Skipping year review job: review not found.', [
                'year_review_id' => $this->id,
            ]);

            return;
        }

        $reviewService = app()->make(YearReviewServiceInterface::class);
        $review = $reviewService->generateReview($yearReview->user_id, $yearReview->year);

        $yearReview->review = $review;
        $yearReview->available_from ??= config("year_review.{$yearReview->year}.available");
        $yearReview->status = 'success';
        $yearReview->save();
    }

    public function failed(\Throwable $exception): void
    {
        YearReview::query()
            ->where('id', $this->id)
            ->update([
                'status' => 'failed',
            ]);
    }
}
