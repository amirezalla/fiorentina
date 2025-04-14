<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\AdStatistic;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Botble\Media\RvMedia;
use Illuminate\Support\Facades\DB;


class AdController extends BaseController
{
    public function __construct(protected RvMedia $rvMedia)
    {
    }

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()->add("Advertisements");
    }
    public function index(Request $request)
    {
        $this->pageTitle("Ads List");
    
        $ads = Ad::query()
            // 1) add the sums for impressions & clicks:
            ->withSum('adStatistics as total_impressions', 'impressions')
            ->withSum('adStatistics as total_clicks', 'clicks')
            
            // 2) apply the existing filters
            ->where(function ($q) use ($request) {
                $q->when(
                    $request->filled('group') && in_array($request->group, array_keys(Ad::GROUPS)),
                    fn($q) => $q->where('group', $request->group)
                )
                ->when(
                    $request->filled('q'),
                    fn($q) => $q->where('title', 'LIKE', '%' . $request->q . '%')
                )
                ->when(
                    $request->filled('status') && in_array(intval($request->status), [1, 2]),
                    function ($q) use ($request) {
                        $request->status == 1
                            ? $q->where('status', 1)
                            : $q->where('status', 0);
                    }
                );
            })
            ->latest()
            ->paginate(20);
    
        return view('ads.view', compact('ads'));
    }

    public function create()
    {
        $this->pageTitle("Create Ad");
        return view('ads.create');
    }

    public function store(Request $request)
{
    // Validate the incoming request data including start_date and expiry_date.
    $validated = $request->validate([
        'post_title'  => 'required|string|max:255',
        'width'       => 'nullable|numeric|min:0',
        'height'      => 'nullable|numeric|min:0',
        'weight'      => 'required|numeric|min:1',
        'type'        => ['required', Rule::in(array_keys(Ad::TYPES))],
        'group'       => ['required', Rule::in(array_keys(Ad::GROUPS))],
        'start_date'  => 'nullable|date',
        'expiry_date' => 'nullable|date',
    ]);

    $advertisement = new Ad();
    $advertisement->title   = $validated['post_title'];
    $advertisement->group   = $request->group;
    $advertisement->weight  = $request->weight;
    $advertisement->type    = $request->type;
    $advertisement->width   = $request->width;
    $advertisement->height  = $request->height;
    $advertisement->url     = $request->url;
    $advertisement->amp     = $request->amp;
    $advertisement->start_date  = $request->start_date;
    $advertisement->expiry_date = $request->expiry_date;

    // Set status: if start_date is provided and it's not today, mark as pending (0); otherwise, active (1)
    if ($request->start_date && $request->start_date != date('Y-m-d')) {
        $advertisement->status = 0; // pending
    } else {
        $advertisement->status = 1; // active
    }

    // For image-based ads (type 1)
    if ($advertisement->type == 1) {
        // Save the advertisement to get an ID.
        $advertisement->save();

        if ($request->hasFile('images')) {
            $files = $request->file('images');

            foreach ($files as $file) {
                if ($file->isValid()) {
                    // Check file extension and reject if it is .webp.
                    $ext = strtolower($file->getClientOriginalExtension());
                    if ($ext === 'webp') {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['image' => "Una o più immagini caricate hanno un'estensione .webp, che non è accettabile."]);
                    }

                    // Generate a unique filename.
                    $filename = Str::random(32) . time() . "." . $file->getClientOriginalExtension();

                    // Read and optionally resize the image.
                    $imageResized = ImageManager::gd()->read($file);
                    if ($request->width && $request->height) {
                        $imageResized = $imageResized->resize($request->width, $request->height);
                    }
                    $imageResized = $imageResized->encode();

                    // Save the processed image to a temporary path.
                    $tempPath = sys_get_temp_dir() . '/' . $filename;
                    file_put_contents($tempPath, $imageResized);

                    // Upload the image via RvMedia.
                    $uploadResult = $this->rvMedia->uploadFromPath($tempPath, 0, 'ads-images/');
                    unlink($tempPath);

                    // Create an associated AdImage record.
                    // Laravel automatically sets 'ad_id' from the relationship.
                    $advertisement->images()->create([
                        'image_url' => $uploadResult['data']->url,
                    ]);
                }
            }
        }
    } else {
        // For Google Ad Manager or other ad types (type 2)
        $advertisement->image = $request->image;
        $advertisement->save();
    }

    try {
        // (If not already saved, ensure the advertisement is saved.)
        if (!$advertisement->exists) {
            $advertisement->save();
        }

        // If start_date is set in the future, dispatch a job to activate the ad on that day.
        if ($advertisement->start_date && $advertisement->start_date != date('Y-m-d')) {
            $startDate = \Carbon\Carbon::parse($advertisement->start_date);
            $delay = $startDate->diffInSeconds(\Carbon\Carbon::now());
            if ($delay > 0) {
                dispatch(new \App\Jobs\ActivateAdJob($advertisement))
                    ->delay($delay);
            }
        }

        return redirect()->route('ads.index')->with('success', 'Advertisement created successfully!');
    } catch (\Exception $e) {
        \Log::error('Failed to save advertisement: ' . $e->getMessage());
        return redirect()->back()->withErrors('Failed to save advertisement. Please try again.');
    }
}

