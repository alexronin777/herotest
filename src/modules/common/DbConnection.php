<?php

namespace modules\common;

class DbConnection implements DbInterface
{
    private string $servername;
    private string $dbname;
    private string $username;
    private string $password;

    private \PDO $connection;

    public function __construct(string $servername, string $dbname, string $username, string $password)
    {
        $this->servername = $servername;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;

        $this->connection = new \PDO("mysql:host=" . $this->servername . ";dbname=" . $this->dbname, $this->username, $this->password);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function execute($params = null): bool
    {
        return $this->connection->execute($params);
    }

    public function lastInsertId($name = null): string
    {
        return $this->connection->lastInsertId();
    }

    public function prepare($query, array $options = array())
    {
        return $this->connection->prepare($query, $options);
    }

    public function bindParam($param, &$var, $type = \PDO::PARAM_STR, $maxLength = null, $driverOptions = null)
    {
        $this->connection->bindParam($param, $var, $type, $maxLength, $driverOptions);
    }

    public function bindFloat($placeholder, &$val)
    {
        $this->connection->bindParam($placeholder, $val);
    }

    public function bindInt($placeholder, &$val)
    {
        die('ssssss');
        $this->connection->bindParam($placeholder, $val, \PDO::PARAM_INT);
    }

    public function bindString($placeholder, &$val)
    {
        $this->connection->bindParam($placeholder, $val);
    }

    public function fetchAll(...$args)
    {
        return $this->connection->fetchAll(\PDO::FETCH_ASSOC, ...$args);
    }
}