<?php

class DatabaseService
{
    protected $pdo = null;

    /**
     * @return PDO
     */
    protected function getPdo()
    {
        if (!$this->pdo)
        {
            $this->pdo = new PDO(Config::get('database.dsn'), Config::get('database.user'), Config::get('database.password'));
            $this->pdo->exec('SET NAMES utf8');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $this->pdo;
    }

    public function generateUuid($length = 32)
    {
        return substr(sha1(uniqid('', true) . microtime(true)), 0, $length);
    }

    public function getTableRow($table_name, $where_query, array $where_parts = array())
    {
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `' . $table_name . '` WHERE ' . $where_query;
        $statement = $pdo->prepare($sql);
        $statement->execute($where_parts);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result)
        {
            throw new Exception('Cannot find row in table ' . $table_name . ' with sql: ' . $sql);
        }

        return $result;
    }

    public function getTableRows($table_name, $where_query, array $where_parts = array())
    {
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `' . $table_name . '` WHERE ' . $where_query;
        $statement = $pdo->prepare($sql);
        $statement->execute($where_parts);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!$results)
        {
            return array();
        }

        return $results;
    }

    public function countTableRows($table_name, $where_query = '', array $where_parts = array())
    {
        $pdo = $this->getPdo();
        if ($where_query)
        {
            $where_query = ' WHERE ' . $where_query;
        }

        $sql = 'SELECT COUNT(*) __count FROM `' . $table_name . '` ' . $where_query;
        $statement = $pdo->prepare($sql);
        $statement->execute($where_parts);

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        return (int) $results['__count'];
    }

    /**
     * @return {Number}
     */
    public function deleteTableRows($table_name, $where_query, array $where_parts = array())
    {
        $pdo = $this->getPdo();
        $sql = 'DELETE FROM `' . $table_name . '` WHERE ' . $where_query;
        $statement = $pdo->prepare($sql);
        $statement->execute($where_parts);

        return $statement->rowCount();
    }

    public function deleteTableRow($table_name, $where_query, array $where_parts = array())
    {
        $pdo = $this->getPdo();
        $sql = 'DELETE FROM `' . $table_name . '` WHERE ' . $where_query;
        $statement = $pdo->prepare($sql);
        $statement->execute($where_parts);

        if (!$statement->rowCount())
        {
            throw new Exception('Cannot delete row in table ' . $table_name . ' with sql: ' . $sql);
        }
    }

   public function updateTableRow($table_name, $update_query, $where_query, array $where_parts = array())
    {
        $pdo = $this->getPdo();
        $sql = 'UPDATE `' . $table_name . '` SET ' . $update_query . ' WHERE ' . $where_query;
        $statement = $pdo->prepare($sql);
        $statement->execute($where_parts);
        
        if (!$statement->rowCount())
        {
            throw new Exception('Cannot update row in table ' . $table_name . ' with sql: ' . $sql);
        }
    }

    /**
     * @return {Number}
     */
    public function updateTableRows($table_name, $update_query, $where_query, array $where_parts = array())
    {
        $pdo = $this->getPdo();
        $sql = 'UPDATE `' . $table_name . '` SET ' . $update_query . ' WHERE ' . $where_query;
        $statement = $pdo->prepare($sql);
        $statement->execute($where_parts);

        return $statement->rowCount();
    }

    /**
     * @return {String}
     */
    public function createTableRow($table_name, array $data)
    {
        if (!isset($data['id']))
        {
            $data['id'] = $this->generateUuid();
        }
        
        $pdo = $this->getPdo();
        $values = array();
        $keys = array();
        $values_question_marks = array();
        foreach ($data as $key => $value)
        {
            $keys[] = '`' . $key . '`';
            $values[] = $value;
            $values_question_marks[] = '?';
        }
        
        $sql = 'INSERT INTO `' . $table_name . '` (' . implode(',', $keys) . ') VALUES (' . implode(',', $values_question_marks) . ')';
        $statement = $pdo->prepare($sql);
        $statement->execute($values);

        if (!$statement->rowCount())
        {
            throw new Exception('Cannot create row in table ' . $table_name . ' with sql: ' . $sql);
        }
        
        return $data['id'];
    }

    public function queryRows($query, array $values = array())
    {
        $pdo = $this->getPdo();
        $statement = $pdo->prepare($query);
        $statement->execute($values);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!$results)
        {
            return array();
        }

        return $results;
    }



}
