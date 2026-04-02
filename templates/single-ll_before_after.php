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
if ( !empty($detail_sections_field) ) {
    $is_tabs = count($detail_sections_field) > 1;
    foreach ( $detail_sections_field as $section ) {
        $detail_sections[] = [
            'tab_id'  => 'ba-detail-tab-' . uniqid(),
            'title'   => $section['ll_ba_detail_title'] ?? '',
            'content' => $section['ll_ba_detail_content'] ?? '',
            'tag'     => $is_tabs ? 'button' : 'div',
            'is_tab'  => $is_tabs,
        ];
    }
}

$images_field = get_field('field_ll_ba_images');
$ba_images = [];
if ( !empty($images_field) ) {
    foreach ( $images_field as $image ) {
        $ratio_class = 'ba-single__ratio--square';
        if ( $image['ll_ba_image_ratio'] === 'wide' ) {
            $ratio_class = 'ba-single__ratio--wide';
        } elseif ( $image['ll_ba_image_ratio'] === 'panorama' ) {
            $ratio_class = 'ba-single__ratio--panorama';
        } elseif ( $image['ll_ba_image_ratio'] === 'vertical' ) {
            $ratio_class = 'ba-single__ratio--vertical';
        }

        $ba_images[] = [
            'option'           => $image['ll_ba_image_options'],
            'ratio'            => $ratio_class,
            'single_image_id'  => $image['ll_ba_single_image'],
            'before_image_id'  => $image['ll_ba_before_image'],
            'after_image_id'   => $image['ll_ba_after_image'],
            'video_url'        => $image['ll_ba_video_url'],
            'video_title'      => $image['ll_ba_video_title'],
            'comparison_slider'=> $image['ll_ba_comparison_slider'],
        ];
    }
}
?>

