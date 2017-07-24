<?php

namespace Sneek\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Proccess the controllers and resolve their dependencies
 * @author Flávio Jr.
 */
class ControllerProvider implements ServiceProviderInterface
{
    /**
     * The directory where the controllers are located
     * @var string
     */
    protected $controllerDir;

    /**
     * The root namespace of your controller class
     * @var [type]
     */
    protected $rootNamespace;

    /**
     * The root part of the namespace
     * Not necessary if the namespace follow the same directory strucure
     * @var [type]
     */
    protected $rootMirror;

    public function __construct($controllerDir, $rootNamespace, $rootMirror = null)
    {
        $this->controllerDir = $controllerDir;
        $this->rootNamespace = $rootNamespace;
        $this->rootMirror = $rootMirror;
    }

    /**
     * Register the controllers
     * @param  Container $app
     * @return void
     */
    public function register(Container $app)
    {
        $this->registerControllers($app);
    }

    /**
     * Iterates over the controller directory and register their namespaces
     * @param  Container $app
     * @return void
     */
    public function registerControllers(Container $app)
    {
        if (!is_dir($this->controllerDir)) {
            throw new \Exception("Diretório não existe");
        }

        $files = array_diff(scandir($this->controllerDir), ['.', '..']);

        foreach ($files as $file) {
            $className = $this->translateDirectoryToNamespace($file);

            $reflection = new \ReflectionClass($className);

            $constructor = $reflection->getConstructor();

            $parameters = [];

            if ($constructor) {
                $parameters = $this->resolveDependecies($constructor->getParameters());
            }

            //Instantiate the class passing it dependencies
            $controller = $reflection->newInstanceArgs($parameters);

            //Register the controller indexed by its namespace
            $app[$className] = function () use($controller) {
                return $controller;
            };
        }
    }

    /**
     * Replace the file path to a namespacec
     * @param  string $classFile The path do the class file
     * @return string The full qualified class name
     */
    private function translateDirectoryToNamespace($classFile)
    {
        $fileName = $this->controllerDir . '/' . $classFile;

        $fileArray = array_reverse(explode('/', $fileName));

        $className = '';

        foreach ($fileArray as $piece) {

            if ($piece == $this->rootMirror) {
                $piece = $this->rootNamespace;
            }

            $className = $piece . '\\' . $className;

            if ($piece == $this->rootNamespace) {
                break;
            }
        }

        return rtrim($className, '.php\\');
    }

    /**
     * Resolve the constructor dependencies
     * @param  array  $params
     * @return array  The dependencies of the controller
     */
    private function resolveDependecies(array $params)
    {
        $controllerParams = [];

        foreach ($params as $param) {

            if (!$param->getClass()) {
                throw new \Exception('Não é possível resolver dependência diferente do tipo objeto');
            }

            $class = $param->getClass()->name;

            $reflectClass = new \ReflectionClass($class);

            $dependencieParams = [];

            if($construct = $reflectClass->getConstructor()) {
                $dependencieParams = $this->resolveDependecies($construct->getParameters());
            }

            $controllerParams[] = $reflectClass->newInstanceArgs($dependencieParams);
        }

        return $controllerParams;
    }
}
