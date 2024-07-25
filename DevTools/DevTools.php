<?php
namespace DevTools;

use DevTools\Tools\GitSync;
use DevTools\Tools\ImagesDownloader;
use DevTools\Tools\OldWebsiteParcer;

class DevTools
{
    /*
     * Connect all tools here
     */
    private $tools = [
        GitSync::class,
        ImagesDownloader::class,
        OldWebsiteParcer::class
    ];

    public const ADMIN_MENU_SLUG = 'migration-tools';

    private static $instance;
    private function __construct()
    {
        add_action('admin_menu', array($this, 'addAdminMenu'));
        $this->connectTools();
    }

    private function connectTools()
    {
        foreach ($this->tools as $tool) {
            $toolInstance = new $tool();
            $toolInstance->initWpInterfaces();
        }
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addAdminMenu()
    {
        add_menu_page(
            'Dev Tools',
            'Dev Tools',
            'manage_options',
            self::ADMIN_MENU_SLUG,
            array($this, 'view'),
            'dashicons-migrate'
        );
    }

    public function view()
    {
        echo '<div class="wrap">';
        echo '<h2>Dev Tools</h2>';
    }
}
