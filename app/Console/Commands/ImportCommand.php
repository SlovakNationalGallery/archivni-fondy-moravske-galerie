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
        $path = realpath($file);
        $cacheKey = sprintf('%s.mtime', $path);
        $cached = Cache::get($cacheKey);
        $mtime = filemtime($path);
        if ($cached !== $mtime || $this->option('force')) {
            $importer->importFile($path);
            Cache::set($cacheKey, $mtime);
        } else {
            $this->output->writeln(sprintf('%s unchanged. Aborting...', $path));
        }
    }
}
