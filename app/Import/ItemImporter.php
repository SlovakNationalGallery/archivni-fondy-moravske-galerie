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
        'Rok_od' => 'date_earliest',
        'Rok_do' => 'date_latest',
        'Autor_snímku' => 'author_image',
        'Celek_1' => 'part_of_1',
        'Celek_2' => 'part_of_2',
        'Instituce' => 'institution',
        'Fond' => 'archive_fund',
        'Sbírka' => 'collection',
        'Inv_číslo' => 'inventory_number',
        'Karton' => 'archive_box',
        'Složka' => 'archive_folder',
        'Typ_dokumentu' => 'work_type',
        'Související' => 'related_item',
        'Entity (místa, jména)' => 'related_entity',
    ];

    protected $options = [
        'delimiter' => ';',
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
            $images = Str::of($row['Indexace_digitalizátu'])
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
        foreach ([
            ['archive_fund', 'inventory_number'],
            ['archive_fund', 'archive_box', 'archive_folder'],
        ] as $columns) {
            $conditions = $data->only($columns);
            if ($conditions->has($columns) && !$conditions->some('is_null')) {
                return $conditions->toArray();
            }
        }

        throw new \Exception;
    }

    protected function map(Collection $row)
    {
        return $row
            ->intersectByKeys($this->map)
            ->mapWithKeys(function ($value, $key) {
                $value = $value !== '' ? $value : null;
                return [$this->map[$key] => $value];
            });
    }
}