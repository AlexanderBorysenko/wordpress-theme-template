<section class="content-block container-fluid <?= $class ?? '' ?>">
    <?= component('content-block-typography', [
        'class' => 'content-block__main',
        'slot' => $slot
    ]); ?>
    <div class="content-block__media">
        <?php
        if (count($images) <= 1):
            $image = get_image($images[0]);
            if ($image):
                ?>
                <div class="content-block__image-container">
                    <?= component('image-component', [
                        'image' => $image,
                        'class' => 'content-block__image',
                    ]); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
        <?php endif; ?>
    </div>
</section>