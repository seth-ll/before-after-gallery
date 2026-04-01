<?php
/**
 * Template: Single Before & After post
 *
 * Override: place this file at {theme}/ll-before-after/single-ll_before_after.php
 */

defined('ABSPATH') || exit;

use LiftedLogic\LLBag\Hooks\Hooks;

$page_theme = 'theme-two';
$treatment_title = get_field('ll_ba_title') ? get_field('ll_ba_title') : 'Treatments Used:';
$global_cta_title = get_field('ll_ba_global_cta_title', 'options') ?? '';
$global_cta_link = get_field('ll_ba_global_cta_link', 'options') ?? '';
$categories = [];

$detail_sections_field = get_field('ll_ba_detail_sections') ?? [];
$detail_sections = [];
if(!empty($detail_sections_field)) {
    $is_tabs = count($detail_sections_field) > 1 ? true : false;
  foreach ($detail_sections_field as $section) {
    $detail_sections[] = [
        'tab_id' => 'ba-detail-tab-' . uniqid(),
        'title' => $section['ll_ba_detail_title'] ?? '',
        'content' => $section['ll_ba_detail_content'] ?? '',
        'tag' => $is_tabs ? 'button' : 'div',
        'is_tab' => $is_tabs,
    ];
  }
}

$images_field = get_field('field_ll_ba_images');
$ba_images = [];
if(!empty($images_field)) {
  foreach ($images_field as $image) {
    $ratio_class = 'ba-aspect-square';
    if( $image['ll_ba_image_ratio'] === 'wide' ) {
        $ratio_class = 'ba-aspect-[16/9]';
    }elseif( $image['ll_ba_image_ratio'] === 'panorama' ) {
        $ratio_class = 'ba-aspect-[3/1]';
    }elseif( $image['ll_ba_image_ratio'] === 'vertical' ) {
        $ratio_class = 'ba-aspect-[4/5]';
    }


    $ba_images[] = [
      'option' => $image['ll_ba_image_options'],
      'ratio' => $ratio_class,
      'single_image_id' => $image['ll_ba_single_image'],
      'before_image_id' => $image['ll_ba_before_image'],
      'after_image_id' => $image['ll_ba_after_image'],
    ];
  }
}
?>

