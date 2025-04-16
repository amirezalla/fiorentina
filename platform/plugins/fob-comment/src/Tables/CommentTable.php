<?php

namespace FriendsOfBotble\Comment\Tables;

use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\Action;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\DateColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\LinkableColumn;
use Botble\Table\Columns\StatusColumn;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Models\Comment;
use Illuminate\Validation\Rule;

class CommentTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->setView('plugins/fob-comment::tables.table')
            ->model(Comment::class)
            ->setOption('id', 'fob-comment-table');
            if(request()->input('filter_values')=='trash'){
                $this->addActions([
                    Action::make('reply')
                        ->renderUsing(fn (Action $action) => view(
                            'plugins/fob-comment::tables.reply-button',
                            compact('action')
                        )->render()),
                    EditAction::make()->route('fob-comment.comments.edit')
                    
                        ]);
            }else{
                $this->addActions([
                    Action::make('reply')
                        ->renderUsing(fn (Action $action) => view(
                            'plugins/fob-comment::tables.reply-button',
                            compact('action')
                        )->render()),
                    EditAction::make()->route('fob-comment.comments.edit'),
                    DeleteAction::make()->route('fob-comment.comments.destroy'),
                        ]);
            }
            
            $this->addColumns([
                IdColumn::make(),
                FormattedColumn::make('author')
                    ->label(trans('plugins/fob-comment::comment.author'))
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        return view(
                            'plugins/fob-comment::tables.author-column',
                            ['item' => $column->getItem()]
                        )->render();
                    }),
                FormattedColumn::make('content')
                    ->label(trans('plugins/fob-comment::comment.common.comment'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $model = $column->getItem();

                        $model->loadMissing('comment');

                        $url = $model->comment->reference_url ?? '#';
                        $url = sprintf('%s#comment-%s', $url, $model->comment?->getKey());

                        return view('plugins/fob-comment::tables.content-column', compact('model', 'url'))->render();
                    })
                    ->limit(100),
                LinkableColumn::make('reference')
                    ->label(trans('plugins/fob-comment::comment.responsed_to'))
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (LinkableColumn $column) {
                        $model = $column->getItem();

                        if (class_exists($model->reference_type)
                            && ($reference = $model->reference)) {
                            return $reference->name ?? $reference->title ?? $reference->id;
                        }

                        return $model->reference_url ?: '-';
                    })
                    ->urlUsing(function (LinkableColumn $column) {
                        $model = $column->getItem();

                        $url = $model->reference_url;

                        if (class_exists($model->reference_type)
                            && ($reference = $model->reference)) {
                            $url = $reference->url;
                        }

                        return sprintf('%s#comment-%s', $url ?: '#', $model->getKey());
                    })
                    ->externalLink(),
                StatusColumn::make(),
                DateColumn::make('created_at')
                    ->label(trans('plugins/fob-comment::comment.submitted_on'))
                    ->dateFormat('Y-m-d H:i:s'),
            ])
            ->addBulkAction(
                DeleteBulkAction::make()->permission('fob-comment.comments.destroy'),
            )
            ->addBulkChange(
                StatusBulkChange::make()
                    ->choices(CommentStatus::labels())
                    ->validate(['required', Rule::in(CommentStatus::values())]),
            )
            ->setOption('topExtraActions', $this->getTrashLink()); // Add trash link
    }

    protected function getTrashLink(): string
    {
        return view('plugins/fob-comment::tables.trash-button', [
            'url' => route('fob-comment.comments.trash'),
        ])->render();
    }

    public function query()
    {
        // Call the parent to get the base query
        $query = parent::query();
    
        // Eager-load the reference relationship (important for filtering by post)
        $query->with('reference');
    
        // If you have a GET param "post_name", filter by the Post title
        if ($postName = request()->input('post_name')) {
            $query->whereHas('reference', function ($q) use ($postName) {
                // If your post column is "title", change this accordingly
                $q->where('name', 'LIKE', '%' . $postName . '%');
            });
        }

        if ($filterColumns = request()->input('filter_columns')) {
            $filterOperators = request()->input('filter_operators', []);
            $filterValues = request()->input('filter_values', []);
            
            foreach ($filterColumns as $index => $column) {
                // Default to '=' if operator is not set
                $operator = isset($filterOperators[$index]) ? $filterOperators[$index] : '=';
                $value = isset($filterValues[$index]) ? $filterValues[$index] : null;
                
                if ($value !== null) {
                    $query->where($column, $operator, $value);
                }
            }
        }
    
        // Preserve your "onlyTrashed" filter
        if (request()->get('onlyTrashed')) {
            $query->onlyTrashed();
        }
    
        return $query;
    }
}
