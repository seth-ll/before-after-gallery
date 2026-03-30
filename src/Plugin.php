<?php

namespace LiftedLogic\LLBag;

use Illuminate\Container\Container;
use LiftedLogic\LLBag\Admin\AdminMenu;
use LiftedLogic\LLBag\PostType\BeforeAfterPostType;

class Plugin
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->container->singleton(BeforeAfterPostType::class);
        $this->container->singleton(AdminMenu::class);
    }

    public function boot(): void
    {
        $this->container->make(BeforeAfterPostType::class)->register();

        if (is_admin()) {
            $this->container->make(AdminMenu::class)->register();
        }
    }
}
