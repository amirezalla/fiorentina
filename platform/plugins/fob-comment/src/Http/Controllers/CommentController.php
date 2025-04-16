<?php

namespace FriendsOfBotble\Comment\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use FriendsOfBotble\Comment\Forms\CommentForm;
use FriendsOfBotble\Comment\Http\Requests\CommentRequest;
use FriendsOfBotble\Comment\Enums\CommentStatus;

use FriendsOfBotble\Comment\Models\Comment;
use FriendsOfBotble\Comment\Tables\CommentTable;

class CommentController extends BaseController
{
    public function index(CommentTable $commentTable)
    {
        $this->pageTitle(trans('plugins/fob-comment::comment.title'));

        return $commentTable->renderTable();
    }

    public function edit(Comment $comment)
    {
        $this->pageTitle(trans('plugins/fob-comment::comment.edit_comment'));

        return CommentForm::createFromModel($comment)->renderForm();
    }

    public function update(Comment $comment, CommentRequest $request)
    {
        CommentForm::createFromModel($comment)
            ->onlyValidatedData()
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('fob-comment.comments.index')
            ->withUpdatedSuccessMessage();
    }

    public function trash()
    {
        $this->pageTitle('Comments Trash Can');
    
        // Retrieve only soft-deleted comments
        $comments = Comment::onlyTrashed()->paginate(10); // Adjust pagination as needed
    
        return view('comments.trash.view', compact('comments'));
    }
    


    public function destroy(Comment $comment)
    {
        // Soft delete the comment
        $comment->status = CommentStatus::TRASH;
        $comment->save();
        // $comment->delete();

        return $this
            ->httpResponse()
            ->setPreviousRoute('fob-comment.comments.index')
            ->withDeletedSuccessMessage();
    }

    public function restore($id)
    {
        // Find the soft-deleted comment
        $comment = Comment::findOrFail($id);

        $comment->status = CommentStatus::APPROVED;
        $comment->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('fob-comment.comments.index');
    }


    public function spam(){

    }

    public function notApproved(){

    }
}
