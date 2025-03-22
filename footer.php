<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hacon
 */
?>
<?= component('mobile-contact-bar'); ?>
</main>

<?= component('contact-modal'); ?>
<?= component('footer-component') ?>

<?php wp_footer(); ?>

</body>

</html>