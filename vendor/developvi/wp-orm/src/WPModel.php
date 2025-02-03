<?php


namespace DVI\WpOrm;

class WPModel
{
    protected $table;
    protected $primaryKey = 'id';
    protected $wpdb;
    protected $selectClause = '*';
    protected $whereClauses = [];
    protected $joinClauses = [];
    protected $orderByClause = '';
    protected $groupByClause = '';
    protected $havingClause = '';
    protected $limitClause = '';
    protected $offsetClause = '';
    protected $with = [];

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $this->wpdb->prefix . $this->table;
    }

    public function select($columns = ['*'])
    {
        $this->selectClause = implode(', ', $columns);
        return $this;
    }
    public function with($relations)
    {
        $this->with = is_array($relations) ? $relations : func_get_args();
        return $this;
    }

    public function where($column, $operator = '=', $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->whereClauses[] = $this->wpdb->prepare("$column $operator %s", $value);
        return $this;
    }

    public function orWhere($column, $operator = '=', $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        if (!empty($this->whereClauses)) {
            $this->whereClauses[] = 'OR ' . $this->wpdb->prepare("$column $operator %s", $value);
        } else {
            $this->where($column, $operator, $value);
        }
        return $this;
    }

    public function whereAny(array $columns, $operator = '=', $value = null)
    {
        $clauses = [];
        foreach ($columns as $column) {
            $clauses[] = $this->wpdb->prepare("$column $operator %s", $value);
        }
        $this->whereClauses[] = '(' . implode(' OR ', $clauses) . ')';
        return $this;
    }

    public function whereAll(array $columns, $operator = '=', $value = null)
    {
        foreach ($columns as $column) {
            $this->whereClauses[] = $this->wpdb->prepare("$column $operator %s", $value);
        }
        return $this;
    }

    public function join($table, $first, $operator, $second)
    {
        $this->joinClauses[] = "INNER JOIN {$this->wpdb->prefix}{$table} ON $first $operator $second";
        return $this;
    }

    public function leftJoin($table, $first, $operator, $second)
    {
        $this->joinClauses[] = "LEFT JOIN {$this->wpdb->prefix}{$table} ON $first $operator $second";
        return $this;
    }

    public function rightJoin($table, $first, $operator, $second)
    {
        $this->joinClauses[] = "RIGHT JOIN {$this->wpdb->prefix}{$table} ON $first $operator $second";
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderByClause = "ORDER BY $column $direction";
        return $this;
    }

    public function groupBy($column)
    {
        $this->groupByClause = "GROUP BY $column";
        return $this;
    }

    public function having($column, $operator = '=', $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->havingClause = $this->wpdb->prepare("HAVING $column $operator %s", $value);
        return $this;
    }

    public function limit($limit)
    {
        $this->limitClause = "LIMIT " . intval($limit);
        return $this;
    }

    public function offset($offset)
    {
        $this->offsetClause = "OFFSET " . intval($offset);
        return $this;
    }

    public function get($id)
    {
        $query = $this->wpdb->prepare("SELECT {$this->selectClause} FROM {$this->table} WHERE {$this->primaryKey} = %d", $id);
        $result = $this->wpdb->get_row($query);
        $model = $result ? $this->newInstance($result) : null;

        if ($model) {
            $this->eagerLoadRelations($model);
        }

        return $model;
    }

    public function getAll()
    {
        $joinClause = implode(' ', $this->joinClauses);
        $whereClause = !empty($this->whereClauses) ? 'WHERE ' . implode(' AND ', $this->whereClauses) : '';
        $query = "SELECT {$this->selectClause} FROM {$this->table} $joinClause $whereClause {$this->groupByClause} {$this->havingClause} {$this->orderByClause} {$this->limitClause} {$this->offsetClause}";
        $results = $this->wpdb->get_results($query);
        $models = array_map([$this, 'newInstance'], $results);

        foreach ($models as $model) {
            $this->eagerLoadRelations($model);
        }

        return $models;
    }
    public function create(array $data)
    {
        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function update($id, $data)
    {
        return $this->wpdb->update($this->table, $data, [$this->primaryKey => $id]);
    }

    public function delete($id)
    {
        return $this->wpdb->delete($this->table, [$this->primaryKey => $id]);
    }

    public function count()
    {
        $joinClause = implode(' ', $this->joinClauses);
        $whereClause = !empty($this->whereClauses) ? 'WHERE ' . implode(' AND ', $this->whereClauses) : '';
        $query = "SELECT COUNT(*) FROM {$this->table} $joinClause $whereClause";
        return $this->wpdb->get_var($query);
    }

    public function first()
    {
        $joinClause = implode(' ', $this->joinClauses);
        $whereClause = !empty($this->whereClauses) ? 'WHERE ' . implode(' AND ', $this->whereClauses) : '';
        $query = "SELECT {$this->selectClause} FROM {$this->table} $joinClause $whereClause {$this->orderByClause} LIMIT 1";
        $result = $this->wpdb->get_row($query);
        $model = $result ? $this->newInstance($result) : null;

        if ($model) {
            $this->eagerLoadRelations($model);
        }

        return $model;
    }


    public function exists()
    {
        return $this->count() > 0;
    }

    public function avg($column)
    {
        $joinClause = implode(' ', $this->joinClauses);
        $whereClause = !empty($this->whereClauses) ? 'WHERE ' . implode(' AND ', $this->whereClauses) : '';
        $query = "SELECT AVG($column) FROM {$this->table} $joinClause $whereClause";
        return $this->wpdb->get_var($query);
    }

    public function sum($column)
    {
        $joinClause = implode(' ', $this->joinClauses);
        $whereClause = !empty($this->whereClauses) ? 'WHERE ' . implode(' AND ', $this->whereClauses) : '';
        $query = "SELECT SUM($column) FROM {$this->table} $joinClause $whereClause";
        return $this->wpdb->get_var($query);
    }

    public function max($column)
    {
        $joinClause = implode(' ', $this->joinClauses);
        $whereClause = !empty($this->whereClauses) ? 'WHERE ' . implode(' AND ', $this->whereClauses) : '';
        $query = "SELECT MAX($column) FROM {$this->table} $joinClause $whereClause";
        return $this->wpdb->get_var($query);
    }

    public function min($column)
    {
        $joinClause = implode(' ', $this->joinClauses);
        $whereClause = !empty($this->whereClauses) ? 'WHERE ' . implode(' AND ', $this->whereClauses) : '';
        $query = "SELECT MIN($column) FROM {$this->table} $joinClause $whereClause";
        return $this->wpdb->get_var($query);
    }

    public function pluck($column)
    {
        $query = "SELECT $column FROM {$this->table} " . $this->buildQueryClauses();
        return $this->wpdb->get_col($query);
    }

    public function distinct()
    {
        $this->selectClause = 'DISTINCT ' . $this->selectClause;
        return $this;
    }

    public function updateOrInsert($attributes, $values = [])
    {
        $exists = $this->whereAll(array_keys($attributes), '=', array_values($attributes))->exists();
        if ($exists) {
            return $this->update($attributes[$this->primaryKey], $values);
        } else {
            return $this->create(array_merge($attributes, $values));
        }
    }
    public function hasOne($related, $foreignKey, $localKey)
    {
        $relatedModel = new $related();
        $relatedTable = $relatedModel->table;
        $query = "SELECT * FROM $relatedTable WHERE $foreignKey = %d";
        return $this->wpdb->get_row($this->wpdb->prepare($query, $this->$localKey));
    }

    public function hasMany($related, $foreignKey, $localKey)
    {
        $relatedModel = new $related();
        $relatedTable = $relatedModel->table;
        $query = "SELECT * FROM $relatedTable WHERE $foreignKey = %d";
        return $this->wpdb->get_results($this->wpdb->prepare($query, $this->$localKey));
    }

    public function belongsToMany($related, $pivotTable, $foreignPivotKey, $relatedPivotKey, $localKey, $relatedKey)
    {
        $relatedModel = new $related();
        $relatedTable = $relatedModel->table;
        $query = "
            SELECT $relatedTable.* FROM $relatedTable
            INNER JOIN $pivotTable ON $relatedTable.$relatedKey = $pivotTable.$relatedPivotKey
            WHERE $pivotTable.$foreignPivotKey = %d
        ";
        return $this->wpdb->get_results($this->wpdb->prepare($query, $this->$localKey));
    }
    public function beginTransaction()
    {
        $this->wpdb->query('START TRANSACTION');
    }

    public function commit()
    {
        $this->wpdb->query('COMMIT');
    }

    public function rollback()
    {
        $this->wpdb->query('ROLLBACK');
    }

    protected function buildQueryClauses()
    {
        $joinClause = implode(' ', $this->joinClauses);
        $whereClause = !empty($this->whereClauses) ? 'WHERE ' . implode(' AND ', $this->whereClauses) : '';
        return "$joinClause $whereClause {$this->groupByClause} {$this->havingClause} {$this->orderByClause} {$this->limitClause} {$this->offsetClause}";
    }
 
    protected function newInstance($attributes)
    {
        $model = new static();
        foreach ($attributes as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    protected function eagerLoadRelations($model)
    {
        foreach ($this->with as $relation) {
            if (method_exists($model, $relation)) {
                $model->$relation = $model->$relation();
            }
        }
    }
    public function __callStatic($method, $args)
    {
        return (new static)->$method(...$args);
    }
}

