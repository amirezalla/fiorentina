<?php

namespace App\Http\Controllers;

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


class VoteController extends BaseController
{

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add("Advertisements");
    }

    public function index()
    {
        $this->pageTitle("Votes List");
        $votes = Vote::query()->latest()->paginate(20);
        return view('votes.view', compact('votes'));
    }
    /**
     * Store a newly created vote in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'vote_number' => 'required|integer|min:4|max:8',
        ]);

        // Create a new vote
        Vote::create([
            'player_id' => $request->player_id,
            'vote_number' => $request->vote_number,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Vote submitted successfully!');
    }

    public function create()
    {
        return view('votes.create');
    }

//    public function store(Request $request)
//    {
//        // Validate the incoming request data
//        $validated = $request->validate([
//            'post_title' => 'required|string|max:255',
//            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//            'width' => 'nullable|numeric|min:0',
//            'height' => 'nullable|numeric|min:0',
//            'type' => ['required', Rule::in(array_keys(Ad::TYPES))],
//            'group' => ['required', Rule::in(array_keys(Ad::GROUPS))],
//        ]);
//
//        // Create a new Ad instance
//        $advertisement = new Ad();
//        $advertisement->title = $validated['post_title'];
//        $advertisement->group = $request->group;
//        $advertisement->type = $request->type;
//        $advertisement->width = $request->width;
//        $advertisement->height = $request->height;
//
//        // Handle file upload
//        if ($request->hasFile('image') && $request->file('image')->isValid()) {
//            $filename = Str::random(32) . time() . "." . $request->file('image')->getClientOriginalExtension();
//            $imageResized = ImageManager::gd()->read($request->image);
//            if($request->width && $request->height){
//                $imageResized=$imageResized->resize($request->width, $request->height);
//            }
//            $imageResized=$imageResized->encode();
//            $path = "ads-images/" . $filename;
//            Storage::disk('public')->put($path, $imageResized);
//            $advertisement->image = $path;
//        }
//
//        try {
//            // Save the advertisement to the database
//            $advertisement->save();
//            return redirect()->route('ads.index')->with('success', 'Advertisement created successfully!');
//        } catch (\Exception $e) {
//            Log::error('Failed to save advertisement: ' . $e->getMessage());
//            return redirect()->back()->withErrors('Failed to save advertisement. Please try again.');
//        }
//    }


    public function edit(Ad $ad)
    {
        return view('ads.edit', compact('ad'));
    }

    public function update(Request $request, Ad $ad)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'post_title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'type' => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group' => ['required', Rule::in(array_keys(Ad::GROUPS))],
        ]);
        $data = [
            'title' => $request->post_title,
            'group' => $request->group,
            'type' => $request->type,
            'width' => $request->width,
            'height' => $request->height,
        ];
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $filename = Str::random(32) . time() . "." . $request->file('image')->getClientOriginalExtension();
            $imageResized = ImageManager::gd()->read($request->image);
            if($request->width && $request->height){
                $imageResized=$imageResized->resize($request->width, $request->height);
            }
            $imageResized=$imageResized->encode();
            $path = "ads-images/" . $filename;
            Storage::disk('public')->put($path, $imageResized);
            $data['image'] = $path;
        }
        try {
            // Update the advertisement
            $ad->update($data);
            return redirect()->route('ads.index')->with('success', 'Advertisement updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to save advertisement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to save advertisement. Please try again.');
        }
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();
        return redirect()->route('ads.index')->with('success', 'Ad deleted successfully.');
    }
}
