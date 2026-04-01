<?php

namespace LiftedLogic\LLBag;

use Illuminate\Container\Container;
use LiftedLogic\LLBag\Admin\AdminMenu;
use LiftedLogic\LLBag\Admin\FilterSettingsPage;
use LiftedLogic\LLBag\Admin\SettingsPage;
use LiftedLogic\LLBag\Filters\FilterManager;
use LiftedLogic\LLBag\Frontend\AjaxHandler;
use LiftedLogic\LLBag\Frontend\Shortcodes;
use LiftedLogic\LLBag\Frontend\TemplateLoader;
use LiftedLogic\LLBag\PostType\BeforeAfterPostType;
use LiftedLogic\LLBag\PostType\Fields;
use LiftedLogic\LLBag\PostType\TaxonomyRegistrar;

class Plugin {
  private Container $container;

  public function __construct() {
    $this->container = new Container();
    $this->registerBindings();
  }

  private function registerBindings(): void {
    $this->container->singleton(FilterManager::class);
    $this->container->singleton(BeforeAfterPostType::class);
    $this->container->singleton(Fields::class);
    $this->container->singleton(TaxonomyRegistrar::class);
    $this->container->singleton(SettingsPage::class);
    $this->container->singleton(TemplateLoader::class);
    // $this->container->singleton(Shortcodes::class);
    $this->container->singleton(AjaxHandler::class);

    $this->container->singleton(FilterSettingsPage::class, function () {
      return new FilterSettingsPage($this->container->make(FilterManager::class));
    });

    $this->container->singleton(AdminMenu::class, function () {
      return new AdminMenu($this->container->make(FilterSettingsPage::class));
    });
  }

  public function boot(): void {
    $this->container->make(BeforeAfterPostType::class)->register();
    $this->container->make(TaxonomyRegistrar::class)->register();
    $this->container->make(Fields::class)->register();
    $this->container->make(SettingsPage::class)->register();
    $this->container->make(TemplateLoader::class)->register();
    // $this->container->make(Shortcodes::class)->register();
    $this->container->make(AjaxHandler::class)->register();

    if (is_admin()) {
      $this->container->make(AdminMenu::class)->register();
    }
  }
}
