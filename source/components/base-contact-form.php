<form action="<?= $action ?>" class="base-contact-form <?= $class ?? '' ?>">
    <?= $slot ?>
    <input type="hidden" name="nonce" value="<?= wp_create_nonce('contact-form') ?>" />
</form>