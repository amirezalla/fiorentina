
<x-core::button type="button" color="warning" class="btn-icon notapproved-btn" size="sm" data-bs-toggle="tooltip"
    data-bs-original-title="Not Approved"
    data-url="{{ route('fob-comment.comments.notapproved', $action->getItem()->getKey()) }}">
    <x-core::icon name="ti ti-x" />
</x-core::button>
