@php
    use Carbon\Carbon;
@endphp
<div class="col-lg-12 mx-auto">
    <div class="page-sidebar mt-3">
        <section>
            <div class="page-content">
                <div class="post-group">
                    <div class="post-group__header">
                        <h3 class="post-group__title">Calendario Fiorentina</h3>
                    </div>
                </div>
            </div>



            @php
                $updateScheduledMessage = App\Http\Controllers\StandingController::fetchCalendario();
            @endphp


            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <table class="calendario table table-striped table-responsive" id="sortableTable">
                            <thead>
                                <tr>
                                    <th data-column="data" onclick="sortTable('data')">Data <span
                                            class="sort-arrow"></span></th>
                                    <th data-column="match" onclick="sortTable('match')">Match <span
                                            class="sort-arrow"></span></th>
                                    <th data-column="orario" onclick="sortTable('orario')">Orario/Risultati <span
                                            class="sort-arrow"></span></th>
                                    <th data-column="campionato" onclick="sortTable('campionato')">Campionato <span
                                            class="sort-arrow"></span></th>
                                    <th>
                                        Diretta
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matches = App\Models\Calendario::where('match_date', '>', '2024-08-16 23:00:00 ')->orderBy('match_date', 'asc')->get() as $match)
                                    @php
                                        $homeTeam = json_decode($match->home_team, true);
                                        $awayTeam = json_decode($match->away_team, true);
                                        $score = json_decode($match->score, true);
                                        $odds = json_decode($match->odds, true);
                                    @endphp
                                    <tr>
                                        <td>
                                            @php
                                                Carbon::setLocale('it'); // Set the locale to Italian
                                                $formattedDate = Carbon::parse($match->match_date)->translatedFormat(
                                                    'd F Y',
                                                );
                                            @endphp
                                            <span data-nf={{ $match->match_date }}>
                                                {{ $formattedDate }}
                                        </td>

                                        </span>
                                        <td>
                                            <div class="team-container">
                                                <div class="col-6">
                                                    <img src="{{ $homeTeam['logo'] }}"
                                                        alt="{{ $homeTeam['shortname'] }}"
                                                        style="width: 20px; height: auto;">
                                                    <span
                                                        @if ($score['away'] < $score['home']) style='font-weight:bold' @endif>
                                                        {{ $homeTeam['name'] }}
                                                    </span>
                                                </div>
                                                <div class='col-6'>
                                                    <img src="{{ $awayTeam['logo'] }}"
                                                        alt="{{ $awayTeam['shortname'] }}"
                                                        style="width: 20px; height: auto;">
                                                    <span
                                                        @if ($score['away'] > $score['home']) style='font-weight:bold' @endif>
                                                        {{ $awayTeam['name'] }}
                                                    </span>

                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            @if ($match->status != 'SCHEDULED')
                                                <span>
                                                    {{ $score['home'] ?? '-' }} -
                                                    {{ $score['away'] ?? '-' }}
                                                </span>

                                                <span>
                                                    @php
                                                        $isHomeFiorentina =
                                                            $homeTeam['name'] == 'Fiorentina' ||
                                                            $homeTeam['name'] == 'Fiorentina (Ita)' ||
                                                            $homeTeam['name'] == 'Fiorentina (Ita) *';
                                                        $isAwayFiorentina =
                                                            $awayTeam['name'] == 'Fiorentina' ||
                                                            $awayTeam['name'] == 'Fiorentina (Ita)' ||
                                                            $awayTeam['name'] == 'Fiorentina (Ita) *';
                                                    @endphp

                                                    @if (($isHomeFiorentina && $score['home'] > $score['away']) || ($isAwayFiorentina && $score['away'] > $score['home']))
                                                        <span
                                                            class="badge badge-pill badge-success ml-1 p-1 font-weight-bold">
                                                            V
                                                        </span>
                                                    @elseif ($score['home'] == $score['away'])
                                                        <span
                                                            class="badge badge-pill badge-warning ml-1 p-1 font-weight-bold">
                                                            N
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge badge-pill badge-danger ml-1 p-1 font-weight-bold">
                                                            P
                                                        </span>
                                                    @endif
                                                </span>
                                            @else
                                                @php
                                                    $time = Carbon::parse($match->match_date)->format('H:i');
                                                    if ($time == '00:00') {
                                                        $time = 'Da Confermare';
                                                    }
                                                @endphp
                                                {{ $time }}
                                            @endif

                                        </td>
                                        <td class="text-center">
                                            <img src="{{ $match->competition }}" alt="{{ $match->group }}"
                                                style="width: 30px; height: auto;">
                                        </td>
                                        <td>
                                            @if ($match->status != 'SCHEDULED')
                                                <a class="btn btn-p"
                                                    href="/diretta?match_id={{ $match->match_id }}">Diretta</a>
                                            @else
                                                <a class="btn btn-p-outline notifica-btn"
                                                    data-match-id="{{ $match->match_id }}">Notifica</a>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </section>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.notifica-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const matchId = this.getAttribute('data-match-id');

                        Swal.fire("SweetAlert2 is working!");

                    });
                });
            });





            let sortOrder = {}; // Keeps track of the sort order for each column

            function sortTable(column) {
                const table = document.getElementById('sortableTable');
                const tbody = table.tBodies[0]; // Only consider the first tbody
                const rows = Array.from(tbody.rows);

                // Toggle sort order for this column
                if (!sortOrder[column]) {
                    sortOrder[column] = 'asc'; // Set default sort order to ascending
                } else {
                    sortOrder[column] = sortOrder[column] === 'asc' ? 'desc' : 'asc'; // Toggle sort order
                }

                // Determine the index of the column to sort
                const columnIndex = Array.from(table.querySelectorAll('th')).findIndex(th => th.dataset.column === column);

                rows.sort((rowA, rowB) => {
                    let cellA = rowA.cells[columnIndex].innerText.trim();
                    let cellB = rowB.cells[columnIndex].innerText.trim();

                    // For the 'data' column, compare by the 'data-nf' attribute
                    if (column === 'data') {
                        const spanA = rowA.cells[columnIndex].querySelector('span');
                        const spanB = rowB.cells[columnIndex].querySelector('span');
                        if (spanA && spanB) {
                            cellA = spanA.getAttribute('data-nf');
                            cellB = spanB.getAttribute('data-nf');
                        }
                    }

                    if (sortOrder[column] === 'asc') {
                        return cellA.localeCompare(cellB, undefined, {
                            numeric: true
                        });
                    } else {
                        return cellB.localeCompare(cellA, undefined, {
                            numeric: true
                        });
                    }
                });

                // Rebuild the table body with the sorted rows
                rows.forEach(row => tbody.appendChild(row)); // Appending rows will automatically move them

                updateSortArrows(column);

            }

            function updateSortArrows(column) {
                const table = document.getElementById('sortableTable');
                const headers = table.querySelectorAll('th');

                // Clear all arrow indicators
                headers.forEach(header => {
                    const arrow = header.querySelector('.sort-arrow');
                    arrow.innerHTML = ''; // Remove the arrow text
                });

                // Add arrow to the currently sorted column
                const currentHeader = table.querySelector(`th[data-column="${column}"] .sort-arrow`);
                if (sortOrder[column] === 'asc') {
                    currentHeader.innerHTML = '▲'; // Ascending arrow
                } else {
                    currentHeader.innerHTML = '▼'; // Descending arrow
                }
            }
        </script>
    </div>
</div>
