<?php
namespace ThemeCore\ThemeModules;

use ThemeCore\Contracts\ThemeModuleInterface;

class ThemeModule implements ThemeModuleInterface
{
    protected static $instances;

    protected function __construct($config = [])
    {
    }

    public function init()
    {
    }

    /**
     * @return static
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (!isset(static::$instances[get_called_class()])) {
            // Check if the class that is being called from is the same as the current instance
            $calledClass = get_called_class();
            if ($calledClass::$instance === null) {
                throw new \Exception("$calledClass is not initialized");
            }
        }
        return static::$instances[get_called_class()];
    }

    public static function initModule(array $config = []): void
    {
        $calledClass = get_called_class();
        if (isset(static::$instances[$calledClass])) {
            return;
        }
        static::$instances[$calledClass] = new static($config);
        static::$instances[$calledClass]->init();
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

}