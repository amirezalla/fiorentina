<?php

namespace Botble\Blog\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\IsFeaturedFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TagFieldOption;
use Botble\Base\Forms\Fields\DatetimeField;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TreeCategoryField;
use Botble\Base\Forms\FormAbstract;
use Botble\Blog\Http\Requests\PostRequest;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;

class PostForm extends FormAbstract
{
    public function getActionButtons(): string
    {
        return view('plugins/blog::partials.form-actions')->render();
    }

    public function setup(): void
    {
        $this
            ->model(Post::class)
            ->setValidatorClass(PostRequest::class)
            ->hasTabs()
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->toArray())
            ->add(
                'is_featured',
                OnOffField::class,
                IsFeaturedFieldOption::make()
                    ->toArray()
            )
            ->add('published_at', DatetimeField::class, DatePickerFieldOption::make()
                ->label(trans('plugins/blog::posts.form.scheduled_publishing'))
                ->defaultValue(null))
            ->add('content', EditorField::class, ContentFieldOption::make()->allowedShortcodes()->toArray())
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            ->when(get_post_formats(true), function (PostForm $form, array $postFormats) {
                if (count($postFormats) > 1) {
                    $choices = [];

                    foreach ($postFormats as $postFormat) {
                        $choices[$postFormat[0]] = $postFormat[1];
                    }

                    $form
                        ->add(
                            'format_type',
                            RadioField::class,
                            RadioFieldOption::make()
                                ->label(trans('plugins/blog::posts.form.format_type'))
                                ->choices($choices)
                                ->toArray()
                        );
                }
            })

            // ... (inside the setup() method, before setBreakFieldPoint('status'))
->add('hero_order', SelectField::class, [
    'label'         => 'Seleziona ordinamento',
    'choices'       => [
        ''  => 'Seleziona ordinamento',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
    ],
    'default_value' => null,
])
->add('in_aggiornamento', OnOffField::class, [
    'label'         => 'In Aggiornamento',
    // The following options simulate a checkbox with Yes/No labels.
    'yes_label'     => 'Si',
    'no_label'      => 'No',
    'default_value' => 0,
])



            ->add(
                'categories[]',
                TreeCategoryField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/blog::posts.form.categories'))
                    ->choices(get_categories_with_children())
                    ->when($this->getModel()->getKey(), function (SelectFieldOption $fieldOption) {
                        return $fieldOption->selected($this->getModel()->categories()->pluck('category_id')->all());
                    }, function (SelectFieldOption $fieldOption) {
                        return $fieldOption
                            ->selected(Category::query()
                            ->where('is_default', 1)
                            ->pluck('id')
                            ->all());
                    })
                    ->toArray()
            )->add(
    'author_id',
    SelectField::class,
    SelectFieldOption::make()
        ->label('Autore principale')
        ->attributes([
            'class'                     => 'form-control select-search-full',
            'data-ajax--url'            => route('author.search'),
            'data-ajax--cache'          => 'true',
            'data-placeholder'          => 'Cerca per nome, email o ID…',
            'data-allow-clear'          => 'true',
        ])
        ->choices(
            (
                $this->getModel()->getKey() && $this->getModel()->author
                    ? [
                        $this->getModel()->author->id =>
                            trim("{$this->getModel()->author->first_name} {$this->getModel()->author->last_name}")
                            ?: $this->getModel()->author->email,
                    ]
                    : []
            )
        )
        ->selected($this->getModel()->getKey() ? $this->getModel()->author_id : '')
        ->toArray()
)



            
->add(
    'collaborators',
    SelectField::class,
    SelectFieldOption::make()
        ->label('Collaboratori')
        ->attributes([
            'class'            => 'form-control select-search-full', // o select-search-ajax
            'multiple'         => true,
            'data-ajax--url'   => route('users.search'), // <-- CORRETTO per Select2 AJAX
            'data-ajax--cache' => 'true',
            'data-placeholder' => 'Cerca per nome, email o ID…',
            'data-allow-clear' => 'true',
        ])
        ->choices($this->getModel()->getKey()
            ? $this->getModel()->collaborators
                ->mapWithKeys(fn($u) => [$u->id => "{$u->username} ({$u->email})"])
                ->toArray()
            : []
        )
        ->selected($this->getModel()->getKey()
            ? $this->getModel()->collaborators->pluck('id')->all()
            : []
        )
        ->toArray()
)
->add('inviati', \Botble\Base\Forms\Fields\TagField::class, [
    'label' => 'Inviati speciali',
    'attr'  => [
        'data-role' => 'tagsinput',
        'placeholder' => 'Scrivi un nome e premi Invio...',
    ],
    'value' => $this->getModel()->inviati
        ? implode(',', (array) $this->getModel()->inviati)
        : '',
])


            ->add('image', MediaImageField::class)
            ->add(
                'tag',
                TagField::class,
                TagFieldOption::make()
                    ->label(trans('plugins/blog::posts.form.tags'))
                    ->when($this->getModel()->getKey(), function (TagFieldOption $fieldOption) {
                        return $fieldOption
                            ->selected(
                                $this
                                ->getModel()
                                ->tags()
                                ->select('name')
                                ->get()
                                ->map(fn (Tag $item) => $item->name)
                                ->implode(',')
                            );
                    })
                    ->placeholder(trans('plugins/blog::base.write_some_tags'))
                    ->ajaxUrl(route('tags.all'))
                    ->toArray()
            )
            ->setBreakFieldPoint('status');
    }
}
