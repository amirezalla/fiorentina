<div class="poll-card card my-4 shadow-sm">
    <div class="card-header bg-white border-0 pb-2">
        <h6 class="m-0 fw-semibold text-uppercase">{{ $poll->question }}</h6>
    </div>

    <div class="card-body pt-3">
        <p class="small text-muted mb-4">Totale voti: {{ $totalVotes }}</p>

        @foreach ($poll->options as $option)
            @php
                $percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100) : 0;
            @endphp

            <!-- Only two extra spans; “fill” span is the bar -->
            <button class="poll-option w-100 mb-3 position-relative" data-id="{{ $option->id }}"
                {{ session('votedOption') ? 'disabled' : '' }}>
                <span class="option-text">{{ $option->option }}</span>
                <span class="option-perc fw-semibold">{{ $percentage }}%</span>
                <span class="fill" style="width: {{ $percentage }}%"></span>
            </button>
        @endforeach
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
                // Assume optionId is obtained from the clicked button's data-id attribute
                fetch(`/pollone-options/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        optionId: optionId
                    }) // send the optionId in the payload
                })
                location.reload(); // Reload the page to show updated results



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
