<x-core::button type="button" color="primary" class="btn-icon" size="sm" data-bs-toggle="tooltip"
    data-bs-original-title="{{ trans('plugins/fob-comment::comment.restore') }}"
    data-url="{{ route('fob-comment.comments.restore', $action->getItem()->getKey()) }}">
    <x-core::icon name="ti ti-refresh" />
</x-core::button>
