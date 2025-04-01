<?php

namespace Botble\Blog\Http\Controllers;

use App\Jobs\PostPublishingJob;
use Botble\ACL\Models\User;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\Blog\Forms\PostForm;
use Botble\Blog\Http\Requests\PostRequest;
use Botble\Blog\Models\Post;
use Botble\Blog\Services\StoreCategoryService;
use Botble\Blog\Services\StoreTagService;
use Botble\Blog\Tables\PostTable;
use Botble\Page\Models\Page;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PostController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/blog::base.menu_name'))
            ->add(trans('plugins/blog::posts.menu_name'), route('posts.index'));
    }

    public function index(PostTable $dataTable)
    {
        $this->pageTitle(trans('plugins/blog::posts.menu_name'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/blog::posts.create'));

        return PostForm::create()->renderForm();
    }

    public function store(
        PostRequest          $request,
        StoreTagService      $tagService,
        StoreCategoryService $categoryService
    )
    {
        $postForm = PostForm::create();

        $postForm->saving(function (PostForm $form) use ($request, $tagService, $categoryService) {

            $published_at = $request->filled('published_at') ? Carbon::parse($request->published_at) : null;

            $form
                ->getModel()
                ->fill([
                    ...$request->input(),
                    'author_id' => Auth::guard()->id(),
                    'author_type' => User::class,
                    'published_at' => $published_at ?? null,
                ])
                ->save();

            $post = $form->getModel();

            if ($published_at) {
                PostPublishingJob::dispatch($post->id, $post->published_at)->delay($published_at);
            }

            $form->fireModelEvents($post);

            $tagService->execute($request, $post);

            $categoryService->execute($request, $post);
        });

        return $this
            ->httpResponse()
            ->setPreviousRoute('posts.index')
            ->setNextRoute('posts.edit', $postForm->getModel()->getKey())
            ->withCreatedSuccessMessage();
    }

    public function edit(Post $post)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $post->name]));

        return PostForm::createFromModel($post)->renderForm();
    }

    public function update(
        Post                 $post,
        PostRequest          $request,
        StoreTagService      $tagService,
        StoreCategoryService $categoryService,
    )
    {
        PostForm::createFromModel($post)
            ->setRequest($request)
            ->saving(function (PostForm $form) use ($categoryService, $tagService) {
                $request = $form->getRequest();

                $published_at = $request->filled('published_at') ? Carbon::parse($request->published_at) : null;

                $request->merge([
                    'published_at' => $published_at ?? null,
                ]);

                $post = $form->getModel();
                $post->fill($request->input());
                $post->save();

                if ($published_at) {
                    PostPublishingJob::dispatch($post->id, $post->published_at)->delay($published_at);
                }

                $form->fireModelEvents($post);

                $tagService->execute($request, $post);

                $categoryService->execute($request, $post);
            });

        return $this
            ->httpResponse()
            ->setPreviousRoute('posts.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Post $post)
    {
        return DeleteResourceAction::make($post);
    }

    public function getWidgetRecentPosts(Request $request): BaseHttpResponse
    {
        $limit = $request->integer('paginate', 10);
        $limit = $limit > 0 ? $limit : 10;

        $posts = Post::query()
            ->with(['slugable'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $this
            ->httpResponse()
            ->setData(view('plugins/blog::widgets.posts', compact('posts', 'limit'))->render());
    }


    public function quickEdit(Request $request, $id)
    {
        $post = Post::findOrFail($id);
    
        // Validate incoming data as needed:
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'date' => 'nullable|date',
            'hour' => 'nullable|integer|min:0|max:23',
            'minute' => 'nullable|integer|min:0|max:59',
            'status' => 'required|in:published,draft',
            'categories' => 'array',
            'tags' => 'nullable|string',
            // Add other rules for additional fields
        ]);
    
        // Update post fields:
        $post->name = $data['name'];
        $post->slug = $data['slug'];
        // Combine date + hour + minute if provided
        if (isset($data['date'])) {
            $dateTime = $data['date'] . ' ' . str_pad($data['hour'] ?? 0, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data['minute'] ?? 0, 2, '0', STR_PAD_LEFT) . ':00';
            $post->created_at = $dateTime;
        }
        $post->status = $data['status'];
    
        // Handle categories relationship
        if (isset($data['categories'])) {
            // Example: if your categories are a BelongsToMany relationship
            $post->categories()->sync($data['categories']);
        }
    
        // Handle tags
        // If you store tags in a pivot table or a custom field, adapt accordingly.
    
        $post->save();
    
        // Return or redirect
        return redirect()->back()->with('success', 'Post updated successfully via Quick Edit!');
    }

    public function getQuickEditForm($id)
{
    $post = Post::findOrFail($id);
    $form = app(\Botble\Blog\Forms\PostQuickEditForm::class)
                ->setModel($post)
                ->renderForm(); // renderForm() returns the HTML for the form

    return response()->json(['html' => $form]);
}

}
