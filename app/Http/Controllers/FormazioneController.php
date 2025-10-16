<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\FormationVote;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class FormazioneController extends Controller
{
    /** GET /formazione */
    public function index(Request $request)
    {
        // Next upcoming match (SCHEDULED first, else LIVE), earliest by date
        $match = Calendario::where('status', 'SCHEDULED')
            ->orWhere('status', 'LIVE')
            ->orderBy('match_date', 'asc')
            ->first();

        if (!$match) {
            return view('formazione.index', [
                'match' => null,
                'team'  => 'fiorentina',
                'playersByRole' => collect(),
                'formations' => $this->formations(),
            ]);
        }

        // Players grouped by normalized role (GK/DF/MF/FW)
        $players = Player::query()->orderBy('jersey_number')->get();
        $playersByRole = $players->groupBy(function ($p) {
            $pos = strtoupper(trim($p->position ?? ''));
            // normalize common variants
            return match(true) {
                str_starts_with($pos, 'GK') || $pos === 'PORTIERE' || $pos === 'GOALKEEPER' => 'GK',
                str_starts_with($pos, 'DF') || $pos === 'DIFENSORE' || $pos === 'DEFENDER' => 'DF',
                str_starts_with($pos, 'MF') || $pos === 'CENTROCAMPISTA' || $pos === 'MIDFIELDER' => 'MF',
                str_starts_with($pos, 'FW') || $pos === 'ATTACCANTE' || $pos === 'FORWARD' => 'FW',
                default => 'MF', // fallback midfield
            };
        });

        return view('formazione.index', [
            'match'         => $match,
            'team'          => 'fiorentina',
            'playersByRole' => $playersByRole,
            'formations'    => $this->formations(),
        ]);
    }

    /** POST /formazione (store the vote) */
    public function store(Request $request)
    {
        // Ensure match still valid
        $match = Calendario::where('status', 'SCHEDULED')
            ->orWhere('status', 'LIVE')
            ->orderBy('match_date', 'asc')
            ->first();

        if (!$match) {
            throw ValidationException::withMessages([
                'match' => 'Nessuna partita imminente disponibile per la votazione.',
            ]);
        }

        // Validate
        $data = $request->validate([
            'team'      => ['required','string','in:fiorentina,another'],
            'formation' => ['required','string','regex:/^\d-\d-\d(-\d)?$/'], // supports 4-3-3, 4-2-3-1
            'positions' => ['required','array'], // slot => player_id
            'positions.*' => ['integer','exists:players,id'],
        ]);

        // Make sure number of slots matches the chosen formation (GK + D + M + F [+ AM etc. for 4-2-3-1])
        $expectedSlots = $this->expectedSlotsFromFormation($data['formation']);
        if (count($data['positions']) !== count($expectedSlots)) {
            throw ValidationException::withMessages([
                'positions' => 'I giocatori selezionati non corrispondono alla formazione scelta.',
            ]);
        }

        // Ensure roles are consistent: slot type GK/DF/MF/FW must match player position
        $players = Player::whereIn('id', array_values($data['positions']))->get()->keyBy('id');
        foreach ($expectedSlots as $slot => $role) {
            $playerId = $data['positions'][$slot] ?? null;
            if (!$playerId) {
                throw ValidationException::withMessages([
                    'positions' => "Manca un giocatore per lo slot {$slot}.",
                ]);
            }
            $p = $players[$playerId] ?? null;
            if (!$p || $this->normalizeRole($p->position) !== $role) {
                throw ValidationException::withMessages([
                    'positions' => "Giocatore non valido per lo slot {$slot} ({$role}).",
                ]);
            }
        }

        // Prevent duplicate vote from the same session for this match/team
        $sessionId = $request->session()->getId();
        $already = FormationVote::where([
            'match_id' => $match->match_id, // NB: Calendario has match_id field
            'team'     => $data['team'],
            'session_id' => $sessionId,
        ])->exists();

        if ($already) {
            throw ValidationException::withMessages([
                'vote' => 'Hai già espresso la tua formazione per questa partita da questa sessione.',
            ]);
        }

        // Store vote
        FormationVote::create([
            'match_id'   => $match->match_id,
            'team'       => $data['team'],
            'formation'  => $data['formation'],
            'positions'  => $data['positions'],
            'ip'         => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
            'session_id' => $sessionId,
        ]);

        return redirect()->route('formazione.index')
            ->with('ok', 'Voto registrato! Grazie per aver scelto la tua formazione.');
    }

    /** Available formations displayed in the select */
    protected function formations(): array
    {
        return [
            '3-4-3',
            '3-4-2-1',
            '3-4-1-2',
            '3-5-2 ',
            '4-3-3',
            '4-3-1-2',
            '4-3-2-1',
            '4-4-2',
            '4-5-1',
            '5-3-2',
            '5-4-1',
        ];
    }

    /** Build expected slot map for a formation.
     *  Example for 4-3-3: ['GK'=>'GK','DF1'=>'DF','DF2'=>'DF','DF3'=>'DF','DF4'=>'DF','MF1'=>'MF','MF2'=>'MF','MF3'=>'MF','FW1'=>'FW','FW2'=>'FW','FW3'=>'FW']
     */
    protected function expectedSlotsFromFormation(string $formation): array
    {
        // supports 4-3-3 or 4-2-3-1 (4 lines incl. GK)
        $parts = array_map('intval', explode('-', $formation));
        if (count($parts) === 3) {
            [$d,$m,$f] = $parts;
            return
                ['GK' => 'GK'] +
                $this->slots('DF', $d) +
                $this->slots('MF', $m) +
                $this->slots('FW', $f);
        }
        if (count($parts) === 4) {
            [$d,$m1,$m2,$f] = $parts; // treat m1+m2 as total midfielders
            $mid = $m1 + $m2;
            return
                ['GK' => 'GK'] +
                $this->slots('DF', $d) +
                $this->slots('MF', $mid) +
                $this->slots('FW', $f);
        }
        return ['GK' => 'GK'] + $this->slots('DF',4) + $this->slots('MF',3) + $this->slots('FW',3);
    }

    protected function slots(string $prefix, int $n): array
    {
        $out = [];
        for ($i=1;$i<=$n;$i++) $out[$prefix.$i] = $prefix;
        return $out;
    }

    protected function normalizeRole(?string $pos): string
    {
        $pos = strtoupper(trim($pos ?? ''));
        return match(true) {
            str_starts_with($pos, 'GK') || $pos === 'PORTIERE' || $pos === 'GOALKEEPER' => 'GK',
            str_starts_with($pos, 'DF') || $pos === 'DIFENSORE' || $pos === 'DEFENDER' => 'DF',
            str_starts_with($pos, 'MF') || $pos === 'CENTROCAMPISTA' || $pos === 'MIDFIELDER' => 'MF',
            str_starts_with($pos, 'FW') || $pos === 'ATTACCANTE' || $pos === 'FORWARD' => 'FW',
            default => 'MF',
        };
    }
}
