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
    protected $signature = 'import {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $importer = new ItemImporter(new CsvRepository());
        $csv = storage_path('app/import/data.csv');
        $cached = Cache::get('data.mtime');
        $mtime = filemtime($csv);
        if ($cached !== $mtime || $this->option('force')) {
            Cache::set('data.mtime', $mtime);
            $importer->importFile($csv, storage_path('app/import/images'));
        } else {
            $this->output->writeln(sprintf('%s unchanged. Aborting...', $csv));
        }
    }
}
