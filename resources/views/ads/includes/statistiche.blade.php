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
                        if ($stat['incident_name'] == 'Possesso Palla') {
                            $maxValue = 100;
                        } else {
                            $maxValue = $stat['value_home'] + $stat['value_away'];
                        }
                        if ($maxValue != 0) {
                            $homeWidth =
                                is_numeric($stat['value_home']) && is_numeric($stat['value_away'])
                                    ? ($stat['value_home'] / $maxValue) * 100
                                    : 0;
                            $awayWidth =
                                is_numeric($stat['value_home']) && is_numeric($stat['value_away'])
                                    ? ($stat['away'] / $maxValue) * 100
                                    : 0;
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
