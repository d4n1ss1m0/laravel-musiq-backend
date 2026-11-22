<?php

namespace App\Console\Commands;

use App\Jobs\ProcessUserYearReview;
use App\Models\Auth\User;
use App\Models\YearReview;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class YearEndReview extends Command
{
    protected $signature = 'year:review {year} {--force : Regenerate existing reviews}';

    protected $description = 'Create user year reviews and queue their generation';

    public function handle(): int
    {
        $year = $this->argument('year');
        $config = config("year_review.{$year}");

        if (! preg_match('/^\d{4}$/', $year) || ! is_array($config)
            || ! isset($config['available'], $config['datasets'], $config['pages'])) {
            $this->error('A year review configuration with available, datasets and pages is required.');

            return self::FAILURE;
        }

        try {
            $availableFrom = CarbonImmutable::parse($config['available']);
        } catch (\Throwable) {
            $this->error('Invalid year review availability date.');

            return self::FAILURE;
        }

        $queued = 0;
        User::query()
            ->chunkById(100, function ($users) use ($year, $availableFrom, &$queued) {
                foreach ($users as $user) {
                    DB::transaction(function () use ($user, $year, $availableFrom, &$queued): void {
                        // Serialize concurrent command runs for the same user.
                        User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
                        $review = YearReview::query()->firstOrNew([
                            'user_id' => $user->id,
                            'year' => $year,
                        ]);

                        $shouldGenerate = ! $review->exists || $review->status === 'failed' || $this->option('force');
                        $review->available_from = $availableFrom;

                        if ($shouldGenerate) {
                            $review->review ??= [];
                            $review->status = 'pending';
                        }

                        $review->save();

                        if ($shouldGenerate) {
                            ProcessUserYearReview::dispatch($review->id)->onQueue('year_reviews')->afterCommit();
                            $queued++;
                        }
                    });
                }
            });

        $this->info("Queued {$queued} year reviews for {$year}.");

        return self::SUCCESS;
    }
}
