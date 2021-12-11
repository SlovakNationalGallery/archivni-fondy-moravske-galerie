<?php

namespace App\Import;

use App\Models\Item;
use App\Import\CsvRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ItemImporter
{
    protected $repository;

    protected $map = [
        'Název' => 'title',
        'Obsah' => 'description',
        'Datace' => 'dating',
        'Rod_od' => 'date_earliest',
        'Rok_do' => 'date_latest',
        'Celek 1' => 'part_of_1',
        'Celek 2' => 'part_of_2',
        'Instituce' => 'institution',
        'Fond' => 'archive_fund',
        'Karton' => 'archive_box',
        'Původní spisové číslo' => 'archive_folder',
        'Folia' => 'archive_file',
        'Odkazy' => 'archive_folder_references',
        'Typ dokumentu' => 'work_type',
    ];

    protected $options = [
        'delimiter' => ';',
        'input_encoding' => 'CP1250',
    ];

    public function __construct(CsvRepository $repository)
    {
        $this->repository = $repository;
    }

    public function importFile($file, $imageDir)
    {
        $rows = $this->repository->getAll($file, $this->options);
        foreach ($rows as $row) {
            $data = $this->map(collect($row));
            $images = Str::of($row['Idexace digitalizátu'])
                ->explode(';')
                ->map(function ($image) use ($imageDir) {
                    return "$imageDir/$image";
                });
            $this->import($data, $images);
        }
    }

    public function import(Collection $data, Collection $images)
    {
        $conditions = $this->getConditions($data);
        $item = Item::where($conditions)->first() ?? new Item();
        $item->forceFill($data->toArray());
        $item->save();

        $item->clearMediaCollection();
        $images
            ->filter(function ($image) { return is_file($image); })
            ->each(function ($image) use ($item) {
                $item->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection();
            });
    }

    protected function getConditions(Collection $data)
    {
        $columns = ['archive_fund', 'archive_box', 'archive_folder', 'archive_file'];
        $conditions = $data->only($columns);
        if ($conditions->has($columns) && !$conditions->some('is_null')) {
            return $conditions->toArray();
        }

        throw new \Exception;
    }

    protected function map(Collection $row)
    {
        return $row
            ->intersectByKeys($this->map)
            ->mapWithKeys(function ($value, $key) {
                $mappedKey = $this->map[$key];
                if ($value === '') {
                    return [$mappedKey => null];
                }

                $methodName = sprintf('map%s', Str::camel($mappedKey));
                if (method_exists($this, $methodName)) {
                    $value = $this->$methodName($value);
                }

                return [$mappedKey => $value];
            });
    }

    protected function mapDateEarliest($dateEarliest)
    {
        return $this->parseDates($dateEarliest)->first();
    }

    protected function mapDateLatest($dateLatest)
    {
        return $this->parseDates($dateLatest)->last();
    }

    protected function parseDates($dates)
    {
        return Str::of($dates)
            ->explode('-')
            ->filter(function ($date) { return ctype_digit($date); })
            ->map(function ($date) { return (int)$date; });
    }

    protected function mapArchiveFolderReferences($archiveFolderReferences)
    {
        return Str::of($archiveFolderReferences)
            ->explode(';')
            ->map(function ($archiveFolderReference) {
                return trim($archiveFolderReference);
            });
    }
}