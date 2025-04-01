<?php

namespace Botble\Blog\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Blog\Http\Requests\PostQuickEditRequest; // Create or adjust this request for quick edit validation
use Botble\Blog\Models\Post;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\DatetimeField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TreeCategoryField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TagFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;

class PostQuickEditForm extends FormAbstract
{
    /**
     * Get the action buttons for the quick edit form.
     *
     * @return string
     */
    public function getActionButtons(): string
    {
        // Create a dedicated partial or simply return buttons inline
        return view('plugins/blog::partials.quick-edit-form-actions')->render();
    }

    /**
     * Setup the quick edit form with only a subset of fields.
     *
     * @return void
     */
    public function setup(): void
    {
        $this
            ->model(Post::class)
            ->setValidatorClass(PostQuickEditRequest::class)
            // Quick Edit: Title field
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            // Quick Edit: Slug field (not included in your full form, so add it here)
            ->add('slug', TextField::class, [
                'label' => 'Slug',
                'attr'  => ['required' => true],
            ])
            // Quick Edit: Scheduled Publishing (published_at)
            ->add('published_at', DatetimeField::class, DatePickerFieldOption::make()
                ->label(trans('plugins/blog::posts.form.scheduled_publishing'))
                ->defaultValue(null))
            // Quick Edit: Status
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            // Quick Edit: Categories (using the tree field)
            ->add('categories[]', TreeCategoryField::class, SelectFieldOption::make()
            ->label(trans('plugins/blog::posts.form.categories'))
            ->choices(get_categories_with_children())
            ->selected($this->getModel() && $this->getModel()->getKey()
                ? $this->getModel()->categories()->pluck('category_id')->all()
                : [])
            ->toArray())
            // Quick Edit: Tags
            ->add('tag', TagField::class, TagFieldOption::make()
                ->label(trans('plugins/blog::posts.form.tags'))
                ->placeholder(trans('plugins/blog::base.write_some_tags'))
                ->ajaxUrl(route('tags.all'))
                ->toArray())
            ->setBreakFieldPoint('status');
    }
}
