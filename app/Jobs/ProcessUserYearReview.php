<?php

namespace App\Jobs;

use App\Models\TrackImport;
use App\Models\TrackImportArtist;
use App\Models\YearReview;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Service\YearReviewService\YearReviewServiceInterface;
use getID3;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessUserYearReview implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $id)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reviewService = app()->make(YearReviewServiceInterface::class);
        $yearReview = YearReview::query()
            ->where('id', $this->id)
            ->first();
        $review = $reviewService->generateReview($yearReview->user_id, $yearReview->year);
        dd('stop');

        $yearReview->review = json_encode($review);
        $yearReview->status = "success";
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
