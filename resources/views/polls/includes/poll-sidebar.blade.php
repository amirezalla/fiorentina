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
