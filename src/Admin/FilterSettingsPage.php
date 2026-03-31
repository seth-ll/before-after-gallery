<?php

namespace LiftedLogic\LLBag\Admin;

use LiftedLogic\LLBag\Filters\FilterManager;

class FilterSettingsPage {
  public function __construct(private readonly FilterManager $filterManager) {}

  public function render(): void {
    $filters = $this->filterManager->all();
    require LL_BAG_PATH . 'views/admin/filter-settings.php';
  }

  public function handleSave(): void {
    // TODO: verify nonce + capability, sanitize POST data, call $this->filterManager->save()
    // wp_safe_redirect(admin_url('edit.php?post_type=ll_before_after&page=ll-bag-filters&saved=1'));
    // exit;
  }
}
