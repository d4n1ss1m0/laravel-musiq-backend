<?php

namespace App\Console\Commands;

use App\Jobs\ProcessUserYearReview;
use App\Models\YearReview;
use Illuminate\Console\Command;

class TestUserYearReview extends Command
{
    protected $signature = 'year-review:test
        {id : Year review ID}';

    protected $description = 'Run ProcessUserYearReview synchronously and display the result';

    public function handle(): int
    {
        $id = (int) $this->argument('id');
        $yearReview = YearReview::query()->find($id);

        if (! $yearReview) {
            $this->error("Year review not found: {$id}");

            return self::FAILURE;
        }

        $this->info(
            "Processing year review {$yearReview->id} " .
            "for user {$yearReview->user_id}, year {$yearReview->year}..."
        );

        ProcessUserYearReview::dispatchSync($yearReview->id);

        $yearReview->refresh();

        $this->info("Status: {$yearReview->status}");
        $this->line(
            json_encode(
                $yearReview->review,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ) ?: 'null'
        );

        return self::SUCCESS;
    }
}
