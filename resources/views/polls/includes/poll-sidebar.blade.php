<div class="container my-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="card-title mb-0">{{ $poll->question }}</h2>
        </div>
        <div class="card-body">
            <p class="text-muted">Totale voti: {{ $totalVotes }}</p>

            <div id="options-container">
                @foreach ($poll->options as $option)
                    @php
                        $percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100, 2) : 0;
                    @endphp
                    <div class="mb-3">
                        <button
                            class="btn btn-outline-primary vote-btn w-100 position-relative d-flex justify-content-between align-items-center"
                            data-id="{{ $option->id }}" style="overflow: hidden;">
                            {{-- "Filling" background using an absolutely positioned div --}}
                            <div class="position-absolute top-0 start-0 h-100 bg-primary opacity-25"
                                style="width: {{ $percentage }}%; z-index:1;">
                            </div>
                            <span class="mx-2" style="z-index:2;">{{ $option->option }}</span>
                            <span class="mx-2" style="z-index:2;">{{ $percentage }}%</span>
                        </button>
                    </div>
                @endforeach
            </div>

            <div id="results-container" class="mt-4 d-none">
                {{-- We’ll dynamically update if you want more details here --}}
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = "{{ csrf_token() }}"; // Laravel CSRF token

        if (!csrfToken) {
            console.warn('CSRF token not found!');
            return;
        }

        const buttons = document.querySelectorAll('.vote-btn');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const optionId = this.getAttribute('data-id');
                console.log(optionId);
                fetch(`/options/${optionId}/vote`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(
                                `Request failed: ${response.status} ${response.statusText}`
                            );
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.results) {
                            updateResults(data.results, optionId);
                        } else {
                            console.warn('Unexpected response format:', data);
                        }
                    })
                    .catch(error => {
                        console.error('Vote submission failed:', error);
                    });

                // Disable the clicked button
                this.disabled = true;
            });
        });
    });

    function updateResults(results, votedOptionId) {
        // Optionally show a "results-container" or just update the buttons
        const resultsContainer = document.getElementById('results-container');
        resultsContainer.classList.remove('d-none');
        resultsContainer.innerHTML = '';

        let totalVotes = 0;
        results.forEach(r => totalVotes += (r.votes || 0));

        results.forEach(result => {
            // Update each button’s background fill and text
            const button = document.querySelector(`.vote-btn[data-id="${result.id}"]`);
            if (!button) return;

            const newPercentage = result.percentage || 0;
            const fillDiv = button.querySelector('div.bg-primary');
            if (fillDiv) {
                fillDiv.style.width = newPercentage + '%';
            }

            // The text at the end of the button
            const spans = button.querySelectorAll('span');
            // Typically spans[1] might be the percentage text
            if (spans[1]) {
                spans[1].textContent = `${newPercentage}%`;
            }

            // If it’s not the voted button, disable it
            if (result.id.toString() !== votedOptionId) {
                button.disabled = true;
            }

            // Add a line to resultsContainer if you want a separate summary
            const div = document.createElement('div');
            div.textContent = `${result.option}: ${result.votes} voti (${newPercentage}%)`;
            resultsContainer.appendChild(div);
        });
    }
</script>
