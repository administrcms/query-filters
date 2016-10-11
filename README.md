# Query filters for an Eloquent model

Inspired by Laracasts and Jeffrey Way - https://github.com/laracasts/Dedicated-Query-String-Filtering/

# Install using Composer
`composer require administrcms/query-filters`

And add the ServiceProvider - `Administr\QueryFilters\QueryFiltersServiceProvider::class`

# Usage

Define a QueryFilter class (you can run the `php artisan administr:query-filter filterName` command to generate the scaffold):

```php
<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Administr\QueryFilters;

class LessonFilters extends Filter 
{
    /**
     * @param  string $order
     * @return Builder
     */
    public function popular($order = 'desc')
    {
        return $this->builder->orderBy('views', $order);
    }

    /**
     * @param  string $level
     * @return Builder
     */
    public function difficulty($level)
    {
        return $this->builder->where('difficulty', $level);
    }
}
```

Make your model use the `Administr\QueryFilters\Filterable` trait.

And lastly in your controller or where you want this logic to run:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\QueryFilters\LessonFilters;

class LessonsController extends Controller
{
    public function index(LessonFilters $filters)
    {
        return Lesson::filter($filters)->get();
    }
}
```

# [ListView](https://github.com/administrcms/listview) sorting

By default the base Filter class will handle the sorting for your main table. If you have a ListView that uses a column which is a relationship to the model, you'll have to handle that column for yourself. Let's imagine that you have User model and it has a relationship to UserType (one User has one UserType). So in your ListView you want to sort by the column that shows the name of the UserType. We'll assume that the column in the ListView is defined as `type.name`. In that case you need to define the custom sort like this:

```php
public function sortTypeName($dir)
{
    return $this
        ->builder
        ->join('user_types', 'user_types.id', '=', 'users.type_id')
        ->orderBy('user_types.id', $dir);
}
```