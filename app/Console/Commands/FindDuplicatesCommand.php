<?php

namespace App\Console\Commands;

use App\Import\CsvRepository;
use App\Import\ItemImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FindDuplicatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find-duplicates {files*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find duplicates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $repository = new CsvRepository();
        $importer = new ItemImporter($repository);

        $uniqueIds = [];
        collect($this->argument('files'))
            ->map(function ($file) use ($repository, $importer, $uniqueIds) {
                $realpath = realpath($file);
                $data = $repository->getAll($realpath, $importer->options);

                foreach ($data as $line => $row) {
                    $uniqueId = $importer->mapUniqueId($row['Idexace digitalizátu']);

                    if (isset($uniqueIds[$uniqueId])) {
                        $this->output->writeln(sprintf(
                            '"%s" found both in %s:%d and %s:%d',
                            $uniqueId,
                            $uniqueIds[$uniqueId]['file'],
                            $uniqueIds[$uniqueId]['line'] + 1,
                            $file,
                            $line + 1
                        ));
                    } else {
                        $uniqueIds[$uniqueId] = compact('file', 'line');
                    }
                }
            });
    }
}
