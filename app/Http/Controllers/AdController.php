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
    $groupId = (int) $request->query('group');
    if (!$groupId) {
        return redirect()->route('ads.groups.index')->with('error', 'No group specified.');
    }

    $allGroups = \App\Models\Ad::GROUPS;
    $groupName = $allGroups[$groupId] ?? 'Unknown Group';

    // Load ads in this group (with their group + images)
    $ads = \App\Models\Ad::where('group', $groupId)
        ->with(['groupRef.images'])   // needs Ad::groupRef() belongsTo AdGroup
        ->orderBy('id')
        ->get();

    $adsCount = $ads->count();

    // Load the group with images (for single-ad case)
    $adGroup = \App\Models\AdGroup::with('images')->find($groupId);

    // Labels aggregation (kept if you already use it)
    $labels = [];
    foreach ($ads as $ad) {
        $lbl = trim((string) ($ad->label ?? ''));
        if ($lbl !== '') {
            if (!isset($labels[$lbl])) $labels[$lbl] = ['count' => 0, 'weight' => 0];
            $labels[$lbl]['count']  += 1;
            $labels[$lbl]['weight'] += (int) $ad->weight;
        }
    }

    // Decide default mode
    //  - single ad  → images mode
    //  - multiple   → group (equalize) mode by default; you can still go "ads" or "labels"
    $mode = $request->query('mode');
    if (!$mode) {
        $mode = ($adsCount === 1) ? 'images' : 'group'; // 'group' is the equalize tab
    }

    return view('ads.sort', compact('ads', 'groupId', 'groupName', 'labels', 'mode', 'adsCount', 'adGroup'));
}

// In AdController@updateSortAds

public function updateSortAds(Request $request)
{
    $validated = $request->validate([
        'group' => 'required|integer',
        'mode'  => 'required|string|in:ads,labels,images,group',
    ]);

    $groupId = (int) $validated['group'];
    $mode    = $validated['mode'];

    if ($mode === 'images') {
        // Update weights on ad_group_images (single-ad case)
        $data = $request->validate([
            'image_weights'   => 'required|array',
            'image_weights.*' => 'required|integer|min:0|max:1000',
        ]);

        $group = \App\Models\AdGroup::findOrFail($groupId);
        $images = \App\Models\AdGroupImage::where('group_id', $group->id)
                    ->whereIn('id', array_keys($data['image_weights']))
                    ->get();

        foreach ($images as $img) {
            $img->weight = (int) $data['image_weights'][$img->id];
            $img->save();
        }

        return redirect()->route('ads.sort', ['group' => $groupId, 'mode' => 'images'])
            ->with('success', 'Image weights updated.');
    }

    if ($mode === 'group') {
        // Equalize total across all ads in this group
        $data = $request->validate([
            'total_weight' => 'required|integer|min:0|max:100000',
        ]);

        $total = (int) $data['total_weight'];

        $ads = \App\Models\Ad::where('group', $groupId)->orderBy('id')->get();
        $n   = max(1, $ads->count());

        $base = intdiv($total, $n);
        $rem  = $total % $n;

        foreach ($ads as $i => $ad) {
            $ad->weight = $base + ($i < $rem ? 1 : 0);
            $ad->save();
        }

        return redirect()->route('ads.sort', ['group' => $groupId, 'mode' => 'group'])
            ->with('success', 'Group total distributed equally across ads.');
    }

    if ($mode === 'labels') {
        // Scale each label’s sum and proportionally update underlying ads
        $data = $request->validate([
            'label_weights'   => 'required|array',
            'label_weights.*' => 'required|integer|min:0|max:100000',
        ]);

        // collect all ads in group keyed by label
        $ads = \App\Models\Ad::where('group', $groupId)->orderBy('id')->get()->groupBy(function($ad) {
            return trim((string) ($ad->label ?? ''));
        });

        foreach ($data['label_weights'] as $label => $newSum) {
            $bag = $ads->get($label, collect());
            if ($bag->isEmpty()) continue;

            $oldSum = (int) $bag->sum('weight');
            $newSum = (int) $newSum;

            if ($oldSum === 0) {
                // If old sum is 0: set equally
                $n = $bag->count();
                $base = intdiv($newSum, $n);
                $rem  = $newSum % $n;
                foreach ($bag->values() as $i => $ad) {
                    $ad->weight = $base + ($i < $rem ? 1 : 0);
                    $ad->save();
                }
            } else {
                // Scale proportionally, then fix rounding to hit exact total
                $scaled = [];
                $sumScaled = 0;
                foreach ($bag as $ad) {
                    $w = (int) round($ad->weight * ($newSum / $oldSum));
                    $scaled[$ad->id] = $w;
                    $sumScaled += $w;
                }
                // Fix rounding diff
                $diff = $newSum - $sumScaled;
                if ($diff !== 0) {
                    // add/subtract 1 to the first |diff| items
                    foreach ($bag as $ad) {
                        if ($diff === 0) break;
                        $scaled[$ad->id] += ($diff > 0 ? 1 : -1);
                        $diff += ($diff > 0 ? -1 : 1);
                    }
                }
                // save
                foreach ($bag as $ad) {
                    $ad->weight = max(0, (int) $scaled[$ad->id]);
                    $ad->save();
                }
            }
        }

        return redirect()->route('ads.sort', ['group' => $groupId, 'mode' => 'labels'])
            ->with('success', 'Label weights updated.');
    }

    // Fallback: manual per-ad sliders (your previous behavior)
    $data = $request->validate([
        'weights'   => 'required|array',
        'weights.*' => 'required|integer|min:0|max:100000',
    ]);

    foreach ($data['weights'] as $adId => $w) {
        if ($ad = \App\Models\Ad::find($adId)) {
            $ad->weight = (int) $w;
            $ad->save();
        }
    }

    return redirect()->route('ads.sort', ['group' => $groupId, 'mode' => 'ads'])
        ->with('success', 'Ad weights updated.');
}

}
