{{-- resources/views/match/partials/stats-html.blade.php --}}
<div class="statistics-container">
    @foreach ($allStats as $stat)
        @if ($stat['stage_name'] == 'Partita')
            <div class="stat-row mb-3">
                <!-- Home Value -->
                <div class="stat-value">{{ $stat['value_home'] }}</div>

                <!-- Stat Bar -->
                <div class="stat-bar">
                    @php
                        // Convert to numeric
                        $valueHome = is_numeric(str_replace('%', '', $stat['value_home']))
                            ? (float) str_replace('%', '', $stat['value_home'])
                            : 0;
                        $valueAway = is_numeric(str_replace('%', '', $stat['value_away']))
                            ? (float) str_replace('%', '', $stat['value_away'])
                            : 0;

                        // Summation
                        $maxValue = $valueHome + $valueAway;
                        if ($maxValue != 0) {
                            $homeWidth = ($valueHome / $maxValue) * 100;
                            $awayWidth = ($valueAway / $maxValue) * 100;
                        } else {
                            $homeWidth = $awayWidth = 0;
                        }

                        // Use $isHomeFiorentina if passed in from the controller
                        $isHomeFiorentina = $isHomeFiorentina ?? false;
                        $homeClass = $isHomeFiorentina ? 'fiorentina-fill' : 'away-fill';
                        $awayClass = !$isHomeFiorentina ? 'fiorentina-fill' : 'away-fill';
                    @endphp

                    <!-- Home Team Bar (left) -->
                    <div class="stat-bar-fill {{ $homeClass }}" style="width: {{ $homeWidth }}%;"></div>
                    <!-- Away Team Bar (right) -->
                    <div class="stat-bar-fill {{ $awayClass }}" style="width: {{ $awayWidth }}%;"></div>
                </div>

                <!-- Stat Label -->
                <div class="stat-label text-dark">{{ $stat['incident_name'] }}</div>

                <!-- Away Value -->
                <div class="stat-value">{{ $stat['value_away'] }}</div>
            </div>
        @endif
    @endforeach
</div>
