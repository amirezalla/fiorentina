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

public function store(Request $request)
    {
        // partita prossima (o live)
        $match = Calendario::where('status', 'SCHEDULED')
            ->orWhere('status', 'LIVE')
            ->orderBy('match_date', 'asc')
            ->first();

        if (!$match) {
            return redirect('/prossima-partita-formazione-dei-tifosi')
                ->with('error', 'Nessuna partita disponibile per la votazione.');
        }

        // validazione base
        $data = $request->validate([
            'team'                  => 'required|string|max:50',
            'formation'             => 'required|string|max:10', // es: 4-3-3
            'positions'             => 'required|array|min:1',
            'positions.*'           => 'integer',                 // id giocatore
        ]);

        $ip         = $request->ip();
        $sessionId  = $request->session()->getId();

        // evita doppio voto sulla stessa partita (stesso IP o stessa sessione)
        $already = FormationVote::where('match_id', $match->match_id)
            ->where(function ($q) use ($ip, $sessionId) {
                $q->where('ip', $ip)->orWhere('session_id', $sessionId);
            })
            ->exists();

        if ($already) {
            return redirect('/prossima-partita-formazione-dei-tifosi')
                ->with('error', 'Hai già votato per questa partita. È consentito un solo voto.');
        }

        FormationVote::create([
            'match_id'   => $match->match_id,
            'team'       => $data['team'],
            'formation'  => $data['formation'],
            'positions'  => $data['positions'], // cast array->json nel Model
            'ip'         => $ip,
            'user_agent' => (string) $request->header('User-Agent', ''),
            'session_id' => $sessionId,
        ]);

        return redirect('/prossima-partita-formazione-dei-tifosi')
            ->with('ok', 'Voto registrato! Grazie per aver scelto la tua formazione. Puoi esprimere un solo voto per partita.');
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
