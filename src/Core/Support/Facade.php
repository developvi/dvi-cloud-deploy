<?php

namespace DVICloudDeploy\Core\Support;

abstract class Facade
{
    /**
     * Store the resolved instances.
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Get the underlying class instance for the facade.
     *
     * @return object
     * @throws \Exception
     */
    public static function getFacadeRoot()
    {
        $class = static::getFacadeAccessor();

        if (!class_exists($class)) {
            throw new \Exception("Class {$class} does not exist.");
        }

        // Instantiate the class if it doesn't exist in the instances array
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new $class();
        }

        return static::$instances[$class];
    }

    /**
     * Define the accessor for the facade (must be implemented in child classes).
     *
     * @return string
     */
    abstract protected static function getFacadeAccessor();

    /**
     * Handle dynamic, static calls to the facade.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (!method_exists($instance, $method)) {
            throw new \Exception("Method {$method} does not exist on class " . get_class($instance));
        }

        return $instance->$method(...$args);
    }
}
