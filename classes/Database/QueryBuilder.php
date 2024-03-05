<?php
class MySqlQueryBuilder
{

    /**
     * connection instance 
     *
     * @var \PDO
     */
    private $connection;
    /**
     * table name
     *
     * @var string
     */
    private $table;

    /**
     * Total Rows
     *
     * @var int
     */
    private $rows = 0;

    /**
     * user's data container
     *
     * @var array
     */
    private $data = [];

    /**
     * container used for where statements
     *
     * @var array
     */
    private $wheres = [];

    /**
     * container used for select statements
     *
     * @var array
     */
    private $selects = [];

    /**
     * container used for join statements
     *
     * @var array
     */
    private $joins = [];

    /**
     * user's bindings container   
     *
     * @var array
     */
    private $bindings = [];


    public function __construct(MySqlConnection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * set the table name
     *
     * @param  string $table
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }


    /**
     * from statement
     *
     * @param  mixed $table
     * @return $this
     */
    public function from($table)
    {
        return $this->table($table);
    }

    /**
     * set select clause
     *
     * @param  mixed $selects
     * @return object
     */
    public function select(...$selects)
    {
        $this->selects = array_merge($this->selects, $selects);

        return $this;
    }


    /**
     * get only  one record
     *
     * @param  string $table
     * @return \stdclass
     */
    public function get($table = null)
    {
        if ($table) {
            $this->table($table);
        }

        $sql = $this->fetchStatement();

        $result = $this->queryExcute($sql, $this->bindings)->fetch();

        $this->reset();

        return $result;
    }

    /**
     * get All Records from Table
     *
     * @param string $table
     * @return array
     */
    public function getAll($table = null)
    {
        if ($table) {
            $this->table($table);
        }

        $sql = $this->fetchStatement();

        $query = $this->queryExcute($sql, $this->bindings);

        $results = $query->fetchAll();

        $this->rows = $query->rowCount();

        $this->reset();

        return $results;
    }

    /**
     * Get total rows from last fetch all statement
     *
     * @return int
     */
    public function rows()
    {
        return $this->rows;
    }

    /**
     * Prepare Select Statement
     *
     * @return string
     */
    private function fetchStatement()
    {
        $sql = 'SELECT ';

        if ($this->selects) {
            $sql .= implode(',', $this->selects);
        } else {
            $sql .= '*';
        }

        $sql .= ' FROM ' . $this->table . ' ';

        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' ', $this->wheres);
        }

        return $sql;
    }


    /**
     * sql WHERE clause
     *
     * @param  mixed $params
     * @return object
     */
    public function where(...$params)
    {
        $sql = array_shift($params);

        $this->addToBindings($params);

        $this->wheres[] = $sql;

        return $this;
    }


    /**
     * merge user data if array to data container
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return $this
     */
    public function data($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);

            $this->addToBindings($key);
        } else {
            $this->data[$key] = $value;

            $this->addToBindings($value);
        }

        return $this;
    }


    /**
     * Insert to database table
     * 
     * @param array $data
     * @return object
     */
    public function insert($table = null)
    {
        if ($table) {
            $this->table($table);
        }

        $sql = 'INSERT INTO ' . $this->table . ' SET ';

        $sql .= $this->setFields();

        $this->queryExcute($sql, $this->bindings);

        $this->reset();

        return $this;
    }

    /**
     * Delete Clause
     *
     * @param string $table
     * @return $this
     */
    public function delete($table = null)
    {
        if ($table) {
            $this->table($table);
        }

        $sql = 'DELETE FROM ' . $this->table . ' ';

        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' ' , $this->wheres);
        }

        $this->queryExcute($sql, $this->bindings);

        $this->reset();

        return $this;
    }


    /**
     * update database record
     *
     * @param  mixed $table
     * @return object
     */
    public function update($table)
    {
        if ($table) {
            $this->table($table);
        }
        $sqlQuery = "UPDATE " . $this->table . " SET ";

        $sqlQuery .= $this->setFields();

        if ($this->wheres) {
            $sqlQuery .= ' WHERE ' . implode(' and ', $this->wheres);
        }
        $this->queryExcute($sqlQuery, $this->bindings);
        return $this;
    }

    /**
     * Set the query for insert and update
     *
     * @return string
     */
    private function setFields()
    {
        $sql = '';

        foreach (array_keys($this->data) as $key) {
            $sql .= '`' . $key . '` = ? , ';
        }

        $sql = rtrim($sql, ', ');

        return $sql;
    }


    /**
     * adding values to bindings array
     *
     * @param  mixed $value
     * @return void
     */
    private function addToBindings(string|array $value)
    {
        if (is_array($value)) {
            $this->bindings = array_merge($this->bindings, array_values($value));
        } else {
            $this->bindings[] = $value;
        }
    }


    /**
     * executing a sql query
     *
     * @param  mixed $params
     * @return \PDOStatement
     */
    private function queryExcute(...$params)
    {
        $sqlQuery = array_shift($params);

        if (count($params) == 1 and is_array($params[0])) {
            $params = $params[0];
        }

        try {
            $query = $this->connection->getConnection()->prepare($sqlQuery);

            foreach ($params as $key => $value) {
                $query->bindValue($key + 1, htmlspecialchars($value));
            }

            $query->execute();

            return $query;

        } catch (\PDOException $e) {

            echo $sqlQuery;
            echo '<pre>';
            print_r($this->bindings);
            echo '</pre>';
            die($e->getMessage());
        }
    }

    /**
     * Reset All Data
     *
     * @return void
     */
    private function reset()
    {
        $this->table = null;
        $this->data = [];
        $this->joins = [];
        $this->wheres = [];
        $this->selects = [];
        $this->bindings = [];
    }
}