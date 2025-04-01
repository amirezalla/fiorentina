<?php

namespace App\Table\Actions;

use Botble\Table\Abstracts\TableAction;

class QuickEditAction extends TableAction
{
    protected string $icon = 'fa fa-edit'; // FontAwesome icon class
    protected string $title = 'Quick Edit';

    public static function make(): static
    {
        return new static();
    }

    public function render($item)
    {
        // The button includes a data-id attribute so you know which row to toggle.
        return '<button type="button" class="btn btn-sm btn-secondary quick-edit-btn" data-id="' . $item->id . '">
                    <i class="' . $this->icon . '"></i> ' . $this->title . '
                </button>';
    }
}
