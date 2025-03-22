<?php
namespace ThemeCore\ThemeModules\CarbonFields\Managers;

use Carbon_Fields\Container;

class ThemeOptionsManager
{
    /**
     * Store group containers
     */
    private static $groupContainers = [];

    /**
     * Store the default container for each group
     */
    private static $defaultContainerIds = [];

    /**
     * Store container instances
     */
    private static $childContainers = [];

    /**
     * Get the internal slug for a group. The slug for usage only in scope of this class
     * 
     * @param string $groupName The name of the group
     * @return string The internal slug
     */
    private static function getGroupInternalSlug($groupName): string
    {
        return sanitize_title($groupName);
    }

    /**
     * Create a new theme options menu group
     * 
     * @param string $groupName The name of the group
     * @return string The slug of the created container
     */

    public static function createRootContainer($groupName, $disableOwnPage = true): \Carbon_Fields\Container\Theme_Options_Container
    {
        $groupInternalSlug = self::getGroupInternalSlug($groupName);

        // Check if already registered
        if (isset(self::$groupContainers[$groupInternalSlug])) {
            return self::$groupContainers[$groupInternalSlug];
        }

        // Create the container
        $container = Container::make('theme_options', $groupName);

        // Store container instance in groupContainers
        self::$groupContainers[$groupInternalSlug] = $container;

        if ($disableOwnPage) {
            self::disableOwnPage($groupName);
        }

        return self::$groupContainers[$groupInternalSlug];
    }

    /**
     * Disable the default page for a group
     * 
     * @param string $groupName The name of the group
     */
    public static function disableOwnPage($groupName)
    {
        $groupInternalSlug = self::getGroupInternalSlug($groupName);

        // Check if group exists
        if (!isset(self::$groupContainers[$groupInternalSlug])) {
            throw new \Exception("Group '{$groupName}' does not exist");
        }

        $containerId = self::$groupContainers[$groupInternalSlug]->get_id();
        $wpSlug      = "crb_$containerId.php";

        // Remove the direct submenu entry
        add_action('admin_menu', function () use ($wpSlug) {
            remove_submenu_page($wpSlug, $wpSlug);
        }, 999);

        // Handle redirection to default container (if set)

        add_action('admin_init', function () use ($wpSlug, $groupInternalSlug) {
            if (!isset(self::$defaultContainerIds[$groupInternalSlug])) {
                return;
            }
            $defaultContainerId     = self::$defaultContainerIds[$groupInternalSlug];
            $defaultContainerwpSlug = "crb_$defaultContainerId.php";
            if (!empty($_GET['page']) && $_GET['page'] === $wpSlug) {
                wp_redirect(admin_url("admin.php?page={$defaultContainerwpSlug}"));
                exit;
            }
        });
    }

    /**
     * Create a child container under a parent group
     * 
     * @param string $parentGroupName The name of the parent group
     * @param string $containerName The name of the new container
     * @param bool $makeDefault Whether to set this container as default for the group (defaults to false)
     * @return \Carbon_Fields\Container\Theme_Options_Container The created container
     */
    public static function createChildContainer($parentGroupName, $containerName, $makeDefault = false): \Carbon_Fields\Container\Theme_Options_Container
    {
        $parentGroupSlug = self::getGroupInternalSlug($parentGroupName);

        // Check if parent group exists
        if (!isset(self::$groupContainers[$parentGroupSlug])) {
            try {
                $container = self::createRootContainer($parentGroupName);
            } catch (\Exception $e) {
                throw new \Exception("Parent group '{$parentGroupName}' does not exist");
            }
        }

        $parentContainer = self::$groupContainers[$parentGroupSlug];

        $container = Container::make('theme_options', $containerName)
            ->set_page_parent($parentContainer);

        $containerId = $container->get_id();

        // Store container instance in groupContainers
        self::$childContainers[$containerId] = $container;

        // Set as default container for this group if needed
        if ($makeDefault || !isset(self::$defaultContainerIds[$parentGroupSlug])) {
            self::$defaultContainerIds[$parentGroupSlug] = $containerId;
        }

        return $container;
    }

    public static function createPostTypeChildContainer($postType, $groupName): \Carbon_Fields\Container\Theme_Options_Container
    {
        $container = Container::make('theme_options', $groupName)
            ->set_page_parent("edit.php?post_type=$postType");

        return $container;
    }

}