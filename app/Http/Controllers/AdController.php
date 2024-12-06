<?php

namespace App\Http\Controllers;

use App\Models\Ad;
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


class AdController extends BaseController
{

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add("Advertisements");
    }

    public function index()
    {
        $this->pageTitle("Ads List");
        $ads = Ad::query()->latest()->paginate(20);
        return view('ads.view', compact('ads'));
    }


    public function create()
    {
        $this->pageTitle("Crea Ad");

        return view('ads.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'post_title' => 'required|string|max:255',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'required|numeric|min:1',
            'type' => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group' => ['required', Rule::in(array_keys(Ad::GROUPS))],
        ]);

        // Create a new Ad instance
        $advertisement = new Ad();
        $advertisement->title = $validated['post_title'];
        $advertisement->group = $request->group;
        $advertisement->weight = $request->weight;
        $advertisement->type = $request->type;
        $advertisement->width = $request->width;
        $advertisement->height = $request->height;
        $advertisement->url = $request->url;
        $advertisement->amp = $request->amp;

        // Handle file upload
        if($advertisement->type ==1){
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $filename = Str::random(32) . time() . "." . $request->file('image')->getClientOriginalExtension();
                $imageResized = ImageManager::gd()->read($request->image);
                if($request->width && $request->height){
                    $imageResized=$imageResized->resize($request->width, $request->height);
                }
                $imageResized=$imageResized->encode();
                $path = "ads-images/" . $filename;
                Storage::disk('public')->put($path, $imageResized);
                $advertisement->image = $path;
            }
        }else{
            $advertisement->image=$request->image;
        }


        try {
            // Save the advertisement to the database
            $advertisement->save();
            return redirect()->route('ads.index')->with('success', 'Advertisement created successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to save advertisement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to save advertisement. Please try again.');
        }
    }


    public function edit(Ad $ad)
    {
        return view('ads.edit', compact('ad'));
    }

    public function update(Request $request, Ad $ad)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'post_title' => 'required|string|max:255',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'required|numeric|min:1',
            'type' => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group' => ['required', Rule::in(array_keys(Ad::GROUPS))],
        ]);
        if($request->status=='1'){
            $status=1;
        }else{$status=0;}
        $data = [
            'title' => $request->post_title,
            'group' => $request->group,
            'type' => $request->type,
            'width' => $request->width,
            'height' => $request->height,
            'url' => $request->url,
            'weight' => $request->weight,
            'amp' => $request->amp??null,
            'status' => $status,
        ];
        if($data['type'] ==1){
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
        }else{
            $data['image'] =$request->image;
        }
        try {
            // Update the advertisement
            $ad->update($data);
            $ad->refresh(); // Refreshes the model from the database to reflect the latest state
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
