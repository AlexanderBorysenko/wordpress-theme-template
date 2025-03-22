<?php
namespace ThemeCore\Services\ConfigurationService;

/**
 * Configuration Class
 * 
 * A singleton class to access configuration values from config files
 * using dot notation.
 */

class ConfigurationService
{
    /**
     * The singleton instance of the class
     */
    private static $instance = null;

    /**
     * Store loaded configurations
     */
    private $configs = [];

    private $configPath = 'config';

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
    }

    /**
     * Get the singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get configuration value using dot notation
     */
    public function get($key, $default = null)
    {
        $parts    = explode('.', $key);
        $filename = array_shift($parts);

        // Load the config file if not already loaded
        if (!isset($this->configs[$filename])) {
            $this->loadConfig($filename);
        }

        if (!isset($this->configs[$filename])) {
            return $default;
        }

        $config = $this->configs[$filename];
        foreach ($parts as $part) {
            if (!is_array($config) || !isset($config[$part])) {
                return $default;
            }
            $config = $config[$part];
        }

        return $config;
    }

    /**
     * Load configuration from file
     */
    private function loadConfig($name)
    {
        $configPath = path_join(get_template_directory(), $this->configPath);

        // Try PHP file first
        $phpFile = path_join($configPath, "$name.php");
        if (file_exists($phpFile)) {
            $this->configs[$name] = require $phpFile;
            return;
        }

        // Then try JSON file
        $jsonFile = path_join($configPath, "$name.json");
        if (file_exists($jsonFile)) {
            $jsonContents         = file_get_contents($jsonFile);
            $this->configs[$name] = json_decode($jsonContents, true);
            return;
        }

        $this->configs[$name] = [];
    }

}