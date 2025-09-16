@extends('plugins/member::themes.dashboard.layouts.master')

@section('content')
    {!! apply_filters(MEMBER_TOP_STATISTIC_FILTER, null) !!}



    @if ($commentsData && $commentsData->isNotEmpty())

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
                        <option value="created_at_desc"
                            {{ request()->get('sortBy') == 'created_at_desc' ? 'selected' : '' }}>{{ __('Most Recent') }}
                        </option>
                        <option value="created_at_asc"
                            {{ request()->get('sortBy') == 'created_at_asc' ? 'selected' : '' }}>{{ __('Oldest') }}
                        </option>
                        <option value="replies_count_desc"
                            {{ request()->get('sortBy') == 'replies_count_desc' ? 'selected' : '' }}>
                            {{ __('Most Replied') }}</option>
                    </select>
                </div>
            </div>
        </div>
        @foreach ($commentsData as $data)
            @php
                $comment = $data['comment'];
                $post = $data['post'];
                $repliesCount = $data['replies_count'];
                $activityUrl = $comment
                    ? route('public.member.activity.comment', $comment->id)
                    : (Route::has('public.member.activity.comments')
                        ? route('public.member.activity.comments')
                        : null);
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
                        @if ($activityUrl)
                            <a href="{{ $activityUrl }}" class="small link-primary">
                                {{ __('View replies') }} @if ($repliesCount)
                                    ({{ $repliesCount }})
                                @endif
                            </a>
                        @endif

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

        <div class="d-flex justify-content-between">
            <div>
                Mostrando {{ $commentsData->firstItem() }} a {{ $commentsData->lastItem() }} di
                {{ $commentsData->total() }} commenti.
            </div>
            <div>
                {{ $commentsData->links() }}
            </div>
        </div>
    @else
        <p>{{ __('No comments found.') }}</p>
    @endif



    {{-- <activity-log-component></activity-log-component> --}}


@stop
<style>
    /* Card look to match the dashboard widgets */
    .activity-card {
        border: 1px solid #e9ecef;
        border-radius: .5rem;
    }

    /* Clean two-line clamp for the comment body */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .menu .activity-badge {
        margin-left: .5rem;
        background: #eee;
        color: #333;
        border-radius: 999px;
        padding: 0 .5rem;
        font-size: .75rem;
    }

    .menu-activity-preview {
        margin: .35rem 0 .5rem .5rem;
        padding: .5rem;
        border-left: 2px solid #eee;
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
    // Update filters on change (AJAX)
    document.getElementById('filterForm').addEventListener('change', function(event) {
        event.preventDefault();
        let formData = new FormData(this);
        fetch(window.location.href, {
                method: 'GET',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('commentsContainer').innerHTML = data;
            });
    });
</script>
