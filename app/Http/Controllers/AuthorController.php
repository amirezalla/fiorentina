<?php

namespace App\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\ACL\Models\User;
use Botble\Base\Enums\BaseStatusEnum;
use Theme;

class AuthorController extends BaseController
{
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
             ->add($user->name);

        Theme::setTitle($user->name . ' | ' . setting('site_title'));

        return Theme::scope('author', compact('user', 'posts'))->render();
    }
}
