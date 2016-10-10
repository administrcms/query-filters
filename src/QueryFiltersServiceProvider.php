<?php

namespace Administr\QueryFilters;


use Administr\QueryFilters\Commands\MakeQueryFilter;
use Illuminate\Support\ServiceProvider;

class QueryFiltersServiceProvider extends ServiceProvider
{
    public function boot()
    {
        
    }

    public function register()
    {
        $this->commands([
            MakeQueryFilter::class,
        ]);
    }
}