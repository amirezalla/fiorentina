<?php

namespace App\Tables\HeaderActions;

use Botble\Table\HeaderActions\HeaderAction;

class BulkRestoreHeaderAction extends HeaderAction
{
    protected array $options = [];

    /**
     * Make a new header action.
     *
     * @param string $name
     * @return static
     */
    public static function make(string $name = 'restore'): static
    {
        return new static($name);
    }

    /**
     * Set additional options.
     */
    public function setOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Retrieve an option.
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Get the URL for the bulk restore action.
     */
    public function getUrl(): string
    {
        return $this->getOption('link', '#');
    }

    /**
     * Get the label for the bulk restore action.
     */
    public function getLabel(): string
    {
        return $this->getOption('label', 'Bulk Restore');
    }

    /**
     * Render the header action.
     */
    public function render(): string
    {
        $url = $this->getUrl();
        $label = $this->getLabel();
        // Return a button with the data-action attribute set to the header action name
        // and data-href set to your custom URL.
        return '<button class="btn" tabindex="0" type="button" data-action="' . $this->getName() . '" >
                    <span>' . $label . '</span>
                </button>';
    }
}
