<?php
/**
 * Content Block
 */
?>

<section <?= $htmlAttributesString(['class' => 'content-block container-large']) ?>>
    <?= component(
        'content-block-typography-wrapper',
        ['class' => 'content-block__main'],
        [
            'slot' => $slot,
        ]
    ); ?>
    <div class="content-block__image-container">
        <?= component(
            'image-component',
            ['class' => 'content-block__image'],
            [
                'reference' => $image ?? null,
            ]
        ); ?>
    </div>
</section>