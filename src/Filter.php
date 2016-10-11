<?php

namespace Administr\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filter
{
    /**
     * Request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * Builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Specify method names, which should be run
     * even when the value is with length of 0.
     *
     * @var array
     */
    protected $alwaysRun = [];

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters to the builder.
     *
     * @param  Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $name => $value) {
            $name = camel_case($name);

            if (! method_exists($this, $name)) {
                continue;
            }

            if(! $this->shouldRunMethod($name, $value)) {
                continue;
            }

            call_user_func([$this, $name], $value);
        }

        return $this->builder;
    }

    /**
     * Handle sorting of ListView module. If the field being sorted
     * is not in the main table, you have to define a sort method
     * which follows the convention sortFieldName, so if the
     * column is type.name it will be sortTypeName and it
     * will accept the sort direction as parameter
     * which will be equeal to 'asc' or 'desc'.
     *
     * @param array $sort
     * @return Builder
     */
    public function sort(array $sort)
    {
        $tableName = $this->builder->getModel()->getTable();

        foreach($sort as $field => $dir)
        {
            $builder = $this->builder;

            // Convert to method for sorting - sortId, sortName, sortFirstName ...
            $method = 'sort' . studly_case(str_replace('.', '_', $field));

            // If it is not a method, then try to do the orderBy query for the field
            if(! method_exists($this, $method)) {
                $builder = $builder->orderBy("{$tableName}.{$field}", $dir);
                continue;
            }

            $builder = call_user_func([$this, $method], $dir);
        }

        return $builder;
    }

    /**
     * @param $method
     * @param $value
     * @return bool
     */
    protected function shouldRunMethod($method, $value)
    {
        return in_array($method, $this->alwaysRun) || strlen($value) > 0;
    }
}