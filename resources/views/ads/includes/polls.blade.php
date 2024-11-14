<div class="container">
    @foreach (['Fiorentina Initial Lineup', 'Fiorentina Subs'] as $category)
        <h3>{{ $category }}</h3>
        <div class="row">
            @foreach ($lineup[$category] as $player)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="playerpoll-card d-flex align-items-center p-3 border rounded">
                        <img src="{{ $player->player_image }}" alt="{{ $player->player_full_name }}"
                            class="playerpoll-image mr-3">
                        <div class="player-info">
                            <p class="mb-1">{{ $player->player_full_name }}</p>
                            <div class="stars" data-player-id="{{ $player->id }}">
                                @for ($i = 1; $i <= 10; $i++)
                                    <span class="star" data-value="{{ $i }}">☆</span>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.stars').forEach(function(starsContainer) {
            let selectedValue = 0;

            starsContainer.querySelectorAll('.star').forEach(function(star) {
                star.addEventListener('mouseover', function() {
                    resetStars(starsContainer);
                    highlightStars(starsContainer, parseInt(this.dataset.value));
                });

                star.addEventListener('mouseout', function() {
                    resetStars(starsContainer);
                    if (selectedValue > 0) highlightStars(starsContainer,
                    selectedValue);
                });

                star.addEventListener('click', function() {
                    selectedValue = parseInt(this.dataset.value);
                    let playerId = starsContainer.dataset.playerId;

                    // Send an AJAX request
                    fetch('/rate-player', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                playerId: playerId,
                                rating: selectedValue
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert('Rating submitted successfully');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while submitting the rating.');
                        });
                });
            });
        });

        function resetStars(container) {
            container.querySelectorAll('.star').forEach(star => star.classList.remove('selected'));
        }

        function highlightStars(container, value) {
            container.querySelectorAll('.star').forEach((star, index) => {
                if (index < value) star.classList.add('selected');
            });
        }
    });
</script>