public function groupsIndex()
{
    // 1) Retrieve all groups from the Ad model
    $allGroups = Ad::GROUPS;  // an associative array: [groupId => "Group Name", ...]

    // 2) Retrieve counts of ads per group in a single query, grouped by 'group' column
    $counts = Ad::select('group', DB::raw('COUNT(*) as total'))
        ->groupBy('group')
        ->pluck('total', 'group')
        ->toArray();
    // $counts will look like [3 => 5, 4 => 2, ...] meaning group 3 has 5 ads, group 4 has 2 ads, etc.

    // 3) Separate groups into desktop or mobile
    //    We'll define "mobile" if the name has "MOBILE" in it. Otherwise it’s desktop.
    $desktopGroups = [];
    $mobileGroups = [];

    foreach ($allGroups as $groupId => $groupName) {
        if (Str::contains(Str::upper($groupName), 'MOBILE')) {
            $mobileGroups[$groupId] = $groupName;
        } else {
            $desktopGroups[$groupId] = $groupName;
        }
    }

    return view('ads.groups', compact('desktopGroups', 'mobileGroups', 'counts'));
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
            'width'      => 'nullable|numeric|min:0',
            'height'     => 'nullable|numeric|min:0',
            'weight'     => 'required|numeric|min:1',
            'type'       => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group'      => ['required', Rule::in(array_keys(Ad::GROUPS))],
        ]);

        $status = $request->status == '1' ? 1 : 0;

        $data = [
            'title'  => $request->post_title,
            'group'  => $request->group,
            'type'   => $request->type,
            'width'  => $request->width,
            'height' => $request->height,
            'url'    => $request->url,
            'weight' => $request->weight,
            'amp'    => $request->amp ?? null,
            'status' => $status,
        ];

        if ($data['type'] == 1) {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $filename     = Str::random(32) . time() . "." . $request->file('image')->getClientOriginalExtension();
                $imageResized = ImageManager::gd()->read($request->image);

                if ($request->width && $request->height) {
                    $imageResized = $imageResized->resize($request->width, $request->height);
                }
                $imageResized = $imageResized->encode();
                $path = "ads-images/" . $filename;
                Storage::disk('public')->put($path, $imageResized);
                $data['image'] = $path;
            }
        } else {
            $data['image'] = $request->image;
        }

        try {
            $ad->update($data);
            $ad->refresh();
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

    public function trackClick($id)
    {
        $ad = Ad::findOrFail($id);
        // increment clicks for the given ad
        AdStatistic::trackClick($ad->id);

        // redirect to the ad’s URL
        return redirect()->to($ad->url);
    }
}
