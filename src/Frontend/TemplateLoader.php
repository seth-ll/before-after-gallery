<?php

namespace LiftedLogic\LLBag\Frontend;

use LiftedLogic\LLBag\PostType\BeforeAfterPostType;

class TemplateLoader {
  public function register(): void {
    add_filter('template_include', [$this, 'loadTemplate']);
  }

  public function loadTemplate(string $template): string {
    if (is_singular(BeforeAfterPostType::SLUG)) {
      return self::resolve('single-ll_before_after.php') ?? $template;
    }

    if (is_post_type_archive(BeforeAfterPostType::SLUG)) {
      return self::resolve('archive-ll_before_after.php') ?? $template;
    }

    if (is_category()) {
      return self::resolve('archive-ll_before_after-posts.php') ?? $template;
    }

    return $template;
  }

  /**
   * Resolve a template file, checking theme override first.
   * Use this in shortcodes and partials to load a template file.
   *
   * @param string               $file Relative path within templates/ (e.g. 'partials/post-card.php')
   * @param array<string, mixed> $data Variables to extract into template scope
   */
  public static function get(string $file, array $data = []): void {
    $themeFile  = get_stylesheet_directory() . '/ll-before-after/' . $file;
    $pluginFile = LL_BAG_PATH . 'templates/' . $file;

    $resolved = file_exists($themeFile) ? $themeFile : (file_exists($pluginFile) ? $pluginFile : null);

    if ($resolved === null) {
      return;
    }

    if (!empty($data)) {
      extract($data, EXTR_SKIP);
    }

    require $resolved;
  }

  /**
   * Like get(), but captures output and returns it as a string.
   *
   * @param array<string, mixed> $data
   */
  public static function render(string $file, array $data = []): string {
    ob_start();
    self::get($file, $data);
    return (string) ob_get_clean();
  }

  /**
   * Return the resolved absolute path without loading it.
   */
  public static function resolve(string $file): ?string {
    $themeFile  = get_stylesheet_directory() . '/ll-before-after/' . $file;
    $pluginFile = LL_BAG_PATH . 'templates/' . $file;

    if (file_exists($themeFile))  return $themeFile;
    if (file_exists($pluginFile)) return $pluginFile;

    return null;
  }
}
