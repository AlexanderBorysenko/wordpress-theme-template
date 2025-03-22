<?php
function getLanguages()
{
    if (!function_exists('pll_the_languages')) {
        return [];
    }

    // Get args for pll_the_languages with context awareness
    $args = [
        'show_flags'       => 0,
        'display_names_as' => 'name',
        'hide_if_empty'    => 0,
        'echo'             => 0,
        'raw'              => 1,
        'force_home'       => 0, // Don't force home URL
    ];

    // Add context-specific parameters
    $queried_object_id = get_queried_object_id();
    if (is_singular() && $queried_object_id) {
        $args['post_id'] = $queried_object_id;
    } elseif ((is_tax() || is_category() || is_tag()) && $queried_object_id) {
        $args['term_id'] = $queried_object_id;
    }

    $languages = pll_the_languages($args);

    // Return empty array if no languages found
    if (!$languages) {
        return [];
    }

    return $languages;
}