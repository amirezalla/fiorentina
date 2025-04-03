<?php

namespace Botble\Blog\Tables;

use Botble\ACL\Models\User;
use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Blog\Exports\PostExport;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use FriendsOfBotble\Comment\Models\Comment;

class PostTable extends TableAbstract
{
    protected string $exportClass = PostExport::class;

    protected int $defaultSortColumn = 6;

    public function setup(): void
    {
        $this
            ->model(Post::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('posts.create'));
            if (request()->get('deleted') == 1) {
                $this->addActions([
                    EditAction::make()->route('posts.edit'),
                    new class('restore') extends \Botble\Table\Abstracts\TableActionAbstract {
                        public function render(): string
                        {
                            $post = $this->getItem();
                            return '<a class="btn btn-sm btn-icon btn-success" href="'
                                . route('posts.restore', $post->id)
                                . '" data-dt-single-action data-method="POST" data-confirmation-modal="true" data-confirmation-modal-title="Conferma ripristino" data-confirmation-modal-message="Sei sicuro di voler ripristinare questo record?" data-confirmation-modal-button="Ripristina" data-confirmation-modal-cancel-button="Annulla">
                                    <i class="fa fa-trash-arrow-up" style="margin:0 !important"><span class="sr-only">Ripristina</span></i>
                                </a>';
                        }
                    },
                ]);
            } else {
                $this->addActions([
                    EditAction::make()->route('posts.edit'),
                    DeleteAction::make()->route('posts.soft-delete'),
                ]);
            }
            $this->addColumns([
                ImageColumn::make(),
                NameColumn::make()->route('posts.edit'),
                FormattedColumn::make('categories_name')
                    ->title(trans('plugins/blog::posts.categories'))
                    ->width(100)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $categories = $column
                        ->getItem()
                        ->categories
                        ->unique('id') // Remove duplicate category models
                        ->sortBy('name')
                        ->map(function (Category $category) {
                            return Html::link(
                                route('categories.edit', $category->getKey()),
                                $category->name,
                                ['target' => '_blank']
                            );
                        })
                        ->all();

                        return implode(', ', $categories);
                    })
                    ->withEmptyState(),
                FormattedColumn::make('author_id')
                    ->title(trans('plugins/blog::posts.author'))
                    ->width(100)
                    ->orderable(false)
                    ->searchable(true)
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->author?->name)
                    ->renderUsing(function (FormattedColumn $column) {
                        $post = $column->getItem();
                        $author = $post->author;

                        if (! $author->getKey()) {
                            return null;
                        }

                        if ($post->author_id && $post->author_type === User::class) {
                            return Html::link($author->url, $author->name, ['target' => '_blank']);
                        }

                        return null;
                    })
                    ->withEmptyState(),
                    FormattedColumn::make('views')
                    ->title('Views')
                    ->width(60)
                    ->orderable(true)
                    ->searchable(false)
                    ->renderUsing(function (FormattedColumn $column) {
                        $post = $column->getItem();
                        $count = $post->views ?? 0;
                        return '<span class="badge badge-primary text-primary">
                                    <i class="fa fa-eye"></i> ' . $count . '
                                </span>';
                    }),
                FormattedColumn::make('comments')
                    ->title('Comments')
                    ->width(60)
                    ->orderable(false)
                    ->searchable(false)
                    ->renderUsing(function (FormattedColumn $column) {
                        $post = $column->getItem();
                        $count = Comment::where('reference_id', $post->id)
                            ->where('reference_type', \Botble\Blog\Models\Post::class)
                            ->count();
                        $url = url('admin/comments?post_name=' . urlencode($post->name));
                        return '<a href="' . $url . '" class="badge badge-primary text-primary">
                                    <i class="fa fa-comment"></i> ' . $count . '
                                </a>';
                    }),
                    FormattedColumn::make('created_at')
                    ->title(trans('core/base::tables.created_at'))
                    ->renderUsing(function (\Botble\Table\Columns\FormattedColumn $column) {
                         $value = $column->getItem()->created_at;
                         if (!$value) {
                             return '';
                         }
                         $date = \Carbon\Carbon::parse($value)->locale('it');
                         $formattedDate = ucfirst($date->translatedFormat('d M Y'));
                         $formattedTime = $date->format('H:i');
                         return '<span style="font-size: smaller;">' . $formattedDate . ' alle ' . $formattedTime . '</span>';
                    }),
                
                
                                StatusColumn::make()
                            ]);
                // New column that outputs only the Quick Edit button.
                if (request()->get('deleted') != 1){
                    $this->addColumns([
                    FormattedColumn::make('quick_edit')
                    ->title('Azione rapida')
                    ->orderable(false)
                    ->searchable(false)
                    ->renderUsing(function (FormattedColumn $column) {
                        $post = $column->getItem();
                        $categoriesJson = json_encode($post->categories->pluck('id'));
                        $tagsJson = ''; // Adapt as needed
                
                        return '
                            <button 
                                type="button" 
                                class="btn btn-sm btn-secondary quick-edit-btn"
                                data-id="' . $post->id . '"
                            >
                                <i class="fa fa-edit"></i> Modifica Rapida
                            </button>
                        ';
                    })
                    ]);
                }
                
            
            

            $this->addBulkActions([
                DeleteBulkAction::make()->permission('posts.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
                SelectBulkChange::make()
                    ->name('category')
                    ->title(trans('plugins/blog::posts.category'))
                    ->searchable()
                    ->choices(fn () => Category::query()->pluck('name', 'id')->all()),
                SelectBulkChange::make()
                    ->name('author_id')
                    ->title(trans('plugins/blog::posts.author'))
                    ->searchable()
                    ->choices(fn () => User::query()->pluck('first_name', 'id')->all()),
            ])
            ->queryUsing(function (Builder $query) {
                // If the request parameter 'deleted' is not 1, only show non-deleted posts:
                if (request()->get('deleted') != 1) {
                    $query->whereNull('deleted_at');
                } else {
                    // When ?deleted=1 is set, only show soft-deleted posts:
                    $query->whereNotNull('deleted_at');
                }
                
                return $query
                    ->with([
                        'categories' => function (BelongsToMany $query) {
                            $query->select(['categories.id', 'categories.name']);
                        },
                        'author',
                    ])
                    ->select([
                        'id',
                        'name',
                        'image',
                        'views',
                        'created_at',
                        'status',
                        'updated_at',
                        'author_id',
                        'author_type',
                    ])
                    ->orderBy('created_at', 'desc');
            })
            ->onAjax(function (PostTable $table) {
                return $table->toJson(
                    $table
                        ->table
                        ->eloquent($table->query())
                        ->filter(function ($query) {
                            if ($keyword = $this->request->input('search.value')) {
                                $keyword = '%' . $keyword . '%';

                                return $query
                                    ->where('name', 'LIKE', $keyword)
                                    ->orWhereHas('categories', function ($subQuery) use ($keyword) {
                                        return $subQuery->where('name', 'LIKE', $keyword);
                                    })
                                    ->orWhereHas('author', function ($subQuery) use ($keyword) {
                                        return $subQuery
                                            ->where('first_name', 'LIKE', $keyword)
                                            ->orWhere('last_name', 'LIKE', $keyword)
                                            ->orWhereRaw('concat(first_name, " ", last_name) LIKE ?', $keyword);
                                    });
                            }

                            return $query;
                        })
                );
            })
            ->onFilterQuery(function (
                EloquentBuilder|QueryBuilder|EloquentRelation $query,
                string $key,
                string $operator,
                ?string $value
            ) {
                if (! $value) {
                    return false;
                }
                if ($key === 'category') {
                    return $query->whereHas(
                        'categories',
                        fn (BaseQueryBuilder $query) => $query->where('categories.id', $value)
                    );
                }
                if ($key === 'author_id') {
                    return $query->where('author_id', $value)
                                 ->where('author_type', User::class);
                }
                return false;
            })
            ->onSavingBulkChangeItem(function (Post $item, string $inputKey, ?string $inputValue) {
                if ($inputKey !== 'category') {
                    return null;
                }

                $item->categories()->sync([$inputValue]);

                return $item;
            });
    }
}
