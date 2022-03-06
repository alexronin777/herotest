<?php

namespace modules\common;

use Illuminate\Container\Container;

abstract class Dependency
{
    protected Container $Container;

    public function __construct(Container $Container)
    {
        $this->Container = $Container;
    }

    abstract public function register(?array $configuration);
}