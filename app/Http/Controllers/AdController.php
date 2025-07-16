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
        /* ─────────────── 1. validation ─────────────── */
        $validated = $request->validate([
            'post_title'        => 'required|string|max:255',
            'width'             => 'nullable|numeric|min:0',
            'height'            => 'nullable|numeric|min:0',
            'weight'            => 'required|numeric|min:1',
            'type'              => ['required', Rule::in(array_keys(Ad::TYPES))],
            'group'             => ['required', Rule::in(array_keys(Ad::GROUPS))],
            'start_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date',
            'images'            => 'nullable|array',
            'images.*'          => 'image|mimes:jpeg,png,jpg,gif,bmp|max:2048',
            'urls'              => 'nullable|array',
            'urls.*'            => 'nullable|url|max:255',
        ]);
    
        /* ─────────────── 2. create the Ad shell ─────────────── */
        $ad = new Ad();
        $ad->title        = $validated['post_title'];
        $ad->group        = $validated['group'];
        $ad->weight       = $validated['weight'];
        $ad->type         = $validated['type'];
        $ad->width        = $validated['width']  ?? null;
        $ad->height       = $validated['height'] ?? null;
        $ad->amp          = $request->amp;
        $ad->start_date   = $validated['start_date']  ?? date('Y-m-d');
        $ad->expiry_date  = $validated['expiry_date'] ?? null;
        /* status */
        $ad->status = ($ad->start_date !== date('Y-m-d')) ? 0 : 1;
    
        /* ─────────────── 3. handle TYPE 1 (images) ─────────────── */
        if ($ad->type == Ad::TYPE_ANNUNCIO_IMMAGINE) {
    
            // Persist now so we have an id for the relationship
            $ad->save();
    
            $files   = $request->file('images', []);
            $targets = $request->input('urls', []);
    
            if (count($files) !== count($targets)) {
                return back()
                       ->withInput()
                       ->withErrors(['urls' => 'Devi fornire un URL per ogni immagine caricata.']);
            }
    
            $storedUrls = [];   // will be saved in `ads.urls`
    
            foreach ($files as $idx => $file) {
                if ($file->isValid()) {
    
                    /* 3‑a  block .webp */
                    $ext = strtolower($file->getClientOriginalExtension());
                    if ($ext === 'webp') {
                        return back()
                               ->withInput()
                               ->withErrors(['images' => "Le immagini .webp non sono consentite."]);
                    }
    
                    /* 3‑b  process + upload */
                    $filename = Str::random(32) . time() . ".$ext";
    
                    $img = ImageManager::gd()->read($file);
                    if ($ad->width && $ad->height) {
                        $img = $img->resize($ad->width, $ad->height);
                    }
                    $tempPath = sys_get_temp_dir() . "/$filename";
                    $img->encode()->save($tempPath);
    
                    $uploaded = $this->rvMedia
                                     ->uploadFromPath($tempPath, 0, 'ads-images/');
                    unlink($tempPath);
    
                    /* 3‑c  save child row */
                    $ad->images()->create([
                        'image_url' => $uploaded['data']->url,
                    ]);
    
                    /* 3‑d  keep redirect url in same order */
                    $storedUrls[] = $targets[$idx] ?? '';
                }
            }
    
            /* 3‑e  save the ordered url list in JSON column */
            $ad->url = json_encode($storedUrls);
            $ad->save();   // update only
        }
    
        /* ─────────────── 4. TYPE 2 (Google / AMP) ─────────────── */
        elseif ($ad->type == Ad::TYPE_GOOGLE_ADS) {
            $ad->amp = $request->amp;   // as before
            $ad->save();
        }
    
        /* ─────────────── 5. schedule future activation ─────────────── */
        if ($ad->start_date !== date('Y-m-d')) {
            $delay = \Carbon\Carbon::parse($ad->start_date)
                                   ->diffInSeconds(now());
            if ($delay > 0) {
                dispatch(new \App\Jobs\ActivateAdJob($ad))->delay($delay);
            }
        }
    
        return redirect()->route('ads.index')
                         ->with('success', 'Advertisement created successfully!');
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
    /* ---------------------------------------------------------------
     * 1.  Validate
     * --------------------------------------------------------------*/
    $isImageAd = $request->input('type') == Ad::TYPE_ANNUNCIO_IMMAGINE;

    $rules = [
        'post_title' => 'required|string|max:255',
        'width'      => 'nullable|numeric|min:0',
        'height'     => 'nullable|numeric|min:0',
        'weight'     => 'required|numeric|min:1',
        'type'       => ['required', Rule::in(array_keys(Ad::TYPES))],
        'group'      => ['required', Rule::in(array_keys(Ad::GROUPS))],
        'start_date' => 'nullable|date',
        'expiry_date'=> 'nullable|date',
    ];

    if ($isImageAd) {
        $rules['images.*']        = 'image|mimes:jpeg,png,jpg,gif,bmp|max:2048';
        $rules['urls_existing.*'] = 'nullable|url|max:255';
        $rules['urls_new.*']      = 'nullable|url|max:255';
    } else {
        $rules['amp']             = 'required|string';
    }

    $validated = $request->validate($rules);

    /* ---------------------------------------------------------------
     * 2.  Fill simple scalar columns
     * --------------------------------------------------------------*/
    $ad->fill([
        'title'       => $validated['post_title'],
        'group'       => $validated['group'],
        'type'        => $validated['type'],
        'weight'      => $validated['weight'],
        'width'       => $validated['width']  ?? null,
        'height'      => $validated['height'] ?? null,
        'amp'         => $request->amp,
        'start_date'  => $validated['start_date']  ?? date('Y-m-d'),
        'expiry_date' => $validated['expiry_date'] ?? null,
        'status'      => $request->boolean('status') ? 1 : 0,
    ])->save();

    /* ---------------------------------------------------------------
     * 3.  Handle image-based ads
     * --------------------------------------------------------------*/
    if ($isImageAd) {

        /* ---------- 3a. remove images that the editor flagged for deletion */
        $deleteIds = array_filter(explode(',', $request->input('deleted_images', '')));
        if ($deleteIds) {
            $ad->images()->whereIn('id', $deleteIds)->delete();
        }

        /* ---------- 3b. update target URLs of the still-existing images */
        $urlsExisting = $request->input('urls_existing', []);

        /* ---------- 3c. handle NEW uploads */
        $newFiles = $request->file('images', []);
        $urlsNew  = $request->input('urls_new', []);

        if (count($newFiles) !== count($urlsNew)) {
            return back()
                ->withInput()
                ->withErrors(['urls_new' => 'Devi fornire un URL per ogni immagine caricata.']);
        }

        $storedUrlMap = []; // [imageId => targetUrl]

        foreach ($newFiles as $idx => $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            if (strtolower($file->getClientOriginalExtension()) === 'webp') {
                return back()->withInput()
                    ->withErrors(['images' => 'Le immagini .webp non sono consentite.']);
            }

            $fileName = Str::random(32) . time() . '.' . $file->getClientOriginalExtension();

            // Resize if width+height supplied
            $img = \Intervention\Image\Facades\Image::make($file);
            if ($ad->width && $ad->height) {
                $img->resize($ad->width, $ad->height);
            }
            $tmp = sys_get_temp_dir() . "/{$fileName}";
            $img->save($tmp);

            /** @var \RvMedia $this->rvMedia (injected via constructor) */
            $upload = $this->rvMedia
                           ->uploadFromPath($tmp, 0, 'ads-images/');
            @unlink($tmp);

            $newImg = $ad->images()->create([
                'image_url' => $upload['data']->url,
            ]);

            $storedUrlMap[$newImg->id] = $urlsNew[$idx] ?? '';
        }

        /* ---------- 3d. rebuild the “url” JSON so its indices match images order */
        $finalUrls = [];

        $ad->load('images'); // refresh after deletions / insertions
        foreach ($ad->images()->orderBy('id')->get() as $img) {
            $finalUrls[] =
                $urlsExisting[$img->id]    // updated via form
             ?? $storedUrlMap[$img->id]   // brand-new upload
             ?? '';                       // keep empty if user left it blank
        }

        $ad->url = json_encode($finalUrls);
        $ad->save();
    }

    /* ---------------------------------------------------------------
     * 4.  Schedule activation job (if start date is in the future)
     * --------------------------------------------------------------*/
    if ($ad->start_date && \Carbon\Carbon::parse($ad->start_date)->isFuture()) {
        $delay = \Carbon\Carbon::parse($ad->start_date)->diffInSeconds(now());
        \App\Jobs\ActivateAdJob::dispatch($ad)->delay($delay);
    }

    return redirect()
        ->route('ads.index')
        ->with('success', 'Advertisement updated successfully!');
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

public function updateSortAds(Request $request)
{
    // Validate the incoming data
    $validated = $request->validate([
        'group'   => 'required|numeric',
        'weights' => 'required|array',
    ]);

    $groupId = $validated['group'];
    $weights = $validated['weights'];

    try {
        // Loop through each ad id and update the ad weight accordingly
        foreach ($weights as $adId => $newWeight) {
            $ad = Ad::find($adId);
            if ($ad) {
                $ad->weight = $newWeight;
                $ad->save();
            }
        }
        return redirect()->route('ads.sort', ['group' => $groupId])
                         ->with('success', 'Ad weights updated successfully.');
    } catch (\Exception $e) {
        \Log::error('Error updating ad weights: ' . $e->getMessage());
        return redirect()->route('ads.sort', ['group' => $groupId])
                         ->with('error', 'Failed to update ad weights. Please try again.');
    }
}
}
