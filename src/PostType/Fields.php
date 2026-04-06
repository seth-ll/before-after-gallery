<?php

namespace LiftedLogic\LLBag\PostType;

use LiftedLogic\LLBag\Filters\FilterManager;

class Fields {
  public function register(): void {
    add_action('acf/init', [$this, 'registerFields']);
    add_filter('acf/load_field/name=ll_ba_related_terms', [$this, 'loadRelatedTermChoices']);
  }

  /**
   * Populate the related terms checkbox choices at render time, after all
   * taxonomies are guaranteed to be registered (avoids acf/init timing issue).
   */
  public function loadRelatedTermChoices(array $field): array {
    $field['choices'] = [];

    (new FilterManager())->getEnabled()
      ->filter(fn($f) => empty($f['builtin']) && !empty($f['meta_key']))
      ->each(function ($f) use (&$field) {
        $taxonomy = $f['meta_key'];
        $terms    = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
        if (is_wp_error($terms) || empty($terms)) return;
        $field['choices'][$f['label']] = array_column(
          array_map(fn($t) => ["{$taxonomy}:{$t->slug}", $t->name], $terms),
          1, 0
        );
      });

    return $field;
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
                    'operator' => '!=',
                    'value' => 'two-images',
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
              'key' => 'field_ll_ba_comparison_slider',
              'label' => 'Use Comparison Slider?',
              'name' => 'll_ba_comparison_slider',
              'type' => 'true_false',
              'instructions' => 'For best results use two images that are cropped identically with the subject in the same area of the image.',
              'default_value' => 0,
              'ui' => 1,
              'conditional_logic' => [
                [
                  [
                    'field' => 'field_ll_ba_image_options',
                    'operator' => '==',
                    'value' => 'two-images',
                  ],
                ],
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
            [
              'key' => 'field_ll_ba_video_title',
              'label' => 'Video Title (For Screen Readers)',
              'name' => 'll_ba_video_title',
              'type' => 'text',
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

    // Related Posts meta box — displayed below Attributes in the sidebar
    acf_add_local_field_group([
      'key'        => 'group_ll_ba_related',
      'title'      => 'Related Posts',
      'fields'     => [
        [
          'key'          => 'field_ll_ba_related_terms',
          'label'        => 'Match Related Posts By',
          'name'         => 'll_ba_related_terms',
          'type'         => 'checkbox',
          'instructions' => 'Select specific terms to use when finding related posts. Defaults to the current post\'s Card Display terms if left empty.',
          'choices'      => [], // populated at render time via acf/load_field
          'return_format'=> 'value',
        ],
      ],
      'position'   => 'normal',
      'menu_order' => 100,
      'location'   => [
        [
          [
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => BeforeAfterPostType::SLUG,
          ],
        ],
      ],
    ]);
  }
}
