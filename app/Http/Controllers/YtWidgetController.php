<?php

namespace App\Http\Controllers;

use App\Models\YtWidget;
use Illuminate\Http\Request;

class YtWidgetController extends Controller
{
    /* ----------- 1. edit form ----------- */
    public function edit()
    {
        $widget = YtWidget::first() ?? new YtWidget;   // singleton row
        return view('yt.yt-widget-edit', compact('widget'));
    }

    /* ----------- 2. save form ----------- */
    public function update(Request $request)
    {
        $request->validate([
            'type'           => 'required|in:live,playlist',
            'live_url'       => 'nullable|required_if:type,live|url',
            'playlist_urls'  => 'array',
            'playlist_urls.*'=> 'nullable|required_if:type,playlist|url',
        ]);

        $widget = YtWidget::first() ?? new YtWidget;

        $widget->type          = $request->type;
        $widget->live_url      = $request->live_url;
        $widget->playlist_urls = collect($request->playlist_urls)
                                   ->filter()          // drop empty rows
                                   ->values();        // keep raw URLs (NO yt_id here)

        $widget->save();

        return back()->with('status', 'Widget salvato!');
    }
}
