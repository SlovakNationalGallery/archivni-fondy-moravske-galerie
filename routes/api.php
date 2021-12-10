<?php

use App\Http\Resources\ItemResource;
use App\Models\Item;
use ElasticAdapter\Search\Aggregation;
use ElasticAdapter\Search\Bucket;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use ElasticScoutDriverPlus\Support\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('items', function (Request $request) {
    $filter = (array)$request->get('filter');
    $sort = (array)$request->get('sort');
    $size = (int)$request->get('size', 1);

    try {
        $query = Item::filterQuery($filter)->buildQuery();
    } catch (QueryBuilderException $e) {
        $query = ['match_all' => new \stdClass];
    }

    $searchRequest = Item::searchQuery($query);

    collect($sort)
        ->only(Item::$sortables)
        ->intersect(['asc', 'desc'])
        ->each(function ($direction, $field) use ($searchRequest) {
            $searchRequest->sort($field, $direction);
        });

    $items = $searchRequest->paginate($size);
    $items->setCollection($items->models());
    $items->appends($request->query());

    return ItemResource::collection($items);
});

Route::get('items/aggregations', function (Request $request) {
    $filter = (array)$request->get('filter');
    $terms = (array)($request->get('terms'));
    $min = (array)$request->get('min');
    $max = (array)$request->get('max');
    $size = (int)$request->get('size', 1);

    try {
        $query = Item::filterQuery($filter)->buildQuery();
    } catch (QueryBuilderException $e) {
        $query = ['match_all' => new \stdClass];
    }

    $searchRequest = Item::searchQuery($query);

    foreach ($terms as $agg => $field) {
        $searchRequest->aggregate($agg, [
            'terms' => [
                'field' => $field,
                'size' => $size,
            ]
        ]);
    }

    foreach ($min as $agg => $field) {
        $searchRequest->aggregate($agg, [
            'min' => [
                'field' => $field,
            ]
        ]);
    }

    foreach ($max as $agg => $field) {
        $searchRequest->aggregate($agg, [
            'max' => [
                'field' => $field,
            ]
        ]);
    }

    $searchResult = $searchRequest->execute();
    return response()->json($searchResult->aggregations()->map(function (Aggregation $aggregation) {
        return $aggregation->raw()['value'] ?? $aggregation->buckets()->mapWithKeys(function (Bucket $bucket) {
            return [$bucket->key() => $bucket->docCount()];
        });
    }));
});

Route::get('items/{id}', function (Request $request, $id) {
    $query = Query::ids()->values([(string)$id]);
    $items = Item::searchQuery($query)->execute();

    if (!$items->total()) {
        abort(404);
    }

    return response()->json($items->hits()->first());
});