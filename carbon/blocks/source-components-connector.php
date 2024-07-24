<?php
foreach (glob(get_template_directory() . '/source/components/*.carbon.php') as $file) {
    require_once $file;
}