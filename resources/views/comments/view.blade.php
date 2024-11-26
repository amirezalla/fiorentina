@extends('admin.layouts.app') {{-- Adjust layout as per your project --}}

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Deleted Comments</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Author</th>
                        <th>Email</th>
                        <th>Content</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->name }}</td>
                            <td>{{ $comment->email }}</td>
                            <td>{{ Str::limit(strip_tags($comment->content), 100) }}</td>
                            <td>{{ $comment->deleted_at }}</td>
                            <td>
                                <form action="{{ route('fob-comment.comments.restore', $comment->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No deleted comments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{ $comments->links() }} {{-- Pagination --}}
            </div>
        </div>
    </div>
@endsection
