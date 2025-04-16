<x-core::button type="button" color="success" class="btn-icon" size="sm" data-bs-toggle="tooltip"
    data-bs-original-title="restore" data-url="{{ route('fob-comment.comments.restore', $action->getItem()->getKey()) }}">
    <x-core::icon name="ti ti-refresh" />
</x-core::button>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.restore-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Retrieve the URL from the data attribute
                var url = btn.getAttribute('data-url');

                // Send a POST request using the Fetch API
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token for security
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Restore successful:', data);
                        // Optionally update the UI or reload the page to reflect the change
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error restoring comment:', error);
                    });
            });
        });
    });
</script>
