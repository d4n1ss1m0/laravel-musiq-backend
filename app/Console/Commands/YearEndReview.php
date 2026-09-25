<?php

namespace App\Console\Commands;

use App\Jobs\ProcessUserYearReview;
use App\Models\Auth\User;
use App\Models\Track;
use App\Models\YearReview;
use App\Service\FileService\FileServiceInterface;
use Illuminate\Console\Command;

class YearEndReview extends Command
{
    protected $signature = 'year:review {year}';

    protected $description = 'Reindex tracks loudness metadata: integrated_lufs and true_peak_db';

    public function handle(): int
    {
        $year = $this->argument('year');
        User::query()
            ->chunkById(100, function ($users) use ($year) {
                foreach ($users as $user) {
                    $review = YearReview::query()
                        ->create([
                            'user_id' => $user->id,
                            'year' => $year,
                            'review' => json_encode([]),
                            'status' => 'pending'
                        ]);
                    ProcessUserYearReview::dispatch($review->id)->onQueue('year_reviews');
                }
            });


        return self::SUCCESS;
    }
}
