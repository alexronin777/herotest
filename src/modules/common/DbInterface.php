<?php

namespace modules\common;

interface DbInterface
{
    public function execute($params = null): bool;

    public function lastInsertId($name = null);

    public function prepare($query, array $options = array());

    public function bindParam($param, &$var, $type = \PDO::PARAM_STR, $maxLength = null, $driverOptions = null);

    public function fetchAll(...$args);

    public function bindInt($placeholder, &$val);

    public function bindString($placeholder, &$val);

    public function bindFloat($placeholder, &$val);

}