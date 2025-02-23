<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifica;
use App\Models\Calendario;
use Illuminate\Support\Facades\Mail;

class NotificaController extends Controller
{
    public function store(Request $request)
    {
        // Store the notification data in the database
        Notifica::create([
            'email'    => $request->email,
            'match_id' => $request->match_id
        ]);

        // Fetch the match from the Calendario model using the match_id
        $calendario = Calendario::where('match_id',$request->match_id);
        if (!$calendario) {
            return response()->json(['success' => false, 'error' => 'Match non trovato.'], 404);
        }

        // Decode the JSON fields for home and away teams
        $home_team = json_decode($calendario->home_team);
        $away_team = json_decode($calendario->away_team);

        // Prepare the email subject and content in Italian
        $subject = "Notifica partita: {$home_team->name} vs {$away_team->name}";

        // Format the match date/time (assuming $calendario->match_time holds the datetime)
        $matchTime = date('d/m/Y H:i', strtotime($calendario->match_time));

        // Assuming matchday is stored in $calendario->matchday; adjust as needed
        $matchday = $calendario->matchday;

        // Build the email content using HTML, including team logos if available
        $content = "
            <p>Hai attivato la notifica per la partita:</p>
            <p>
                <strong>{$home_team->name} ({$home_team->shortname})</strong> vs 
                <strong>{$away_team->name} ({$away_team->shortname})</strong>
            </p>
            <p>Data e ora: {$matchTime}</p>
            <p>Giornata: {$matchday} di gruppo: Serie A</p>
            <p>Ti invieremo un'email per unirti al nostro commento in diretta 15 minuti prima dell'inizio della partita.</p>
            <p>La chat in diretta per questa partita sarà disponibile immediatamente quando la partita inizierà.</p>
            <p>
                <img src='{$home_team->logo}' alt='{$home_team->name} Logo' style='max-height:50px; margin-right:10px;'>
                <img src='{$away_team->logo}' alt='{$away_team->name} Logo' style='max-height:50px;'>
            </p>
        ";

        // Send the email using the custom view 'emails.template'
        Mail::send('emails.template', [
            'subject' => $subject,
            'content' => $content,
        ], function ($message) use ($request, $subject) {
            $message->to($request->email)
                    ->subject($subject);
        });

        return response()->json(['success' => true]);
    }
}
