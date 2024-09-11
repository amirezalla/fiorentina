<div class="container">
    @foreach ($summaries->groupBy('stage_name') as $stageName => $items)
        <div class="stage mb-4">
            <h4 class="stage-tempo">{{ $stageName }}
                @php
                    $firstIncident = $items->first();
                    $participants = json_decode($firstIncident->incident_participants, true);
                @endphp

            </h4>
            @foreach ($items as $item)
                @php
                    $participants = json_decode($item->incident_participants, true);
                    $isHomeTeam = $item->incident_team == 1; // Team 1 for home, Team 2 for away
                @endphp

                <div
                    class="incident d-flex align-items-center mb-2 {{ $isHomeTeam ? 'justify-content-start' : 'justify-content-end' }}">
                    @if ($isHomeTeam)
                        <!-- Home Team Incident -->
                        <div class="incident-content d-flex align-items-center">
                            <div class="incident-time">
                                {{ $item->incident_time }}
                            </div>
                            <div class="incident-icon m-2">
                                @if ($participants[0]['incident_type'] === 'GOAL')
                                    <i class="fa fa-futbol"></i>
                                @elseif ($participants[0]['incident_type'] === 'YELLOW_CARD')
                                    <i class="fa fa-square text-warning"></i>
                                    @if (isset($participants[1]))
                                        @if ($participants[1]['incident_type'] === 'RED_CARD')
                                            <i class="fa fa-square text-danger"></i>
                                        @endif
                                    @endif
                                @elseif ($participants[0]['incident_type'] === 'RED_CARD')
                                    <i class="fa fa-square text-danger"></i>
                                @elseif ($participants[0]['incident_type'] === 'SUBSTITUTION_OUT')
                                    <i class="fa fa-exchange-alt"></i>
                                @elseif ($participants[0]['incident_type'] === 'PENALTY_KICK')
                                    @dd($participants[1]['incident_type'])
                                    @if ($participants[1]['incident_type'] === 'PENALTY_MISSED')
                                        <i class="fa fa-xmark text-danger"></i>
                                    @endif
                                @elseif ($participants[0]['incident_type'] === 'PENALTY_KICK')
                                    @if ($participants[1]['incident_type'] === 'PENALTY_SCORED')
                                        <i class="fa fa-futbol "></i>
                                    @endif
                                @endif
                            </div>
                            <div class="incident-detail">
                                @foreach ($participants as $participant)
                                    @if ($participant['incident_type'] == 'ASSISTANCE')
                                        <span>{{ $participant['participant_name'] }}</span>
                                    @elseif ($participant['incident_type'] == 'PENALTY_KICK')
                                    @else
                                        <strong>{{ $participant['participant_name'] }}</strong>
                                    @endif
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                                @if ($item->incident_type === 'GOAL' && isset($item['home_score']) && isset($item['away_score']))
                                    <span>({{ $participants[0]['home_score'] }} -
                                        {{ $participants[0]['away_score'] }})</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Away Team Incident -->
                        <div class="incident-content d-flex align-items-center">
                            <div class="incident-detail text-right mr-2">
                                @foreach ($participants as $participant)
                                    <strong>{{ $participant['participant_name'] }}</strong>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                                @if ($item->incident_type === 'GOAL' && isset($participants[0]['home_score']) && isset($participants[0]['away_score']))
                                    <span>({{ $participants[0]['home_score'] }} -
                                        {{ $participants[0]['away_score'] }})</span>
                                @endif
                            </div>
                            <div class="incident-icon m-2">
                                @if ($participants[0]['incident_type'] === 'GOAL')
                                    <i class="fa fa-futbol"></i>
                                @elseif ($participants[0]['incident_type'] === 'YELLOW_CARD')
                                    <i class="fa fa-square text-warning"></i>
                                    @if (isset($participants[1]))
                                        @if ($participants[1]['incident_type'] === 'RED_CARD')
                                            <i class="fa fa-square text-danger"></i>
                                        @endif
                                    @endif
                                @elseif ($participants[0]['incident_type'] === 'RED_CARD')
                                    <i class="fa fa-square text-danger"></i>
                                @elseif ($participants[0]['incident_type'] === 'SUBSTITUTION_OUT')
                                    <i class="fa fa-exchange-alt"></i>
                                @endif
                            </div>
                            <div class="incident-time">
                                {{ $item->incident_time }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>
