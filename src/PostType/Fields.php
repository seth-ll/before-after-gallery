<?php

namespace LiftedLogic\LLBag\PostType;

class Fields {
  public function register(): void {
    add_action('acf/init', [$this, 'registerFields']);
  }

  public function registerFields(): void {
    // Category Fields
    acf_add_local_field_group([
      'key'    => 'group_ll_ba_category',
      'title'  => 'Before & After Category Settings',
      'fields' => [
        [
          'key'           => 'field_ll_ba_category_bg_image',
          'label'         => 'Background Image',
          'name'          => 'll_ba_category_bg_image',
          'type'          => 'image',
          'return_format' => 'id',
          'instructions'  => 'Used as the background image for this category card on the Before & After archive.',
        ],
      ],
      'location' => [
        [
          [
            'param'    => 'taxonomy',
            'operator' => '==',
            'value'    => 'category',
          ],
        ],
      ],
    ]);
    // Single Post Fields
    acf_add_local_field_group([
      'key'    => 'group_ll_before_after',
      'title'  => 'Before & After Details',
      'fields' => [
        [
          'key' => 'field_ll_details_tab',
          'label' => 'Details',
          'type' => 'tab',
          'placement' => 'left',
          'endpoint' => 0,
        ],
        [
          'key' => 'field_ll_ba_title',
          'label' => 'Treatment Label',
          'name' => 'll_ba_title',
          'type' => 'text',
          'instructions' => 'If left blank, this will default to "Treatments Used"',
        ],
        [
          'key' => 'field_ll_ba_detail_sections',
          'label' => 'Detail Sections',
          'name' => 'll_ba_detail_sections',
          'type' => 'repeater',
          'layout' => 'block',
          'button_label' => 'Add Detail Section',
          'instructions' => 'If more than one Detail Section is used, Details will show in tabs.',
          'max' => 3,
          'sub_fields' => [
            [
              'key' => 'field_ll_ba_detail_title',
              'label' => 'Title',
              'name' => 'll_ba_detail_title',
              'type' => 'text',
            ],
            [
              'key' => 'field_ll_ba_detail_content',
              'label' => 'Content',
              'name' => 'll_ba_detail_content',
              'type' => 'wysiwyg',
              'wrapper' => [ 'class' => '' ],
            ],
          ],
        ],
        [
          'key' => 'field_ll_images_tab',
          'label' => 'Images',
          'type' => 'tab',
          'placement' => 'left',
          'endpoint' => 0,
        ],
        [
          'key' => 'field_ll_ba_images',
          'label' => 'Images',
          'name' => 'll_ba_images',
          'type' => 'repeater',
          'layout' => 'block',
          'button_label' => 'Add Image',
          'sub_fields' => [
            [
              'key' => 'field_ll_ba_image_options',
              'label' => 'Image Options',
              'name' => 'll_ba_image_options',
              'type' => 'select',
              'choices' => [
                'one-image' => 'One Image',
                'two-images' => 'Two Images',
                'video' => 'Video',
              ],
              'default_value' => '',
              'return_format' => 'value',
              'wrapper' => [
                'width' => '50%',
              ],
            ],
            [
              'key' => 'field_ll_ba_image_ratio',
              'label' => 'Image Ratio',
              'name' => 'll_ba_image_ratio',
              'type' => 'select',
              'choices' => [
                'wide' => '16/9 (Wide)',
                'square' => '1/1 (Square)',
                'panorama' => '3/1 (Panorama)',
                'vertical' => '4/5 (Vertical)',
              ],
              'default_value' => '',
              'return_format' => 'value',
              'wrapper' => [
                'width' => '50%',
              ],
            ],
            [
              'key' => 'field_ll_ba_single_image',
              'label' => 'Before and After Image',
              'name' => 'll_ba_single_image',
              'type' => 'image',
              'return_format' => 'id',
              'conditional_logic' => [
                [
                  [
                    'field' => 'field_ll_ba_image_options',
                    'operator' => '==',
                    'value' => 'one-image',
                  ],
                ],
              ],
            ],
            [
              'key' => 'field_ll_ba_before_image',
              'label' => 'Before Image',
              'name' => 'll_ba_before_image',
              'type' => 'image',
              'return_format' => 'id',
              'conditional_logic' => [
                [
                  [
                    'field' => 'field_ll_ba_image_options',
                    'operator' => '==',
                    'value' => 'two-images',
                  ],
                ],
              ],
              'wrapper' => [
                'width' => '50%',
              ],
            ],
            [
              'key' => 'field_ll_ba_after_image',
              'label' => 'After Image',
              'name' => 'll_ba_after_image',
              'type' => 'image',
              'return_format' => 'id',
              'conditional_logic' => [
                [
                  [
                    'field' => 'field_ll_ba_image_options',
                    'operator' => '==',
                    'value' => 'two-images',
                  ],
                ],
              ],
              'wrapper' => [
                'width' => '50%',
              ],
            ],
            [
              'key' => 'field_ll_ba_video_url',
              'label' => 'Video URL',
              'name' => 'll_ba_video_url',
              'type' => 'url',
              'conditional_logic' => [
                [
                  [
                    'field' => 'field_ll_ba_image_options',
                    'operator' => '==',
                    'value' => 'video',
                  ],
                ],
              ],
            ],
          ],
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
