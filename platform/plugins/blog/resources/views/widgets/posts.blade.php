@if ($posts->isNotEmpty())
    <div class="table-responsive">
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>
                    #
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ trans('core/base::tables.name') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-end">
                    {{ trans('core/base::tables.created_at') }}
                </x-core::table.header.cell>
            </x-core::table.header>

            <x-core::table.body>
                @foreach ($posts as $post)
                    <x-core::table.body.row>
                        <x-core::table.body.cell>
                            {{ $loop->index + 1 }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            @if ($post->slug)
                                <a href="{{ $post->url }}" target="_blank">{{ Str::limit($post->name, 80) }}</a>
                            @else
                                <strong>{{ Str::limit($post->name, 80) }}</strong>
                            @endif
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-end text-nowrap">
                            {{ BaseHelper::formatDate($post->created_at) }}
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
    </div>
@else
    <x-core::empty-state :title="__('No results found')" :subtitle="trans('plugins/blog::posts.no_new_post_now')" />
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Toggle the inline quick edit form when the Quick Edit button is clicked.
    $(document).on('click', '.quick-edit-btn', function() {
        var id = $(this).data('id');
        $('#quick-edit-row-' + id).toggle();
    });

    // Hide the quick edit form when the Cancel button is clicked.
    $(document).on('click', '.cancel-quick-edit', function() {
        var id = $(this).data('id');
        $('#quick-edit-row-' + id).hide();
    });
</script>
