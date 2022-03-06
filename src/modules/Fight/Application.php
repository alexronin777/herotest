<?php

namespace modules\Fight;

use Illuminate\Container\Container;
use Symfony\Component\Console\Command\Command;

class Application
{
    private Container $container;

    public function __construct(?array $configuration)
    {
        $this->registerContainer();
        $this->registerDependencies($configuration);
    }

    public function getCommand($name): Command
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Expected string, got [' . gettype($name) . ']');
        }

        return $this->container->make($name);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getController($name, $responseType = 'direct')
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Expected string, got [' . gettype($name) . ']');
        }

        return $this->makeController($name);
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    protected function registerContainer()
    {
        $container = $this->buildContainer();
        $this->setContainer($container);
    }

    protected function buildContainer(): Container
    {
        return new Container();
    }

    protected function registerDependencies(?array $configuration)
    {
        $dependencyClasses = $this->getDependencyClasses();

        foreach ($dependencyClasses as $className) {
            $service = $this->buildDependencyService($className);
            $service->register($configuration);
        }
    }

    protected function getDependencyClasses()
    {
        $dependencyClasses = [];
        $dependenciesFileName = $this->getDependenciesFileName();
        if (\file_exists($dependenciesFileName)) {
            $dependencyClasses = require($dependenciesFileName);
        }
        return $dependencyClasses;
    }

    protected function getDependenciesFileName(): string
    {
        return $this->getRootPath() . '/Application/Config/dependencies.php';
    }

    /**
     * @throws \ReflectionException
     */
    protected function getRootPath(): string
    {
        $reflector = new \ReflectionClass(get_class($this));
        $fileName = $reflector->getFileName();

        return realpath(dirname($fileName));
    }

    protected function buildDependencyService($className)
    {
        if (class_exists($className)) {
            return new $className($this->container);
        }

        throw new \RuntimeException('Dependency Service [' . $className . '] not found');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function makeController($alias)
    {
        return $this->container->make($alias);
    }
}