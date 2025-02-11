<?php
namespace DevTools\Tools;

class GitSync extends Tool
{
    protected $toolName = 'Git Sync';
    protected $ajaxAction = 'git_sync';

    public function view()
    {
        if (isset($_POST['git_branch'])) {
            $branch = sanitize_text_field($_POST['git_branch']);
            $output = $this->pullBranch($branch);
            echo '
        <pre>' . esc_html($output) . '</pre>';
        }

        $branches = $this->getBranches();
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

    public function getBranches(): array
    {
        $output = [];
        $template_directory = get_template_directory();
        exec("cd {$template_directory} && /usr/bin/git branch 2>&1", $output, $result_code);
        $branches = array_map('trim', $output);
        return $branches;
    }

    public function pullBranch($branch): string
    {
        $output = [];
        $template_directory = get_template_directory();

        $branch = str_replace('* ', '', $branch);
        $branch = trim($branch);

        exec("cd {$template_directory} && /usr/bin/git reset --hard 2>&1 && /usr/bin/git checkout {$branch} 2>&1 && /usr/bin/git pull 2>&1", $output, $result_code);
        return "$result_code\n\n" . implode("\n", $output);
    }
}