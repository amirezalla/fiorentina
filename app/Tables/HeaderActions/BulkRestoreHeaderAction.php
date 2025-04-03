<?php

namespace App\Tables\HeaderActions;

use Botble\Table\HeaderActions\HeaderAction;

class BulkRestoreHeaderAction extends HeaderAction
{
    protected array $options = [];

    public static function make(string $name = 'restore'): static
    {
        return new static($name);
    }

    public function setOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    public function render(): string
    {
        $link  = $this->getOption('link', '#');
        $label = $this->getOption('label', 'Bulk Restore');

        return '<a href="' . $link . '" class="btn btn-sm btn-primary" data-custom-bulk-restore="true">' . $label . '</a>';
    }
}
