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
        'Autor_sb_předmětu' => 'author',
        'Inv_číslo_MG' => 'inventory_number_mg',
        'Datace' => 'dating',
        'Rod_od' => 'date_earliest',
        'Rok_do' => 'date_latest',
        'Autor_snímku' => 'author_image',
        'Celek 1' => 'part_of_1',
        'Celek 2' => 'part_of_2',
        'Vlastník' => 'institution',
        'Fond' => 'archive_fund',
        'Sbírka' => 'collection',
        'Inv_číslo' => 'inventory_number',
        'Karton' => 'archive_box',
        'Složka ' => 'archive_folder',
        'Typ dokumentu' => 'work_type',
        'Související' => 'archive_folder_references',
        'Idexace digitalizátu' => 'images',
        'Entity (místa, jména)' => 'entities',
    ];

    protected $options = [
        'delimiter' => ';',
        'input_encoding' => 'CP1250',
    ];

    public function __construct(CsvRepository $repository)
    {
        $this->repository = $repository;
    }

    public function importFile($file)
    {
        $rows = $this->repository->getAll($file, $this->options);
        foreach ($rows as $row) {
            $data = $this->map(collect($row));
            $this->import($data);
        }
    }

    public function import(Collection $data)
    {
        $conditions = $this->getConditions($data);
        $item = Item::where($conditions)->first() ?? new Item();
        $item->forceFill($data->toArray());
        $item->save();
    }

    protected function getConditions(Collection $data)
    {
        return $data->only([
            'archive_fund',
            'archive_box',
            'archive_folder',
            'inventory_number'
        ])
            ->toArray();
    }

    protected function map(Collection $row)
    {
        return $row
            ->intersectByKeys($this->map)
            ->mapWithKeys(function ($value, $key) {
                $value = trim($value);
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

    protected function mapImages($images)
    {
        return Str::of($images)
            ->explode(';')
            ->map(function ($image) {
                $image = trim($image);
                preg_match('/^(?<folder>[[:alnum:]]+)_(?<subfolder>[[:alnum:]]+)/', $image, $matches);
                if (!isset($matches['folder'], $matches['subfolder'])) {
                    return null;
                }

                return sprintf(
                    'MGHQ/%s/%s/%s.jp2',
                    $matches['folder'],
                    $matches['subfolder'],
                    $image,
                );
            })
            ->filter()
            ->values();
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
            ->filter(function ($date) {
                return ctype_digit($date);
            })
            ->map(function ($date) {
                return (int)$date;
            });
    }

    protected function mapArchiveFolderReferences($archiveFolderReferences)
    {
        return Str::of($archiveFolderReferences)
            ->explode(';')
            ->map(function ($archiveFolderReference) {
                return trim($archiveFolderReference);
            });
    }

    protected function mapEntities($entities)
    {
        return Str::of($entities)
            ->explode(';')
            ->map(function ($entity) {
                return trim($entity);
            });
    }
}
