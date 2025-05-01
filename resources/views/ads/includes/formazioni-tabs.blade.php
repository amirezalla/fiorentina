<div class="tab-content" id="teamtabContent">

    <!-- Home tab content -->
    <div class="tab-pane fade @if ($isHomeFiorentina) show active @endif" id="home" role="tabpanel"
        aria-labelledby="home-tab">

        @if ($isHomeFiorentina)
            @include('ads.includes.formazioni', [
                'groupedLineups' => $fiorentinaLineups,
                'team' => 'fiorentina',
            ])
        @else
            @include('ads.includes.formazioni', [
                'groupedLineups' => $anotherTeamLineups,
                'team' => 'another',
            ])
        @endif
    </div>

    <!-- Away tab content -->
    <div class="tab-pane fade @if ($isAwayFiorentina) show active @endif" id="away" role="tabpanel"
        aria-labelledby="away-tab">

        @if ($isAwayFiorentina)
            @include('ads.includes.formazioni', [
                'groupedLineups' => $fiorentinaLineups,
                'team' => 'fiorentina',
            ])
        @else
            @include('ads.includes.formazioni', [
                'groupedLineups' => $anotherTeamLineups,
                'team' => 'another',
            ])
        @endif
    </div>
</div>
