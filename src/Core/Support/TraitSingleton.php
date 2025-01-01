<?php

namespace DVICloudDeploy\Core\Support;


trait TraitSingleton
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * SingletonTrait constructor.
     */
    private function __construct() {}

    /**
     * Prevent the instance from being cloned
     */
    private function __clone() {}

    /**
     * Prevent from being unserialized
     */
    private function __wakeup() {}
}
