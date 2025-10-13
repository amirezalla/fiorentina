<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\AdGroup;
use App\Models\AdGroupImage;
use App\Models\AdLabel;
use App\Models\AdStatistic;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()->add('Advertisements');
    }

    public function click(Request $request)
{
        $dest = (string) $request->query('url', '');
        dd($dest);

        // Bump click if we know the image id

            if ($img = AdGroupImage::where('url', $dest)->first()) {
                $img->bumpClick();
            }


        // Very defensive fallback
        if ($dest === '' || !preg_match('~^https?://~i', $dest)) {
            $dest = url('/'); // home as a safe default
        }

        return redirect()->away($dest);

}


    /* -----------------------------------------------------------
     | Index
     * ----------------------------------------------------------*/
    public function index(Request $request)
    {
        $this->pageTitle('Ads List');

        $range = match ($request->input('tf')) {
            'today' => [now()->toDateString(), now()->toDateString()],
            '7'     => [now()->subDays(6)->toDateString(), now()->toDateString()],
            '30'    => [now()->subDays(29)->toDateString(), now()->toDateString()],
            '90'    => [now()->subDays(89)->toDateString(), now()->toDateString()],
            default => null,
        };

        $ads = Ad::query()
            ->when(
                $range,
                fn ($q) => $q->withSum(['adStatistics as total_impressions' => fn ($s) => $s->whereBetween('date', $range)], 'impressions')
                             ->withSum(['adStatistics as total_clicks'       => fn ($s) => $s->whereBetween('date', $range)], 'clicks'),
                fn ($q) => $q->withSum('adStatistics as total_impressions', 'impressions')
                             ->withSum('adStatistics as total_clicks',       'clicks')
            )
            ->where(function ($q) use ($request) {
                $q->when(
                    $request->filled('group') && is_numeric($request->group),
                    fn ($q) => $q->where('group', (int) $request->group)
                )->when(
                    $request->filled('q'),
                    fn ($q) => $q->where('title', 'LIKE', '%' . $request->q . '%')
                )->when(
                    $request->filled('status') && in_array((int) $request->status, [1, 2], true),
                    fn ($q) => $q->where('status', (int) $request->status === 1 ? 1 : 0)
                );
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('ads.view', [
            'ads' => $ads,
            'tf'  => $request->input('tf', 'all'),
        ]);
    }

    /* -----------------------------------------------------------
     | Create
     * ----------------------------------------------------------*/
    public function create()
    {
        $this->pageTitle('Create Ad');
        return view('ads.create');
    }

    /* -----------------------------------------------------------
     | Store
     * ----------------------------------------------------------*/
    public function store(Request $request)
    {
        // Base rules (we keep legacy `group` as a simple integer; you can restrict with Rule::in(array_keys(Ad::GROUPS)))
        $rules = [
            'post_title'   => 'required|string|max:255',
            'weight'       => 'required|integer|min:0|max:10',
            'type'         => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group'        => 'nullable|integer',                  // legacy position constant
            'ad_group_id'  => 'nullable|integer|exists:ad_groups,id', // FK to image group
            'width'        => 'nullable|integer|min:0',
            'height'       => 'nullable|integer|min:0',
            'start_date'   => 'nullable|date',
            'expiry_date'  => 'nullable|date|after_or_equal:start_date',
            'placement'    => ['nullable','in:homepage,article,both'],
        ];

        // Conditional rules
        if ((int) $request->input('type') === Ad::TYPE_ANNUNCIO_IMMAGINE) {
            $rules['ad_group_id'] = 'required|integer|exists:ad_groups,id';
        } else {
            $rules['amp'] = 'required|string';
        }

        $validated = $request->validate($rules);

        $ad = new Ad();
        $ad->title        = $validated['post_title'];
        $ad->type         = (int) $validated['type'];
        $ad->group        = $validated['group'] ?? null;          // legacy position
        $ad->ad_group_id  = $validated['ad_group_id'] ?? null;    // FK to ad_groups
        $ad->weight       = (int) $validated['weight'];
        $ad->width        = $validated['width']  ?? null;
        $ad->height       = $validated['height'] ?? null;
        $ad->amp          = $request->input('amp');               // only for Google ads
        $ad->placement    = $validated['placement'] ?? null;

        $ad->visualization_condition = $this->packVisualizationCondition($request);
        $ad->start_date   = $validated['start_date']  ?? date('Y-m-d');
        $ad->expiry_date  = $validated['expiry_date'] ?? null;
        $ad->status       = ($ad->start_date !== date('Y-m-d')) ? 0 : 1;

        $ad->save();

        if (!empty($ad->label)) {
            AdLabel::firstOrCreate(
                ['name' => $ad->label],
                ['slug' => Str::slug($ad->label)]
            );
        }

        if ($ad->start_date !== date('Y-m-d')) {
            $delay = \Carbon\Carbon::parse($ad->start_date)->diffInSeconds(now());
            if ($delay > 0) dispatch(new \App\Jobs\ActivateAdJob($ad))->delay($delay);
        }

        return redirect()->route('ads.index')->with('success', 'Advertisement created successfully!');
    }

    /* -----------------------------------------------------------
     | Edit
     * ----------------------------------------------------------*/
    public function edit(Ad $ad)
    {
        return view('ads.edit', compact('ad'));
    }

    /* -----------------------------------------------------------
     | Update
     * ----------------------------------------------------------*/
    public function update(Request $request, Ad $ad)
    {
        $isImageAd = (int) $request->input('type') === Ad::TYPE_ANNUNCIO_IMMAGINE;

        $rules = [
            'post_title'   => 'required|string|max:255',
            'weight'       => 'required|integer|min:0|max:10',
            'type'         => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group'        => 'nullable|integer',                  // legacy position constant
            'ad_group_id'  => 'nullable|integer|exists:ad_groups,id',
            'width'        => 'nullable|integer|min:0',
            'height'       => 'nullable|integer|min:0',
            'start_date'   => 'nullable|date',
            'expiry_date'  => 'nullable|date|after_or_equal:start_date',
            'placement'    => ['nullable','in:homepage,article,both'],
            'status'       => ['nullable', Rule::in(['0','1'])],
        ];

        if ($isImageAd) {
            $rules['ad_group_id'] = 'required|integer|exists:ad_groups,id';
        } else {
            $rules['amp'] = 'required|string';
        }

        $validated = $request->validate($rules);

        $ad->fill([
            'title'       => $validated['post_title'],
            'type'        => (int) $validated['type'],
            'group'       => $validated['group'] ?? null,
            'ad_group_id' => $validated['ad_group_id'] ?? null,
            'weight'      => (int) $validated['weight'],
            'width'       => $validated['width']  ?? null,
            'height'      => $validated['height'] ?? null,
            'amp'         => $request->input('amp'),
            'start_date'  => $validated['start_date']  ?? date('Y-m-d'),
            'expiry_date' => $validated['expiry_date'] ?? null,
            'status'      => $request->boolean('status') ? 1 : 0,
            'placement'   => $validated['placement'] ?? null,
        ]);

        $ad->visualization_condition = $this->packVisualizationCondition($request);
        $ad->save();

        if (!empty($ad->label)) {
            AdLabel::firstOrCreate(
                ['name' => $ad->label],
                ['slug' => Str::slug($ad->label)]
            );
        }

        return back()->with('success', 'Advertisement updated successfully!');
    }

    /* -----------------------------------------------------------
     | Destroy
     * ----------------------------------------------------------*/
    public function destroy(Ad $ad)
    {
        $ad->delete();
        return redirect()->route('ads.index')->with('success', 'Ad deleted successfully.');
    }

    /* -----------------------------------------------------------
     | Click tracking
     * ----------------------------------------------------------*/
    public function trackClick(int $id)
    {
        $ad = Ad::findOrFail($id);
        AdStatistic::trackClick($ad->id);

        $target = $ad->getRedirectUrl() ?: $ad->url ?: '/';
        return redirect()->away($target);
    }

    /* -----------------------------------------------------------
     | Sort weights (by ads / images for single ad)
     * ----------------------------------------------------------*/
    public function sortAds(Request $request)
    {
        $positionGroupId = (int) $request->query('group'); // legacy position
        if (!$positionGroupId) {
            return redirect()->route('ads.groups.index')->with('error', 'No group specified.');
        }

        $groupName = Ad::GROUPS[$positionGroupId] ?? 'Unknown Group';

        $ads = Ad::where('group', $positionGroupId)
            ->with(['groupRef.images']) // if you kept this relation
            ->orderBy('id')
            ->get();

        $adsCount = $ads->count();

        // If exactly one ad, we edit its image group
        $adGroupForImages = null;
        if ($adsCount === 1) {
            $only = $ads->first();
            if ($only && $only->ad_group_id) {
                $adGroupForImages = AdGroup::with('images')->find($only->ad_group_id);
            }
        }

        // labels (kept in case you still use it elsewhere)
        $labels = [];
        foreach ($ads as $item) {
            $lbl = trim((string) ($item->label ?? ''));
            if ($lbl !== '') {
                if (!isset($labels[$lbl])) $labels[$lbl] = ['count' => 0, 'weight' => 0];
                $labels[$lbl]['count']  += 1;
                $labels[$lbl]['weight'] += (int) $item->weight;
            }
        }

        $mode = $request->query('mode') ?: (($adsCount === 1) ? 'images' : 'ads');

        return view('ads.sort', [
            'ads'       => $ads,
            'groupId'   => $positionGroupId,
            'groupName' => $groupName,
            'labels'    => $labels,
            'mode'      => $mode,
            'adsCount'  => $adsCount,
            'adGroup'   => $adGroupForImages,   // note: this is an image group when single ad
        ]);
    }

    public function updateSortAds(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|integer',                 // legacy position id in the URL
            'mode'  => 'required|string|in:ads,images',
        ]);

        $positionGroupId = (int) $validated['group'];
        $mode = $validated['mode'];

        if ($mode === 'images') {
            // We need the concrete image group id (ad_group_id of the only ad)
            $imgForm = $request->validate([
                'ad_group_id'     => 'required|integer|exists:ad_groups,id',
                'image_weights'   => 'required|array',
                'image_weights.*' => 'required|integer|min:0|max:10',
            ]);

            $images = AdGroupImage::where('group_id', $imgForm['ad_group_id'])
                ->whereIn('id', array_keys($imgForm['image_weights']))
                ->get();

            foreach ($images as $img) {
                $img->weight = (int) $imgForm['image_weights'][$img->id];
                $img->save();
            }

            return redirect()->route('ads.sort', ['group' => $positionGroupId, 'mode' => 'images'])
                ->with('success', 'Image weights updated.');
        }

        // default: by ads in this legacy position
        $data = $request->validate([
            'weights'   => 'required|array',
            'weights.*' => 'required|integer|min:0|max:10',
        ]);

        $ads = Ad::where('group', $positionGroupId)->get()->keyBy('id');
        foreach ($data['weights'] as $adId => $w) {
            if (isset($ads[$adId])) {
                $ads[$adId]->weight = (int) $w;
                $ads[$adId]->save();
            }
        }

        return redirect()->route('ads.sort', ['group' => $positionGroupId, 'mode' => 'ads'])
            ->with('success', 'Ad weights updated.');
    }

    /* -----------------------------------------------------------
     | Helpers
     * ----------------------------------------------------------*/
    private function packVisualizationCondition(Request $r): ?string
    {
        return match ($r->input('vis_cond_type')) {
            'page_impressions' => json_encode([
                'type' => 'page',
                'max'  => (int) $r->input('vis_page_value'),
            ]),
            'ad_impressions' => json_encode([
                'type'    => 'ad',
                'max'     => (int) $r->input('vis_ad_max'),
                'seconds' => (int) $r->input('vis_ad_seconds'),
            ]),
            default => null,
        };
    }


    // App/Http/Controllers/AdController.php

