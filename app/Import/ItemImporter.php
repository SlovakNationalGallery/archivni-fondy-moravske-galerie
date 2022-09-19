<?php

namespace App\Import;

use App\Models\Item;
use App\Import\CsvRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ItemImporter
{
    public $map = [
        'title' => 'Název',
        'description' => 'Obsah',
        'author' => 'Autor_sb_předmětu',
        'inventory_number_mg' => 'Inv_číslo_MG',
        'dating' => 'Datace',
        'date_earliest' => 'Rok_od',
        'date_latest' => 'Rok_do',
        'author_image' => 'Autor_snímku',
        'part_of_1' => 'Celek_1',
        'part_of_2' => 'Celek_2',
        'institution' => 'Vlastník',
        'archive_fund' => 'Fond',
        'collection' => 'Sbírka',
        'inventory_number' => 'Inv_číslo',
        'archive_box' => 'Karton',
        'archive_folder' => 'Složka',
        'work_type' => 'Typ_dokumentu',
        'archive_folder_references' => 'Související',
        'images' => 'Indexace_digitalizátu',
        'entities' => 'Entity (místa, jména)',
        'unique_id' => 'Indexace_digitalizátu',
    ];

    public $options = [
        'delimiter' => ';',
        'input_encoding' => 'CP1250',
    ];

    protected $repository;

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

    public function map(Collection $row)
    {
        return collect($this->map)
            ->map(function ($column, $mappedKey) use ($row) {
                $value = trim($row[$column]);
                if ($value === '') {
                    return null;
                }

                $methodName = sprintf('map%s', Str::camel($mappedKey));
                if (method_exists($this, $methodName)) {
                    return $this->$methodName($value);
                }

                return $value;
            });
    }

    public function mapUniqueId($uniqueId)
    {
        return Str::of($uniqueId)
            ->explode(';')
            ->filter()
            ->first();
    }

    public function mapImages($images)
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

    public function mapDateEarliest($dateEarliest)
    {
        return $this->parseDates($dateEarliest)->first();
    }

    public function mapDateLatest($dateLatest)
    {
        return $this->parseDates($dateLatest)->last();
    }

    public function mapArchiveFolderReferences($archiveFolderReferences)
    {
        return Str::of($archiveFolderReferences)
            ->explode(';')
            ->map(function ($archiveFolderReference) {
                return trim($archiveFolderReference);
            });
    }

    public function mapEntities($entities)
    {
        return Str::of($entities)
            ->explode(';')
            ->map(function ($entity) {
                return trim($entity);
            });
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

    protected function getConditions(Collection $data)
    {
        return $data->only('unique_id')->toArray();
    }
}
