<x-core::button 
    type="button" 
    color="gray" 
    class="btn-icon spam-btn" 
    size="sm" 
    data-bs-toggle="tooltip"
    data-bs-original-title="Spam"
    data-url="{{ route('fob-comment.comments.spam', $action->getItem()->getKey()) }}">
    <x-core::icon name="ti ti-ban" />
</x-core::button>

