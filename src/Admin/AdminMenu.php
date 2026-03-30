<?php

namespace LiftedLogic\LLBag\Admin;

use LiftedLogic\LLBag\PostType\BeforeAfterPostType;
use LiftedLogic\LLBag\Support\Vite;

class AdminMenu
{
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(string $hook): void
    {
        $screen = get_current_screen();

        if ($screen?->post_type !== BeforeAfterPostType::SLUG) {
            return;
        }

        Vite::enqueueAdminAssets();
    }
}
