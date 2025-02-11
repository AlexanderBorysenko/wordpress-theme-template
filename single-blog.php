<?php
get_header();

component('breadcrumbs');

$author_full_name = get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name');
component(
    'blog-post-hero',
    [
        'title' => get_the_title(),
        'author' => $author_full_name,
        'date' => get_the_date(),
        'image' => get_image(get_post_thumbnail_id()),
    ]
);

component('blog-post-content');

$random_blog_posts = get_posts([
    'numberposts' => 8,
    'orderby' => 'rand',
    'exclude' => get_the_ID(),
    'post_type' => 'blog'
]);
component('blog-posts-list-section', [
    'pre_title' => 'Our Blog',
    'title' => 'News & Feeds From Homes by Creation',
    'items' => $random_blog_posts,
    'class' => 'mb-5-5'
]);
component('contact-us-section');

get_footer();