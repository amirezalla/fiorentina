@foreach (['Fiorentina Subs', 'Fiorentina Initial Lineup'] as $category)
    <h3>{{ $category }}</h3>
    @foreach ($polls[$category] as $player)
        <div class="playerpoll-card">
            <img src="{{ $player->player_image }}" alt="{{ $player->player_full_name }}" class="playerpoll-image">
            <p>{{ $player->player_full_name }}</p>
            <div class="stars" data-player-id="{{ $player->id }}">
                @for ($i = 1; $i <= 10; $i++)
                    <span class="star" data-value="{{ $i }}">☆</span>
                @endfor
            </div>
        </div>
    @endforeach
@endforeach
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.stars .star').forEach(function(star) {
            star.addEventListener('mouseover', function() {
                let stars = this.parentElement.querySelectorAll('.star');
                stars.forEach(s => s.classList.remove('selected'));
                let value = parseInt(this.dataset.value);
                stars.forEach((s, index) => {
                    if (index < value) s.classList.add('selected');
                });
            });

            star.addEventListener('click', function() {
                let value = parseInt(this.dataset.value);
                let playerId = this.parentElement.dataset.playerId;

                // Send an AJAX request
                fetch('/rate-player', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            playerId: playerId,
                            rating: value
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
</script>
