<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Botble\Setting\Models\Setting;

class ChatSettingsController extends Controller
{
    public function index()
    {
        // Fetch current settings
        $lightWords = Setting::where('key', 'light_words_censor')->value('value') ?? '[]';
        $autoMessage = Setting::where('key', 'chat_first_message')->value('value') ?? '';

        return view('diretta.chat-settings', [
            'lightWords' => json_decode($lightWords, true),
            'autoMessage' => $autoMessage,
        ]);
    }

    public function updateLightWords(Request $request)
    {
        $request->validate([
            'light_words' => 'required|array',
            'light_words.*' => 'string'
        ]);

        Setting::updateOrCreate(
            ['key' => 'light_words_censor'],
            ['value' => json_encode(array_map('trim', $request->light_words))]
        );

        return redirect()->back()->with('success', 'Light words updated successfully.');
    }

    public function updateAutoMessage(Request $request)
    {
        $request->validate([
            'auto_message' => 'required|string',
        ]);

        Setting::updateOrCreate(
            ['key' => 'chat_first_message'],
            ['value' => $request->auto_message]
        );

        return redirect()->back()->with('success', 'Auto first message updated successfully.');
    }
}
