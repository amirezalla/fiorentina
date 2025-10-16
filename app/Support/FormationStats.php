<?php

namespace App\Support;

use App\Models\FormationVote;
use App\Models\Player;
use App\Models\Calendario;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FormationStats
{
    /** Build all stats for a given match_id */
    public static function aggregate(string $matchId): array
    {
        $votes = FormationVote::where('match_id', $matchId)->get();
        $total = $votes->count();

        if ($total === 0) {
            return [
                'totalVotes' => 0,
                'topFormation' => null,
                'formationBreakdown' => [],
                'slots' => [],
            ];
        }

        // formation distribution
        $formationBreakdown = $votes->groupBy('formation')
            ->map->count()
            ->sortDesc()
            ->toArray();

        $topFormation = array_key_first($formationBreakdown);

        // tally per slot (GK, DF1.., MF.., FW..)
        $slotTallies = [];
        foreach ($votes as $v) {
            $pos = (array) $v->positions;
            foreach ($pos as $slot => $pid) {
                if (!$pid) continue;
                $slotTallies[$slot][$pid] = ($slotTallies[$slot][$pid] ?? 0) + 1;
            }
        }

        // pick winner per slot + percentage
        $winners = [];
        $allIds = [];
        foreach ($slotTallies as $slot => $counts) {
            arsort($counts);
            $pid = (int) array_key_first($counts);
            $cnt = $counts[$pid] ?? 0;
            $winners[$slot] = ['player_id' => $pid, 'count' => $cnt, 'perc' => (int) round($cnt * 100 / $total)];
            $allIds[] = $pid;
        }

        $players = Player::whereIn('id', array_unique($allIds))->get()->keyBy('id');

        // decorate with player models
        $slots = [];
        foreach ($winners as $slot => $info) {
            $p = $players->get($info['player_id']);
            $slots[$slot] = [
                'player' => $p,
                'perc'   => $info['perc'],
            ];
        }

        return [
            'totalVotes'        => $total,
            'topFormation'      => $topFormation,
            'formationBreakdown'=> $formationBreakdown,
            'slots'             => $slots,
        ];
    }

    /** helper to parse team JSON and return ['name'=>..., 'logo'=>...] */
    public static function teamInfo(?string $json): array
    {
        $arr = json_decode($json ?? '{}', true) ?: [];
        return [
            'name' => Arr::get($arr, 'name', '—'),
            'logo' => Arr::get($arr, 'logo'),
        ];
    }
}
