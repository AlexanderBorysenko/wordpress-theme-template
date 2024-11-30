<?php
/**
 * SVG icon component
 * @param string $icon
 * @param string $class
 * @param array $arrtibutes
 */
?>
<svg <?= assemble_html_attributes($props, [
    'class' => 'svg-icon'
]) ?>>
    <use href="<?= get_template_directory_uri() ?>/source/images/icons.svg#<?= $icon ?>"></use>
</svg>