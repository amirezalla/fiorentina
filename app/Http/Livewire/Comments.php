<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use FriendsOfBotble\Comment\Models\Comment;
class Comments extends Component
{
    use WithPagination;

    protected $listeners = ['load-more' => 'loadMore'];

    public function loadMore()
    {
        $this->emit('load-more');
    }

    public function render()
    {
        $comments = Comment::paginate(10);

        return view('livewire.comments', compact('comments'));
    }
}