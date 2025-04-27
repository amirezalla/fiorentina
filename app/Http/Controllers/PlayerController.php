<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Player;
use App\Models\Vote;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;
use App\Http\Forms\AdForms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Http;



class PlayerController extends BaseController
{

    public function index()
    {
        $this->pageTitle("Players List");
        $players = Player::query()->latest()->paginate(20);
        return view('players.view', compact('players'));
    }
    public function create()
    {
        return view('players.create');
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'league' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'season' => 'required|string|in:2024-2025,2025-2026,2026-2027', // Validate selected season
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
            'flag_id' => 'required|integer',
            'jersey_number' => 'required|integer',
            'status' => 'required|string|in:published,draft,pending',
        ]);

        // Handle the image upload
        $imagePath = $request->file('image')->store('players', 'public');

        // Create a new player record
        Player::create([
            'name' => $request->name,
            'league' => $request->league,
            'position' => $request->position,
            'season' => $request->season,
            'image' => $imagePath,
            'flag_id' => $request->flag_id,
            'jersey_number' => $request->jersey_number,
            'status' => $request->status,
        ]);

        // Redirect to the player list with a success message
        return redirect()->route('players.index')->with('success', 'Player created successfully.');
    }
    public function edit(Player $player)
    {
        return view('players.edit', compact('player'));
    }
    public function update(Request $request, Player $player)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'league' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'season' => 'required|string|in:2024-2025,2025-2026,2026-2027',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image is optional
            'flag_id' => 'required|integer',
            'jersey_number' => 'required|integer',
            'status' => 'required|string|in:published,draft,pending',
        ]);

        // Handle the image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($player->image) {
                Storage::disk('wasabi')->delete($player->image);
            }

            // Store the new image
            $imagePath = $request->file('image')->store('players', 'wasabi');
            $player->image = $imagePath;
        }

        // Update the player record
        $player->update([
            'name' => $request->name,
            'league' => $request->league,
            'position' => $request->position,
            'season' => $request->season,
            'flag_id' => $request->flag_id,
            'jersey_number' => $request->jersey_number,
            'status' => $request->status,
        ]);

        // Save the changes
        $player->save();

        // Redirect to the player list with a success message
        return redirect()->route('players.index')->with('success', 'Player updated successfully.');
    }
    public function destroy(Player $player)
    {
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted successfully.');
    }
    public static function fetchSquad(){

                $response = Http::withHeaders([
                    "x-rapidapi-host" => 'flashlive-sports.p.rapidapi.com',
                    "x-rapidapi-key" => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb'
                ])->get('https://flashlive-sports.p.rapidapi.com/v1/teams/squad?sport_id=1&locale=en_INT&team_id=Q3A3IbXH');

                $playersGroups=$response->json()['DATA'];
                foreach($playersGroups as $playersGroup){
                    foreach($playersGroup['ITEMS'] as $player ){

                        Player::where('name', $player['PLAYER_NAME'])->update(
                            [
                                'image' => $player['PLAYER_IMAGE_PATH']??'',
                                'flag_id' => $player['PLAYER_FLAG_ID'],
                                'jersey_number' => $player['PLAYER_JERSEY_NUMBER']??'',
                            ]
                        );
                    }
                }

                // Dump the filtered data
                dd(Player::all());
    }


}
