<?php
/**
 * Description: Adds a Git Sync page to the WordPress admin panel.
 */

// Hook to add the admin menu item
add_action('admin_menu', 'git_sync_menu');

function git_sync_menu()
{
    add_menu_page(
        'Git Sync',
        'Git Sync',
        'manage_options',
        'git-sync',
        'git_sync_page',
        'dashicons-networking',
        999
    );
}

function git_sync_page()
{
    if (isset($_POST['git_branch'])) {
        $branch = sanitize_text_field($_POST['git_branch']);
        $output = git_sync_branch($branch);
        echo '<pre>' . esc_html($output) . '</pre>';
    }

    $branches = git_sync_get_branches();
    ?>
<div class="wrap">
    <h1>Git Sync</h1>
    <form method="post" action="">
        <label for="git_branch">Select Branch:</label>
        <select name="git_branch" id="git_branch">
            <?php foreach ($branches as $branch): ?>
            <option value="<?php echo esc_attr($branch); ?>"><?php echo esc_html($branch); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Pull Branch" class="button button-primary">
    </form>
</div>
<?php
}

function git_sync_get_branches()
{
    $output = [];
    $template_directory = get_template_directory();
    exec("cd {$template_directory} && git branch", $output);
    $branches = array_map('trim', $output);
    return $branches;
}

function git_sync_branch($branch)
{
    $output = [];
    $template_directory = get_template_directory();

    $branch = str_replace('* ', '', $branch);
    $branch = trim($branch);

    exec("cd {$template_directory} && git checkout {$branch} && git pull", $output);
    return implode("\n", $output);
}
?>
