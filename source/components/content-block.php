<section class="content-block container-large <?= $class ?? '' ?>">
    <?= component('content-block-typography-wrapper', [
        'class' => 'content-block__main',
        'slot' => $slot
    ]); ?>
    <?php
    $image = get_image($image);
    if ($image):
        ?>
        <div class="content-block__media-container">
            <img src="<?= $image['src'] ?>" loading="lazy" alt="<?= $image['alt'] ?>" width="<?= $image['width'] ?>"
                height="<?= $image['height'] ?>" sizes="<?= $image['sizes'] ?>" srcset="<?= $image['srcset'] ?>"
                class="content-block__image">
        </div>
    <?php endif; ?>
</section>