<main class="ll-ba-single ba-grid ba-grid-cols-1 lg:ba-grid-cols-2 <?= $page_theme ?>">
    <div class="ba-p-16 ba-bg-background-fill ba-h-screen">
        <?= Hooks::bag_back_button_markup() ?>
        <h4 class="ba_hdg-5 ba-text-text-heading">
            <?= $treatment_title ?>
        </h4>
        <?php if( !empty($categories) ) : ?>        
            <ul class="ba-flex ba-flex-wrap ba-gap-[6px]">
                <?php foreach( $categories as $category ) : ?>
                    <li>
                        <a href="">
                            Category->Name
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if( !empty($detail_sections) ) : ?>
            <div class="ba-flex">
                <?php foreach( $detail_sections as $key => $section_trigger ) : ?>                
                    <<?= $section_trigger['tag'] ?>
                        class="ba-py-2 ba-px-4 ba-border-b ba-border-detail-tabs-stroke"
                        <?php if($section_trigger['is_tab']) : ?>
                            data-toggle-target="#<?= $section_trigger['tab_id'] ?>"
                            data-toggle-class="ba-is-active"
                            data-toggle-radio-group="ba-single-page-detail-sections"
                            aria-expanded="false"
                            <?= $key === 0 ? 'data-toggle-is-active' : '' ?>
                        <?php endif; ?>
                        >
                        <?= $section_trigger['title'] ?>
                    </<?= $section_trigger['tag'] ?>>
                <?php endforeach; ?>
            </div>
            <div>
                <?php foreach( $detail_sections as $section_content ) : ?>
                    <div id="<?= $section_content['tab_id'] ?>" class="wysiwyg <?= $section_content['is_tab'] ? 'ba-hidden [&.ba-is-active]:ba-block' : '' ?>">
                        <?= $section_content['content'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Link Card -->
         <?php if( is_array($global_cta_link) ) : ?>         
            <div>
                <?= Hooks::bag_link_card_markup( $global_cta_title, $global_cta_link ) ?>
            </div>
         <?php endif; ?>

        <!-- Slider -->
        <div class="splide ba-related-slider" aria-label="Before & After Gallery Related Posts">
            <div class="ba-flex ba-justify-between ba-items-center">
                <div class="flex-1">
                    <p class="hdg-6">
                        <?= $global_cta_title ?>
                    </p>
                </div>
                <div class="ba-flex ba-gap-3 splide__arrows ba-flex-none">
                    <button class="ba-relative ba-left-0 ba-flex ba-items-center ba-justify-center ba-flex-none ba-duration-300 ba-ease-in-out group/button ba-rounded-buttons disabled:ba-opacity-50 disabled:ba-pointer-events-none ba-size-[44px] ba-bg-theme-arrow-buttons-fill hover:ba-bg-theme-arrow-buttons-fill-hover splide__arrow--prev">
                        <svg class="ba-duration-300 ba-ease-in-out ba-text-theme-arrow-buttons-elements ba-size-5 icon icon-arrow-right group-hover/button:ba-text-theme-arrow-buttons-elements-hover" aria-hidden="true"><use xlink:href="#icon-arrow-right"></use></svg>
                        <span class="ba-sr-only">Previous Slide</span>
                    </button>
                    <button class="ba-relative ba-right-0 ba-flex ba-items-center ba-justify-center ba-flex-none ba-duration-300 ba-ease-in-out group/button ba-rounded-buttons disabled:ba-opacity-50 disabled:ba-pointer-events-none ba-size-[44px] ba-bg-theme-arrow-buttons-fill hover:ba-bg-theme-arrow-buttons-fill-hover splide__arrow--next">
                        <svg class="ba-duration-300 ba-ease-in-out ba-text-theme-arrow-buttons-elements ba-size-5 icon icon-arrow-right group-hover/button:ba-text-theme-arrow-buttons-elements-hover" aria-hidden="true"><use xlink:href="#icon-arrow-right"></use></svg>
                        <span class="ba-sr-only">Next Slide</span>
                    </button>
                </div>
            </div>
            <div class="splide__track">
                <ul class="splide__list">
                    <li class="splide__slide">Slide 1</li>
                    <li class="splide__slide">Slide 2</li>
                    <li class="splide__slide">Slide 3</li>
                </ul>
            </div>
        </div>
        
    </div>
    <div class="ba-h-full ba-relative ba-bg-cards-background">
        <div class="ba-sticky ba-top-[200px]">
            <?php if( !empty( $ba_images ) ) : ?>
                <div class="splide ba-single-page-slider" aria-label="Before & After Gallery Related Posts">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php foreach( $ba_images as $image ) : ?>
                                <?php if( $image['option'] === 'one-image' && $image['single_image_id'] ) : ?>
                                    <li class="ba-w-full ba-relative splide__slide <?= $image['ratio'] ?>">
                                        <?php
                                            bag_include_partial( 'fit-image', [
                                                'image_id'       => $image['single_image_id'],
                                                'thumbnail_size' => 'large',
                                                'fit'            => 'object-cover',
                                                'position'       => 'object-center',
                                                'loading'        => true,
                                            ] );
                                        ?>
                                    </li>
                                <?php elseif( $image['option'] === 'two-images' ) : ?>
                                    <li class="splide__slide">
                                        <div class="ba-flex ba-gap-px <?= $image['ratio'] === 'wide' || $image['ratio'] === 'panorama' ? 'ba-flex-col' : '' ?>">
                                            <?php if( $image['before_image_id'] ) : ?>
                                                <div class="ba-w-full ba-relative <?= $image['ratio'] ?>">
                                                    <?php
                                                        bag_include_partial( 'fit-image', [
                                                            'image_id'       => $image['before_image_id'],
                                                            'thumbnail_size' => 'large',
                                                            'fit'            => 'object-cover',
                                                            'position'       => 'object-center',
                                                            'loading'        => true,
                                                        ] );
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if( $image['after_image_id'] ) : ?>
                                                <div class="ba-w-full ba-relative <?= $image['ratio'] ?>">
                                                    <?php
                                                        bag_include_partial( 'fit-image', [
                                                            'image_id'       => $image['after_image_id'],
                                                            'thumbnail_size' => 'large',
                                                            'fit'            => 'object-cover',
                                                            'position'       => 'object-center',
                                                            'loading'        => true,
                                                        ] );
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="splide ba-single-page-slider-nav" aria-label="Before & After Gallery Thumbnails">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php foreach( $ba_images as $image ) : ?>
                                <?php
                                    $nav_image_id = $image['option'] === 'two-images'
                                        ? $image['before_image_id']
                                        : $image['single_image_id'];
                                ?>
                                <?php if( $nav_image_id ) : ?>
                                    <li class="splide__slide ba-aspect-square ba-relative">
                                        <?php
                                            bag_include_partial( 'fit-image', [
                                                'image_id'       => $nav_image_id,
                                                'thumbnail_size' => 'medium',
                                                'fit'            => 'object-cover',
                                                'position'       => 'object-center',
                                                'loading'        => true,
                                            ] );
                                        ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

