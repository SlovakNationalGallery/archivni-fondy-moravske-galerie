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
    ];

    public static $filterables = [
        'part_of_1',
        'part_of_2',
    ];

    public static $rangeables = [
    ];

    public static $sortables = [
    ];

    public static function filterQuery(array $filter, BoolQueryBuilder $builder = null)
    {
        $builder = $builder ?: Query::bool();
        foreach ($filter as $field => $value) {
            if (is_string($value) && in_array($field, self::$filterables, true)) {
                $builder->filter(['term' => [$field => $value]]);
            } else if (is_array($value) && in_array($field, self::$rangeables, true)) {
                $range = collect($value)
                    ->only(['lt', 'lte', 'gt', 'gte'])
                    ->transform(function ($value) {
                        return (string)$value;
                    })
                    ->all();
                $builder->filter(['range' => [$field => $range]]);
            } else {
                throw new \Exception;
            }
        }
        return $builder;
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
