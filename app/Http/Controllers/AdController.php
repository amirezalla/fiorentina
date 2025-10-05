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
    $this->pageTitle('Ads List');

    /* -----------------------------------------------------------------
     | 1.  Resolve the selected time‑frame into a date window
     * ----------------------------------------------------------------*/
    $range = match ($request->input('tf')) {
        'today' => [now()->toDateString(), now()->toDateString()],
        '7'     => [now()->subDays(6)->toDateString(),  now()->toDateString()],
        '30'    => [now()->subDays(29)->toDateString(), now()->toDateString()],
        '90'    => [now()->subDays(89)->toDateString(), now()->toDateString()],
        default => null,   // “all time”
    };

    /* -----------------------------------------------------------------
     | 2.  Build the query
     * ----------------------------------------------------------------*/
    $ads = Ad::query()

        /* ---- 2‑a  add the sums for impressions & clicks ------------ */
        ->when($range,
            /* windowed sums (selected tf) */
            fn($q) => $q->withSum(
                        ['adStatistics as total_impressions' => fn($s)
                            => $s->whereBetween('date', $range)], 'impressions')
                        ->withSum(
                        ['adStatistics as total_clicks' => fn($s)
                            => $s->whereBetween('date', $range)], 'clicks'),
            /* fallback: all‑time sums */
            fn($q) => $q->withSum('adStatistics as total_impressions', 'impressions')
                        ->withSum('adStatistics as total_clicks',       'clicks')
        )

        /* ---- 2‑b  existing filters -------------------------------- */
        ->where(function ($q) use ($request) {
            $q->when(
                $request->filled('group') && in_array($request->group, array_keys(Ad::GROUPS)),
                fn($q) => $q->where('group', $request->group)
            )->when(
                $request->filled('q'),
                fn($q) => $q->where('title', 'LIKE', '%' . $request->q . '%')
            )->when(
                $request->filled('status') && in_array((int) $request->status, [1, 2]),
                fn($q)   => $q->where('status', $request->status == 1 ? 1 : 0)
            );
        })

        ->latest()
        ->paginate(20)
        ->withQueryString();   // keep the tf key during pagination

    /* -----------------------------------------------------------------
     | 3.  Render view – also pass the tf key so the <select> keeps state
     * ----------------------------------------------------------------*/
    return view('ads.view', [
        'ads' => $ads,
        'tf'  => $request->input('tf', 'all'),
    ]);
}


    public function create()
    {
        $this->pageTitle("Create Ad");
        return view('ads.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'post_title'       => 'required|string|max:255',
        'width'            => 'nullable|numeric|min:0',
        'height'           => 'nullable|numeric|min:0',
        'weight'           => 'required|numeric|min:1',
        'type'             => ['required', Rule::in(array_keys(Ad::TYPES))],
        'group'            => ['required', Rule::in(array_keys(Ad::GROUPS))],
        'start_date'       => 'nullable|date',
        'expiry_date'      => 'nullable|date',
        'images'           => 'nullable|array',
        'images.*'         => 'image|mimes:jpeg,png,jpg,gif,bmp|max:2048',
        'urls'             => 'nullable|array',
        'urls.*'           => 'nullable|url|max:255',
        'placement'        => ['nullable','in:homepage,article,both'],
'label' => 'nullable|string|max:100',
'ad_group_id' => [
    'nullable',
    'exists:ad_groups,id',
],
    ]);

    $ad = new Ad();
    $ad->title                  = $validated['post_title'];
    $ad->group                  = $validated['group'];
    $ad->weight                 = $validated['weight'];
    $ad->type                   = $validated['type'];
    $ad->width                  = $validated['width']  ?? null;
    $ad->height                 = $validated['height'] ?? null;
    $ad->amp                    = $request->amp;
    $ad->visualization_condition= $this->packVisualizationCondition($request);
    $ad->placement              = $request->input('placement');
    $ad->start_date             = $validated['start_date']  ?? date('Y-m-d');
    $ad->expiry_date            = $validated['expiry_date'] ?? null;
    
    $ad->status                 = ($ad->start_date !== date('Y-m-d')) ? 0 : 1;
$ad->label  = $validated['label'] ?? null;
// ... rest unchanged


