<?php
use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('content-block', 'Content Block')
    ->add_fields([
        Field::make('media_gallery', 'images', 'Images'),
        get_margin_bottom_select_field()->set_attribute('data-toggle-block-class', 'true'),
        Field::make('select', 'orientation', 'Image Orientation')
            ->add_options([
                '_left-image' => 'Left',
                '_right-image' => 'Right',
            ])
            ->set_default_value('_right-image'),
    ])
    ->set_inner_blocks()
    ->set_inner_blocks_position('below')
    ->set_inner_blocks_template([
        'core/headline',
        [
            'placeholder' => 'Add a headline',
        ],
        [
            'core/paragraph',
            [
                'placeholder' => 'Add a paragraph',
            ],
        ]
    ])
    ->set_icon('media-document')
    ->set_category('theme-blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $fields['class'] = $fields['margin_bottom'] ?? '';
        if (isset($attributes['className'])) {
            $fields['class'] .= ' ' . $attributes['className'];
        }
        if (isset($fields['orientation'])) {
            $fields['class'] .= ' ' . $fields['orientation'];
        }
        component('content-block', $fields + [
            'slot' => $inner_blocks,
        ]);
    });
