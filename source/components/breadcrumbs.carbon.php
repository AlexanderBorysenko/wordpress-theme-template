<?php
use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('breadcrumbs', 'Breadcrumbs Block')
    ->add_tab('Content', [
        Field::make('checkbox', 'disable_copy_button', 'Disable Copy Button')->set_default_value(true),
    ])
    ->add_tab('Layouting', [
        getMarginBottomSelectField(),
    ])
    ->set_mode('preview')
    ->set_icon('admin-site')
    ->set_category('theme-blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        component(
            'breadcrumbs',
            [
                'class' => [
                    $fields['margin_bottom'] ?? null,
                    $attributes['className'] ?? null,
                ],
            ],
            $fields + [
                'block' => true,
            ]
        );
    });