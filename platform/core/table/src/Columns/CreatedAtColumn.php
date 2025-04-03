<?php

namespace Botble\Table\Columns;

use Carbon\Carbon;

class CreatedAtColumn extends DateColumn
{
    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data ?: 'created_at', $name)
            ->title(trans('core/base::tables.created_at'))
            ->renderUsing(function ($value) {
                if (!$value) {
                    return '';
                }
                // Parse the date, set locale to Italian, and format as "05 Feb 2025"
                // Note: translatedFormat will output month abbreviations according to the locale.
                $date = Carbon::parse($value)->locale('it')->translatedFormat('d M Y');
                return $date;
            });
    }
}
