<x-core::button type="button" color="success" class="btn-icon approved-btn" size="sm" data-bs-toggle="tooltip"
    data-bs-original-title="Approved"
    data-url="{{ route('fob-comment.comments.approved', $action->getItem()->getKey()) }}">
    <x-core::icon name="ti ti-check" />
</x-core::button>
