<div class="row container mt-4">
    <div class="col-12">
        <div>
            <h1>{{ $poll->question }}</h1>
            <div id="options-container">
                @foreach ($poll->options as $option)
                    <div class="row">
                        <button class="col-12 btn btn-outline-primary vote-btn" data-id="{{ $option->id }}"
                            style="--fill-width: {{ $option->percentage }}%;">
                            <span
                                @if ($option->percentage > 16.66) class="option-text-w"

                @else
                    class="option-text-p" @endif>
                                {{ $option->option }}</span>
                            <span
                                @if ($option->percentage < 88) class="percentage-text-p"

                @else
                    class="percentage-text-w" @endif>{{ $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100, 2) : 0 }}
                                %</span>
                        </button>
                    </div>
                @endforeach
            </div>
            <div id="results-container">
                @foreach ($poll->options as $option)
                    <div class="result" id="result-{{ $option->id }}">
                        {{ $option->option }}: <span class="percentage">0%</span>
                    </div>
                @endforeach
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

                fetch(`poll-options/${optionId}/vote`, {
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

                this.disabled = true; // Disable the voted button
            });
        });
    });

    function updateResults(results, votedOptionId) {
        results.forEach(result => {
            const button = document.querySelector(`.vote-btn[data-id="${result.id}"]`);
            if (!button) return;

            const percentage = result.percentage || 0;
            const optionText = result.option || '';

            // Update button style and text
            button.style.setProperty('--fill-width', `${percentage}%`);
            const percentageText = button.querySelector('.percentage-text');
            if (percentageText) {
                percentageText.textContent = `${percentage}%`;
            }

            // Disable buttons that weren’t voted on
            if (result.id.toString() !== votedOptionId) {
                button.disabled = true;
            }
        });
    }
</script>
