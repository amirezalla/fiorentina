<div class="container">
    @foreach (['Fiorentina Initial Lineup', 'Fiorentina Subs'] as $category)
        <h3>{{ $category }}</h3>
        <div class="row">
            @php $memberPolls = auth('member')->check() ? auth('member')->user()->polls()->whereIn('match_lineups_id',collect($lineup[$category])->pluck('id')->toArray())->pluck('value','match_lineups_id') : collect()  @endphp
            @foreach ($lineup[$category] as $player)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="playerpoll-card d-flex align-items-center p-3 border rounded">
                        <div class="flex flex-column">
                            <img src="{{ $player->player_image }}" alt="{{ $player->player_full_name }}"
                                 class="playerpoll-image mr-3">
                            <span>{{ $player->getAverageRate() }}</span>
                        </div>
                        <div class="player-info">
                            <p class="mb-1">{{ $player->player_full_name }}</p>
                            <div class="stars" @if($memberPolls->has($player->id)) data-default="{{ $memberPolls[$player->id] }}" @endif data-player-id="{{ $player->id }}"
                                 data-vote-url="{{ route('polls.store',$player->id) }}">
                                @for ($i = 1; $i <= 10; $i++)
                                    <span class="star @if($memberPolls->has($player->id) && $memberPolls->get($player->id) > $i) selected @endif" data-value="{{ $i }}">☆</span>
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
    document.querySelectorAll('.stars').forEach((parentElement) => {
        parentElement.querySelectorAll('.star').forEach((starElement) => {

            starElement.addEventListener("mouseenter", (event) => {
                const count = parseInt(event.target.getAttribute('data-value'));
                fillStars(parentElement, count);
            });

            starElement.addEventListener("mouseleave", () => {
                const count = parseInt(parentElement.getAttribute('data-default'));
                fillStars(parentElement, count);
            });

            starElement.addEventListener("click", (event) => {
                const rate = parseInt(event.target.getAttribute('data-value'));
                axios.post(parentElement.getAttribute('data-vote-url'), {
                    rate,
                }).then((response) => {
                    parentElement.setAttribute('data-default',response.data.value)
                    fillStars(parentElement,response.data.value)
                }).catch((error) => {
                    console.log(error)
                });
            });

        });
    });

    function fillStars(parentElement, count) {
        const elements = parentElement.querySelectorAll('.star');
        for (let el of elements) {
            el.classList.remove("selected");
        }
        if (count) {
            for (let i = 0; i < count; i++) {
                elements[i].classList.add("selected");
            }
        }
    }
</script>
