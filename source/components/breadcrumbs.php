<?php
/**
 * Breadcrumbs
 */
?>

<div <?= $htmlAttributesString(['class' => [
    'breadcrumbs',
    'container-large' => $block ?? false,
    '_block'          => $block ?? false,
]]) ?>>
    <div class="breadcrumbs__inner">
        <?php
        if (function_exists('yoast_breadcrumb')) {
            yoast_breadcrumb('<p id="breadcrumbs" class="breadcrumbs__navigation">', '</p>');
        }
        ?>
        <?php if (!($disable_copy_button ?? false)) { ?>
            <?= component('copy-button'); ?>
        <?php } ?>
    </div>
</div>