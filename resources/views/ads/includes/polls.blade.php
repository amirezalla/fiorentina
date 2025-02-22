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
    console.log($('.playerpoll-card .stars .star'))
</script>
