<?php

namespace Administr\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Filter a result set.
     *
     * @param  Builder $query
     * @param  Filter $filter
     * @return Builder
     */
    public function scopeFilter($query, Filter $filter)
    {
        return $filter->apply($query);
    }
}