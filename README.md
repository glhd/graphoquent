# Graphoquent

Graphoquent is a Laravel packages that turns Eloquent models into 
queryable GraphQL objects.

# Automatic Type Inference

By default, Graphoquent tries to infer your model's type from 
three places:

1. The model's `$casts` array
2. The model's `$dates` array
3. The model's `@property` and `@property-read` DocBlock annotations

Given the following model:

```php
/**
 * @property int $count
 */
class Foo extends Model
{
	use \Galahad\Graphoquent\Queryable;
	
	protected $casts = [
	    'stars' => 'float',
	];
    	
    protected $dates = [
        'created_at',
    ];
}
```

Graphoquent will build the following GraphQL Type:

```graphql
type Foo {
	count: Int
	stars: Float
	created_at: String
}
```
