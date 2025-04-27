<?php
namespace ThemeCore\ThemeModules\ThemeDependenciesUpdater;

use ThemeCore\ThemeModules\ThemeModule;

/**
 * Module for updating theme core dependencies via Composer.
 */
class ThemeDependenciesUpdater extends ThemeModule
{

    /**
     * Slug for the admin page.
     */
    private const PAGE_SLUG = 'theme-dependencies-updater';

    /**
     * Nonce action name.
     */
    private const ACTION_NONCE = 'update_theme_dependencies_nonce';

    /**
     * Initialize hooks.
     */
    public function init()
    {
        add_action('admin_menu', [
            $this,
            'addAdminPage'
        ]);
        add_action('admin_init', [
            $this,
            'handleUpdateAction'
        ]);
    }

    /**
     * Add the admin page to the WordPress menu.
     */
    public function addAdminPage()
    {
        add_menu_page(
            __('Theme Dependencies Updater', 'theme-text-domain'), // Page title
            __('Theme Dependencies Updater', 'theme-text-domain'), // Menu title
            'manage_options', // Capability required
            self::PAGE_SLUG, // Menu slug
            [
                $this,
                'renderAdminPage'
            ], // Callback function to display the page content
            'dashicons-update', // Icon URL
            position: 1 // Position
        );
    }

    /**
     * Render the content of the admin page.
     */
    public function renderAdminPage()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p><strong><?php esc_html_e('If you are facing issues with editor blocks etc. after wordpress update, please press this button to update theme core dependencies.', 'theme-text-domain'); ?></strong>
            </p>

            <form method="post" action="">
                <?php wp_nonce_field(self::ACTION_NONCE, self::ACTION_NONCE . '_field'); ?>
                <input type="hidden" name="action" value="update_dependencies">
                <?php submit_button(__('Update Dependencies', 'theme-text-domain'), 'primary', 'update_deps_submit'); ?>
            </form>

            <?php
            // Display feedback notices, if any.
            settings_errors('theme_dependencies_update_notices');
            ?>
        </div>
        <?php
    }

    /**
     * Handle the form submission for updating dependencies.
     */
    public function handleUpdateAction()
    {
        // Check if our specific button was clicked and the action is set
        if (!isset($_POST['update_deps_submit']) || !isset($_POST['action']) || $_POST['action'] !== 'update_dependencies') {
            return;
        }

        // Verify nonce for security
        if (!isset($_POST[self::ACTION_NONCE . '_field']) || !wp_verify_nonce($_POST[self::ACTION_NONCE . '_field'], self::ACTION_NONCE)) {
            add_settings_error(
                'theme_dependencies_update_notices',
                'security_error',
                __('Security check failed.', 'theme-text-domain'),
                'error'
            );
            return;
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            add_settings_error(
                'theme_dependencies_update_notices',
                'permissions_error',
                __('You do not have sufficient permissions to perform this action.', 'theme-text-domain'),
                'error'
            );
            return;
        }

        // Get theme path and ThemeCore path
        $themePath = get_template_directory();
        // Assuming ThemeCore is directly inside the theme root. Adjust if necessary.
        $themeCorePath = $themePath . '/ThemeCore';

        if (!is_dir($themeCorePath)) {
            add_settings_error(
                'theme_dependencies_update_notices',
                'path_error',
                sprintf(__('Error: ThemeCore directory not found at %s', 'theme-text-domain'), '<code>' . esc_html($themeCorePath) . '</code>'),
                'error'
            );
            return;
        }

        // Check if proc_open is available
        if (!function_exists('proc_open')) {
            add_settings_error(
                'theme_dependencies_update_notices',
                'proc_open_disabled',
                __('Error: `proc_open` function is disabled on this server. Cannot execute composer commands.', 'theme-text-domain'),
                'error'
            );
            return;
        }

        // Prepare the command
        $command = sprintf(
            'cd %s && composer install && composer update',
            escapeshellarg($themeCorePath)
        );

        // Increase execution time limit if possible/allowed
        @set_time_limit(300); // 5 minutes, might not work in safe mode

        // Define descriptors for proc_open
        $descriptorspec = [
            0 => [
                "pipe",
                "r"
            ], // stdin
            1 => [
                "pipe",
                "w"
            ], // stdout
            2 => [
                "pipe",
                "w"
            ]  // stderr
        ];

        $pipes = [];
        // Execute the command using proc_open
        $process = proc_open($command, $descriptorspec, $pipes, $themeCorePath);

        $output = '';
        $errors = '';

        if (is_resource($process)) {
            // Close stdin as we're not sending any input
            fclose($pipes[0]);

            // Read stdout and stderr
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // Wait for the process to terminate and get the exit code
            $return_value = proc_close($process);

            // Combine output and errors for display, prioritizing errors if present
            $full_output = trim($output . "\n" . $errors);

            // Provide feedback based on the exit code and output/errors
            if ($return_value !== 0) {
                $error_message = sprintf(__('Error: Composer command failed with exit code %d.', 'theme-text-domain'), $return_value);
                if (strpos(strtolower($errors), 'command not found') !== false || strpos(strtolower($output), 'command not found') !== false) {
                    $error_message .= ' ' . __('`composer` command not found. Make sure Composer is installed and accessible in the system PATH.', 'theme-text-domain');
                } else {
                    $error_message .= ' ' . __('Details:', 'theme-text-domain');
                }
                add_settings_error(
                    'theme_dependencies_update_notices',
                    'update_error',
                    $error_message . '<br><pre>' . esc_html($full_output) . '</pre>',
                    'error'
                );
            } else {
                add_settings_error(
                    'theme_dependencies_update_notices',
                    'update_success',
                    __('Dependencies updated successfully. Output:', 'theme-text-domain') . '<br><pre>' . esc_html($full_output) . '</pre>',
                    'success' // Use 'success' type for a green notice
                );
            }
        } else {
            // proc_open failed to create the process
            add_settings_error(
                'theme_dependencies_update_notices',
                'proc_open_failed',
                sprintf(__('Error: Failed to execute command using `proc_open`. Command attempted: %s', 'theme-text-domain'), '<code>' . esc_html($command) . '</code>'),
                'error'
            );
        }


        // Optional: Redirect back to the same page to clear POST data and show notices cleanly.
        // wp_redirect(admin_url('admin.php?page=' . self::PAGE_SLUG));
        // exit;
        // Note: Handling in admin_init and using settings_errors often avoids the need for explicit redirection.
    }

}