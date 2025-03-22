<header <?= $htmlAttributesString(['class' => ['header']]) ?>>
    <?= component(
        'preheader',
        ['class' => ''],
        []
    ); ?>
    <div class="header__inner container-large">
        <?= component('header-logo', [
            'class' => 'header__logo',
        ]); ?>
        <?= component('header-navigation', [
            'class' => 'header__navigation',
        ]); ?>
        <?= component(
            'mobile-modals-header-toggle-button',
            ['class' => 'header__mobile-navigation-toggle-button'],
            []
        ); ?>
        <?= component('header-socials', [
            'class' => 'header__socials',
        ]); ?>

        <a href="#contact-modal" class="header__contact-button">
            <?= pll__('Contact Us') ?>
        </a>
    </div>
</header>