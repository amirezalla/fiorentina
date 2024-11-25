<div class="col-lg-12 mx-auto">
    <div class="page-sidebar mt-3">
        <section>
            <div class="page-content">
                <div class="post-group">
                    <div class="post-group__header">
                        <h3 class="post-group__title">CLASSIFICA SERIE A</h3>
                    </div>
                </div>
            </div>

            <div>
                @php
                    $updateMessage = App\Http\Controllers\StandingController::fetchStandingsIfNeeded();
                    $updateScheduledMessage = App\Http\Controllers\StandingController::fetchScheduledMatches();
                @endphp
            </div>
            <table class="table table-sm table-striped">
                <thead style="background: blueviolet; border: 1px solid white; color: white; font-weight: 900;">
                    <tr>
                        <th style="border-right: 1px solid white;">#</th> <!-- New column for rank numbers -->
                        <th style="border-right: 1px solid white;">Team</th> <!-- Label for team -->
                        <th style="border-right: 1px solid white;">PT</th>
                        <th style="border-right: 1px solid white;">G</th>
                        <th style="border-right: 1px solid white;">V</th>
                        <th style="border-right: 1px solid white;">N</th>
                        <th style="border-right: 1px solid white;">P</th>
                        <th>DR</th>
                    </tr>
                </thead>
                <tbody style="background: white; border: 1px solid white;">
                    @foreach (App\Models\Standing::orderBy('points', 'desc')->get() as $index => $standing)
                        @php
                            // Assign special styles or labels based on the position
                            $rank = $index + 1;
                            $labelClass = '';
                            if ($rank <= 4) {
                                $labelClass = 'badge badge-success'; // First place
                            } elseif ($rank == 5) {
                                $labelClass = 'badge badge-warning'; // Top 4
                            } elseif ($rank == 6) {
                                $labelClass = 'badge badge-warning'; // Top 6
                            } elseif ($rank >= 18) {
                                $labelClass = 'badge badge-danger'; // Top 6
                            } else {
                                $labelClass = 'text-dark badge badge-light'; // Top 6
                            }
                        @endphp
                        <tr style="border-bottom:1px solid blueviolet">
                            <!-- Show rank number and assign special colors for top teams -->
                            <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center;" @endif
                                style="border-right: 1px solid blueviolet; text-align: center;">
                                <span class="{{ $labelClass }}">{{ $rank }}</span>
                            </td>
                            <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;" @endif
                                style="border-right: 1px solid blueviolet;">
                                <img src="{{ $standing->crest_url }}" width="15"> {{ $standing->short_name }}
                            </td>
                            <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                style="border-right: 1px solid blueviolet; text-align: center;">
                                {{ $standing->points }}
                            </td>
                            <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                style="border-right: 1px solid blueviolet; text-align: center;">
                                {{ $standing->played_games }}
                            </td>
                            <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                style="border-right: 1px solid blueviolet; text-align: center;">
                                {{ $standing->won }}
                            </td>
                            <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                style="border-right: 1px solid blueviolet; text-align: center;">
                                {{ $standing->draw }}
                            </td>
                            <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                style="border-right: 1px solid blueviolet; text-align: center;">
                                {{ $standing->lost }}
                            </td>
                            <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                style="text-align: center;">
                                {{ $standing->goal_difference }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="legend mb-4">
                <span class="badge badge-success"
                    style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                Champions League
                <span class="badge badge-warning"
                    style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                Europa & Conference League
                <br>
                <span class="badge badge-danger"
                    style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                Serie B
            </div>
        </section>
    </div>
</div>
