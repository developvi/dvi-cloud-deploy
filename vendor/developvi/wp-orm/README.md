WPModel Class Documentation
===========================

Introduction
------------

The `WPModel` class is a custom database model class designed for WordPress using the `$wpdb` object for database interactions. It provides a fluent interface for building and executing SQL queries.

Properties
----------

*   `protected $table;` - The table name with prefix.
*   `protected $primaryKey = 'id';` - The primary key of the table.
*   `protected $wpdb;` - WordPress database object.
*   `protected $selectClause = '*';` - Default select clause.
*   `protected $whereClauses = [];` - Array to store WHERE clauses.
*   `protected $joinClauses = [];` - Array to store JOIN clauses.
*   `protected $orderByClause = '';` - ORDER BY clause.
*   `protected $groupByClause = '';` - GROUP BY clause.
*   `protected $havingClause = '';` - HAVING clause.
*   `protected $limitClause = '';` - LIMIT clause.
*   `protected $offsetClause = '';` - OFFSET clause.
*   `protected $with = [];` - Relations to eager load.

Methods
-------

### \_\_construct()

Initializes the `$wpdb` object and sets the table name.

### select($columns = \['\*'\])

Sets the columns to be selected in the query.

    $model->select(['name', 'email']);

### with($relations)

Eager loads relationships.

    $model->with('posts');

### where($column, $operator = '=', $value = null)

Adds a WHERE clause to the query.

    $model->where('status', '=', 'active');

### orWhere($column, $operator = '=', $value = null)

Adds an OR WHERE clause to the query.

### whereAny(array $columns, $operator = '=', $value = null)

Adds WHERE clauses for any of the specified columns.

### whereAll(array $columns, $operator = '=', $value = null)

Adds WHERE clauses for all of the specified columns.

### join($table, $first, $operator, $second)

Adds an INNER JOIN clause to the query.

### leftJoin($table, $first, $operator, $second)

Adds a LEFT JOIN clause to the query.

### rightJoin($table, $first, $operator, $second)

Adds a RIGHT JOIN clause to the query.

### orderBy($column, $direction = 'ASC')

Sets the ORDER BY clause.

### groupBy($column)

Sets the GROUP BY clause.

### having($column, $operator = '=', $value = null)

Sets the HAVING clause.

### limit($limit)

Sets the LIMIT clause.

### offset($offset)

Sets the OFFSET clause.

### get($id)

Retrieves a single record by ID.

### getAll()

Retrieves all records based on the current query.

### create($data)

Inserts a new record into the database.

### update($id, $data)

Updates a record with the given ID.

### delete($id)

Deletes a record with the given ID.

### count()

Returns the count of records based on the current query.

### first()

Retrieves the first record based on the current query.

### exists()

Checks if any records exist based on the current query.

### avg($column)

Returns the average value of a column.

### sum($column)

Returns the sum of a column.

### max($column)

Returns the maximum value of a column.

### min($column)

Returns the minimum value of a column.

### pluck($column)

Returns an array of values for a single column.

### distinct()

Selects distinct values in the query.

### updateOrInsert($attributes, $values = \[\])

Updates an existing record or inserts a new one.

### hasOne($related, $foreignKey, $localKey)

Defines a one-to-one relationship.

### hasMany($related, $foreignKey, $localKey)

Defines a one-to-many relationship.

### belongsToMany($related, $pivotTable, $foreignPivotKey, $relatedPivotKey, $localKey, $relatedKey)

Defines a many-to-many relationship.

### beginTransaction()

Starts a database transaction.

### commit()

Commits the current transaction.

### rollback()

Rolls back the current transaction.

Example Usage
-------------

    
 ```php
  class Post extends WPModel
   {
      protected $table = 'posts';
      protected $primaryKey = 'ID';
   }
   $post = new Post();
   $post->first();
 ```


 ```php
class Post extends WPModel
{
    protected $table = 'posts';
    protected $primaryKey = 'ID';

    public function postMeta()
    {
        return $this->hasOne(PostMeta::class, 'post_id', $this->primaryKey);
    }

}
class PostMeta extends WPModel
{
    protected $table = 'postmeta';
    protected $primaryKey = 'post_id';
}
   $post = new Post();
   $post->with('postMeta')->first();


 ```
