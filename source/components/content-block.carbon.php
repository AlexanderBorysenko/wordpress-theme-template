<?php
use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('content-block', 'Content Block')
    ->add_fields([
        Field::make('image', 'image', 'Image')->set_conditional_logic([[
            'field'   => 'orientation',
            'compare' => '!=',
            'value'   => '_no-image',
        ]]),
        getMarginBottomSelectField()->set_attribute('data-toggle-block-class', 'true'),
        Field::make('select', 'orientation', 'Image Orientation')->add_options([
            '_left-image'  => 'Left',
            '_right-image' => 'Right',
            '_no-image'    => 'No Image',
        ])->set_default_value('_right-image'),
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
        ],
    ])
    ->set_icon('media-document')
    ->set_category('theme-blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        component(
            'content-block',
            ['class' => [
                $fields['margin_bottom'] ?? null,
                $attributes['className'] ?? null,
                $fields['orientation'] ?? null,
            ]],
            $fields + [
                'slot' => $inner_blocks,
            ]
        );
    });
