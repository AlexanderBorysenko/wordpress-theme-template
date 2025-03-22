<?php
/**
 * SVG icon component
 * @param string $icon
 * @param string $class
 * @param array $arrtibutes
 */

$version = filemtime(get_template_directory() . '/source/images/icons.svg');
?>
<svg <?= $htmlAttributesString([
    'class' => 'svg-icon',
]) ?>>
    <use href="<?= get_template_directory_uri() ?>/source/images/icons.svg?v=<?= $version ?>#<?= $icon ?>"></use>
</svg>