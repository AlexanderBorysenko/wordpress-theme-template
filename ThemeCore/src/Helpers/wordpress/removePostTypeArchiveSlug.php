<?php

/**
 * Removes the post type slug from the permalink structure for a given post type.
 * [Same description and IMPORTANT notes as before]
 *
 * @param string $postType The slug of the custom post type (e.g., 'service').
 */
function removePostTypeArchiveSlug(string $postType): void
{

    if (empty($postType)) {
        trigger_error('Post type slug cannot be empty for removePostTypeArchiveSlug_v2()', E_USER_WARNING);
        return;
    }

    // --- Filter 1: Modify the generated permalink (Identical to previous version) ---
    add_filter('post_type_link', function (string $post_link, WP_Post $post) use ($postType): string {
        if ($post->post_type === $postType) {
            $cpt_object = get_post_type_object($postType);
            if ($cpt_object && isset($cpt_object->rewrite['slug'])) {
                $original_link_before_replace = $post_link; // For debugging
                $cpt_slug                     = $cpt_object->rewrite['slug'];
                $slug_to_remove               = '/' . trim($cpt_slug, '/') . '/';
                $post_link                    = str_replace($slug_to_remove, '/', $post_link);

                // Debugging Log:
                // error_log("CPT Link Filter V2: Original Link for Post {$post->ID} ({$post->post_type}): " . $original_link_before_replace);
                // error_log("CPT Link Filter V2: Modified Link for Post {$post->ID}: " . $post_link);

            } else {
                // Fallback/Warning
                $slug_to_remove = '/' . $postType . '/';
                if (strpos($post_link, $slug_to_remove) !== false) {
                    $post_link = str_replace($slug_to_remove, '/', $post_link);
                    trigger_error("removePostTypeArchiveSlug_v2: Could not find rewrite->slug for CPT '{$postType}'. Attempting fallback.", E_USER_NOTICE);
                }
            }
        }
        return $post_link;
    }, 10, 2);

    // --- Action 2: Help WordPress interpret the incoming request URL (Revised Checks) ---
    add_action('pre_get_posts', function (WP_Query $query) use ($postType): void {

        // --- Basic checks: Only run on main frontend queries ---
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // Debugging Log: Log query vars for main frontend queries
        // error_log("Pre Get Posts V2: Query Vars: " . print_r($query->query_vars, true));

        // --- Condition Check: Is WP trying to resolve a slug ('name' or 'pagename')? ---
        // Use 'pagename' as well, as WP might interpret a root slug as a page initially.
        $potential_slug = $query->get('name') ?: $query->get('pagename');

        if (empty($potential_slug)) {
            // Not a request based on a slug we can handle here.
            return;
        }

        // --- Condition Check: Has WP already figured out the post type? ---
        // If it already knows it's our CPT OR some other specific CPT/post type (not post/page), leave it alone.
        $current_post_type = $query->get('post_type');
        if (
            !empty($current_post_type) && !is_array($current_post_type) && !in_array($current_post_type, [
                '',
                'post',
                'page'
            ], true)
        ) {
            // WP seems to know what it's doing, or it's a query for multiple types. Don't interfere.
            // error_log("Pre Get Posts V2: Query already has specific post_type '{$current_post_type}'. Skipping.");
            return;
        }

        // --- Check if a post/page with this slug exists in the TARGET post type ---
        $args        = [
            'name'           => sanitize_key($potential_slug), // Use the determined slug
            'post_type'      => $postType, // Check *only* in our target CPT
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ];
        $posts_exist = get_posts($args);

        // --- If we found exactly one published post in our CPT with that slug ---
        if (count($posts_exist) === 1) {

            // Debugging Log:
            // error_log("Pre Get Posts V2: Found matching post ID {$posts_exist[0]} in CPT '{$postType}' for slug '{$potential_slug}'. Modifying query.");

            // --- Modify the query ---
            $query->set('post_type', $postType); // Set the correct post type
            $query->set('name', sanitize_key($potential_slug)); // Ensure 'name' is set correctly

            // Unset potential page conflicts explicitly
            $query->set('pagename', '');
            $query->set('page_id', ''); // Might also be relevant

            // Set query flags to ensure WP treats it as a single post display
            $query->is_single   = true;
            $query->is_singular = true; // General flag for single items
            $query->is_page     = false;
            $query->is_archive  = false;
            $query->is_home     = false;
            // Potentially add more flags if needed (e.g., is_attachment = false)

        } else {
            // Debugging Log:
            // if (empty($posts_exist)) { error_log("Pre Get Posts V2: No post found in CPT '{$postType}' for slug '{$potential_slug}'."); }
            // else { error_log("Pre Get Posts V2: Found multiple posts (".count($posts_exist).") in CPT '{$postType}' for slug '{$potential_slug}'. Ambiguous."); }
        }

    }, 10, 1); // Priority 10
}