<main class="ba-single <?= $page_theme ?>">

    <div class="ba-single__sidebar">

        <div class="ba-single__back">
            <?= Hooks::bag_back_button_markup() ?>
        </div>

        <div class="ba-single__header">
            <h4 class="ba-single__title ba_hdg-5">
                <?= $treatment_title ?>
            </h4>
            <?php if ( !empty($categories) ) : ?>
                <ul class="ba-single__categories">
                    <?php foreach ( $categories as $category ) : ?>
                        <li class="ba-single__category">
                            <a href="">Category->Name</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <?php if ( !empty($detail_sections) ) : ?>
            <div class="ba-single__details">
                <div class="ba-single__detail-triggers">
                    <?php foreach ( $detail_sections as $key => $section_trigger ) : ?>
                        <<?= $section_trigger['tag'] ?>
                            class="ba-single__detail-trigger"
                            <?php if ( $section_trigger['is_tab'] ) : ?>
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
                <div class="ba-single__detail-panels">
                    <?php foreach ( $detail_sections as $section_content ) : ?>
                        <div id="<?= $section_content['tab_id'] ?>" class="ba-single__detail-panel wysiwyg<?= $section_content['is_tab'] ? ' ba-single__detail-panel--tab' : '' ?>">
                            <?= $section_content['content'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Link Card -->
        <?php if ( is_array($global_cta_link) ) : ?>
            <div class="ba-single__cta">
                <?= Hooks::bag_link_card_markup( $global_cta_title, $global_cta_link ) ?>
            </div>
        <?php endif; ?>

        <!-- Related Slider -->
        <div class="splide ba-single__related ba-related-slider" aria-label="Before & After Gallery Related Posts">
            <div class="ba-single__related-header">
                <div class="ba-single__related-title-wrap">
                    <p class="ba-single__related-title">
                        <?= $global_cta_title ?>
                    </p>
                </div>
                <div class="ba-single__related-arrows splide__arrows">
                    <button class="ba-single__related-arrow ba-single__related-arrow--prev splide__arrow--prev">
                        <svg class="ba-single__related-arrow-icon icon icon-arrow-right" aria-hidden="true"><use xlink:href="#icon-arrow-right"></use></svg>
                        <span class="sr-only">Previous Slide</span>
                    </button>
                    <button class="ba-single__related-arrow ba-single__related-arrow--next splide__arrow--next">
                        <svg class="ba-single__related-arrow-icon icon icon-arrow-right" aria-hidden="true"><use xlink:href="#icon-arrow-right"></use></svg>
                        <span class="sr-only">Next Slide</span>
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

    <div class="ba-single__gallery">
        <div class="ba-single__gallery-inner">
            <?php if ( !empty( $ba_images ) ) : ?>

                <div class="splide ba-single-page-slider" aria-label="Before & After Gallery">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php foreach ( $ba_images as $image ) : ?>

                                <?php if ( $image['option'] === 'one-image' && $image['single_image_id'] ) : ?>
                                    <li class="splide__slide">
                                        <div class="ba-single__slide-inner">
                                            <div class="ba-single__slide-image <?= $image['ratio'] ?>">
                                                <?php bag_include_partial( 'fit-image', [
                                                    'image_id'       => $image['single_image_id'],
                                                    'thumbnail_size' => 'large',
                                                    'fit'            => 'object-cover',
                                                    'position'       => 'object-center',
                                                    'loading'        => true,
                                                ] ); ?>
                                            </div>
                                        </div>
                                    </li>

                                <?php elseif ( $image['option'] === 'two-images' ) : ?>
                                    <?php if ( $image['comparison_slider'] ) : ?>
                                        <li class="splide__slide">
                                            <div class="ba-single__slide-inner">
                                                <div class="ba-comparison-slider <?= $image['ratio'] ?>">
                                                    <div class="ba-comparison-slider__before">
                                                        <?php bag_include_partial( 'fit-image', [
                                                            'image_id'       => $image['before_image_id'],
                                                            'thumbnail_size' => 'large',
                                                            'fit'            => 'object-cover',
                                                            'position'       => 'object-center',
                                                            'loading'        => true,
                                                        ] ); ?>
                                                    </div>
                                                    <div class="ba-comparison-slider__after">
                                                        <?php bag_include_partial( 'fit-image', [
                                                            'image_id'       => $image['after_image_id'],
                                                            'thumbnail_size' => 'large',
                                                            'fit'            => 'object-cover',
                                                            'position'       => 'object-center',
                                                            'loading'        => true,
                                                        ] ); ?>
                                                    </div>
                                                    <div class="ba-comparison-slider__divider">
                                                        <div class="ba-comparison-slider__line"></div>
                                                        <div class="ba-comparison-slider__handle"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php else : ?>
                                        <li class="splide__slide">
                                            <div class="ba-single__slide-inner">
                                                <div class="ba-single__side-by-side <?= $image['ratio'] === 'ba-single__ratio--wide' || $image['ratio'] === 'ba-single__ratio--panorama' ? 'ba-single__side-by-side--stacked' : '' ?>">
                                                    <?php if ( $image['before_image_id'] ) : ?>
                                                        <div class="ba-single__side-by-side-image <?= $image['ratio'] ?>">
                                                            <?php bag_include_partial( 'fit-image', [
                                                                'image_id'       => $image['before_image_id'],
                                                                'thumbnail_size' => 'large',
                                                                'fit'            => 'object-cover',
                                                                'position'       => 'object-center',
                                                                'loading'        => true,
                                                            ] ); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ( $image['after_image_id'] ) : ?>
                                                        <div class="ba-single__side-by-side-image <?= $image['ratio'] ?>">
                                                            <?php bag_include_partial( 'fit-image', [
                                                                'image_id'       => $image['after_image_id'],
                                                                'thumbnail_size' => 'large',
                                                                'fit'            => 'object-cover',
                                                                'position'       => 'object-center',
                                                                'loading'        => true,
                                                            ] ); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>

                                <?php elseif ( $image['option'] === 'video' ) : ?>
                                    <li class="splide__slide">
                                        <div class="ba-single__slide-inner">
                                            <div class="ba-single__video <?= $image['ratio'] ?>">
                                                <?php bag_include_partial( 'fit-image', [
                                                    'image_id'       => $image['single_image_id'],
                                                    'thumbnail_size' => 'large',
                                                    'fit'            => 'object-cover',
                                                    'position'       => 'object-center',
                                                    'loading'        => true,
                                                ] ); ?>
                                                <div class="ba-single__video-overlay">
                                                    <a class="ba-single__video-trigger js-init-video" href="<?= $image['video_url'] ?>" data-title="<?= $image['video_title'] ?>">
                                                        <svg class="ba-single__video-icon icon icon-play-triangle" aria-hidden="true"><use xlink:href="#icon-play-triangle"></use></svg>
                                                        <span class="sr-only">View <?= $image['video_title'] ?> video</span>
                                                    </a>
                                                </div>
                                            </div>
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
                            <?php foreach ( $ba_images as $image ) : ?>
                                <?php
                                    $nav_image_id = $image['option'] === 'two-images'
                                        ? $image['before_image_id']
                                        : $image['single_image_id'];
                                ?>
                                <?php if ( $nav_image_id ) : ?>
                                    <li class="splide__slide ba-single__nav-slide">
                                        <?php bag_include_partial( 'fit-image', [
                                            'image_id'       => $nav_image_id,
                                            'thumbnail_size' => 'medium',
                                            'fit'            => 'object-cover',
                                            'position'       => 'object-center',
                                            'loading'        => true,
                                        ] ); ?>
                                        <?php if ( $image['option'] === 'video' ) : ?>
                                            <div class="ba-single__nav-video">
                                                <div class="ba-single__nav-video-overlay"></div>
                                                <svg class="ba-single__nav-video-icon icon icon-play-triangle" aria-hidden="true"><use xlink:href="#icon-play-triangle"></use></svg>
                                            </div>
                                        <?php endif; ?>
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
