
<?php

class Database extends PDO
{
    private static $instance = null;

    private function __construct($dsn, $user, $pass)
    {
        parent::__construct($dsn, $user, $pass);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function getInstance($dsn, $user = null, $pass = null)
    {
        if (self::$instance === null) {
            self::$instance = new Database($dsn, $user, $pass);
        }

        return self::$instance;
    }

    public function insert($table, array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->prepare($sql);
        $stmt->execute($data);

        return $this->lastInsertId();
    }

    public function update($table, array $data, array $where)
    {
        $set = [];
        $params = [];

        foreach ($data as $column => $value) {
            $set[] = "$column = :set_$column";
            $params["set_$column"] = $value;
        }

        $conditions = [];

        foreach ($where as $column => $value) {
            $conditions[] = "$column = :where_$column";
            $params["where_$column"] = $value;
        }

        $sql = "UPDATE $table SET " . implode(', ', $set)
             . " WHERE " . implode(' AND ', $conditions);

        return $this->prepare($sql)->execute($params);
    }

    public function delete($table, array $where)
    {
        $conditions = [];
        $params = [];

        foreach ($where as $column => $value) {
            $conditions[] = "$column = :where_$column";
            $params["where_$column"] = $value;
        }

        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $conditions);

        return $this->prepare($sql)->execute($params);
    }

    public function getRows($sql, array $params = [])
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getRow($sql, array $params = [])
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch();
    }
}
