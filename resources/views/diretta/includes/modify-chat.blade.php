@php use Botble\Member\Models\Member; @endphp

<div class="container mt-3">
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle" id="chat-table">
            <thead class="table-light">
                <tr>
                    @if (Str::contains(request()->url(), '/chat-view'))
                        <th style="width:70px;">Actions</th>
                    @endif
                    <th>User</th>
                    <th>Message</th>
                    <th>Date&nbsp;/&nbsp;Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chats as $chat)
                    @continue($loop->first)
                    @php $user = Member::find($chat['user_id']); @endphp
                    <tr id="row-{{ $chat->id }}">
                        {{-- ACTIONS --}}
                        @if (Str::contains(request()->url(), '/chat-view'))
                            <td>
                                <button class="btn btn-link p-0 me-2 text-danger delete-btn" data-id="{{ $chat->id }}"
                                    aria-label="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <button class="btn btn-link p-0 text-muted edit-btn" data-id="{{ $chat->id }}"
                                    data-message="{{ e($chat['message']) }}" aria-label="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                        @endif

                        {{-- USER --}}
                        <td>
                            @if ($user)
                                <a href="{{ url("admin/members/edit/{$user->id}") }}">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </a>
                            @else
                                <em class="text-muted">Unknown</em>
                            @endif
                        </td>

                        {{-- MESSAGE --}}
                        <td class="chat-msg">{{ $chat['message'] }}</td>

                        {{-- DATE --}}
                        <td class="text-nowrap">{{ $chat['created_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ────────────────────── BOOTSTRAP EDIT MODAL ─────────────────────── --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-form">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id">
                    <div class="mb-3">
                        <textarea class="form-control" id="edit-message" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('footer')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            let editIdInput = document.getElementById('edit-id');
            let editMsgArea = document.getElementById('edit-message');

            // ── OPEN MODAL ───────────────────────────────────────────────
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    editIdInput.value = btn.dataset.id;
                    editMsgArea.value = btn.dataset.message;
                    editModal.show();
                });
            });

            // ── SUBMIT EDIT ─────────────────────────────────────────────
            document.getElementById('edit-form').addEventListener('submit', e => {
                e.preventDefault();
                const id = editIdInput.value;
                fetch(`/chat/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            message: editMsgArea.value
                        })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            // update row text
                            document.querySelector(`#row-${id} .chat-msg`).textContent = res.message;
                            // update data-message attribute
                            document.querySelector(`.edit-btn[data-id="${id}"]`).dataset.message = res
                                .message;
                            editModal.hide();
                        } else {
                            alert('Error while updating');
                        }
                    });
            });

            // ── DELETE ──────────────────────────────────────────────────
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (!confirm('Delete this message?')) return;
                    const id = btn.dataset.id;
                    fetch(`/chat/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': @json(csrf_token())
                            }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                document.getElementById(`row-${id}`).remove();
                            } else {
                                alert('Error while deleting');
                            }
                        });
                });
            });
        });
    </script>
@endpush
