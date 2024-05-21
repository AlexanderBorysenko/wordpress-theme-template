<?php
/**
 * Prints the variable in a formatted way
 *
 * @param $args The variables to be printed
 * @return void
 */
global $dumps;
$dumps = [];

function dump()
{
    foreach (func_get_args() as $var) {
        global $dumps;
        $backtrace = debug_backtrace();
        $file = $backtrace[0]['file'];
        $line = $backtrace[0]['line'];
        $context = "$file:$line";
        $dumps[] = [
            'context' => $context,
            'var' => $var
        ];
    }
}

// add the wp_footer action to print the dumps with style

add_action('wp_footer', function () {
    global $dumps;

    if (empty ($dumps)) {
        return;
    }

    ?>
<style>
.wp-dump {
    background: #f9f9f9;
    border: 1px solid #ccc;
    color: #000;
    display: block;
    margin: 0;
    overflow: auto;
    padding: 10px;
    position: fixed;
    top: 10vh;
    left: 10vh;
    right: 10vh;
    bottom: 10vh;
    z-index: 9999;
}

.wp-dump-section {
    margin-bottom: 20px;
    border: 1px solid #ccc;
    padding: 10px;
}

</style>
<div class="wp-dump">
    <?php
        foreach ($dumps as $key => $dump) {
            ?>
    <div class="wp-dump-section">
        <h3>
            <?php echo $dump['context']; ?>
        </h3>
        <pre><?php print_r($dump['var']); ?></pre>
    </div>
    <?php
        }
        ?>
</div>
<?php
});
