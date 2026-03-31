<?php

namespace LiftedLogic\LLBag\PostType;

class Fields {
  public function register(): void {
    add_action('acf/init', [$this, 'registerFields']);
  }

  public function registerFields(): void {
    acf_add_local_field_group([
      'key'    => 'group_ll_before_after',
      'title'  => 'Before & After Details',
      'fields' => [
        [
          'key' => 'field_ll_ba_title',
          'label' => 'Title',
          'name' => MetaBoxes::TITLE_KEY,
          'type' => 'text',
        ],
        [
          'key' => 'field_ll_ba_before_image',
          'label' => 'Before Image',
          'name' => MetaBoxes::BEFORE_IMAGE_KEY,
          'type' => 'image',
          'return_format' => 'id',
        ],
        [
          'key' => 'field_ll_ba_after_image',
          'label' => 'After Image',
          'name' => MetaBoxes::AFTER_IMAGE_KEY,
          'type' => 'image',
          'return_format' => 'id',
        ],
      ],
      'location' => [
        [
          [
            'param' => 'post_type',
            'operator' => '==',
            'value' => BeforeAfterPostType::SLUG,
          ],
        ],
      ],
    ]);
  }
}
