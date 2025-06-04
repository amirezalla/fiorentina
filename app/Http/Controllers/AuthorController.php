<?php

namespace App\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\ACL\Models\User;
use Botble\Base\Enums\BaseStatusEnum;
use Theme;
use Botble\Base\Supports\Breadcrumb;

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
                          ->paginate(12);

        // basic meta + breadcrumb
        Theme::breadcrumb()
             ->add(__('Home'), route('public.index'))
             ->add($user->first_name . ' ' . $user->last_name, route('public.author', $user->id));

            $this->pageTitle($user->first_name . ' ' . $user->last_name . ', Autore presso ' . setting('site_title'));
        Theme::setTitle($user->first_name . ' '.$user->last_name .', Autore presso ' . setting('site_title'));

        return Theme::scope('author', compact('user', 'posts'))->render();
    }
}