// upsert the label into ad_labels
if (!empty($ad->label)) {
    \App\Models\AdLabel::firstOrCreate(['name' => $ad->label]);
}

    if ($ad->type == Ad::TYPE_ANNUNCIO_IMMAGINE) {
    $request->validate(['ad_group_id' => 'required|exists:ad_groups,id']);
$ad->ad_group_id = $validated['ad_group_id'];
        $ad->save();
    } elseif ($ad->type == Ad::TYPE_GOOGLE_ADS) {
        $ad->save();
    }

    if ($ad->start_date !== date('Y-m-d')) {
        $delay = \Carbon\Carbon::parse($ad->start_date)->diffInSeconds(now());
        if ($delay > 0) dispatch(new \App\Jobs\ActivateAdJob($ad))->delay($delay);
    }

    return redirect()->route('ads.index')->with('success', 'Advertisement created successfully!');
}


    private function packVisualizationCondition(Request $r): ?string
{
    switch ($r->input('vis_cond_type')) {
        case 'page_impressions':
            return json_encode([
                'type' => 'page',
                'max'  => (int) $r->input('vis_page_value'),
            ]);

        case 'ad_impressions':
            return json_encode([
                'type'    => 'ad',
                'max'     => (int) $r->input('vis_ad_max'),
                'seconds' => (int) $r->input('vis_ad_seconds'),
            ]);

        default:
            return null;
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
    $isImageAd = $request->input('type') == Ad::TYPE_ANNUNCIO_IMMAGINE;

    $rules = [
        'post_title'      => 'required|string|max:255',
        'width'           => 'nullable|numeric|min:0',
        'height'          => 'nullable|numeric|min:0',
        'weight'          => 'required|numeric|min:1',
        'type'            => ['required', Rule::in(array_keys(Ad::TYPES))],
        'group'           => ['required', Rule::in(array_keys(Ad::GROUPS))],
        'start_date'      => 'nullable|date',
        'expiry_date'     => 'nullable|date',
        'placement'       => ['nullable','in:homepage,article,both'],
            'label' => 'nullable|string|max:100',

    ];

    if ($isImageAd) {
        $rules['images.*']        = 'image|mimes:jpeg,png,jpg,gif,bmp|max:2048';
        $rules['urls_existing.*'] = 'nullable|url|max:255';
        $rules['urls_new.*']      = 'nullable|url|max:255';
    } else {
        $rules['amp']             = 'required|string';
    }

    $validated = $request->validate($rules);

    $ad->fill([
        'title'                  => $validated['post_title'],
        'group'                  => $validated['group'],
        'type'                   => $validated['type'],
        'weight'                 => $validated['weight'],
        'width'                  => $validated['width']  ?? null,
        'height'                 => $validated['height'] ?? null,
        'amp'                    => $request->amp,
        'start_date'             => $validated['start_date']  ?? date('Y-m-d'),
        'expiry_date'            => $validated['expiry_date'] ?? null,
        'status'                 => $request->boolean('status') ? 1 : 0,
        'visualization_condition'=> $this->packVisualizationCondition($request),
        'placement'              => $request->input('placement'),
            'label' => $validated['label'] ?? null,

    ])->save();

    if (!empty($ad->label)) {
    \App\Models\AdLabel::firstOrCreate(['name' => $ad->label]);
}
    if ($isImageAd) {
        $deleteIds = array_filter(explode(',', $request->input('deleted_images', '')));
        if ($deleteIds) $ad->images()->whereIn('id', $deleteIds)->delete();

        $urlsExisting = $request->input('urls_existing', []);
        $newFiles     = $request->file('images', []);
        $urlsNew      = $request->input('urls_new', []);

        if (count($newFiles) !== count($urlsNew)) {
            return back()->withInput()->withErrors(['urls_new' => 'Devi fornire un URL per ogni immagine caricata.']);
        }

        $storedUrlMap = [];
        foreach ($newFiles as $i => $file) {
            if (!$file || !$file->isValid() || strtolower($file->getClientOriginalExtension()) === 'webp') {
                return back()->withInput()->withErrors(['images' => 'File non valido o formato non consentito.']);
            }
            $name = Str::random(32) . time() . '.' . $file->getClientOriginalExtension();
            $img  = \Intervention\Image\Facades\Image::make($file);
            if ($ad->width && $ad->height) $img->resize($ad->width, $ad->height);
            $tmp  = sys_get_temp_dir() . "/$name";
            $img->save($tmp);
            $up   = $this->rvMedia->uploadFromPath($tmp, 0, 'ads-images/');
            unlink($tmp);
            $newImg = $ad->images()->create(['image_url' => $up['data']->url]);
            $storedUrlMap[$newImg->id] = $urlsNew[$i] ?? '';
        }

        $finalUrls = [];
        $ad->load('images');
        foreach ($ad->images()->orderBy('id')->get() as $img) {
            $finalUrls[] = $urlsExisting[$img->id] ?? $storedUrlMap[$img->id] ?? '';
        }
        $ad->url = json_encode($finalUrls);
        $ad->save();
    }

    return back()->with('success','Advertisement updated successfully!');
}


    public function destroy(Ad $ad)
    {
        $ad->delete();
        return redirect()->route('ads.index')->with('success', 'Ad deleted successfully.');
    }

    public function trackClick(int $id)
    {
        $ad = Ad::findOrFail($id);
    
        // 1. add +1 to today’s click counter
        AdStatistic::trackClick($ad->id);
    
        // 2. pick the right redirect
        //    – uses getRedirectUrl() you already placed in the model
        //    – falls back to the legacy single‑url column if needed
        $target = $ad->getRedirectUrl() ?: $ad->url ?: '/';
    
        // 3. send the user on her way
        return redirect()->away($target);
    }


    public function sortAds(Request $request)
{
    // Expect the group id in the query string (?group=...)
    $groupId = $request->query('group');
    if (!$groupId) {
        return redirect()->route('ads.groups.index')->with('error', 'No group specified.');
    }

    // Get group name from your Ad::GROUPS constant
    $allGroups = Ad::GROUPS;
    $groupName = isset($allGroups[$groupId]) ? $allGroups[$groupId] : 'Unknown Group';

    // Query ads that belong to this group (you might want to add ordering here)
    $ads = Ad::where('group', $groupId)->orderBy('id')->get();

    return view('ads.sort', compact('ads', 'groupId', 'groupName'));
}

// In AdController@updateSortAds

public function updateSortAds(Request $request)
{
    $validated = $request->validate([
        'group' => 'required|numeric',
        'mode'  => 'nullable|in:ads,labels',
        // ads-mode payload:
        'weights' => 'sometimes|array',
        // labels-mode payload:
        'label_weights' => 'sometimes|array',
    ]);

    $groupId = (int) $validated['group'];
    $mode    = $validated['mode'] ?? 'ads';

    try {
        if ($mode === 'labels') {
            // New: Adjust sums per label, scale contained ads
            $labelTargets = $validated['label_weights'] ?? [];

            // Fetch all ads in group once
            $ads = \App\Models\Ad::where('group', $groupId)->get()
                   ->groupBy(fn($ad) => $ad->label ?: '(no label)');

            foreach ($labelTargets as $label => $targetSum) {
                $target = max(0, (int) $targetSum); // allow 0 to mute a label entirely
                $bucket = $ads->get($label, collect());

                if ($bucket->isEmpty()) continue;

                $currentSum = (int) $bucket->sum('weight');
                if ($currentSum <= 0) {
                    // If current is zero, distribute evenly with weight=1 if target > 0
                    if ($target > 0) {
                        $per = max(1, (int) floor($target / max(1, $bucket->count())));
                        foreach ($bucket as $ad) {
                            $ad->weight = $per;
                            $ad->save();
                        }
                        // If there is remainder, add +1 to first N ads
                        $remainder = $target - ($per * $bucket->count());
                        foreach ($bucket->take($remainder) as $ad) {
                            $ad->weight += 1;
                            $ad->save();
                        }
                    } else {
                        // target == 0 → set all to 0
                        foreach ($bucket as $ad) {
                            $ad->weight = 0;
                            $ad->save();
                        }
                    }
                    continue;
                }

                // Scale proportionally
                $factor = $target / $currentSum;

                // First pass: compute new weights (rounded), ensure non-negative
                $newWeights = [];
                $sumRounded = 0;
                foreach ($bucket as $ad) {
                    $nw = (int) round($ad->weight * $factor);
                    if ($target > 0) $nw = max(1, $nw); // keep at least 1 if target>0
                    else $nw = 0;                       // if target 0, everyone 0
                    $newWeights[$ad->id] = $nw;
                    $sumRounded += $nw;
                }

                // Adjust rounding drift to hit exact target sum
                $diff = $target - $sumRounded;
                if ($diff !== 0 && $bucket->count() > 0) {
                    // Sort ads descending by current weight to spread remainders sensibly
                    $ordered = $bucket->sortByDesc('weight')->values();
                    $i = 0;
                    while ($diff !== 0 && $i < $ordered->count()) {
                        $id = $ordered[$i]->id;
                        if ($diff > 0) {
                            $newWeights[$id] += 1; $diff -= 1;
                        } else { // diff < 0
                            if ($newWeights[$id] > 0) { $newWeights[$id] -= 1; $diff += 1; }
                        }
                        $i = ($i + 1) % $ordered->count();
                    }
                }

                // Persist
                foreach ($bucket as $ad) {
                    $ad->weight = $newWeights[$ad->id];
                    $ad->save();
                }
            }

            return redirect()->route('ads.sort', ['group' => $groupId, 'mode' => 'labels'])
                             ->with('success', 'Label weights updated successfully.');

        } else {
            // Existing per-ad sliders
            foreach (($validated['weights'] ?? []) as $adId => $newWeight) {
                $ad = \App\Models\Ad::where('group', $groupId)->find($adId);
                if ($ad) {
                    $ad->weight = (int) $newWeight;
                    $ad->save();
                }
            }
            return redirect()->route('ads.sort', ['group' => $groupId, 'mode' => 'ads'])
                             ->with('success', 'Ad weights updated successfully.');
        }

    } catch (\Throwable $e) {
        \Log::error('Error updating weights: ' . $e->getMessage());
        return redirect()->route('ads.sort', ['group' => $groupId, 'mode' => $mode])
                         ->with('error', 'Failed to update weights. Please try again.');
    }
}

}
