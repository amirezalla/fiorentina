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
    public function getPreview($slug)
    {
        $slug = SlugHelper::getSlug($slug, "");

        if (!$slug) {
            abort(404);
        }

        $result = apply_filters(BASE_FILTER_PUBLIC_SINGLE_DATA, $slug);

        
        if (! empty($result) && is_array($result)) {
            if (isset($result['view'])) {
                Theme::addBodyAttributes(['id' => Str::slug(Str::snake(Str::afterLast($slug->reference_type, '\\'))) . '-' . $slug->reference_id]);

                return Theme::scope($result['view'], $result['data'], Arr::get($result, 'default_view'))->render();
            }

            return $result;
        }

        abort(404);
    }

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
}
