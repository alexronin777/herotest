<?php

namespace modules\Fight\Application\Config\Dependency;

use modules\common\DbConnection;
use modules\common\DbInterface;
use modules\common\Dependency;

class PersistenceDependency extends Dependency
{
    public function register(?array $configuration)
    {
        $this->Container->singleton(DbInterface::class, function () use ($configuration) {
            return new DbConnection($configuration['servername'], $configuration['dbname'], $configuration['username'], $configuration['password']);
        });
    }
}