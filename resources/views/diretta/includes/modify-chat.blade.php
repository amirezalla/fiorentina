@php use Botble\Member\Models\Member; @endphp

<div class="container mt-3">
    <div class="d-flex mb-2">
        <div class="col-12 justify-content-end d-flex">
            <button class="btn btn-sm btn-danger me-2" id="bulk-delete">Delete selected</button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle" id="chat-table">
            <thead class="table-light">
                <tr>
                    <th> </th>
                    <th>User</th>
                    <th>Message</th>
                    <th>Date&nbsp;/&nbsp;Time</th>
                    @if (Str::contains(request()->url(), '/chat-view'))
                        <th style="width:70px;">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>

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
            const matchId = '{{ request()->match_id ?? (request()->get('match') ?? '') }}'; // adapt if needed
            const tbody = document.querySelector('#chat-table tbody');
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            const editId = document.getElementById('edit-id');
            const editMsg = document.getElementById('edit-message');

            // ───────────────────────── polling every 1000 ms ──────────────────────────
            setInterval(fetchBody, 1000);

            function fetchBody() {
                fetch(/chat/body / $ {
                        matchId
                    })
                    .then(r => r.text())
                    .then(html => {
                        tbody.innerHTML = html;
                        attachRowEvents();
                    });
            }

            // ───────────────────── attach row buttons after (re)load ──────────────────
            function attachRowEvents() {
                // EDIT
                tbody.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.onclick = () => {
                        editId.value = btn.dataset.id;
                        editMsg.value = btn.dataset.message;
                        editModal.show();
                    };
                });

                // DELETE
                tbody.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.onclick = () => {
                        if (!confirm('Delete this message?')) return;
                        const id = btn.dataset.id;
                        fetch(/chat/$ {
                                id
                            }, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(r => r.json())
                            .then(res => {
                                if (res.success) fetchBody();
                            });
                    };
                });
            }

            // ───────────────────────── initial event binding ──────────────────────────
            attachRowEvents();

            // ───────── submit edit (modal) ─────────
            document.getElementById('edit-form').addEventListener('submit', e => {
                e.preventDefault();
                fetch(/chat/$ {
                        editId.value
                    }, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            message: editMsg.value
                        })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            editModal.hide();
                            fetchBody(); // refresh immediately
                        }
                    });
            });
        });
    </script>
@endpush
