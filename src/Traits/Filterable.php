<?php

namespace Module\System\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait Filterable
{
    /**
     * toFilterableArray function
     * #[FilterUsingAge(['age'])]
     *
     * @return array
     */
    protected function toFilterableArray(): array
    {
        return [
            // 'age' => 'born_date'
        ];
    }

    /**
     * hasSoftDeleted function
     *
     * @return boolean
     */
    public static function hasSoftDeleted(): bool
    {
        return Schema::hasColumn(with(new static())->getTable(), 'deleted_at');
    }

    /**
     * scopeFilter function
     *
     * @param Builder $query
     * @param [type] $filters
     * @return void
     */
    public function scopeFilter(Builder $query, $filters)
    {
        $filterableArray = $this->toFilterableArray();
        $filterableParams = $this->mapFilters();

        if (count($filterableArray) <= 0 || is_null($filters)) {
            return $query;
        }

        if (method_exists($this, 'scopeAlterFilterable')) {
            $query = $this->scopeAlterFilterable($query);
        }

        $filters = explode('+', $filters);

        foreach ($filters as $filter) {
            if (!str($filter)->contains(';')) {
                break;
            }

            list($key, $operator, $value) = explode(';', $filter);

            if (
                array_key_exists($key, $filterableParams) &&
                array_key_exists($key, $filterableArray) &&
                in_array($operator, $filterableParams[$key]['operators'])
            ) {
                if (str($filterableArray[$key])->contains('raw::')) {
                    $query = $query->whereRaw(
                        str($filterableArray[$key])->after('raw::') . ' ' . $operator . ' ?',
                        [$value]
                    );
                } elseif (str($filterableArray[$key])->contains('json::')) {
                    $query = $query->where(
                        str($filterableArray[$key])->after('json::'),
                        $operator,
                        $value
                    );
                } elseif (str($filterableArray[$key])->contains('month::')) {
                    $query = $query->whereMonth(
                        str($filterableArray[$key])->after('month::'),
                        $operator,
                        $value
                    );
                } elseif (str($filterableArray[$key])->contains('eager::')) {
                    $relation = str($filterableArray[$key])->after('eager::')->before('->>')->__toString();
                    $fieldname = str($filterableArray[$key])->after('->>')->__toString();

                    $query = $query->whereHas($relation, function ($eager) use ($fieldname, $operator, $value) {
                        $eager->where($fieldname, $operator, $value);
                    });
                } else {
                    $query = $query->where(
                        $this->qualifyColumn($filterableArray[$key]),
                        $operator,
                        $value
                    );
                }
            }
        }

        return $query;
    }
}
