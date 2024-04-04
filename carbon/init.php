<?php
add_action('after_setup_theme', 'crb_load');
function crb_load()
{
    \Carbon_Fields\Carbon_Fields::boot();
}

require_once 'blocks-extra-data.php';

function crb_init_fields()
{
    require_all(
        'carbon/theme-options',
        'carbon/term-meta',
        'carbon/post-meta',
        'carbon/user-meta',
    );
}
add_action('carbon_fields_register_fields', 'crb_init_fields');

function crb_init_blocks()
{
    require_all(
        'carbon/blocks',
    );
}