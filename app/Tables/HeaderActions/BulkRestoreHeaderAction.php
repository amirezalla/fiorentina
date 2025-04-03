<?php

namespace App\Tables\HeaderActions;

use Botble\Table\HeaderActions\HeaderAction;

class BulkRestoreHeaderAction extends HeaderAction
{
    public static function make(string $name = 'restore'): static
    {
        return new static($name);
    }

    public function render(): string
    {
        // Use the options to get the link and label.
        $link  = $this->getOption('link', '#');
        $label = $this->getOption('label', 'Bulk Restore');

        return '<a href="' . $link . '" class="btn btn-sm btn-primary" data-custom-bulk-restore="true">' . $label . '</a>';
    }
}