public function ampPreview(\App\Models\Ad $ad)
{
    abort_unless($ad->type == \App\Models\Ad::TYPE_GOOGLE_ADS && $ad->amp, 404);

    // Optional: guard size or sanitize the amp snippet.
    $amp = $ad->amp;

    // Standard AMP boilerplate + amp-ad component
    $html = <<<AMP
<!doctype html>
<html amp lang="en">
<head>
  <meta charset="utf-8">
  <title>Ad Preview #{$ad->id}</title>
  <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
  <script async src="https://cdn.ampproject.org/v0.js"></script>
  <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
  <style amp-boilerplate>
    body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;
         -moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;
         -ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;
         animation:-amp-start 8s steps(1,end) 0s 1 normal both}
    @-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
    @-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
    @-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
    @-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
    @keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
  </style>
  <noscript>
    <style amp-boilerplate>
      body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}
    </style>
  </noscript>
  <style amp-custom>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:20px;}
    .frame{max-width:1024px;margin:0 auto;}
  </style>
</head>
<body>
  <div class="frame">
    {$amp}
  </div>
</body>
</html>
AMP;

    return response($html)->header('Content-Type', 'text/html; charset=utf-8');
}

public function groupsIndex()
{
    // 1) All legacy “positions” (constants on the Ad model)
    $allGroups = Ad::GROUPS; // [id => "Name", ...]

    // 2) How many ads exist per position (legacy `group` column)
    $counts = Ad::select('group')
        ->selectRaw('COUNT(*) as total')
        ->groupBy('group')
        ->pluck('total', 'group')        // [groupId => total]
        ->toArray();

    // 3) Split into desktop vs mobile by name
    $desktopGroups = [];
    $mobileGroups  = [];

    foreach ($allGroups as $id => $name) {
        if (Str::contains(Str::upper($name), 'MOBILE')) {
            $mobileGroups[$id] = $name;
        } else {
            $desktopGroups[$id] = $name;
        }
    }

    // 4) Render the list view
    return view('ads.groups', [
        'desktopGroups' => $desktopGroups,
        'mobileGroups'  => $mobileGroups,
        'counts'        => $counts,
    ]);
}



}
