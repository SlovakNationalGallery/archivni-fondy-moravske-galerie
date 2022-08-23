<?php

namespace App\Console\Commands;

use App\Import\CsvRepository;
use App\Import\ItemImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {file} {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import items';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $importer = new ItemImporter(new CsvRepository());
        $file = $this->argument('file');
        $csv = storage_path($file);
        $cached = Cache::get('data.mtime');
        $mtime = filemtime($csv);
        if ($cached !== $mtime || $this->option('force')) {
            Cache::set('data.mtime', $mtime);
            $importer->importFile($csv);
        } else {
            $this->output->writeln(sprintf('%s unchanged. Aborting...', $csv));
        }
    }
}
