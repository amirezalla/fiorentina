@extends('plugins/member::themes.dashboard.layouts.master')

@section('content')

    <!-- Search box -->
    <form method="GET" action="{{ route('public.member.activity.comments') }}" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" value="{{ request()->get('search') }}"
                placeholder="{{ __('Cerca tra i commenti o i post') }}" />
            <button class="btn btn-primary" type="submit">{{ __('Search') }}</button>
        </div>
    </form>

    <!-- Filters and Sorting -->
    <div class="filters mb-3">
        <div class="d-flex justify-content-between">
            <div>
                <label for="perPage">{{ __('Comments per page') }}:</label>
                <select name="perPage" id="perPage" class="form-control" onchange="this.form.submit()">
                    <option value="10" {{ request()->get('perPage') == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request()->get('perPage') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request()->get('perPage') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request()->get('perPage') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

            <div>
                <label for="sortBy">{{ __('Sort by') }}:</label>
                <select name="sortBy" id="sortBy" class="form-control" onchange="this.form.submit()">
                    <option value="created_at_desc" {{ request()->get('sortBy') == 'created_at_desc' ? 'selected' : '' }}>
                        {{ __('Most Recent') }}</option>
                    <option value="created_at_asc" {{ request()->get('sortBy') == 'created_at_asc' ? 'selected' : '' }}>
                        {{ __('Oldest') }}</option>
                    <option value="replies_count_desc"
                        {{ request()->get('sortBy') == 'replies_count_desc' ? 'selected' : '' }}>{{ __('Most Replied') }}
                    </option>
                </select>
            </div>
        </div>
    </div>

    <!-- Comments List -->
    <div class="comments-list">
        @foreach ($commentsData as $data)
            @php
                $comment = $data['comment'];
                $post = $data['post'];
                $repliesCount = $data['replies_count'];
                $activityUrl = route('public.member.activity.comment', $comment->id);
            @endphp

            <div class="card mb-3 activity-card">
                <div class="card-body py-3">
                    <div class="small text-muted mb-1">
                        {{ __('On') }}
                        @if ($post)
                            <a href="{{ $post->url }}" target="_blank" class="link-secondary">
                                {{ Str::limit($post->name, 80) }}
                            </a>
                        @else
                            <em>{{ __('(post removed)') }}</em>
                        @endif
                        • {{ $comment->created_at->diffForHumans() }}
                    </div>

                    <div class="mb-2 line-clamp-2 text-body">
                        {!! BaseHelper::clean(e(Str::limit(strip_tags(str_replace('&nbsp;', ' ', $comment->content)), 300))) !!}
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ $activityUrl }}" class="small link-primary">
                            {{ __('View replies') }} @if ($repliesCount)
                                ({{ $repliesCount }})
                            @endif
                        </a>

                        @if ($post)
                            <a href="{{ $post->url }}#comment-{{ $comment->id }}" target="_blank"
                                class="small link-secondary">
                                {{ __('Open in post') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $commentsData->appends(request()->except('page'))->links() }}
    </div>

@stop
