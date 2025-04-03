<?php

namespace Botble\Blog\BulkActions;

use Botble\Base\Contracts\BaseModel;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Table\Abstracts\TableBulkActionAbstract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class RestoreBulkAction extends TableBulkActionAbstract
{
    public function __construct()
    {
        // Label for the dropdown item
        $this->label('Ripristina selezionati')
             ->confirmationModalTitle('Conferma ripristino')
             ->confirmationModalButton('Ripristina')
             ->confirmationModalMessage('Sei sicuro di voler ripristinare gli elementi selezionati?');
    }

    /**
     * The handle or key used internally by Botble to identify this bulk action.
     */
    public function getHandle(): string
    {
        return 'bulk-restore';
    }

    /**
     * Main logic that runs when the bulk action is triggered.
     */
    public function dispatch(BaseModel|Model $model, array $ids): BaseHttpResponse
    {
        // For each ID, find and restore the item
        $model->newQuery()
            ->whereKey($ids)
            ->each(function ($item) {
                // If your model uses SoftDeletes, you can call $item->restore().
                // Otherwise, just set deleted_at to null manually.
                if (method_exists($item, 'restore')) {
                    $item->restore();
                } else {
                    $item->deleted_at = null;
                    // Optionally set status if you want to mark them published again
                    $item->status = BaseStatusEnum::PUBLISHED;
                    $item->save();
                }
            });

        return BaseHttpResponse::make()
            ->setMessage('Elementi ripristinati con successo!');
    }
}
