<?php

namespace App\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\ACL\Models\User;
use Botble\Base\Enums\BaseStatusEnum;
use Theme;
use Botble\Base\Supports\Breadcrumb;
use Botble\Page\Models\Page;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;


class AuthorController extends BaseController
{
        protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add("Author");
    }
    public function show(User $user, PostInterface $postRepo)
    {
        // All published posts by this author
        $posts = $postRepo->getModel()
                          ->where('author_id', $user->id)
                          ->where('status', BaseStatusEnum::PUBLISHED)
                          ->latest()
                          ->paginate(50);

        // basic meta + breadcrumb
        Theme::breadcrumb()
             ->add(__('Home'), route('public.index'))
             ->add($user->first_name . ' ' . $user->last_name, route('public.author', $user->id));
$meta = new SeoOpenGraph();
    SeoHelper::setTitle($user->first_name. ' ' . $user->last_name . ', Autore presso ' . setting('site_title'));
        SeoHelper::setDescription(__('Author page for :name', ['name' => $user->full_name]));
        Theme::setTitle($user->first_name . ' '.$user->last_name .', Autore presso ' . setting('site_title'));
            $meta->setTitle($user->first_name. ' ' . $user->last_name . ', Autore presso ' . setting('site_title'));

        
        return Theme::scope('author', compact('user', 'posts'))->render();
    }
}
