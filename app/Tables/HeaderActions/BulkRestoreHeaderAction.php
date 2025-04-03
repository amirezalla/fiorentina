<?php

namespace App\Table\HeaderActions;

use Botble\Table\Abstracts\HeaderActionAbstract;

class BulkRestoreHeaderAction extends HeaderActionAbstract
{
    public static function make(): static
    {
        return new static();
    }

    public function render(): string
    {
        // Use the options to get the link and label.
        $link  = $this->getOption('link', '#');
        $label = $this->getOption('label', 'Bulk Restore');

        return '<a href="' . $link . '" class="btn btn-sm btn-primary" data-custom-bulk-restore="true">' . $label . '</a>';
    }
}
