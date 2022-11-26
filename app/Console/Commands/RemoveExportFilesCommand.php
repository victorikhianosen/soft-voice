<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RemoveExportFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = Storage::disk('local')->allFiles('public/exports');
        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);
            $lastModified = Carbon::parse($lastModified);
            if (Carbon::parse($lastModified)->addMinutes(10)->lt(now())) {
                Storage::delete($file);
            }
        }
        return Command::SUCCESS;
    }
}
