<?php

defined('ABSPATH') || exit;

add_action( 'acf/init', function () {
    acf_add_local_field_group( [
        'key'    => 'group_ll_ba_related_bna',
        'title'  => 'Related Before & Afters',
        'active' => false,
        'fields' => [
            [
              'label' => 'Content',
              'name' => 'content',
              'type' => 'wysiwyg',
              'wrapper' => [ 'class' => '' ],
            ],
        ],
        'location' => [],
    ] );
} );
