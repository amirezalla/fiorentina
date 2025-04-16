<x-core::button type="button" color="success" class="restore-btn btn-icon" size="sm" data-bs-toggle="tooltip"
    data-bs-original-title="Restore"
    data-url="{{ route('fob-comment.comments.restore', $action->getItem()->getKey()) }}">
    <x-core::icon name="ti ti-refresh" />
</x-core::button>
