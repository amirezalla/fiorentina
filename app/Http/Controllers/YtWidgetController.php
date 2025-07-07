<?php

namespace App\Http\Controllers;

use App\Models\YtWidget;
use Illuminate\Http\Request;

class YtWidgetController extends Controller
{
    public function edit()
    {
        $widget = YtWidget::first() ?? new YtWidget;   // singleton
        return view('yt.yt-widget-edit', compact('widget'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|in:live,playlist',
            'live_url' => 'nullable|required_if:type,live|url',
            'playlist_urls.*' => 'nullable|required_if:type,playlist|url',
        ]);

        $widget = YtWidget::first() ?? new YtWidget;
        $widget->type = $request->type;
        $widget->live_url = $request->live_url;
        $widget->playlist_urls = collect($request->playlist_urls)
                                   ->filter()        // drop empties
                                   ->map(fn($u) => yt_id($u))  // helper to strip ID
                                   ->values();
        $widget->save();

        return back()->with('status', 'Widget salvato!');
    }
}
