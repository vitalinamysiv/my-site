<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class WishlistFilters
{
    public static function apply(Builder|Relation $query, Request $request): Builder|Relation
    {
        if ($request->filled('category') && ctype_digit((string) $request->input('category'))) {
            $query->where('category_id', $request->integer('category'));
        }

        if (is_numeric($request->input('min_price'))) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if (is_numeric($request->input('max_price'))) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        [$column, $direction] = self::sort($request);
        $query->orderBy($column, $direction);

        return $query;
    }

    /**
     * @return array{0: string, 1: string}
     */
    public static function sort(Request $request): array
    {
        $value = (string) $request->input('sort', 'created_at|desc');
        [$column, $direction] = array_pad(explode('|', $value, 2), 2, 'desc');
        $columns = ['price', 'title', 'created_at'];
        if (! in_array($column, $columns, true)) {
            $column = 'created_at';
        }

        return [$column, $direction === 'asc' ? 'asc' : 'desc'];
    }
}
