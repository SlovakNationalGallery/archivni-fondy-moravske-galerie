<?php

namespace App\Models;

use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Searchable;
use ElasticScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Item extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Searchable;

    protected $casts = [
        'archive_folder_references' => 'json',
        'images' => 'json',
        'entities' => 'json',
    ];

    public static $filterables = [
        'authors',
        'part_of',
        'institution',
        'archive_fund',
        'archive_box',
        'archive_folder',
        'work_type',
        'entities',
    ];

    public static $rangeables = [
        'date_earliest',
        'date_latest',
    ];

    public static $sortables = [];

    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['part_of'] = [
            $this->part_of_1,
            $this->part_of_2,
        ];
        $array['authors'] = [
            $this->author,
            $this->author_image,
        ];
        return $array;
    }

    public static function filterQuery(array $filter, BoolQueryBuilder $builder = null)
    {
    }

    protected static function booted()
    {
        static::addGlobalScope('media', function (Builder $builder) {
            $builder->with('media');
        });
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('default')
            ->withResponsiveImages();
    }
}
