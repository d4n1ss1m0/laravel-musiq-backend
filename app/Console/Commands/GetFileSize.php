<?php

namespace App\Console\Commands;

use App\Models\Track;
use Illuminate\Console\Command;

class GetFileSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-file-size {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() {
        $id = $this->argument('id');
        $song = Track::where('uuid', $id)->value('song');
        $filePath = storage_path("app/{$song}");
        $this->info(filesize($filePath));
    }
}
