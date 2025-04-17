@php use Botble\Member\Models\Member; @endphp

<div class="container mt-3">
    <div class="d-flex mb-2">
        <div class="col-12 justify-content-end d-flex">
            <button class="btn btn-danger me-2" id="bulk-delete">Delete selected</button>
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
            const matchId = '{{ $matchId }}';
            const tbody = document.querySelector('#chat-table tbody');
            const isTrash = {{ request()->routeIs('chat.trash.body') ? 'true' : 'false' }};
            const csrf = '{{ csrf_token() }}';

            /* ---------------- fetch body every second ---------------- */
            setInterval(loadBody, 10000);
            loadBody();

            function loadBody() {
                const url = isTrash ? `/chat/trash-body/${matchId}` :
                    `/chat/body/${matchId}`;
                fetch(url).then(r => r.text()).then(html => {
                    tbody.innerHTML = html;
                    bindRowEvents();
                });
            }

            /* ---------------- row‑level buttons ---------------------- */
            function bindRowEvents() {
                if (!isTrash) { // delete in normal list
                    tbody.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.onclick = () => singleDelete(btn.dataset.id);
                    });
                } else { // restore in trash list
                    tbody.querySelectorAll('.restore-btn').forEach(btn => {
                        btn.onclick = () => singleRestore(btn.dataset.id);
                    });
                }
            }

            async function singleDelete(id) {
                await fetch(`/commentary/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                });
                await fetch(`/match/${matchId}/sync-all-commentaries`);
                loadBody();
            }
            async function singleRestore(id) {
                await fetch(`/commentary/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                });
                await fetch(`/match/${matchId}/sync-all-commentaries`);
                loadBody();
            }

            /* ---------------- bulk actions --------------------------- */
            document.getElementById('bulk-delete').onclick = async () => {
                const ids = checkedIds();
                if (!ids.length) return alert('Select rows first');
                await fetch(`/chat/bulk`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids,
                        match_id: matchId
                    })
                });
                await fetch(`/match/${matchId}/sync-all-commentaries`);
                loadBody();
            };

            document.getElementById('bulk-restore').onclick = async () => {
                const ids = checkedIds();
                if (!ids.length) return alert('Select rows first');
                await fetch(`/chat/bulk-restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids,
                        match_id: matchId
                    })
                });
                await fetch(`/match/${matchId}/sync-all-commentaries`);
                loadBody();
            };

            function checkedIds() {
                return [...document.querySelectorAll('.row-check:checked')]
                    .map(cb => cb.value);
            }
        });
    </script>
@endpush
