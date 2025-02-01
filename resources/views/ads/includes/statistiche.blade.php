<div class="statistics-container">

    @dd($statics)
    @foreach ($statics as $stat)
        @if ($stat['stage_name'] == 'Partita')
            <div class="stat-row mb-3">
                <!-- Home Value -->
                <div class="stat-value">{{ $stat['value_home'] }}</div>

                <!-- Stat Bar -->
                <div class="stat-bar">
                    @php
                        // Remove % and ensure numeric values
                        $valueHome = is_numeric(str_replace('%', '', $stat['value_home']))
                            ? (float) str_replace('%', '', $stat['value_home'])
                            : 0;
                        $valueAway = is_numeric(str_replace('%', '', $stat['value_away']))
                            ? (float) str_replace('%', '', $stat['value_away'])
                            : 0;

                        if ($stat['incident_name'] == 'Possesso Palla') {
                            $maxValue = 100;
                        } else {
                            $maxValue = $valueHome + $valueAway;
                        }

                        if ($maxValue != 0) {
                            $homeWidth = ($valueHome / $maxValue) * 100;
                            $awayWidth = ($valueAway / $maxValue) * 100;
                        } else {
                            $homeWidth = $awayWidth = 0;
                        }
                    @endphp

                    <div class="stat-bar-fill 
                @if ($isHomeFiorentina) fiorentina-fill
                @else
                away-fill @endif

                "
                        style="width: {{ $homeWidth }}%;"></div>
                    <div class="stat-bar-fill 
                @if (!$isHomeFiorentina) fiorentina-fill
                @else
                away-fill @endif
                
                "
                        style="width: {{ $awayWidth }}%;"></div>
                </div>

                <!-- Stat Label -->
                <div class="stat-label text-dark">{{ $stat['incident_name'] }}</div>

                <!-- Away Value -->
                <div class="stat-value">{{ $stat['value_away'] }}</div>
            </div>
        @endif
    @endforeach
</div>
