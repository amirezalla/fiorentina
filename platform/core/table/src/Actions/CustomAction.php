<?php

namespace Botble\Table\Actions;

class CustomAction extends Action
{
    public static function make(string $name = 'restore'): static
    {
        return parent::make($name)
            ->label("Ripristina")
            ->color('success')
            ->icon('ti ti-restore');
    }
}
