<?php

namespace App\Http\Controllers;

use App\Models\LiveChat;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use Botble\Member\Models\Member;
use Botble\Base\Supports\Breadcrumb;
use Illuminate\Support\Facades\Http;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use App\Jobs\StoreMessageJob;
use Botble\Setting\Models\Setting;






class ChatController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add("Gestione delle dirette");
    }

    public function list(){

        $this->pageTitle("Dirette");

        return view('diretta.list');
    }


    


    public function fetchMessages($matchId)
    {
        // Ensure the chat exists or create it if not
        $liveChat = LiveChat::firstOrCreate(
            ['match_id' => $matchId],
            ['chat_status' => 'live'] // Default status to 'live' if creating a new chat
        );
    
        // If the chat is finished, return an error
        if ($liveChat->isFinished()) {
            return response()->json(['error' => 'Chat is finished'], 403);
        }
    
        $autoMessage = Setting::where('key', 'chat_first_message')->value('value') 
                       ?? 'La chat è iniziata! Si prega di rispettare le regole, essere gentili e cortesi.';
        // If the chat was just created, create a welcome message from user_id = 1
        if ($liveChat->wasRecentlyCreated) {
            Message::create([
                'match_id' => $matchId,
                'user_id' => 1, // Admin user
                'message' => $autoMessage
            ]);
        }
    
        // Fetch all messages by match_id using the Message model
        $messages = Message::where('match_id', $matchId)->get();
    
        // Load associated members (users) for each message
        foreach ($messages as $message) {
            $message->member = Member::find($message->user_id);
        }
    
        // Return messages and an alert that the chat has started
        return response()->json([
            'alert' => [
                'type' => 'success',
                'message' => 'La chat è iniziata! Si prega di rispettare le regole, essere gentili e cortesi.'
            ],
            'messages' => $messages
        ]);
    }


    private function censorBadWords($message)
    {
        // List of light words you want to censor manually
        // $light = [
        //     "bastardo", "bastardi", "bastarda", "bastarde", "bernarda", "bischero", "bischera", "bocchino",
        //     "bordello", "cacare", "cacarella", "cagare", "cagata", "cagate", "caghetta", "cagone", "cazzata",
        //     "cazzo", "cazzi", "cazzone", "cazzoni", "cazzona", "cesso", "ciucciata", "cogliona", "coglione",
        //     "cretina", "cretino", "culattone", "culattona", "culo", "culone", "culona", "culoni", "deficiente",
        //     "figa", "fighe", "fottuta", "fottuto", "frocio", "frocione", "frocetto", "gesu", "imbecille",
        //     "imbecilli", "incazzare", "incazzato", "incazzati", "madonna", "maronna", "merda", "merdina",
        //     "merdona", "merdaccia", "mignotta", "mignottona", "mignottone", "mortacci", "negro", "negra",
        //     "pippa", "pippona", "pippone", "pippaccia", "pirla", "pompino", "porco", "puttana", "puttanona",
        //     "puttanone", "puttaniere", "puttanate", "rompiballe", "rompipalle", "rompicoglioni", "scazzi", "stronzo", "stronzi", "scopare", "scopata", "stronzata", "stronzo", "stronzone", "troia", "troione", "trombata",
        //     "vaffanculo", "zoccola", "zoccolona"
        // ];

        $lightWordsSetting = Setting::where('key', 'light_words_censor')->value('value');
        $light = json_decode($lightWordsSetting, true) ?? [];

        // If the setting is not found, use an empty array
    
        // Function to censor a word by keeping the first and last letter and replacing the middle with asterisks
        $censorWord = function ($word) {
            return substr($word, 0, 1) . str_repeat('*', max(strlen($word) - 2, 0)) . substr($word, -1);
        };
    
        // Create a pattern to match all bad words (case-insensitive, exact match)
        $badWordPattern = '/\b(' . implode('|', array_map('preg_quote', $light)) . ')\b/i';
    
        // Censor bad words in the message
        $censoredMessage = preg_replace_callback($badWordPattern, function ($matches) use ($censorWord) {
            return $censorWord($matches[0]);
        }, $message);
    
        return $censoredMessage;
    }
    
    


    public function sendMessage(Request $request, $matchId)
{
    $liveChat = LiveChat::where('match_id', $matchId)->first();

    if (!$liveChat || $liveChat->isFinished()) {
        return response()->json(['error' => 'Chat is finished'], 403);
    }

    // Get the message from the request
    $messageContent = $request->message;

    // Censor bad words in the message
    $censoredMessage = $this->censorBadWords($messageContent);

    // Create the message with the censored content
    $messageData = [
        'user_id' => auth('member')->id(),  // Manually set the user ID
        'message' => $censoredMessage,
        'match_id' => $matchId,
    ];
    
    $message = Message::create($messageData);

    // Broadcast the message to others
    // broadcast(new MessageSent($message))->toOthers();

    Queue::push(new StoreMessageJob($messageData));

    return response()->json(['message' => 'Message sent successfully', 'censored_message' => $censoredMessage], 200);
}


    public static function updateChatStatus($matchId, $status)
    {
        $liveChat = LiveChat::where('match_id', $matchId)->first();

        if ($liveChat) {
            $liveChat->update(['chat_status' => $status]);
            return response()->json(['status' => 'Chat status updated!']);
        }

        return response()->json(['error' => 'Live chat not found'], 404);
    }



        // Static function to create chat if it doesn't exist
        public static function createChatIfNotExists($matchId)
        {
            $liveChat = LiveChat::where('match_id', $matchId)->first();
    
            // Create the live chat if it does not exist
            if (!$liveChat) {
                LiveChat::create([
                    'match_id' => $matchId,
                    'chat_status' => 'live'  // Default status is 'live'
                ]);
            }
        }


            /** PATCH /chat/{message} */
    public function update(Request $request, Message $message)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $message->update(['message' => $request->message]);

        // rewrite JSON file
        $this->rewriteJson($message->match_id);

        return response()->json(['success' => true, 'message' => $message->message]);
    }

    /** DELETE /chat/{message} */
    public function destroy(Message $message)
    {
        $matchId = $message->match_id;
        $message->delete();           // soft‑delete

        $this->rewriteJson($matchId); // rewrite JSON file

        return response()->json(['success' => true]);
    }

    /** helper – rewrite messages_X.json with current DB state (excluding soft‑deleted) */
    protected function rewriteJson($matchId): void
    {
        $filePath = 'chat/messages_' . $matchId . '.json';

        $messages = Message::where('match_id', $matchId)->get(['id','user_id','message','created_at']);
        Storage::put($filePath, $messages->toJson());
    }

/* ---------- non‑deleted list (same as before) ---------------- */
public function body($matchId)
{
    $chats = Message::where('match_id', $matchId)->latest()->get();
    return view('diretta.includes.chat-body', compact('chats'))->render();
}

/* ---------- trashed list ------------------------------------ */
public function trashBody($matchId)
{
    $chats = Message::onlyTrashed()->where('match_id',$matchId)->latest()->get();
    return view('diretta.includes.trashmsg', compact('chats'))->render();
}

/* ---------- bulk soft‑delete -------------------------------- */
public function bulkDelete(Request $req)
{
    $ids = $req->input('ids', []);
    Message::whereIn('id', $ids)->delete();
    $this->rewriteJson($req->input('match_id'));
    return response()->json(['success'=>true]);
}

/* ---------- bulk restore ------------------------------------ */
public function bulkRestore(Request $req)
{
    $ids = $req->input('ids', []);
    Message::onlyTrashed()->whereIn('id',$ids)->restore();
    $this->rewriteJson($req->input('match_id'));
    return response()->json(['success'=>true]);
}

}
